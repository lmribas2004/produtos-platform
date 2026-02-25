<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

/**
 * @template TModel of Model
 */
abstract class GenericController extends Controller
{
    /**
     * @var class-string<TModel>
     */
    protected string $model;

    protected ?string $cacheKey = null;
    protected int $cacheTTL = 60;

    /**
     * Regras para store
     */
    abstract protected function rulesStore(): array;

    /**
     * Regras para update
     */
    abstract protected function rulesUpdate(): array;

    protected function modelInstance(): Model
    {
        return new $this->model;
    }

    public function index(): JsonResponse
    {
        try {

            if ($this->cacheKey) {
                $data = Cache::remember($this->cacheKey, $this->cacheTTL, function () {
                    return ($this->model)::all();
                });
            } else {
                $data = ($this->model)::all();
            }

            return response()->json($data, 200);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao listar registros', $e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate($this->rulesStore());

            $model = ($this->model)::create($validated);

            $this->clearCache();

            return response()->json([
                'message' => 'Registro criado com sucesso',
                'data' => $model
            ], 201);

        } catch (Exception $e) {
            return $this->errorResponse('Erro ao criar registro', $e);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {

            $model = ($this->model)::findOrFail($id);

            return response()->json($model, 200);

        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Registro não encontrado'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Erro ao buscar registro', $e);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {

            $model = ($this->model)::findOrFail($id);

            $validated = $request->validate($this->rulesUpdate());

            $model->update($validated);

            $this->clearCache();

            return response()->json([
                'message' => 'Registro atualizado com sucesso',
                'data' => $model
            ], 200);

        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Registro não encontrado'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Erro ao atualizar registro', $e);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {

            $model = ($this->model)::findOrFail($id);
            $model->delete();

            $this->clearCache();

            return response()->json([
                'message' => 'Registro removido com sucesso'
            ], 200);

        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Registro não encontrado'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Erro ao remover registro', $e);
        }
    }

    protected function clearCache(): void
    {
        if ($this->cacheKey) {
            Cache::forget($this->cacheKey);
        }
    }

    protected function errorResponse(string $message, Exception $e): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => $e->getMessage()
        ], 500);
    }
}