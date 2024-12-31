<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1>Form Perpustakaan</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Input Data -->
        <form action="/perputakan" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>

            <div class="mb-3">
                <label for="nis" class="form-label">NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" required>
                <!-- Area untuk barcode -->
                <svg id="barcode" class="mt-2"></svg>
                <button type="button" id="download-barcode" class="btn btn-secondary mt-2">Download Barcode</button>
            </div>

            <div class="mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" required>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>

        <!-- Tabel Data yang Masuk -->
        <h3 class="mt-5">Daftar Pengunjung yang Masuk</h3>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nisInput = document.getElementById('nis');
            const barcodeSvg = document.getElementById('barcode');
            const downloadButton = document.getElementById('download-barcode');

            // Fungsi untuk memperbarui barcode
            const updateBarcode = () => {
                const nisValue = nisInput.value.trim();
                if (nisValue) {
                    JsBarcode(barcodeSvg, nisValue, {
                        format: "CODE128",
                        width: 2,
                        height: 50,
                        displayValue: true
                    });
                } else {
                    barcodeSvg.innerHTML = ''; // Hapus barcode jika kosong
                }
            };

            // Perbarui barcode saat NIS berubah
            nisInput.addEventListener('input', updateBarcode);

            // Fungsi untuk mengunduh barcode
            downloadButton.addEventListener('click', () => {
                const svg = barcodeSvg; // SVG elemen Anda
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Menyaring gambar dari SVG ke dalam canvas
                const img = new Image();
                const svgData = new XMLSerializer().serializeToString(svg);
                const svgBlob = new Blob([svgData], {
                    type: 'image/svg+xml'
                });
                const url = URL.createObjectURL(svgBlob);

                img.onload = function() {
                    // Atur dimensi canvas sesuai dengan dimensi gambar SVG
                    canvas.width = img.width;
                    canvas.height = img.height;

                    // Gambar SVG ke dalam canvas
                    ctx.drawImage(img, 0, 0);

                    // Mengonversi canvas ke format JPG
                    const jpgDataUrl = canvas.toDataURL('image/jpeg',
                    1.0); // 1.0 untuk kualitas maksimal

                    // Membuat elemen <a> untuk mengunduh gambar JPG
                    const link = document.createElement('a');
                    link.href = jpgDataUrl;
                    link.download = 'barcode.jpg';
                    link.click();

                    // Membersihkan URL setelah selesai
                    URL.revokeObjectURL(url);
                };

                // Memuat data SVG sebagai gambar
                img.src = url;
            });

        });
    </script>
</body>

</html>
