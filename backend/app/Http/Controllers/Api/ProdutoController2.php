<?php

namespace App\Http\Controllers\Api;

use App\Models\Produto;

class ProdutoController2 extends GenericController
{
    protected string $model = Produto::class;

    protected ?string $cacheKey = 'produtos';

    protected function rulesStore(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:0'
        ];
    }

    protected function rulesUpdate(): array
    {
        return [
            'nome' => 'sometimes|string|max:255',
            'preco' => 'sometimes|numeric|min:0',
            'quantidade' => 'sometimes|integer|min:0'
        ];
    }
}