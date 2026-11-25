<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan & Laba Rugi Feedmart ({{ $dateFrom }} s/d {{ $dateTo }})</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 900;
            color: #000;
            letter-spacing: 1px;
        }
        .brand-sub {
            font-size: 10px;
            color: #333;
            margin-top: 2px;
            font-weight: bold;
        }
        .report-title {
            text-align: right;
        }
        .report-title h2 {
            margin: 0;
            font-size: 16px;
            color: #000;
            font-weight: 900;
            text-transform: uppercase;
        }
        .report-title p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #333;
            font-weight: bold;
        }
        /* Summary Box */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-cell {
            display: table-cell;
            width: 20%;
            padding: 8px 10px;
            background: #fff;
            border: 2px solid #000;
        }
        .summary-cell + .summary-cell {
            border-left: none;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 900;
            color: #555;
            display: block;
        }
        .summary-val {
            font-size: 13px;
            font-weight: 900;
            color: #000;
            margin-top: 3px;
            display: block;
            font-family: monospace;
        }
        .highlight-profit {
            color: #000;
        }
        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #000000;
            color: #ffffff;
            font-weight: 900;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #000000;
            text-transform: uppercase;
            font-size: 9px;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #000;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafaf8;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 50px;
        }
        /* Print actions */
        .no-print-bar {
            background: #f4f4f0;
            color: #000;
            padding: 8px 16px;
            margin-bottom: 15px;
            border: 2px solid #000;
            box-shadow: 4px 4px 0px #000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-print {
            background: #34d399;
            color: #000;
            border: 2px solid #000;
            box-shadow: 2px 2px 0px #000;
            padding: 6px 14px;
            font-weight: 900;
            cursor: pointer;
            font-size: 11px;
            text-transform: uppercase;
        }
        .btn-close {
            background: #fff;
            color: #000;
            border: 2px solid #000;
            box-shadow: 2px 2px 0px #000;
            padding: 6px 14px;
            font-weight: 900;
            cursor: pointer;
            margin-left: 6px;
            font-size: 11px;
            text-transform: uppercase;
        }
        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Aksi (Hanya tampil di browser, otomatis hilang saat diprint) -->
    <div class="no-print-bar">
        <div>
            <strong>Pratinjau Dokumen PDF Laporan Finansial Feedmart</strong>
            <span style="font-size: 10px; color: #94a3b8; margin-left: 8px;">Pilih tujuan printer "Save as PDF" untuk menyimpan sebagai file PDF.</span>
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <!-- Kop Laporan Resmi -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="brand-title">🌾 FEEDMART INDONESIA</div>
                <div class="brand-sub">Sistem Manajemen Point of Sale & Inventaris Pakan Ternak</div>
                <div class="brand-sub">Jl. Raya Peternakan No. 88 | Telp: (031) 898-7654 | WA: 0812-3456-7890</div>
            </td>
            <td class="report-title">
                <h2>Laporan Penjualan & Laba Kotor</h2>
                <p>Periode: <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}</strong></p>
                <p>Kasir: <strong>{{ $selectedCashier ? $selectedCashier->name : 'Semua Kasir' }}</strong> | Metode: <strong>{{ $paymentMethod ? strtoupper($paymentMethod) : 'Semua Metode' }}</strong></p>
            </td>
        </tr>
    </table>

    <!-- Ringkasan Eksekutif KPI -->
    <div class="summary-grid">
        <div class="summary-cell">
            <span class="summary-label">Total Omzet Penjualan</span>
            <span class="summary-val">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
        </div>
        <div class="summary-cell">
            <span class="summary-label">Total Modal HPP</span>
            <span class="summary-val">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
        </div>
        <div class="summary-cell">
            <span class="summary-label">Laba Kotor (Gross Profit)</span>
            <span class="summary-val highlight-profit">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
        </div>
        <div class="summary-cell">
            <span class="summary-label">Margin Keuntungan</span>
            <span class="summary-val highlight-profit">{{ number_format($profitMargin, 1) }}%</span>
        </div>
        <div class="summary-cell">
            <span class="summary-label">Total Transaksi</span>
            <span class="summary-val">{{ $transactions->count() }} Faktur</span>
        </div>
    </div>

    <!-- Tabel Rincian Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 90px;">Tanggal / Waktu</th>
                <th style="width: 105px;">No. Invoice</th>
                <th style="width: 80px;">Kasir</th>
                <th>Rincian Produk Pakan</th>
                <th class="text-center" style="width: 45px;">Metode</th>
                <th class="text-right" style="width: 80px;">Subtotal</th>
                <th class="text-right" style="width: 60px;">Diskon</th>
                <th class="text-right" style="width: 85px;">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($transactions as $trx)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-bold">{{ $trx->invoice_number }}</td>
                    <td>{{ $trx->user->name }}</td>
                    <td>
                        @foreach($trx->details as $d)
                            <div>• {{ $d->product?->name ?? 'Produk' }} ({{ number_format($d->qty, 2, ',', '.') }} {{ $d->product?->unit }}) @ Rp {{ number_format($d->selling_price, 0, ',', '.') }}</div>
                        @endforeach
                    </td>
                    <td class="text-center font-bold" style="font-size: 8px;">{{ strtoupper($trx->payment_method) }}</td>
                    <td class="text-right">Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $trx->discount > 0 ? '- Rp ' . number_format($trx->discount, 0, ',', '.') : '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($trx->final_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada transaksi penjualan pada filter dan rentang tanggal yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 6px 8px;">TOTAL KESELURUHAN:</td>
                <td class="text-right">Rp {{ number_format($totalSales + $totalDiscount, 0, ',', '.') }}</td>
                <td class="text-right">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #065f46; font-size: 11px;">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Dicetak Oleh:</div>
                <div class="signature-space"></div>
                <div class="font-bold">({{ auth()->user()->name }})</div>
                <div style="font-size: 9px; color: #64748b;">Administrator</div>
            </td>
            <td></td>
            <td>
                <div>{{ \Carbon\Carbon::now()->format('d F Y') }}</div>
                <div>Mengetahui / Menyetujui:</div>
                <div class="signature-space"></div>
                <div class="font-bold">( Pemilik / Direktur Feedmart )</div>
                <div style="font-size: 9px; color: #64748b;">Owner</div>
            </td>
        </tr>
    </table>

    <script>
        // Otomatis buka dialog print saat halaman termuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
