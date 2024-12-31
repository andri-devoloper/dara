<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Barcode</title>

    <meta http-equiv="Content-Security-Policy"
        content="script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com chrome-extension://*;">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
</head>

<body>
    <h1>Scan Barcode</h1>
    <div id="scanner-container" style="width: 100%; max-width: 600px; margin: auto;">
        <video id="scanner-video" autoplay style="width: 100%;"></video>
    </div>
    <p>Barcode Detected: <span id="barcode-result"></span></p>

    <div id="nis-form" style="display: none; text-align: center; margin-top: 20px;">
        <h2>Verifikasi NIS</h2>
        <form id="nis-form-submit">
            <input type="text" id="nis-input" name="nis" placeholder="NIS" required>
            <button type="submit">Verifikasi</button>
        </form>
    </div>

    <script>
        // Setup CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Quagga
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-video'),
                    constraints: {
                        width: 640,
                        height: 480,
                        facingMode: "environment"
                    }
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "upc_reader"]
                }
            }, (err) => {
                if (err) {
                    console.error(err);
                    return;
                }
                Quagga.start();
            });

            // Handle barcode detection
            Quagga.onDetected((data) => {
                const barcode = data.codeResult.code;
                document.getElementById('barcode-result').textContent = barcode;

                // Show the NIS form and pre-fill the input
                $('#nis-form').show();
                $('#nis-input').val(barcode);

                // Stop scanner (optional)
                Quagga.stop();
            });

            // Handle form submission with AJAX
            $('#nis-form-submit').on('submit', function(e) {
                e.preventDefault(); // Prevent normal form submission

                const nis = $('#nis-input').val();

                $.ajax({
                    url: '{{ route('get.nis') }}',
                    method: 'POST',
                    data: {
                        nis: nis
                    },
                    success: function(response) {
                        if (response.found) {
                            alert("NIS ditemukan: " + nis);
                        } else {
                            alert("NIS tidak ditemukan.");
                        }
                    },
                    error: function(err) {
                        console.error("Error checking NIS:", err);
                        alert("Terjadi kesalahan pada server.");
                    }
                });
            });
        });
    </script>
</body>

</html>
