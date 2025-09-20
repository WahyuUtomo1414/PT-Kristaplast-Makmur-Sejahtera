<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Table;
use Tables\Actions\EditAction;
use Tables\Actions\ViewAction;
use Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Actions\DeleteAction as ActionsDeleteAction;

class ProductTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->with('productType', 'status'))
            ->defaultPaginationPageOption(10)
            ->columns([
                View::make('filament.component.product-card'),
            ])->contentGrid([
                'md' => 2,
                'xl' => 3
            ])
            ->searchable();
    }
}
