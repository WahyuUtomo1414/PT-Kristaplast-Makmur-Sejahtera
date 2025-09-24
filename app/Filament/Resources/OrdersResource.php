<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Orders;
use App\Models\Status;
use App\Models\Product;
use App\Models\Shiping;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\OrdersResource\Pages;

class OrdersResource extends Resource
{
    protected static ?string $model = Orders::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?string $navigationLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('code')
                    ->required()
                    ->label('Order Code')
                    ->disabled(fn ($context) => $context === 'edit')
                    ->default(function ($context) {
                        if ($context === 'create') {
                            $randomNumber = rand(100, 99999);
                            return 'ORD-' . $randomNumber . '-' . now()->format('Ymd');
                        }
                        return null;
                    }),
                Repeater::make('detailOrder')
                    ->label('List Product')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->required()
                            ->label('Product')
                            ->options(Product::all()->pluck('name', 'id'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $order = app(Orders::class);
                                $subtotal = $order->calculateSubtotal($state, $get('quantity'));
                                $set('subtotal', $subtotal);
                            }),

                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $order = app(Orders::class);
                                $subtotal = $order->calculateSubtotal($get('product_id'), $state);
                                $set('subtotal', $subtotal);
                            }),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp. ')
                            ->dehydrated(),
                    ])
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $order = app(Orders::class);
                        $total = $order->calculateTotal($get('detailOrder') ?? [], $get('shipping_id'));
                        $set('total_price', $total);
                    })->columnSpanFull(),

                Select::make('shipping_id')
                    ->required()
                    ->columnSpanFull()
                    ->label('Shipping')
                    ->options(Shiping::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $order = app(Orders::class);
                        $total = $order->calculateTotal($get('detailOrder') ?? [], $state);
                        $set('total_price', $total);
                    }),
                Section::make('Pembayaran')
                    ->relationship('ordersPayment') // <--- penting
                    ->schema([
                        Select::make('payment_method_id')
                            ->label('Payment Method')
                            ->options(PaymentMethod::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $paymentMethod = PaymentMethod::find($state);
                                    if ($paymentMethod) {
                                        $set('account_number', $paymentMethod->account_number);
                                        $set('account_name', $paymentMethod->account_name);
                                        $set('payment_procedures', $paymentMethod->payment_procedures);
                                    }
                                } else {
                                    $set('account_number', null);
                                    $set('account_name', null);
                                    $set('payment_procedures', null);
                                }
                            })
                            ->required(),

                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->disabled(),

                        TextInput::make('account_name')
                            ->label('Account Name')
                            ->disabled(),

                        Textarea::make('payment_procedures')
                            ->label('Payment Procedures')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->label('Payment Proof')
                            ->directory('orders-payments')
                            ->image()
                            ->preserveFilenames()
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('note')
                            ->label('Description')
                            ->columnSpanFull(),
                        Select::make('status_id')
                        ->required()
                        ->label('Status')
                        ->searchable()
                        ->default(function () {
                            return Status::where('status_type_id', 4)
                                ->where('name', 'pending')
                                ->value('id');
                        })
                        ->columnSpanFull()
                        ->options(Status::where('status_type_id', 4)->pluck('name', 'id')),
                        ])
                    ->columnSpanFull(),

                TextInput::make('total_price')
                    ->disabled()
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp. ')
                    ->dehydrated(),
                Select::make('status_id')
                    ->required()
                    ->label('Status')
                    ->searchable()
                    ->default(function () {
                        return Status::where('status_type_id', 3)
                            ->where('name', 'PENDING')
                            ->value('id');
                    })
                    ->columnSpanFull()
                    ->options(Status::where('status_type_id', 3)->pluck('name', 'id')),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->color('primary'),
                TextColumn::make('createdBy.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.phone_number')
                    ->label('WhatsApp / Phone Number')
                    ->formatStateUsing(function ($state) {
                        // Ubah 08xxxx menjadi 628xxxx
                        return preg_replace('/^0/', '62', $state);
                    })
                    ->url(fn ($state) => 'https://wa.me/' . preg_replace('/^0/', '62', $state), true)
                    ->color('info')
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('detailOrder.product.name')
                    ->label('Product')  
                    ->sortable(),
                TextColumn::make('detailOrder.quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping.name')
                    ->label('Shipping')
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.')),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updatedBy.name')
                    ->label("Updated by")
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deletedBy.name')
                    ->label("Deleted by")
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->url(fn ($record) => route('orders.export.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrders::route('/create'),
            'edit' => Pages\EditOrders::route('/{record}/edit'),
        ];
    }
}
