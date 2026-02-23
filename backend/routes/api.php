<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdutoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui são registradas as rotas da API da aplicação.
| Todas as rotas neste arquivo recebem automaticamente o prefixo /api
| definido no RouteServiceProvider.
|
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rotas protegidas pelo Keycloak
    |--------------------------------------------------------------------------
    */

    Route::middleware(['keycloak'])->group(function () {

        // CRUD completo de produtos
        Route::apiResource('produtos', ProdutoController::class);

    });

});
