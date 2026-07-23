<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $result = $action->execute($request->validated());

        return $this->createdResponse([
            'user' => AuthUserResource::make($result['user']),
            'token' => $result['token'],
        ], 'Usuario registrado correctamente.');
    }

    public function login(LoginRequest $request, LoginUserAction $action): JsonResponse
    {
        $result = $action->execute($request->validated());

        if (! $result) {
            return $this->errorResponse('Credenciales inválidas.', 401);
        }

        return $this->successResponse([
            'user' => AuthUserResource::make($result['user']),
            'token' => $result['token'],
        ], 'Inicio de sesión correcto.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            AuthUserResource::make($request->user()),
            'Usuario autenticado obtenido correctamente.',
        );
    }

    public function logout(Request $request, LogoutUserAction $action): JsonResponse
    {
        $action->execute($request->user());

        return $this->successResponse(null, 'Sesión cerrada correctamente.');
    }
}
