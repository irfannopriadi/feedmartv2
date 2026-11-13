<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode - {{ $product->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background: #fff;
        }

        .no-print {
            padding: 12px;
            background: #f4f4f0;
            border-bottom: 2px solid #000;
            text-align: center;
        }
        .btn-print {
            background: #34d399;
            color: #000;
            border: 2px solid #000;
            padding: 6px 16px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 2px 2px 0 #000;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* FORMAT 1: THERMAL 40x30mm */
        @if($format === 'thermal_40x30')
            @page {
                size: 40mm 30mm;
                margin: 0;
            }
            .label-item {
                width: 40mm;
                height: 30mm;
                padding: 1.5mm;
                page-break-inside: avoid;
                page-break-after: always;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                text-align: center;
            }
        /* FORMAT 2: THERMAL 50x30mm */
        @elseif($format === 'thermal_50x30')
            @page {
                size: 50mm 30mm;
                margin: 0;
            }
            .label-item {
                width: 50mm;
                height: 30mm;
                padding: 1.5mm;
                page-break-inside: avoid;
                page-break-after: always;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                text-align: center;
            }
        /* FORMAT 3: A4 GRID */
        @else
            @page {
                size: A4;
                margin: 10mm;
            }
            .grid-container {
                display: flex;
                flex-wrap: wrap;
                gap: 4mm;
            }
            .label-item {
                width: 45mm;
                height: 30mm;
                border: 1px dashed #ccc;
                padding: 2mm;
                page-break-inside: avoid;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                text-align: center;
            }
        @endif

        .store-title {
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .prod-name {
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.1;
            max-height: 18px;
            overflow: hidden;
        }
        .barcode-svg {
            width: 100%;
            max-height: 13mm;
        }
        .bottom-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            font-weight: 900;
            border-top: 1px solid #000;
            padding-top: 1px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ CETAK SEKARANG ({{ $qty }} LABEL)</button>
    </div>

    <div class="{{ $format === 'a4_grid' ? 'grid-container' : '' }}">
        @for($i = 0; $i < $qty; $i++)
            <div class="label-item">
                <div class="store-title">{{ $storeName }}</div>
                <div class="prod-name">{{ Str::limit($product->name, 26) }}</div>
                <svg class="barcode-svg barcode-elem" data-code="{{ $product->barcode ?? $product->sku }}"></svg>
                <div class="bottom-row">
                    <span style="font-size: 7.5px;">1 {{ $product->unit }}</span>
                    <span>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                </div>
            </div>
        @endfor
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const elements = document.querySelectorAll('.barcode-elem');
            elements.forEach(el => {
                const code = el.getAttribute('data-code');
                try {
                    JsBarcode(el, code, {
                        format: "CODE128",
                        height: 25,
                        displayValue: true,
                        fontSize: 9,
                        margin: 0
                    });
                } catch(e) {}
            });

            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
