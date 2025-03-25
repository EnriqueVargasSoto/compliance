<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Security\Models\Module;
use Modules\Security\Models\Office;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Método para hacer login y obtener el token
    public function login(Request $request)
    {
        // ✅ Validar entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);

        // ❌ Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'messages' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        return $this->respondWithToken($token);
    }

    // Obtener información del usuario autenticado
    public function me()
    {
        $user = Auth::user();

        // Obtener las oficinas donde el usuario tiene roles
        $offices = Office::whereIn('id', function ($query) use ($user) {
            $query->select('office_id')->from('office_user_roles')->where('user_id', $user->id);
        })->get();

        $roles = collect();
        $permissions = collect();
        $modules = collect();

        // Construir la respuesta con oficinas, roles, permisos y módulos
        $officeData = $offices->map(function ($office) use ($user, &$roles, &$permissions, &$modules) {
            $officeRoles = $this->getRolesByOffice($user->id, $office->id);
            $officePermissions = $this->getPermissionsByOffice($user->id, $office->id);

            // Obtener módulos de los permisos
            $officeModules = $this->getModulesByPermissions($officePermissions);

            $roles = $roles->merge($officeRoles);
            $permissions = $permissions->merge($officePermissions);
            $modules = $modules->merge($officeModules);

            return [
                'id' => $office->id,
                'name' => $office->name,
                'roles' => $officeRoles->unique()->values(),
                'permissions' => $officePermissions->unique()->values(),
                'modules' => $this->orderModulesById($this->removeEmptyChildren($this->buildModuleHierarchy($officeModules))),//$this->buildModuleHierarchy($officeModules),
            ];
        });

        return response()->json([
            'user' => $user->load('roles', 'permissions', 'offices', 'person'),
            'offices' => $officeData,
            'roles' => $roles->unique()->values(),
        ]);
    }

    private function orderModulesById($modules)
    {
        // Ordenar por ID de menor a mayor
        usort($modules, function ($a, $b) {
            return $a['id'] - $b['id'];
        });

        // Aplicar la ordenación a los hijos recursivamente
        foreach ($modules as &$module) {
            if (isset($module['children']) && is_array($module['children'])) {
                $module['children'] = $this->orderModulesById($module['children']);
            }
        }
        return $modules;
    }

    // Obtener roles del usuario en una oficina específica
    private function getRolesByOffice($userId, $officeId)
    {
        return Role::whereIn('id', function ($query) use ($userId, $officeId) {
            $query->select('role_id')
                  ->from('office_user_roles')
                  ->where('user_id', $userId)
                  ->where('office_id', $officeId);
        })->pluck('name');
    }

    // Obtener permisos únicos del usuario en una oficina específica
    private function getPermissionsByOffice($userId, $officeId)
    {
        $rolePermissions = Permission::whereIn('id', function ($query) use ($userId, $officeId) {
            $query->select('permission_id')
                  ->from('role_has_permissions')
                  ->whereIn('role_id', function ($subQuery) use ($userId, $officeId) {
                      $subQuery->select('role_id')
                               ->from('office_user_roles')
                               ->where('user_id', $userId)
                               ->where('office_id', $officeId);
                  });
        })->pluck('name');

        $directPermissions = Permission::whereIn('id', function ($query) use ($userId, $officeId) {
            $query->select('permission_id')
                  ->from('office_user_permissions')
                  ->where('user_id', $userId)
                  ->where('office_id', $officeId);
        })->pluck('name');

        return $rolePermissions->merge($directPermissions)->unique()->values();
    }

    // ✅ Obtener módulos en base a los permisos
    private function getModulesByPermissions($permissions)
    {
        // Obtener IDs de los permisos
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->pluck('id');

        // Obtener los módulos relacionados con los permisos
        $modules = DB::table('module_has_permissions')
            ->join('modules', 'module_has_permissions.module_id', '=', 'modules.id')
            ->whereIn('module_has_permissions.permission_id', $permissionIds)
            ->select('modules.id', 'modules.name', 'modules.parent_id', 'modules.route', 'modules.icon')
            ->distinct()
            ->get();

        // Asegurarnos de incluir los módulos padres en la jerarquía
        return $this->getAllModulesWithParents($modules);
    }

    // ✅ Obtener todos los módulos relacionados, incluyendo los padres
    private function getAllModulesWithParents($modules)
    {
        $allModules = collect($modules);

        foreach ($modules as $module) {
            if ($module->parent_id !== null) {
                $parentModules = DB::table('modules')
                    ->where('id', $module->parent_id)
                    ->select('id', 'name', 'parent_id', 'route', 'icon')
                    ->get();

                $allModules = $allModules->merge($parentModules);
            }
        }

        return $allModules->unique();
    }

    // Construir jerarquía de módulos
    private function buildModuleHierarchy($modules, $parentId = null)
    {
        $tree = [];
        foreach ($modules as $module) {
            if ($module->parent_id == $parentId) {
                $children = $this->buildModuleHierarchy($modules, $module->id);
                $tree[] = [
                    'id' => $module->id,
                    'title' => $module->name,
                    'to' => $module->route,
                    'icon' => ['icon' => $module->icon],
                    'children' => $children,
                ];
            }
        }
        return $tree;
    }

    // Eliminar `children` vacíos recursivamente
    private function removeEmptyChildren($modules)
    {
        return array_map(function ($module) {
            if (!empty($module['children'])) {
                $module['children'] = $this->removeEmptyChildren($module['children']);
            } else {
                unset($module['children']);
            }
            return $module;
        }, $modules);
    }

    // Cerrar sesión (invalidar token)
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Refrescar token
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    // Respuesta del token
    protected function respondWithToken($token)
    {


        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
