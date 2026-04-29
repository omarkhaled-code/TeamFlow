<?php


use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/auth/{provider}', [SocialLoginController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);

// Route::post('/broadcasting/auth', function (Request $request) {
//     return [
//         'user' => $request->user(),
//     ];
// });

Broadcast::routes(['middleware' => ['auth:sanctum']]);
