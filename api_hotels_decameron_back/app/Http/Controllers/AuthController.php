<?php

namespace App\Http\Controllers;

use App\Services\UserServiceInterface;
use Illuminate\Http\Request;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Registra un nuevo usuario delegando la lógica al servicio.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Usar $request->validated() es crucial. Esto nos da un array
            // con solo los datos que pasaron la validación, garantizando
            // que 'role_id' estará presente y no será nulo.
            $userData = $request->validated();

            $user = $this->userService->registerUser($userData);

            return response()->json([
                'status' => true,
                'message' => '¡Usuario creado exitosamente!',
                'user' => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Hubo un error al crear el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Maneja el login de un usuario existente.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // 1. Obtiene las credenciales validadas del FormRequest.
        $credentials = $request->validated();

        // 2. Delega toda la lógica de autenticación al servicio.
        $result = $this->userService->authenticateUser($credentials);

        // 3. Maneja el resultado devuelto por el servicio.
        if ($result['status'] === true) {
            return response()->json($result);
        }

        // Si la autenticación falla, devolvemos un error 401.
        return response()->json($result, 401);
    }

    /**
     * Cierra la sesión del usuario eliminando su token.
     */
    public function logout(Request $request): JsonResponse
    {
        // La lógica de logout es correcta. La mantenemos.
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout exitoso'], 200);
    }
}
