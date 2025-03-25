<?php

namespace Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Obtener los parámetros de paginación de la solicitud
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page');
            $search = $request->get('search');

            $query = Role::with('users')->orderBy('id', 'asc');

            // Aplicar la búsqueda si se proporciona un término
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');

                });
            }

            // Si se proporciona perPage, aplicar paginación, de lo contrario traer todo
            if ($perPage) {
                $roles = $query->paginate($perPage, '*', 'page', $page);
                return response()->json([
                    'data' => $roles->items(),
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $roles->total(),
                    'recordsFiltered' => $roles->total(),
                ]);
            } else {
                $roles = $query->get();
                return response()->json([
                    'data' => $roles,
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $roles->count(),
                    'recordsFiltered' => $roles->count(),
                ]);
            }
        } catch (\Exception  $e) {
            //throw $th;
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al listar los Roles',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('security::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            // Validar que el nombre sea obligatorio
            $request->validate([
                'name' => 'required|string|max:255'
            ], [
                'name.required' => 'El Nombre del Rol es obligatorio.',
                'name.string' => 'El Nombre debe ser un texto.',
                'name.max' => 'El Nombre no puede tener más de 255 caracteres.'
            ]);

            // Crear el módulo
            $role = Role::create($request->all());

            return response()->json(['data' => $role, 'message' => 'Rol creado correctamente']);
            //code...
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación y devolver el mensaje en español
            return response()->json([
                'error' => 'Error de validación',
                'message' => $e->errors() // Laravel devuelve un array de errores
            ], 422);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al crear el Rol',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('security::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('security::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        try {
            // Validar que el nombre sea obligatorio
            $request->validate([
                'name' => 'required|string|max:255'
            ], [
                'name.required' => 'El Nombre del Rol es obligatorio.',
                'name.string' => 'El Nombre debe ser un texto.',
                'name.max' => 'El Nombre no puede tener más de 255 caracteres.'
            ]);
            // Crear el módulo
            $role = Role::find($id);

            if (!$role) {
                return response()->json(['error' => 'Rol no encontrada'], 404);
            }

            $role->update($request->all());

            return response()->json(['data' => $role, 'message' => 'Rol actualizado correctamente']);
            //code...
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación y devolver el mensaje en español
            return response()->json([
                'error' => 'Error de validación',
                'message' => $e->errors() // Laravel devuelve un array de errores
            ], 422);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al actualizar el Rol',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {
        try {
            //  Buscar el módulo
            $role = Role::find($id);

            //  Si no existe, retornar error
            if (!$role) {
                return response()->json(['error' => 'Rol no encontrada'], 404);
            }

            $role->delete();

            return response()->json(['message' => 'Rol eliminado correctamente']);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al eliminar el Rol',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function initTable(Request $request){

        $user = Auth::user();
        $officeId = $request->input('office_id'); // ID de la oficina
        $moduleName = $request->input('module_name') ?? 'modules'; // Nombre del módulo (Ej: "modulos")

        if (!$officeId || !$moduleName) {
            return response()->json(['error' => 'Oficina y módulo son requeridos'], 400);
        }

        // Obtener todos los permisos del usuario en la oficina
        $permissions = $user->getPermissionsByOffice($officeId);

        // Filtrar solo los permisos relacionados con el módulo especificado
        $modulePermissions = $permissions->filter(function ($permission) use ($moduleName) {
            return Str::startsWith($permission, $moduleName . '.');
        })->values();

        $headers = [
            ['title' => 'Rol', 'key'=> 'name'],

            ['title' => 'Guard', 'key'=> 'guard_name'],
            ['title' => 'Acciones', 'key'=> 'actions', 'sortable' => false]
        ];

        $itemSelects = [
            ['title' => '5', 'value'=> 5],
            ['title' => '10', 'value'=> 10],
            ['title' => '25', 'value'=> 25],
            ['title' => '50', 'value'=> 50],
            ['title' => '100', 'value'=> 100],
        ];

        $data = [
            'headers' => $headers,
            'par_page' => 10,
            'page' => 1,
            'title' => 'Roles',
            /* 'buttons' => $buttons,
            'filters' => [],
            'check' => false,
            'colors' => $colors,
            'search' => true, */
            'item_selects' => $itemSelects,
            'permissions' => $modulePermissions, // Solo los permisos del módulo solicitado
        ];
        return response()->json(['data'=>$data]);
    }
}
