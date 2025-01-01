<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        #reader {
            width: 100%;
            max-width: 500px;
            height: 340px;
            margin: auto;
            border-radius: 10px;
            background-color: #ffffff;
        }

        .alert-custom {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .output-container {
            display: none;
            margin-top: 20px;
        }

        .countdown {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container text-center">
        <div id="reader" class="mb-4"></div>

        <div class="alert alert-info" role="alert">
            Scan barcode untuk akses masuk dan keluar dari ruang perpustakaan.
        </div>

        <div id="output-container" class="output-container">
            <p>Hasil: <span id="output"></span></p>
            <div id="data-container" style="display: none;">
                <h3>Detail Data:</h3>
                <ul id="data-list"></ul>
            </div>
            <div id="countdown" class="countdown"></div>
        </div>
    </div>

    <!-- Modal untuk konfirmasi -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah ini proses masuk? Klik "Masuk" untuk masuk, atau "Keluar" untuk keluar.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="confirmEntry">Masuk</button>
                    <button type="button" class="btn btn-danger" id="confirmExit">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk pesan -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        const html5QrCode = new Html5Qrcode("reader");

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            const nis = decodedText;

            document.getElementById('output').textContent = nis;

            // Tampilkan modal konfirmasi
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();

            // Setelah pengguna memilih, kirim data
            document.getElementById('confirmEntry').addEventListener('click', () => {
                handleEntryExit(true, nis, confirmationModal);
            });

            document.getElementById('confirmExit').addEventListener('click', () => {
                handleEntryExit(false, nis, confirmationModal);
            });
        };

        const showMessage = (message) => {
            document.getElementById('messageContent').textContent = message;
            const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            messageModal.show();
        };

        const handleEntryExit = (isEntry, nis, modal) => {
            const url = isEntry ? '/handle-entry' : '/handle-exit';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nis
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showMessage(isEntry ? data.message : `${data.message}. Durasi: ${data.durasi} menit.`);
                    } else {
                        if (isEntry) {
                            showMessage("Data tidak ditemukan. Mengarahkan ke form...");
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        } else {
                            showMessage(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    showMessage("Terjadi kesalahan.");
                });

            // Tutup modal setelah memilih
            modal.hide();
        };

        const config = {
            fps: 10,
            qrbox: 350,
            formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39]
        };

        const startScanning = () => {
            html5QrCode.start({
                    facingMode: "environment"
                }, config, qrCodeSuccessCallback)
                .then(() => {
                    document.getElementById('output-container').style.display = 'block';
                })
                .catch(err => console.error("Start failed", err));
        };

        // Start the QR code scanner automatically when the page loads
        window.onload = startScanning;
    </script>
</body>

</html>
