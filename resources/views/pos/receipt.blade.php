@php
    $settings = \App\Models\Setting::allKeyed();
    $paperSize = $settings['receipt_paper_size'] ?? '58mm';
    $paperWidth = $paperSize === '80mm' ? '76mm' : '54mm';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $transaction->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            size: {{ $paperSize }} auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace, monospace;
            font-size: {{ $paperSize === '80mm' ? '12px' : '11px' }};
            color: #000;
            background: #fff;
            margin: 0 auto;
            padding: 8px;
            width: {{ $paperWidth }};
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .border-b { border-bottom: 1px dashed #000; margin-bottom: 5px; padding-bottom: 5px; }
        .table-items { width: 100%; border-collapse: collapse; margin: 4px 0; }
        .table-items td { vertical-align: top; padding: 2px 0; }
        .no-print {
            margin-bottom: 12px;
            padding: 8px;
            background: #f4f4f0;
            border: 2px solid #000;
            box-shadow: 3px 3px 0px #000;
            text-align: center;
        }
        .no-print button {
            border: 2px solid #000;
            padding: 5px 12px;
            cursor: pointer;
            font-weight: 900;
            font-size: 11px;
            text-transform: uppercase;
            box-shadow: 2px 2px 0px #000;
            transition: all 0.1s;
        }
        .btn-print {
            background: #34d399;
            color: #000;
        }
        .btn-close {
            background: #fff;
            color: #000;
            margin-left: 4px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 4px; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨 CETAK STRUK</button>
        <button class="btn-close" onclick="window.close()">TUTUP</button>
    </div>

    <!-- Store Header -->
    <div class="text-center">
        <div class="font-bold" style="font-size: {{ $paperSize === '80mm' ? '16px' : '14px' }}; letter-spacing: 1px;">
            {{ $settings['store_name'] ?? 'FEEDMART' }}
        </div>
        @if(!empty($settings['store_tagline']))
            <div style="font-size: 9px; font-weight: bold;">{{ $settings['store_tagline'] }}</div>
        @endif
        <div style="font-size: 9px;">{{ $settings['store_address'] ?? 'Jl. Raya Peternakan No. 88' }}</div>
        <div style="font-size: 9px;">Telp/WA: {{ $settings['store_phone'] ?? '0812-3456-7890' }}</div>
    </div>

    <div class="border-t border-b" style="font-size: 10px;">
        <div>No: {{ $transaction->invoice_number }}</div>
        <div>Tgl: {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        <div>Kasir: {{ $transaction->user->name }}</div>
        @if($transaction->customer)
            <div class="font-bold">Pelanggan: {{ $transaction->customer->name }}</div>
        @endif
    </div>

    <!-- Itemized List -->
    <table class="table-items">
        @foreach($transaction->details as $item)
            <tr>
                <td colspan="2" class="font-bold">{{ $item->product->name }}</td>
            </tr>
            <tr>
                <td style="font-size: 10px;">
                    {{ number_format($item->qty, 2, ',', '.') }} {{ $item->product->unit }} x {{ number_format($item->selling_price, 0, ',', '.') }}
                </td>
                <td class="text-right font-bold" style="font-size: 10px;">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <!-- Totals -->
    <div class="border-t" style="font-size: 10px;">
        <table style="width: 100%;">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">- Rp {{ number_format($transaction->discount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="font-bold" style="font-size: 11px;">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 1px dotted #000;"></td>
            </tr>
            <tr>
                <td>Bayar ({{ strtoupper($transaction->payment_method) }})</td>
                <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->payment_method === 'tempo')
                <tr class="font-bold" style="color: red;">
                    <td>STATUS</td>
                    <td class="text-right">KASBON / TEMPO</td>
                </tr>
                @if($transaction->due_date)
                    <tr>
                        <td>Jatuh Tempo</td>
                        <td class="text-right">{{ $transaction->due_date->format('d/m/Y') }}</td>
                    </tr>
                @endif
            @else
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Footer Message -->
    <div class="text-center border-t" style="font-size: 9px; margin-top: 8px;">
        <div>*** TERIMA KASIH ***</div>
        <div>{{ $settings['receipt_footer'] ?? 'Barang yang dibeli tidak dapat ditukar/dikembalikan' }}</div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
