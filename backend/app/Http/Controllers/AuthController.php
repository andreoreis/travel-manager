<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $service;

    public function __construct(AuthService $service) {
        $this->service = $service;
    }

    // Cadastra o usuário na base de dados
    public function register(Request $request)
    {
        return $this->service->register($request);
    }

    // Autentica o usuário
    public function login(Request $request)
    {
        return $this->service->login($request);
    }

    // Info do usuário autenticado
    public function me()
    {
        return $this->service->me();
    }

    // Invalida o token
    public function logout()
    {
        return $this->service->logout();
    }

    // Atualiza o token
    public function refresh()
    {
        return $this->service->refresh();
    }
}
