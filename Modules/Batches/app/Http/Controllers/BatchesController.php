<?php

namespace Modules\Batches\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Batches\Models\Batch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BatchesController extends Controller
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

            $query = Batch::orderBy('id', 'asc');

            // Aplicar la búsqueda si se proporciona un término
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('batch', 'like', '%' . $search . '%');

                });
            }

            // Si se proporciona perPage, aplicar paginación, de lo contrario traer todo
            if ($perPage) {
                $batches = $query->paginate($perPage, '*', 'page', $page);
                $data = $batches->map(function ($lote) {
                    return $this->transformarLote($lote);
                });
                return response()->json([
                    'data' => $data,//$batches->items(),
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $batches->total(),
                    'recordsFiltered' => $batches->total(),
                ]);
            } else {
                $batches = $query->get();
                $data = $batches->map(function ($lote) {
                    return $this->transformarLote($lote);
                });
                return response()->json([
                    'data' => $data,//$batches,
                    //'draw' => intval($request->get('draw')),
                    'recordsTotal' => $batches->count(),
                    'recordsFiltered' => $batches->count(),
                ]);
            }
        } catch (\Exception  $e) {
            //throw $th;
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al listar los Lotes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function transformarLote($lote)
    {
        return [
            'id' => $lote->id,
            'batch' => $lote->batch,
            'date_init' => $lote->date_init,
            'date_end' => $lote->date_end,
            'status' => $lote->status,
            'signed' => $lote->signed,
            'deleted_at' => $lote->deleted_at,
            'created_at' => $lote->created_at,
            'updated_at' => $lote->updated_at,
            'permisos' => $this->obtenerPermisos($lote),
        ];
    }

    private function obtenerPermisos($lote)
    {
        $permisos = [];

        if ($lote->status == 0 && $lote->signed == 0) {
            $permisos[] = [
                'title' => 'Ver Procesos',
                'icon' => 'tabler-eye-spark',
                'action' => 'ver_proceso',
            ];
            $permisos[] = [
                'title' => 'Editar',
                'icon' => 'tabler-pencil',
                'action' => 'editar',
            ];
            $permisos[] = [
                'title' => 'Eliminar',
                'icon' => 'tabler-trash',
                'action' => 'eliminar',
            ];
        }
        if ($lote->status == 1 && $lote->signed == 0) {
            $permisos[] = [
                'title' => 'Ver Procesos',
                'icon' => 'tabler-eye-spark',
                'action' => 'ver_proceso',
            ];
            $permisos[] = [
                'title' => 'Eliminar',
                'icon' => 'tabler-trash',
                'action' => 'eliminar',
            ];
        }
        if ($lote->status == 1 && $lote->signed == 1) {
            $permisos[] = [
                'title' => 'Ver Procesos',
                'icon' => 'tabler-eye-spark',
                'action' => 'ver_proceso',
            ];
        }

        return $permisos;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('batches::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            // Validar que el nombre sea obligatorio
            $request->validate([
                'batch' => 'required|string|max:255',
                'date_init' => 'required|date',
                'date_end' => 'required|date',
            ], [
                'batch.required' => 'El nombre del Lote es obligatorio.',
                'date_init.required' => 'El campo fecha de inicio del Lote es obligatorio.',
                'date_end.required' => 'El campo fecha de fin del Lote es obligatorio.',
                'batch.string' => 'El nombre del Lote debe ser un texto.',
                /* 'company_id.integer' => 'El ID de la Empresa debe ser un número.', */
                'batch.max' => 'El nombre del Lote no puede tener más de 255 caracteres.'
            ]);

            // Crear el módulo
            $batch = Batch::create($request->all());

            return response()->json(['data' => $batch, 'message' => 'Lote creado correctamente']);
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
                'error' => 'Error al crear el Lote',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('batches::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('batches::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        try {
            // Validar que el nombre sea obligatorio
            $request->validate([
                'batch' => 'required|string|max:255',
                'date_init' => 'required|date',
                'date_end' => 'required|date',
            ], [
                'batch.required' => 'El nombre del Lote es obligatorio.',
                'date_init.required' => 'El campo fecha de inicio del Lote es obligatorio.',
                'date_end.required' => 'El campo fecha de fin del Lote es obligatorio.',
                'batch.string' => 'El nombre del Lote debe ser un texto.',
                /* 'company_id.integer' => 'El ID de la Empresa debe ser un número.', */
                'batch.max' => 'El nombre del Lote no puede tener más de 255 caracteres.'
            ]);

            // Crear el módulo
            $batch = Batch::find($id);

            if (!$batch) {
                return response()->json(['error' => 'Lote no encontrado'], 404);
            }

            $batch->update($request->all());

            return response()->json(['data' => $batch, 'message' => 'Lote actualizado correctamente']);
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
                'error' => 'Error al actualizar el Lote',
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
            $batches = Batch::find($id);

            //  Si no existe, retornar error
            if (!$batches) {
                return response()->json(['error' => 'Lote no encontrado'], 404);
            }

            $batches->delete();

            return response()->json(['message' => 'Lote eliminado correctamente']);
        } catch (\Exception  $e) {
            // Capturar otros errores (como SQLSTATE) y devolver un mensaje en español
            return response()->json([
                'error' => 'Error al eliminar el Lote',
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
            ['title' => 'Lote', 'key'=> 'batch'],
            ['title' => 'Fecha Inicio', 'key'=> 'date_init', 'sortable' => false],
            ['title' => 'Fecha Fin Padre', 'key'=> 'date_end', 'sortable' => false],
            ['title' => 'Acciones', 'key'=> 'actions', 'sortable' => false],
        ];

        $itemSelects = [
            ['title' => '5', 'value'=> 5],
            ['title' => '10', 'value'=> 10],
            ['title' => '25', 'value'=> 25],
            ['title' => '50', 'value'=> 50],
            ['title' => '100', 'value'=> 100],
        ];

        $button_add = [

                'label' => 'Nuevo Lote',
                'color' => 'info',
                'icon' => 'tabler-plus',
                'density' => 'default',
                'action' => 'create'

        ];

        $data = [
            'headers' => $headers,
            'per_page' => 10,
            'page' => 1,
            'title' => 'Lotes',
            'item_selects' => $itemSelects,
            'button_add' => $button_add,
            'permissions' => $modulePermissions, // Solo los permisos del módulo solicitado
        ];
        return response()->json(['data'=>$data]);
    }
}
