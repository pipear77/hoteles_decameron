<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

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

        return response()->json($user->load('role'));
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
                'user' => $user->load('role'),
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
     * Asigna un rol a un usuario.
     * Se delega la lógica de negocio al servicio.
     *
     * @param Request $request El request conteniendo el role_id.
     * @param int $id El ID del usuario.
     */
    public function updateRole(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['role_id' => 'required|exists:roles,id']);

            $user = $this->userService->updateUserRole($id, $request->role_id);

            return response()->json([
                'message' => 'Rol asignado exitosamente.',
                'user' => $user->load('role'),
            ], 200);

        } catch (NotFoundHttpException $e) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        } catch (AccessDeniedHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
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
            $deleted = $this->userService->deleteUser($id);
            if (!$deleted) {
                return response()->json(['message' => 'Usuario no encontrado.'], 404);
            }

            return response()->json([
                'message' => 'Usuario eliminado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar usuario: {$e->getMessage()}");
            return response()->json([
                'message' => 'Error al eliminar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
