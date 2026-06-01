<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\Product;
use App\Models\Category;
use App\Models\MemberSaving;
use App\Models\Order;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RetailPromoAndPosTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $memberUser;
    private $memberProfile;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup staff
        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        // Setup member
        $this->memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->memberProfile = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-001',
            'alamat_desa' => 'Desa Merah Putih',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
        ]);

        $this->category = Category::create(['name' => 'Sembako', 'slug' => 'sembako']);
    }

    /**
     * Test Beli 3 Bayar 2 bundle discount logic.
     */
    public function test_beli_3_bayar_2_discount()
    {
        // Create Mie product (triggering str_contains('mie'))
        $productMie = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '8888',
            'name' => 'Mie Goreng Spesial',
            'price_member' => 3000,
            'price_non_member' => 3500,
            'current_stock' => 10,
            'unit' => 'pcs',
            'is_local_product' => false,
        ]);

        // Put 3 Mie in cart. Since it's member, price is 3000. Total gross is 9000.
        // Beli 3 Bayar 2 makes 1 free (3000 discount). Total final is 6000.
        $transactionService = resolve(TransactionService::class);
        $order = $transactionService->checkout($this->memberUser->id, [
            ['product_id' => $productMie->id, 'quantity' => 3]
        ], 'pickup', 'cash');

        $this->assertEquals(6000.00, $order->total_amount);
    }

    /**
     * Test Tebus Murah local crop discount logic.
     */
    public function test_tebus_murah_local_product_discount()
    {
        // Create general product to reach min amount (total > 100000)
        $generalProduct = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '1111',
            'name' => 'Minyak Goreng 2L',
            'price_member' => 35000,
            'price_non_member' => 40000,
            'current_stock' => 5,
            'unit' => 'pcs',
            'is_local_product' => false,
        ]);

        // Create local product (is_local_product = true)
        $localProduct = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '2222',
            'name' => 'Beras Pandan Wangi Lokal',
            'price_member' => 15000,
            'price_non_member' => 18000,
            'current_stock' => 10,
            'unit' => 'kg',
            'is_local_product' => true,
        ]);

        // 3 Minyak Goreng = 3 * 35000 = 105000 (reaches > 100000)
        // 2 kg local Beras = 2 * 15000 = 30000. Total gross is 135000.
        // Tebus Murah: Rp 5.000 discount per local crop item -> 2 * 5000 = 10000 discount.
        // Total expected: 135000 - 10000 = 125000.
        $transactionService = resolve(TransactionService::class);
        $order = $transactionService->checkout($this->memberUser->id, [
            ['product_id' => $generalProduct->id, 'quantity' => 3],
            ['product_id' => $localProduct->id, 'quantity' => 2]
        ], 'pickup', 'cash');

        $this->assertEquals(125000.00, $order->total_amount);
    }

    /**
     * Test Coupon Vouchers logic.
     */
    public function test_apply_coupon_vouchers()
    {
        $product = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '3333',
            'name' => 'Beras Cianjur',
            'price_member' => 50000,
            'price_non_member' => 60000,
            'current_stock' => 10,
            'unit' => 'kg',
            'is_local_product' => false,
        ]);

        $transactionService = resolve(TransactionService::class);

        // Test HEMATTANI: 10% off (member price is 50000, 10% is 5000 -> final 45000)
        $order1 = $transactionService->checkout($this->memberUser->id, [
            ['product_id' => $product->id, 'quantity' => 1]
        ], 'pickup', 'cash', 'HEMATTANI');
        $this->assertEquals(45000.00, $order1->total_amount);

        // Test KDKMPMERDEKA: Flat Rp 15.000 off for total >= 50000 (member price is 50000 -> final 35000)
        $order2 = $transactionService->checkout($this->memberUser->id, [
            ['product_id' => $product->id, 'quantity' => 1]
        ], 'pickup', 'cash', 'KDKMPMERDEKA');
        $this->assertEquals(35000.00, $order2->total_amount);

        // Test ALFAGIFT3D: 20% off (member price is 50000, 20% is 10000 -> final 40000)
        $order3 = $transactionService->checkout($this->memberUser->id, [
            ['product_id' => $product->id, 'quantity' => 1]
        ], 'pickup', 'cash', 'ALFAGIFT3D');
        $this->assertEquals(40000.00, $order3->total_amount);
    }

    /**
     * Test POS Lookup Member returns cooperative balance.
     */
    public function test_pos_lookup_member_returns_sukarela_balance()
    {
        // Seed savings
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 75000.00,
            'transaction_date' => now(),
            'notes' => 'Tabungan awal',
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.pos.member', $this->memberProfile->nik));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'name' => $this->memberUser->name,
            'nomor_anggota' => $this->memberProfile->nomor_anggota,
            'sukarela_balance' => 75000.00
        ]);
    }

    /**
     * Test POS Cashier Checkout with Split Payment.
     */
    public function test_pos_checkout_split_payment()
    {
        $product = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '7777',
            'name' => 'Minyak Goreng',
            'price_member' => 15000,
            'price_non_member' => 18000,
            'current_stock' => 10,
            'unit' => 'liter',
            'is_local_product' => false,
        ]);

        // Seed savings
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 10000.00,
            'transaction_date' => now(),
            'notes' => 'Tabungan',
        ]);

        // Buy 1 Minyak Goreng = 15000.
        // Pay 6000 with Saldo Sukarela, and remaining 9000 in Cash.
        $response = $this->actingAs($this->staffUser)->post(route('staff.pos.checkout'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
            'member_nik' => $this->memberProfile->nik,
            'pay_sukarela_amount' => 6000
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Transaksi POS berhasil diselesaikan!'
        ]);

        // Verify Order is created and status is paid
        $order = Order::latest()->first();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('split:6000', $order->payment_method);
        $this->assertEquals(15000.00, $order->total_amount);

        // Verify savings debit is recorded
        $this->assertDatabaseHas('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => -6000.00,
        ]);

        // Verify remaining savings balance is 4000
        $balance = MemberSaving::where('member_id', $this->memberProfile->id)
            ->where('type', 'sukarela')
            ->sum('amount');
        $this->assertEquals(4000.00, $balance);
    }

    /**
     * Test staff can mutate stock to another branch successfully.
     */
    public function test_staff_can_mutate_stock_to_another_branch_updates_both_stocks()
    {
        $productSource = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '9999123',
            'name' => 'Beras Organik',
            'price_member' => 12000,
            'price_non_member' => 14000,
            'current_stock' => 100,
            'unit' => 'kg',
            'is_local_product' => true,
        ]);

        // POST to mutate-branch
        $response = $this->actingAs($this->staffUser)->post(route('staff.products.mutate-branch', $productSource->id), [
            'target_branch_id' => 2, // Gerai Desa Gotong Royong (seeded in migrations, code: DGR)
            'quantity' => 30
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        // Verify source stock is reduced to 70
        $productSource->refresh();
        $this->assertEquals(70, $productSource->current_stock);

        // Verify target product is created/updated in target branch and has stock 30
        // Suffix matches target branch's code: DGR
        $productTarget = Product::where('branch_id', 2)->where('barcode', '9999123-DGR')->first();
        $this->assertNotNull($productTarget);
        $this->assertEquals(30, $productTarget->current_stock);
        $this->assertEquals('Beras Organik', $productTarget->name);
    }

    /**
     * Test staff cannot mutate stock exceeding available stock.
     */
    public function test_staff_cannot_mutate_more_than_current_stock_throws_validation_error()
    {
        $productSource = Product::create([
            'branch_id' => 1,
            'category_id' => $this->category->id,
            'barcode' => '9999124',
            'name' => 'Beras Organik Premium',
            'price_member' => 12000,
            'price_non_member' => 14000,
            'current_stock' => 20,
            'unit' => 'kg',
            'is_local_product' => true,
        ]);

        // Mutate 50 (which exceeds stock 20)
        $response = $this->actingAs($this->staffUser)->post(route('staff.products.mutate-branch', $productSource->id), [
            'target_branch_id' => 2,
            'quantity' => 50
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertEquals(20, $productSource->refresh()->current_stock);
    }

    /**
     * Test stock mutation is blocked if staff is not from the source branch.
     */
    public function test_cross_branch_stock_mutation_is_forbidden_for_other_branches_staff()
    {
        // Create product belonging to branch 2
        $productSource = Product::create([
            'branch_id' => 2,
            'category_id' => $this->category->id,
            'barcode' => '9999125',
            'name' => 'Susu Segar Lokal',
            'price_member' => 10000,
            'price_non_member' => 12000,
            'current_stock' => 50,
            'unit' => 'liter',
            'is_local_product' => true,
        ]);

        // Current staffUser belongs to branch 1, so they shouldn't be allowed to mutate a product in branch 2.
        // We set target_branch_id to 2 so it passes the 'asal dan tujuan tidak boleh sama' check but fails on product lookup.
        $response = $this->actingAs($this->staffUser)->post(route('staff.products.mutate-branch', $productSource->id), [
            'target_branch_id' => 2,
            'quantity' => 10
        ]);

        $response->assertStatus(404); // fails at firstOrFail because branch is scoped in the query
        $this->assertEquals(50, $productSource->refresh()->current_stock);
    }
}
