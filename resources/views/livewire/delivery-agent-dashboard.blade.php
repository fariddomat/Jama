<div>
    <div class="container mx-auto p-6" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <h1 class="text-2xl font-bold mb-4">Delivery Agent Dashboard</h1>

        <!-- Success/Warning/Error Messages -->
        @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-md">
                {{ session('message') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg shadow-md">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Filters and Search -->
        <div class="mb-6 bg-white p-6 rounded-lg shadow-md border border-gray-200" x-data="{ scanning: false }">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Filter by Status</label>
                    <select wire:model.live="statusFilter" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search by OTP or Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" wire:model.live.debounce.500ms="search" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Search by OTP or Customer Name">
                </div>

                <!-- Barcode Scanner Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Scan Barcode</label>
                    <button @click="scanning = !scanning; if (scanning) initScanner(); else Quagga.stop();" class="mt-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <span x-text="scanning ? 'Stop Scanner' : 'Start Scanner'"></span>
                    </button>
                </div>
            </div>

            <!-- Barcode Scanner -->
            <div x-show="scanning" class="mt-4">
                <div class="relative w-full max-w-[500px] aspect-[4/3] border border-gray-300 rounded-md overflow-hidden">
                    <div id="qr-reader" class="w-full h-full"></div>
                    <div class="scan-line"></div>
                </div>
                <div id="qr-reader-results" class="mt-2 p-2 bg-gray-100 rounded-md text-gray-700"></div>
                <input type="text" wire:model.live="barcode" id="barcode-input" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Barcode">
                @error('barcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                <button @click="restartScanner()" class="mt-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Restart Scanner</button>
            </div>

            <!-- Image Upload -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Upload Barcode Image</label>
                <input type="file" wire:model="image" accept="image/*" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
                <button wire:click="findOrderByImage" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Find Order</button>
                @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Your Orders</h2>
            <x-table
                :columns="['otp', 'customer_name', 'to_address', 'status']"
                :data="$orders"
                routePrefix="dashboard.orders"
                :show="true"
                :edit="false"
                :delete="false"
                :restore="false"
            />
        </div>
    </div>

    @vite(['resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/core@3.x.x/dist/cdn.min.js"></script>
    <script>
        function initScanner() {
            const resultContainer = document.getElementById('qr-reader-results');
            const barcodeInput = document.getElementById('barcode-input');
            let lastResult, countResults = 0;
            let isProcessing = false;

            if (!window.Quagga) {
                console.error('Quagga.js is not loaded');
                resultContainer.innerHTML = 'Error: Quagga.js is not loaded';
                return;
            }

            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#qr-reader'),
                    constraints: {
                        facingMode: "environment",
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    area: {
                        top: "20%",
                        right: "20%",
                        left: "20%",
                        bottom: "20%"
                    },
                },
                numOfWorkers: 2,
                frequency: 10,
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "upc_reader"]
                },
                locate: true,
                locator: {
                    patchSize: "medium",
                    halfSample: true
                }
            }, function(err) {
                if (err) {
                    console.error(`Quagga init error: ${err}`);
                    resultContainer.innerHTML = `Init error: ${err}`;
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                if (isProcessing) return;
                isProcessing = true;

                const decodedText = result.codeResult.code;
                const format = result.codeResult.format;
                if (decodedText !== lastResult) {
                    ++countResults;
                    lastResult = decodedText;
                    console.log(`Decoded: ${decodedText}, Format: ${format}`);
                    resultContainer.innerHTML = `Scan result: ${decodedText} (Format: ${format})`;
                    barcodeInput.value = decodedText;
                    barcodeInput.dispatchEvent(new Event('input'));
                    Quagga.stop();
                }

                setTimeout(() => { isProcessing = false; }, 1000);
            });

            Quagga.onProcessed(function(result) {
                if (result && result.error) {
                    console.warn(`Scan error: ${result.error}`);
                }
            });
        }

        function restartScanner() {
            if (window.Quagga) {
                Quagga.stop();
            }
            document.getElementById('qr-reader').innerHTML = '';
            initScanner();
        }

        window.restartScanner = restartScanner;

        document.addEventListener('DOMContentLoaded', function () {
            Alpine.effect(() => {
                if (Alpine.store('scanning')) {
                    initScanner();
                }
            });
        });
    </script>

    <style>
        #qr-reader {
            width: 100%;
            height: 100%;
        }
        .scan-line {
            position: absolute;
            top: 20%;
            left: 20%;
            width: 60%;
            height: 4px;
            background: red;
            opacity: 0.7;
            animation: scan 2s linear infinite;
            z-index: 10;
        }
        @keyframes scan {
            0% { top: 20%; }
            50% { top: 80%; }
            100% { top: 20%; }
        }
        .aspect-\[4\/3\] {
            aspect-ratio: 4 / 3;
        }
    </style>
</div>
