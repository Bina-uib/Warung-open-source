<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - OpenResto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow mb-4">
        <span class="navbar-brand mb-0 h1">Edit Menu</span>
        <a href="{{ route('admin.menus') }}" class="btn btn-sm btn-outline-light">Kembali</a>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" id="menuForm">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama Menu</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $menu->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label fw-bold">Harga (Rp)</label>
                                <input type="number" name="price" id="price" class="form-control" value="{{ $menu->price }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Gambar Saat Ini</label>
                                @if(isset($menu->image) && $menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-thumbnail mb-2" style="max-height: 150px;">
                                @else
                                    <p class="text-muted font-italic">Tidak ada gambar</p>
                                @endif
                                
                                <label for="image" class="form-label fw-bold">Ganti Gambar (Opsional)</label>
                                <input type="file" id="imageInput" class="form-control" accept="image/*">
                                <input type="hidden" name="cropped_image" id="croppedImage">
                                <small class="text-muted">Format: jpg, jpeg, png (Maks. 2MB)</small>
                            </div>
                            <div class="mb-3 d-none" id="cropperWrapper">
                                <label class="d-block mb-2 fw-bold">Sesuaikan Gambar Baru (Crop 4:3)</label>
                                <div style="max-height: 400px;">
                                    <img id="imagePreview" style="max-width: 100%;">
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success">Perbarui Menu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let cropper;
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const cropperWrapper = document.getElementById('cropperWrapper');

        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                    if (cropper) cropper.destroy();
                    cropperWrapper.classList.remove('d-none');
                    cropper = new Cropper(imagePreview, {
                        aspectRatio: 4 / 3,
                        viewMode: 1,
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        document.getElementById('menuForm').addEventListener('submit', function(e) {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({ width: 800, height: 600 });
                document.getElementById('croppedImage').value = canvas.toDataURL('image/png');
            }
        });
    </script>
</body>
</html>