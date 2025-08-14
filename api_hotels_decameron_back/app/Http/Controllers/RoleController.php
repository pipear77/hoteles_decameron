<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private RoleServiceInterface $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Muestra una lista de todos los roles.
     */
    public function index(): JsonResponse
    {
        $roles = $this->roleService->getAll();
        return response()->json($roles);
    }

    /**
     * Muestra un rol específico.
     */
    public function show(int $id): JsonResponse
    {
        $role = $this->roleService->findById($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    /**
     * Almacena un nuevo rol.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());
        return response()->json($role, 201);
    }

    /**
     * Actualiza un rol específico.
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = $this->roleService->update($id, $request->validated());

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json($role);
    }

    /**
     * Elimina un rol específico.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->roleService->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Role not found or could not be deleted'], 404);
        }

        return response()->json(null, 204);
    }
}
