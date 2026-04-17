<!DOCTYPE html>
<html>
<head>
    <title>QR Test</title>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/dist/html5-qrcode.min.js"></script>
    <style>
        #qr-reader { width: 400px; margin: 0 auto; }
    </style>
</head>
<body>
    <div id="qr-reader"></div>
    <div id="qr-result"></div>

    <script>
    let qrScanner = null;
    
    function initScanner() {
        qrScanner = new Html5QrcodeScanner(
            "qr-reader",
            { fps: 10, qrbox: 250 },
            false
        );
        
        qrScanner.render((text) => {
            document.getElementById('qr-result').innerHTML = `Scanned: ${text}`;
        }, (error) => {
            console.error(error);
        });
    }
    
    // Initialize when page loads
    window.onload = initScanner;
    </script>
</body>
</html>