<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenResto POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; height: 100vh; overflow: hidden; }
        .menu-container { height: 90vh; overflow-y: auto; }
        .cart-container { height: 90vh; background: white; border-left: 1px solid #dee2e6; display: flex; flex-direction: column; }
        .cart-items { flex-grow: 1; overflow-y: auto; padding: 15px; }
        .cart-footer { padding: 20px; border-top: 2px solid #eee; background: #fff; }
        .menu-card { cursor: pointer; transition: transform 0.2s; }
        .menu-card:hover { transform: scale(1.02); }
        .menu-card img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand mb-0 h1">OpenResto POS</span>
        <div>
            <span class="text-white me-3">Admin: {{ session('admin_username') }}</span>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-light">Dashboard</a>
            <a href="{{ route('admin.logout') }}" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Bagian Kiri: Daftar Menu -->
            <div class="col-md-8 p-4 menu-container">
                <div class="row g-3">
                    @foreach($menus as $menu)
                    <div class="col-md-4">
                        <div class="card h-100 menu-card shadow-sm" onclick="addItem({{ $menu->id }}, '{{ $menu->name }}', {{ $menu->price }})">
                            @if(isset($menu->image) && $menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <span class="text-muted">Tanpa Gambar</span>
                                </div>
                            @endif
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $menu->name }}</h5>
                                <p class="card-text text-primary fw-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Bagian Kanan: Keranjang & Checkout -->
            <div class="col-md-4 cart-container shadow">
                <form action="{{ route('order.process') }}" method="POST" id="posForm">
                    @csrf
                    <div class="cart-items">
                        <h4 class="mb-3">Pesanan Aktif</h4>
                        <div id="cartList" class="list-group mb-3">
                            <!-- Item akan muncul di sini via JS -->
                            <p class="text-muted text-center py-5" id="emptyMsg">Belum ada item terpilih</p>
                        </div>

                        <div class="border p-3 rounded bg-light mb-3">
                            <h6>Data Pelanggan</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="text" name="customer_name" class="form-control form-control-sm" placeholder="Nama Pelanggan" required>
                                </div>
                                <div class="col-6">
                                    <select name="order_type" id="orderType" class="form-select form-select-sm" onchange="toggleTable()">
                                        <option value="dine">Dine In (Makan Sini)</option>
                                        <option value="takeaway">Take Away (Bungkus)</option>
                                    </select>
                                </div>
                                <div class="col-6" id="tableContainer">
                                    <input type="number" name="table_number" class="form-control form-control-sm" placeholder="No. Meja">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cart-footer">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="h5">Total</span>
                            <span class="h5 text-primary" id="displayTotal">Rp 0</span>
                            <input type="hidden" name="total_hidden" id="totalHidden" value="0">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="btnCheckout" disabled>Proses & Cetak Struk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = {};
        let grandTotal = 0;

        function addItem(id, name, price) {
            if (cart[id]) {
                cart[id].qty++;
            } else {
                cart[id] = { name: name, price: price, qty: 1 };
            }
            updateCartUI();
        }

        function removeItem(id) {
            delete cart[id];
            updateCartUI();
        }

        function updateCartUI() {
            const cartList = document.getElementById('cartList');
            const emptyMsg = document.getElementById('emptyMsg');
            cartList.innerHTML = '';
            grandTotal = 0;

            const keys = Object.keys(cart);
            if (keys.length === 0) {
                cartList.appendChild(emptyMsg);
                document.getElementById('btnCheckout').disabled = true;
            } else {
                keys.forEach(id => {
                    const item = cart[id];
                    const subtotal = item.price * item.qty;
                    grandTotal += subtotal;

                    const div = document.createElement('div');
                    div.className = 'list-group-item d-flex justify-content-between align-items-center';
                    div.innerHTML = `
                        <div>
                            <h6 class="mb-0">${item.name}</h6>
                            <small class="text-muted">${item.qty} x Rp ${item.price.toLocaleString()}</small>
                            <input type="hidden" name="items[${id}]" value="${item.qty}">
                            <input type="text" name="notes[${id}]" class="form-control form-control-sm mt-1" placeholder="Keterangan (opsional)">
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Rp ${subtotal.toLocaleString()}</div>
                            <button type="button" class="btn btn-sm text-danger p-0" onclick="removeItem(${id})">Hapus</button>
                        </div>
                    `;
                    cartList.appendChild(div);
                });
                document.getElementById('btnCheckout').disabled = false;
            }

            document.getElementById('displayTotal').innerText = 'Rp ' + grandTotal.toLocaleString();
            document.getElementById('totalHidden').value = grandTotal;
        }

        function toggleTable() {
            const type = document.getElementById('orderType').value;
            const container = document.getElementById('tableContainer');
            const input = container.querySelector('input');
            container.style.display = type === 'dine' ? 'block' : 'none';
            input.required = type === 'dine';
        }
    </script>
</body>
</html>