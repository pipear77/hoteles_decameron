<?php
// src/app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Clase controladora para gestionar la autenticación de la API.
 * Sigue el principio SOLID, delegando la lógica de negocio al servicio.
 */
class AuthController extends Controller
{
    /**
     * El servicio de usuarios.
     *
     * @var UserServiceInterface
     */
    protected UserServiceInterface $userService;

    /**
     * Constructor del controlador.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Maneja el login de un usuario de la API.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Se delega la autenticación al servicio.
            $result = $this->userService->authenticateUser($request->validated());

            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            // En caso de fallo, se retorna un error 401 para credenciales inválidas.
            return response()->json([
                'status'  => false,
                'message' => 'Credenciales inválidas.',
                'error'   => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Se delega la lógica de cierre de sesión al servicio.
            $this->userService->logoutUser();

            return response()->json(['message' => 'Logout exitoso'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // En caso de fallo, se retorna un error 500.
            return response()->json([
                'status' => false,
                'message' => 'Hubo un error al cerrar la sesión.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
