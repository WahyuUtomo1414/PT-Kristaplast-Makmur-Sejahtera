<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderExportController extends Controller
{
    public function export(Orders $order)
    {
        $order->load(['detailOrder', 'ordersPayment']); // eager load relasi

        $pdf = Pdf::loadView('pdf.order', compact('order'))
                ->setPaper('a4', 'portrait');

        return $pdf->download("order-{$order->id}.pdf");
    }
}
