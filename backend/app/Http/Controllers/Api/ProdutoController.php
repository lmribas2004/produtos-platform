<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {

            $produtos = Cache::remember('produtos', 60, function () {
                return Produto::all();
            });

            return response()->json($produtos, 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar produtos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'nome'  => 'required|string|max:255',
                'preco' => 'required|numeric|min:0',
                'quantidade' => 'required|integer|min:0'
            ]);

            $produto = Produto::create($validated);

            // Limpa cache
            Cache::forget('produtos');

            return response()->json([
                'message' => 'Produto criado com sucesso',
                'data' => $produto
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {

            $produto = Produto::findOrFail($id);

            return response()->json($produto, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {

            $produto = Produto::findOrFail($id);

            $validated = $request->validate([
                'nome'  => 'sometimes|string|max:255',
                'preco' => 'sometimes|numeric|min:0',
                'quantidade' => 'sometimes|integer|min:0'
            ]);

            $produto->update($validated);

            Cache::forget('produtos');

            return response()->json([
                'message' => 'Produto atualizado com sucesso',
                'data' => $produto
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {

            $produto = Produto::findOrFail($id);
            $produto->delete();

            Cache::forget('produtos');

            return response()->json([
                'message' => 'Produto removido com sucesso'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao remover produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
