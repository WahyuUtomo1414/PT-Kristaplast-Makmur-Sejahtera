<?php

namespace App\Models;

use App\Models\Status;
use App\Models\Product;
use App\Models\Shiping;
use App\Traits\AuditedBySoftDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory, Notifiable, SoftDeletes, AuditedBySoftDelete;
    protected $table = 'order';
    protected $guarded = ['id'];

    public function shipping()
    {
        return $this->belongsTo(Shiping::class, 'shipping_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function detailOrder()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function ordersPayment()
    {
        return $this->hasOne(OrdersPayment::class, 'order_id');
    }

    public function calculateSubtotal(?int $productId, ?int $quantity): int
    {
        $productPrice = Product::find($productId)?->price ?? 0;
        $qty = $quantity ?? 0; // kalau null dianggap 0
        return $productPrice * $qty;
    }

    public function calculateTotal(array $detailOrder, ?int $shippingId): int
    {
        $subtotal = 0;

        foreach ($detailOrder as $detail) {
            $subtotal += $this->calculateSubtotal(
                $detail['product_id'] ?? 0,
                $detail['quantity'] ?? 0
            );
        }

        $shippingPrice = Shiping::find($shippingId)?->price ?? 0;

        return $subtotal + $shippingPrice;
    }
}
