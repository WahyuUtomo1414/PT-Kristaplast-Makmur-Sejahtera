<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Order</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #6ecbcf; }
    </style>
</head>
<body>
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <!-- Logo di kiri -->
        <!-- Info Invoice --> 
        <h2>Invoice Order {{ $order->code }}</h2> 
        <p>Customer     : {{ $order->createdBy->name ?? '-' }}</p> 
        <p>Tanggal      : {{ $order->created_at->format('d F Y') }}</p> 
        <p>Keterangan   : {{ $order->ordersPayment->note ?? '-' }}</p> 
        <p>Status Pesanan : {{ $order->status->name ?? '-' }}</p>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->detailOrder as $detail)
                    <tr>
                        <td>{{ $detail->product->name ?? '-' }}</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp {{ number_format($detail->product->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        <td>{{ $detail->note ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:20px;">
            <h3>Metode Pengiriman : {{ $order->shipping->name }}</h3>
            <h3>Biaya Pengiriman : Rp {{ number_format($order->shipping->price), 0, ',', '.' }}</h3>
            <h3>Total : Rp {{ number_format($order->total_price ?? $order->detailOrder->sum(fn($d) => $d->qty * $d->price), 0, ',', '.') }}</h3>
            <h3>Metode Pembayaran : {{ $order->ordersPayment->paymentMethod->name ?? '-' }}</h3>
            <h3>
                Status Pembayaran :
                @php
                    $status = $order->ordersPayment->status->name ?? 'PENDING';
                    $statusColor = match(strtoupper($status)) {
                        'PENDING' => 'blue',
                        'CONFIRMED' => 'green',
                        'FAILED' => 'red',
                        default => 'black'
                    };
                @endphp
                <span style="color: {{ $statusColor }}; font-weight: bold;">
                    {{ ucfirst(strtolower($status)) }}
                </span>
            </h3>
        </div>
        
    </div>
</body>
</html>
