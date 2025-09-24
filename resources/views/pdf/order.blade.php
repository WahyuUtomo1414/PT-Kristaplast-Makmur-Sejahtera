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
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px;">
        <!-- Logo -->
        <div>
            <img src="{{ public_path('images/3.png') }}" alt="Logo" style="height: 90px;">
        </div>

        <!-- Info Invoice -->
        <div style="margin-left:20px;">
            <h2>Invoice Order #{{ $order->id }}</h2>
            <p>Customer: {{ $order->customer->name ?? '-' }}</p>
            <p>Tanggal: {{ $order->created_at->format('d F Y') }}</p>
            <p>Keterangan: {{ $order->keterangan ?? '-' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->detailOrder as $detail)
                <tr>
                    <td>{{ $detail->product->name ?? '-' }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        <h3>Total: Rp {{ number_format($order->total ?? $order->detailOrder->sum(fn($d) => $d->qty * $d->price), 0, ',', '.') }}</h3>
        <h3>Metode Pembayaran: {{ $order->ordersPayment->method ?? '-' }}</h3>
        <h3>
            Status Pembayaran:
            <span style="color: {{ ($order->ordersPayment->status ?? '') === 'Lunas' ? 'green' : 'red' }}; font-weight: bold;">
                {{ $order->ordersPayment->status ?? 'Belum Lunas' }}
            </span>
        </h3>
    </div>
</body>
</html>
