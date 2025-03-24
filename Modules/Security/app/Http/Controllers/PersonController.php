<?php

namespace Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Security\Models\Person;

class PersonController extends Controller
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

            $query = Person::orderBy('id', 'asc');

            // Aplicar la búsqueda si se proporciona un término
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');

                });
            }

            // Si se proporciona perPage, aplicar paginación, de lo contrario traer todo
            if ($perPage) {
                $people = $query->paginate($perPage, '*', 'page', $page);
                return response()->json([
                    'data' => $people->items(),
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $people->total(),
                    'recordsFiltered' => $people->total(),
                ]);
            } else {
                $people = $query->get();
                return response()->json([
                    'data' => $people,
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $people->count(),
                    'recordsFiltered' => $people->count(),
                ]);
            }
        } catch (\Exception  $e) {
            //throw $th;
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al listar las Personas',
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
                'dni' => 'required|string|max:8',
                'names' => 'required|string|max:255',
                'surnames' => 'required|string|max:255',
                'email' => 'required|string|max:255',
            ], [
                'dni.required' => 'El DNI de la Persona es obligatorio.',
                'names.required' => 'Los Nombres de la Persona es obligatorio.',
                'surnames.required' => 'Los Apellidos de la Persona es obligatorio.',
                'email.required' => 'El Email de la Persona es obligatorio.',
                'dni.string' => 'El DNI debe ser un texto.',
                'names.string' => 'Los Nombres debe ser un texto.',
                'surnames.string' => 'Los Apellidos debe ser un texto.',
                'email.string' => 'El Email debe ser un texto.',
                'dni.max' => 'El DNI no puede tener más de 8 caracteres.',
                'names.max' => 'Los Nombres no puede tener más de 255 caracteres.',
                'surnames.max' => 'Los Apellidos no puede tener más de 255 caracteres.',
                'email.max' => 'El Email no puede tener más de 255 caracteres.'
            ]);

            // Crear el módulo
            $person = Person::create($request->all());

            return response()->json(['data' => $person, 'message' => 'Persona creada correctamente']);
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
                'error' => 'Error al crear la Persona',
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
                'dni' => 'required|string|max:8',
                'names' => 'required|string|max:255',
                'surnames' => 'required|string|max:255',
                'email' => 'required|string|max:255',
            ], [
                'dni.required' => 'El DNI de la Persona es obligatorio.',
                'names.required' => 'Los Nombres de la Persona es obligatorio.',
                'surnames.required' => 'Los Apellidos de la Persona es obligatorio.',
                'email.required' => 'El Email de la Persona es obligatorio.',
                'dni.string' => 'El DNI debe ser un texto.',
                'names.string' => 'Los Nombres debe ser un texto.',
                'surnames.string' => 'Los Apellidos debe ser un texto.',
                'email.string' => 'El Email debe ser un texto.',
                'dni.max' => 'El DNI no puede tener más de 8 caracteres.',
                'names.max' => 'Los Nombres no puede tener más de 255 caracteres.',
                'surnames.max' => 'Los Apellidos no puede tener más de 255 caracteres.',
                'email.max' => 'El Email no puede tener más de 255 caracteres.'
            ]);

            // Crear el módulo
            $person = Person::find($id);

            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $person->update($request->all());

            return response()->json(['data' => $person, 'message' => 'Persona actualizada correctamente']);
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
                'error' => 'Error al actualizar la Persona',
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
            $person = Person::find($id);

            //  Si no existe, retornar error
            if (!$person) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $person->delete();

            return response()->json(['message' => 'Persona eliminada correctamente']);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al eliminar la Persona',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function initTable(){

        $headers = [
            ['title' => 'Apellidos', 'key'=> 'surnames'],
            ['title' => 'Nombres', 'key'=> 'names'],
            ['title' => 'DNI', 'key'=> 'dni', 'sortable' => false],
            ['title' => 'Email', 'key'=> 'Email', 'sortable' => false],
            ['title' => 'Télefono', 'key'=> 'phone', 'sortable' => false],
            ['title' => 'Acciones', 'key'=> 'actions', 'sortable' => false],
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
            'title' => 'Personas',
            /* 'buttons' => $buttons,
            'filters' => [],
            'check' => false,
            'colors' => $colors,
            'search' => true, */
            'item_selects' => $itemSelects
        ];
        return response()->json(['data'=>$data]);
    }
}
