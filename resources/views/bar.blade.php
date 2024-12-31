<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        #reader {
            width: 300px;
            height: 300px;
            margin: auto;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <h1>Barcode Scanner</h1>
    <div id="reader"></div>
    <div id="output-container">
        <p>Hasil: <span id="output"></span></p>
        <div id="data-container" style="display: none;">
            <h3>Detail Data:</h3>
            <ul id="data-list"></ul>
        </div>
        <div id="countdown"></div>

    </div>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            const nis = decodedText;

            document.getElementById('output').textContent = nis;
            // Cek apakah pengguna masuk atau keluar
            const isEntry = confirm("Apakah ini proses masuk? Klik OK untuk masuk, Cancel untuk keluar.");

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
                        if (isEntry) {
                            alert(data.message);
                        } else {
                            alert(`${data.message}. Durasi: ${data.durasi} menit.`);
                        }
                    } else {
                        if (isEntry) {
                            alert("Data tidak ditemukan. Mengarahkan ke form...");
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan.");
                });

            // html5QrCode.stop().then(() => {
            //    console.log("Scanning stopped.");
            // }).catch(err => console.error(err));
        };


        const config = {
            fps: 10,
            qrbox: 250,
            formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39]
        };

        html5QrCode.start({
                facingMode: "environment"
            }, config, qrCodeSuccessCallback)
            .catch(err => console.error("Start failed", err));
    </script>

</body>

</html>
