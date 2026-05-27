<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Keranjang;

class PesananControllerTest extends TestCase
{
    public function test_simpan_menggunakan_stub_auth_dan_db()
    {
        $user = $this->createMock(User::class);
        $user->method('getAuthIdentifier')->willReturn(1);
        $user->method('getAuthIdentifierName')->willReturn('id');

        Auth::shouldReceive('guard')
            ->with('api')
            ->andReturnSelf();
        Auth::shouldReceive('user')
            ->andReturn($user);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturn(response()->json([
                'status' => 'success',
                'order_id' => 1,
                'invoice' => 'INV-TEST-0001'
            ]));

        $controller = new \App\Http\Controllers\PesananController();
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'lat'                => -7.983908,
            'lng'                => 112.621391,
            'alamat_lengkap'     => 'Jl. Test No. 1',
            'detail_alamat'      => 'Lantai 2',
            'ongkir'             => 5000,
            'total_bayar'        => 50000,
            'metode_pembayaran'  => 'Transfer Bank',
        ]);

        $response = $controller->simpan($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
