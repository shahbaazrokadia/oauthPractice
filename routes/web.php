<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));

    $query = http_build_query([
        'client_id' => '8',
        'redirect_uri' => 'http://client.test/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect('http://127.0.0.1:8004/oauth/authorize?'.$query);
})->name('get.token');



Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull('state');

    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );

    $http = new GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1:8004/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => '8',
            'client_secret' => 'CVQ1GSbqROOYvLXBwGTiXIslN71kH46HFbTASwKi',
            'redirect_uri' => 'http://client.test/callback',
            'code' => $request->code,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
});

Route::get('refresh-token', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1:8004/oauth/token', [
    'form_params' => [
        'grant_type' => 'refresh_token',
        'refresh_token' => 'def50200c9d0498df6c2b2390b8556ec5f11ee28ca1ba66693300285f36e7cfac494ab0b2336f23595692d1500174478e15fbc367620d5dafcefdef6e0d8b224c39215651d22a8d6b821a461c609a8a9df23a5d10778e08f0786d24cde8adc9c854c538a515c66f390c43779a634a08259bb73b4d8961d77100e7581f0110025959589afb4fd59ae7554c05cb2182438f6ffbaa0f4fe445674eb6890132db104af0ead7b0e27d10dbeb89dd28876aaa8d5cd6880ce21ae72cd30e0e7a70dd642bb3e11f1b7b63afcafc03f8d311b8f3851faa74a811a5255983ad42d4213ebff738f474b978dfae15ef1e80251ab7d2455a5b28ffd31d2381c9b4411d9107b9f2bc2333984290425111017929c6cfe468756786fc939681e0f6ff6ff9ad9f54151cd99292ad3d5a23d5cd9fd5c4fa3846c1e3cf6c6f8695213e951230af9a1958b2335061dfe5e68aa404fa34e3d287366658377dacf0f46f58f082a0ee94a6b8d40ecfb13',
        'client_id' => '10',
        'client_secret' => '7cCRZVGEJoVTCftyzjekqcIQVgLSNSvnySshaacS',
        'scope' => '',
    ],
    ]);
    return json_decode((string) $response->getBody(), true);
})->name('get.refresh.token');

Route::get('grant-password', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://127.0.0.1:8004/oauth/token', [
    'form_params' => [
        'grant_type' => 'password',
        'client_id' => '10',
        'client_secret' => '7cCRZVGEJoVTCftyzjekqcIQVgLSNSvnySshaacS',
        'username' => 'srokadia@hesabe.com',
        'password' => '12345678',
        'scope' => '*',
    ],
    ]);

    return json_decode((string) $response->getBody(), true);
})->name('get.password.grant');
