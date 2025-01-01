<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <!-- Menggunakan versi Bootstrap terbaru -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Menambahkan gaya untuk memastikan body dan html memiliki tinggi penuh */
        html,
        body {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="d-flex h-100 w-100 justify-content-center align-items-center">
        <div class="row text-center gap-5">
            <a href="{{ route('bar') }}" class="btn btn-primary">Scan Barcode</a>
            <a href="{{ route('showForm') }}" class="btn btn-secondary">From</a>
        </div>
    </div>

    <!-- Script Bootstrap dan Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
