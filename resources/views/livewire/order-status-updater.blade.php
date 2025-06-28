<div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <h2 class="text-2xl font-bold mb-4">@lang('site.update_order_status')</h2>

    <!-- Mode Selection -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">@lang('site.select_method')</label>
        <select wire:model="mode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="scan">@lang('site.scan_barcode')</option>
            <option value="upload">@lang('site.upload_image')</option>
            <option value="manual">@lang('site.enter_order_number')</option>
        </select>
    </div>

    <!-- Barcode Scanner -->
    @if ($mode === 'scan')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.scan_barcode')</label>
            <div id="qr-reader" style="width: 100%; max-width: 500px;"></div>
            <div id="qr-reader-results" class="mt-2 p-2 bg-gray-100 rounded-md text-gray-700"></div>
            <input type="text" wire:model.debounce.500ms="barcode" id="barcode-input" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm" placeholder="@lang('site.barcode')">
            <button onclick="restartScanner()" class="mt-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">@lang('site.restart_scanner')</button>
            @error('barcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Image Upload -->
    @if ($mode === 'upload')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.upload_image')</label>
            <input type="file" wire:model="image" accept="image/*" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm">
            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Order Number -->
    @if ($mode === 'manual')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.order_number')</label>
            <input type="text" wire:model.debounce.500ms="orderNumber" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm" placeholder="@lang('site.order_number')">
            @error('orderNumber') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    @endif

    <!-- Find Button -->
    <button wire:click="findItemOrOrder" class="mb-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        @lang('site.find_order')
    </button>

    <!-- Item/Order Details and Status Update -->
    @if ($item || $order)
        <div class="mb-4 p-4 bg-gray-100 rounded-md">
            @if ($item)
                <p><strong>@lang('site.item'):</strong> {{ $item->name }} (Barcode: {{ $item->barcode }})</p>
                <p><strong>@lang('site.order_number'):</strong> {{ $item->order->id }}</p>
                <p><strong>@lang('site.current_status'):</strong> {{ $item->status->name }}</p>
            @elseif ($order)
                <p><strong>@lang('site.order_number'):</strong> {{ $order->id }}</p>
                <p><strong>@lang('site.current_status'):</strong> {{ $order->status }}</p>
            @endif
            <p><strong>@lang('site.customer'):</strong> {{ $order->customer->name }}</p>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.new_status')</label>
                <select wire:model="status_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">@lang('site.select_status')</option>
                    @foreach ($statuses as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('status_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <button wire:click="updateStatus" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                @lang('site.update_status')
            </button>
        </div>
    @endif

    @if ($errors->has('order'))
        <span class="text-red-500 text-sm">{{ $errors->first('order') }}</span>
    @endif

    @if (session('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    @vite(['resources/js/app.js'])

        <script>
            function initScanner() {
                const resultContainer = document.getElementById('qr-reader-results');
                const barcodeInput = document.getElementById('barcode-input');
                let lastResult, countResults = 0;
                let isProcessing = false;

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
                        readers: ["code_128_reader"] // Prioritize Code 128
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
                Quagga.stop();
                document.getElementById('qr-reader').innerHTML = '';
                initScanner();
            }

            document.addEventListener('DOMContentLoaded', function () {
                if (document.getElementById('qr-reader')) {
                    initScanner();
                }
            });

            window.restartScanner = restartScanner;
        </script>
</div>
