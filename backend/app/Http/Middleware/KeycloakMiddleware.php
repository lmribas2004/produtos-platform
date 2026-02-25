<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Symfony\Component\HttpFoundation\Response;

class KeycloakMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Liberar preflight CORS
        // Verifica se é uma requisição OPTIONS (preflight)
        // Se for, retorna uma resposta 200 OK sem processar o token
        // Isso é necessário para que o navegador permita as requisições CORS
        if ($request->getMethod() === "OPTIONS") {
            return response()->json([], 200);
        }

        // Verifica se o header Authorization existe
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token não informado'
            ], 401);
        }

        // Extrai o token
        $token = str_replace('Bearer ', '', $authHeader);

        try {

            // Busca chave pública do Keycloak (JWKS)
            $jwks = Http::get(
                'http://keycloak:8080/realms/produtos-realm/protocol/openid-connect/certs'
            )->json();

            // Converte chaves
            $keys = JWK::parseKeySet($jwks);

            // Decodifica e valida assinatura
            $decoded = JWT::decode($token, $keys);

            // Adiciona usuário ao request
            $request->attributes->add([
                'keycloak_user' => $decoded
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Token inválido ou expirado',
                'error' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}