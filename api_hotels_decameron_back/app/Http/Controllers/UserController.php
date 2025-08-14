<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        // Inyección de dependencias para el servicio de usuarios.
        // Esto garantiza la separación de responsabilidades.
        $this->userService = $userService;
    }

    /**
     * Muestra una lista de todos los usuarios.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'No se pudo obtener la lista de usuarios.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra un usuario específico.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado.'], 404);
            }

            return response()->json([
                'message' => 'Usuario actualizado exitosamente.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'No se pudo actualizar el usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asigna un único rol a un usuario.
     *
     * @param Request $request El request conteniendo el role_id.
     * @param int $id El ID del usuario.
     */
    public function assignRole(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['role_id' => 'required|exists:roles,id']);

            $user = User::findOrFail($id);
            $user->role_id = $request->role_id;
            $user->save();
            $user->load('role');

            return response()->json([
                'message' => 'Rol asignado exitosamente.',
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            Log::error("Error al asignar rol: {$e->getMessage()}");
            return response()->json([
                'message' => 'Error al asignar el rol.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     *
     * @param int $id El ID del usuario a eliminar.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'Usuario eliminado exitosamente',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario: {$e->getMessage()}");
            return response()->json([
                'message' => 'Error al eliminar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
