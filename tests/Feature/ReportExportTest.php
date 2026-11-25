<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_sales_report_to_excel_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $kasir = User::factory()->kasir()->create();

        $product = Product::create([
            'sku' => 'EXP-001',
            'name' => 'Pakan Bebek 50kg',
            'unit' => 'sak',
            'cost_price' => 300000,
            'selling_price' => 350000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $register = CashRegister::create([
            'user_id' => $kasir->id,
            'opening_cash' => 100000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $trx = Transaction::create([
            'invoice_number' => 'INV-20261111-0001',
            'user_id' => $kasir->id,
            'cash_register_id' => $register->id,
            'subtotal' => 350000,
            'discount' => 10000,
            'final_amount' => 340000,
            'paid_amount' => 350000,
            'change_amount' => 10000,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        TransactionDetail::create([
            'transaction_id' => $trx->id,
            'product_id' => $product->id,
            'cost_price' => 300000,
            'selling_price' => 350000,
            'qty' => 1,
            'subtotal' => 350000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.sales.excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename=', (string) $response->headers->get('Content-Disposition'));

        // Cek isi stream output
        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $this->assertStringContainsString('LAPORAN PENJUALAN & LABA KOTOR - FEEDMART', $content);
        $this->assertStringContainsString('INV-20261111-0001', $content);
        $this->assertStringContainsString('Pakan Bebek 50kg', $content);
    }

    public function test_admin_can_view_and_print_sales_report_pdf(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.sales.pdf'));

        $response->assertStatus(200);
        $response->assertSee('FEEDMART INDONESIA');
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Total Omzet Penjualan');
    }

    public function test_admin_can_export_stock_mutation_to_excel(): void
    {
        $admin = User::factory()->admin()->create();

        $product = Product::create([
            'sku' => 'MUT-001',
            'name' => 'Konsentrat Sapi',
            'unit' => 'sak',
            'cost_price' => 200000,
            'selling_price' => 240000,
            'stock' => 15,
            'is_active' => true,
        ]);

        StockMutation::create([
            'product_id' => $product->id,
            'user_id' => $admin->id,
            'type' => 'IN',
            'qty_change' => 15,
            'qty_before' => 0,
            'qty_after' => 15,
            'notes' => 'Penerimaan stok awal',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.stock-opname.excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $this->assertStringContainsString('JURNAL AUDIT MUTASI INVENTARIS', $content);
        $this->assertStringContainsString('Konsentrat Sapi', $content);
    }

    public function test_kasir_cannot_export_sales_or_mutation_reports(): void
    {
        $kasir = User::factory()->kasir()->create();

        $responseExcel = $this->actingAs($kasir)->get(route('admin.reports.sales.excel'));
        $responseExcel->assertStatus(403);

        $responsePdf = $this->actingAs($kasir)->get(route('admin.reports.sales.pdf'));
        $responsePdf->assertStatus(403);

        $responseMutation = $this->actingAs($kasir)->get(route('admin.stock-opname.excel'));
        $responseMutation->assertStatus(403);
    }
}
