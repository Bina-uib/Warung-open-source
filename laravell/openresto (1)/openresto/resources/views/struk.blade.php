<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        .qty { width: 10%; }
        .price { text-align: right; }
        .total-row { font-weight: bold; }
        
        @media print {
            body { width: 80mm; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h2 style="margin: 0;">OPENRESTO</h2>
        <p>Jl. Kuliner Lezat No. 123<br>Telp: 0812-XXXX-XXXX</p>
    </div>

    <div class="divider"></div>

    <table>
        <tr><td>ID Order</td><td>: #{{ $order->id }}</td></tr>
        <tr><td>Tanggal</td><td>: {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td></tr>
        <tr><td>Customer</td><td>: {{ $order->customer_name }}</td></tr>
        <tr><td>Tipe</td><td>: {{ strtoupper($order->order_type) }} {{ $order->table_number ? '(Meja '.$order->table_number.')' : '' }}</td></tr>
        <tr><td>Kasir</td><td>: {{ $order->admin_user }}</td></tr>
    </table>

    <div class="divider"></div>

    <table>
        @foreach($details as $item)
        <tr>
            <td colspan="3">{{ $item->name }}</td>
        </tr>
        <tr>
            <td class="qty">{{ $item->quantity }}x</td>
            <td>{{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
        </tr>
        @if($item->notes)
        <tr>
            <td colspan="3" style="font-style: italic; font-size: 10px;">* {{ $item->notes }}</td>
        </tr>
        @endif
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ $order->payment_method }})</td>
            <td class="price">Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="price">Rp {{ number_format($order->amount_change, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center">
        <p>Terima Kasih Atas Kunjungan Anda!</p>
        <p>Selamat Menikmati</p>
    </div>

    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px;">Cetak Ulang</button>
        <a href="{{ route('menu') }}" style="display: block; margin-top: 10px;">Kembali ke Menu</a>
    </div>

    <script>
        @if(isset($print) && $print)
        // Auto print saat halaman terbuka
        window.onload = function() {
            // Memberikan sedikit jeda agar browser menyelesaikan proses render
            setTimeout(function() {
                window.print();
            }, 500);
        }
        @endif
    </script>
</body>
</html>