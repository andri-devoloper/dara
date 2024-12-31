<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Barcode</title>
    <style>
        .barcode {
            width: 500px;
            height: 200px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <h1>Generate Barcode</h1>

    <!-- Form Input -->
    <form action="{{ route('generate.barcode') }}" method="POST">
        @csrf
        <label for="nama">Nama:</label>
        <input type="text" id="nama" name="nama" required>
        <br>

        <label for="id">ID:</label>
        <input type="text" id="id" name="id" required>
        <br>

        <label for="keterangan">Keterangan:</label>
        <textarea id="keterangan" name="keterangan" required></textarea>
        <br>

        <button type="submit">Generate Barcode</button>
    </form>
    <!-- Hasil Barcode -->
    @if (isset($barcode))
        <h2>Hasil Barcode</h2>
        <p>Nama: {{ $nama }}</p>
        <p>ID: {{ $id }}</p>
        <p>Keterangan: {{ $keterangan }}</p>

        <h2>Barcode 1D (Code 128)</h2>
        <div class="barcode">
            {!! DNS1D::getBarcodeHTML($id, 'C128', 2, 60) !!}
        </div>


        <h2>Barcode 2D (QR Code)</h2>
        {!! DNS2D::getBarcodeHTML("Nama: $nama\nID: $id\nKeterangan: $keterangan", 'QRCODE') !!}
    @endif

</body>

</html>
