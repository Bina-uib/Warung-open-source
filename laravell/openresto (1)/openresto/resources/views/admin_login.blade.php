<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin OpenResto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="height: 100vh;">
    <div class="container" style="max-width: 400px;">
        @if(session('error'))
            <div class="alert alert-danger text-center shadow">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success text-center shadow">{{ session('success') }}</div>
        @endif
        <div class="card shadow border-0">
            <div class="card-body p-4">
                <h4 class="text-center fw-bold mb-3">Login Admin Dapur</h4>
                <form action="{{ route('admin.login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="" required autocomplete="off">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" value="" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 btn-lg shadow-sm">Masuk Sistem Kasir</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
