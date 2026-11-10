<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@feedmart.test',
            'role' => 'admin',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'admin@feedmart.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_kasir_must_open_cash_register_before_pos(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)->get(route('pos.index'));
        $response->assertRedirect(route('cash-register.open-form'));

        // Open cash register
        $openResponse = $this->actingAs($kasir)->post(route('cash-register.open'), [
            'opening_cash' => 200000,
            'notes' => 'Shift Pagi',
        ]);

        $openResponse->assertRedirect(route('pos.index'));
        $this->assertDatabaseHas('cash_registers', [
            'user_id' => $kasir->id,
            'opening_cash' => 200000,
            'status' => 'open',
        ]);

        // After opening, POS is accessible
        $posResponse = $this->actingAs($kasir)->get(route('pos.index'));
        $posResponse->assertStatus(200);
    }

    public function test_kasir_cannot_access_admin_repack_or_reports(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)->get(route('admin.repack.index'));
        $response->assertStatus(403);

        $reportResponse = $this->actingAs($kasir)->get(route('admin.reports.sales'));
        $reportResponse->assertStatus(403);
    }

    public function test_pos_checkout_api_successfully_creates_sale(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'is_active' => true,
        ]);

        $register = CashRegister::create([
            'user_id' => $kasir->id,
            'opening_cash' => 150000,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $product = Product::create([
            'sku' => 'PAKAN-001',
            'name' => 'Pakan Ikan Hias',
            'unit' => 'bungkus',
            'cost_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)->postJson(route('pos.checkout'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2, 'selling_price' => 15000],
            ],
            'payment_method' => 'cash',
            'paid_amount' => 50000,
            'discount' => 0,
            'cash_register_id' => $register->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertEquals(8, (float) $product->fresh()->stock);
        $this->assertDatabaseHas('transactions', [
            'cash_register_id' => $register->id,
            'final_amount' => 30000,
            'paid_amount' => 50000,
            'change_amount' => 20000,
        ]);
    }
}
