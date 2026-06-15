<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard OpenResto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark px-4 shadow mb-4">
        <div>
            <span class="navbar-brand mb-0 h1">OpenResto Admin Panel</span>
            <small class="text-white-50 d-block">Admin: {{ session('admin_username') ?? 'Guest' }}</small>
        </div>
        <a href="{{ route('admin.logout') }}" class="btn btn-sm btn-danger">Keluar / Logout</a>
    </nav>

    <div class="container-fluid px-4">
        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="{{ route('admin.menus') }}" class="btn btn-secondary">Kelola Menu</a>
            <a href="{{ route('index') }}" class="btn btn-primary">Mulai Pemesanan</a>
        </div>
        <div class="row mb-4">
            
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-center p-4">
                        <small class="text-uppercase text-light fw-bold">Total Omzet Tahun {{ $tahun }}</small>
                        <h2 class="fw-bold mb-0 mt-2">Rp {{ number_format($total_omzet, 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">Grafik Penjualan Bulanan</div>
                    <div class="card-body" style="height: 250px; position: relative;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

        <h4 class="fw-bold mb-3">Antrean Pesanan Masuk (Dapur)</h4>
        <div class="row">
            @if(count($orders) == 0)
                <div class="col-12">
                    <div class="alert alert-warning text-center shadow-sm">Belum ada pesanan masuk dari pelanggan saat ini.</div>
                </div>
            @endif

            @foreach($orders as $o)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <a href="{{ route('order.receipt', $o->id) }}" class="text-decoration-none">
                            <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center" style="cursor: pointer;">
                                <span>Meja {{ $o->table_number == 0 ? 'Takeaway' : $o->table_number }}</span>
                                <span class="badge bg-secondary">{{ date('H:i', strtotime($o->created_at)) }} WIB</span>
                            </div>
                        </a>
                        
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <small class="text-muted d-block mb-2">Nama: {{ $o->customer_name ?? '-' }}</small>
                                <small class="text-muted d-block mb-2">No. HP: {{ $o->customer_hp }}</small>
                                <small class="text-muted d-block mb-2">Tanggal: {{ \Carbon\Carbon::parse($o->created_at)->format('d M Y') }}</small>
                                <ul class="ps-3 mb-0">
                                    @foreach($o->details as $d)
                                        <li class="mb-2">
                                            <span class="fw-bold text-primary">{{ $d->quantity }}x</span> 
                                            <span class="fw-semibold">{{ $d->name }}</span>
                                            @if($d->notes) 
                                                <br><small class="text-danger bg-danger-subtle px-1 rounded">Catatan: "{{ $d->notes }}"</small> 
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <div class="text-end fw-bold text-success border-top pt-2 mt-3 fs-5">
                                Total: Rp {{ number_format($o->total_price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                            <a href="{{ route('order.receipt', $o->id) }}" class="btn btn-sm btn-outline-primary w-100">Lihat Struk</a>
                            <a href="{{ route('order.receipt', ['id' => $o->id, 'print' => 1]) }}" class="btn btn-sm btn-outline-success w-100">Cetak Struk</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nama_bulan) !!}, // Array nama bulan dari Controller
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($chart_data) !!}, // Array total nominal uang dari Controller
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
