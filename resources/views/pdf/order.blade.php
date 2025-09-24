<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h2>Order #{{ $order->id }}</h2>
    <p>Customer: {{ $order->customer->name ?? '-' }}</p>
    <p>Date: {{ $order->created_at->format('d-m-Y') }}</p>

    <h3>Detail Orders</h3>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->detailOrder as $detail)
                <tr>
                    <td>{{ $detail->product->name ?? '-' }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ number_format($detail->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Pembayaran</h3>
    <table>
        <tr>
            <th>Metode</th>
            <td>{{ $order->ordersPayment->method ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ number_format($order->ordersPayment->amount ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $order->ordersPayment->status ?? '-' }}</td>
        </tr>
    </table>
</body>
</html>
