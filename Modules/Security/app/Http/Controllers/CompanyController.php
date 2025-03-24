<?php

namespace Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Security\Models\Company;

class CompanyController extends Controller
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

            $query = Company::orderBy('id', 'asc');

            // Aplicar la búsqueda si se proporciona un término
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');

                });
            }

            // Si se proporciona perPage, aplicar paginación, de lo contrario traer todo
            if ($perPage) {
                $companies = $query->paginate($perPage, '*', 'page', $page);
                return response()->json([
                    'data' => $companies->items(),
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $companies->total(),
                    'recordsFiltered' => $companies->total(),
                ]);
            } else {
                $companies = $query->get();
                return response()->json([
                    'data' => $companies,
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $companies->count(),
                    'recordsFiltered' => $companies->count(),
                ]);
            }
        } catch (\Exception  $e) {
            //throw $th;
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al listar las Compañías',
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
                'name' => 'required|string|max:255',
                'ruc' => 'required|string|max:11|min:11',
            ], [
                'name.required' => 'El nombre del módulo es obligatorio.',
                'ruc.required' => 'El RUC de la compañía es obligatorio.',
                'name.string' => 'El nombre debe ser un texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'ruc.max' => 'El RUC no puede tener más de 11 caracteres.',
                'ruc.min' => 'El RUC no puede tener menos de 11 caracteres.'
            ]);

            // Crear el módulo
            $company = Company::create($request->all());

            return response()->json(['data' => $company, 'message' => 'Compañía creada correctamente']);
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
                'error' => 'Error al crear la Compañía',
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
                'name' => 'required|string|max:255',
                'ruc' => 'required|string|max:11|min:11',
            ], [
                'name.required' => 'El nombre del módulo es obligatorio.',
                'ruc.required' => 'El RUC de la compañía es obligatorio.',
                'name.string' => 'El nombre debe ser un texto.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'ruc.max' => 'El RUC no puede tener más de 11 caracteres.',
                'ruc.min' => 'El RUC no puede tener menos de 11 caracteres.'
            ]);

            // Crear el módulo
            $company = Company::find($id);

            if (!$company) {
                return response()->json(['error' => 'Compañía no encontrada'], 404);
            }

            $company->update($request->all());

            return response()->json(['data' => $company, 'message' => 'Compañía actualizada correctamente']);
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
                'error' => 'Error al actualizar la Compañía',
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
            $company = Company::find($id);

            //  Si no existe, retornar error
            if (!$company) {
                return response()->json(['error' => 'Compañía no encontrada'], 404);
            }

            $company->delete();

            return response()->json(['message' => 'Compañía eliminada correctamente']);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al eliminar la Compañía',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function initTable(){

        $headers = [
            ['title' => 'Nombre', 'key'=> 'name'],
            ['title' => 'RUC', 'key'=> 'ruc', 'sortable' => false],
            ['title' => 'Email', 'key'=> 'email', 'sortable' => false],
            ['title' => 'Telefono', 'key'=> 'phone', 'sortable' => false],
            ['title' => 'Direccion', 'key'=> 'address', 'sortable' => false],
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
            'title' => 'Compañías',
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
