<?php

namespace Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
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

            $query = User::with('person')->orderBy('id', 'asc');

            // Aplicar la búsqueda si se proporciona un término
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');

                });
            }

            // Si se proporciona perPage, aplicar paginación, de lo contrario traer todo
            if ($perPage) {
                $users = $query->paginate($perPage, '*', 'page', $page);
                return response()->json([
                    'data' => $users->items(),
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $users->total(),
                    'recordsFiltered' => $users->total(),
                ]);
            } else {
                $users = $query->get();
                return response()->json([
                    'data' => $users,
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $users->count(),
                    'recordsFiltered' => $users->count(),
                ]);
            }
        } catch (\Exception  $e) {
            //throw $th;
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al listar los Usuarios',
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
                'person_id' => 'required|integer',
                'email' => 'required|string|max:255',
                'password' => 'required|string|max:255'
            ], [
                'person_id.required' => 'La Persona a la que pertenece el Usuario es obligatorio.',
                'email.required' => 'El Email del Usuario es obligatorio.',
                'password.required' => 'El Password del Usuario es obligatorio.',
                'person_id.integer' => 'El ID de la Persona debe ser un número.',
                'email.string' => 'El Email debe ser un texto.',
                'password.string' => 'El Password debe ser un texto.',
                'email.max' => 'El Email no puede tener más de 255 caracteres.',
                'password.max' => 'El Password no puede tener más de 255 caracteres.',
            ]);

            // Crear el módulo
            $user = User::create([
                'person_id' => $request->person_id,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            return response()->json(['data' => $user, 'message' => 'Usuario creado correctamente']);
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
                'error' => 'Error al crear el Usuario',
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
                'person_id' => 'required|integer',
                'email' => 'required|string|max:255',
                'password' => 'required|string|max:255'
            ], [
                'person_id.required' => 'La Persona a la que pertenece el Usuario es obligatorio.',
                'email.required' => 'El Email del Usuario es obligatorio.',
                'password.required' => 'El Password del Usuario es obligatorio.',
                'person_id.integer' => 'El ID de la Persona debe ser un número.',
                'email.string' => 'El Email debe ser un texto.',
                'password.string' => 'El Password debe ser un texto.',
                'email.max' => 'El Email no puede tener más de 255 caracteres.',
                'password.max' => 'El Password no puede tener más de 255 caracteres.',
            ]);
            // Crear el módulo
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrada'], 404);
            }

            $user->person_id = $request->person_id;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            return response()->json(['data' => $user, 'message' => 'Usuario actualizado correctamente']);
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
                'error' => 'Error al actualizar el Usuario',
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
            $user = User::find($id);

            //  Si no existe, retornar error
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrada'], 404);
            }

            $user->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al eliminar el Usuario',
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

            ['title' => 'nombres', 'key'=> 'person.names'],
            ['title' => 'Apellidos', 'key'=> 'person.surnames'],
            ['title' => 'DNI', 'key'=> 'person.dni'],
            ['title' => 'email', 'key'=> 'email'],
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
            'title' => 'Usuarios',
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
