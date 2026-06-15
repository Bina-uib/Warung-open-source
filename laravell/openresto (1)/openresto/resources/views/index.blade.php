<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di OpenResto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container" style="max-width: 450px;">
        @if(session('success'))
            <div class="alert alert-success text-center shadow-sm">{{ session('success') }}</div>
        @endif
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-3">Selamat Datang</h3>
                <p class="text-muted text-center small mb-4">Silakan isi nomor meja & nomor HP Anda untuk memesan</p>
                <form action="{{ route('index.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="customer_name" class="form-control" required autocomplete="name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Pilihan</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order_type" id="type_dine" value="dine" checked>
                            <label class="form-check-label" for="type_dine">Makan di sini</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="order_type" id="type_takeaway" value="takeaway">
                            <label class="form-check-label" for="type_takeaway">Takeaway</label>
                        </div>
                    </div>

                    <div class="mb-3" id="table_group">
                        <label class="form-label">Nomor Meja</label>
                        <input type="number" name="table_number" class="form-control">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Nomor HP Pelanggan</label>
                        <input type="text" name="customer_hp" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm">Masuk & Lihat Menu</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Toggle tampil input nomor meja ketika pilihannya berubah
        document.addEventListener('DOMContentLoaded', function () {
            const dine = document.getElementById('type_dine');
            const takeaway = document.getElementById('type_takeaway');
            const tableGroup = document.getElementById('table_group');

            function update() {
                if (takeaway.checked) {
                    tableGroup.style.display = 'none';
                } else {
                    tableGroup.style.display = '';
                }
            }

            dine.addEventListener('change', update);
            takeaway.addEventListener('change', update);
            update();
        });
    </script>
</body>
</html>
