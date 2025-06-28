<x-app-layout>
<div class="container">
    <h2>Scan Barcode</h2>
    <div id="scanner-container" style="width: 400px; height: 300px; border: 1px solid #ccc;"></div>
    <p><strong>Scanned Code:</strong> <span id="barcode-result">Waiting...</span></p>
</div>

<script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>
<script>
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#scanner-container'),
            constraints: {
                facingMode: "environment" // or user for front camera
            }
        },
        decoder: {
            readers: ["code_128_reader", "ean_reader", "ean_8_reader", "upc_reader"]
        },
    }, function (err) {
        if (err) {
            console.error(err);
            return;
        }
        Quagga.start();
    });

    Quagga.onDetected(function(result) {
        const code = result.codeResult.code;
        document.getElementById('barcode-result').innerText = code;

        // Send result to backend via AJAX if needed
        fetch('/barcode/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode: code })
        }).then(response => response.json())
          .then(data => console.log('Server response:', data));

        Quagga.stop(); // stop after 1 scan
    });
</script>
</x-app-layout>
