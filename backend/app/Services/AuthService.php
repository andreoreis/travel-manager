<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ApiException;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthService {

    private $user_repository;

    public function __construct(UserRepository $user_repository,) {
        $this->user_repository = $user_repository;
    }

    // Cadastra o usuário na base de dados
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = $this->user_repository::create($data);

        $token =  JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }

    // Autentica o usuário
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            throw new ApiException('Não é possível cancelar um pedido aprovado.', Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    // Info do usuário autenticado
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    // Invalida o token
    public function logout()
    {
        JWTAuth::parseToken()->invalidate();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    // Refresh token
    public function refresh()
    {
        $newToken = JWTAuth::refresh();
        return $this->respondWithToken($newToken);
    }

    // Atualiza o token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
