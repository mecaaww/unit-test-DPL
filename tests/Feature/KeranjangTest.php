<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\KeranjangService;
use App\Models\User;
use Mockery\MockInterface;

class KeranjangTest extends TestCase {
    public function test_hapus_memanggil_service_dengan_benar_menggunakan_mock() {
        $user = User::factory()->make(['id' => 1]);
        $this->actingAs($user, 'api');

        $this->mock(KeranjangService::class, function (MockInterface $mock) use ($user) {
            $idKeranjang = 99;
            $mock->shouldReceive('hapusItem')->once()->with($idKeranjang, $user->id)->andReturn(true);
        });

        $response = $this->deleteJson('/api/keranjang/99');
        $response->assertStatus(200)->assertJson(['status' => 'success']);
    }
}

