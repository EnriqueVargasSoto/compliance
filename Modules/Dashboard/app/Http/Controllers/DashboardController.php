<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AthenaService;
use Illuminate\Http\Request;
use Aws\Athena\AthenaClient;
use Exception;

class DashboardController extends Controller
{
    protected $athenaService;

    public function __construct(AthenaService $athenaService)
    {
        $this->athenaService = $athenaService;
    }


    private function runAthenaQuery($query)
    {
        $this->athenaClient = new AthenaClient([
                     'version' => 'latest',
                     'region'  => env('AWS_DEFAULT_REGION'),
                     'credentials' => [
                         'key'    => env('AWS_ACCESS_KEY_ID'),
                         'secret' => env('AWS_SECRET_ACCESS_KEY'),
                     ],
                 ]);
        // Start query execution
        $result = $this->athenaClient->startQueryExecution([
            'QueryString' => $query,
            'QueryExecutionContext' => [
                'Database' => env('AWS_ATHENA_DATABASE'), // Replace with your database
            ],
            'ResultConfiguration' => [
                'OutputLocation' => env('AWS_ATHENA_OUTPUT'), // Replace with your S3 output path
            ],
        ]);

        $queryExecutionId = $result['QueryExecutionId'];

        // Wait for query to finish
        $this->waitForQueryToFinish($queryExecutionId);

        // Get query results
        $queryResult = $this->athenaClient->getQueryResults([
            'QueryExecutionId' => $queryExecutionId,
        ]);

        // Parse the results
        return $this->parseQueryResults($queryResult);
    }

    private function waitForQueryToFinish($queryExecutionId)
    {
        while (true) {
            $execution = $this->athenaClient->getQueryExecution([
                'QueryExecutionId' => $queryExecutionId,
            ]);

            $status = $execution['QueryExecution']['Status']['State'];
            $reason = $execution['QueryExecution']['Status']['StateChangeReason'] ?? 'No reason provided';

            if ($status == 'SUCCEEDED') {
                break;
            }

            if ($status == 'FAILED' || $status == 'CANCELLED') {
                throw new \Exception("Query failed to run with status: $status. Reason: $reason");
            }

            sleep(5);
        }
    }

    private function parseQueryResults($queryResult)
    {
        $rows = $queryResult['ResultSet']['Rows'];
        $result = [];

        // Skip the first row (headers)
        for ($i = 1; $i < count($rows); $i++) {
            $data = array_map(function ($item) {
                return $item['VarCharValue'] ?? null;
            }, $rows[$i]['Data']);

            $result[] = $data;
        }

        return $result;
    }

    // General endpoint to execute a query
    public function quantityDocumentsProcessed()
    {

        try {

            $total = "SELECT COUNT(*) AS total_documents FROM documents";
            $approved = "SELECT COUNT(*) FROM documents WHERE STATUS='SUCCESS'";
            $observed = "SELECT COUNT(*) FROM documents WHERE STATUS='FAILED'";

            if (!$total) {
                return response()->json(['error' => 'Query is required'], 400);
            }


            $result = [
                ['icon' => 'tabler-file-function', 'color' => 'primary', 'title' =>'Documentos Procesados', 'value' => $this->runAthenaQuery($total)[0][0], 'isHover' => false,],
                ['icon' => 'tabler-file-like', 'color' => 'success', 'title' =>'Documentos Conformes', 'value' => $this->runAthenaQuery($approved)[0][0], 'isHover' => false,],
                ['icon' => 'tabler-file-info', 'color' => 'error', 'title' =>'Documentos Observados', 'value' => $this->runAthenaQuery($observed)[0][0], 'isHover' => false,],
            ];
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function latestDocuments(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $limit = (int) $request->input('limit', 5);
        //$client_ruc = $request->input('client_ruc', null);  // Nuevo filtro opcional
        // Se obtiene el filtro, puede ser cualquiera de las columnas: client_ruc, document_number, document_location, client_name
        $filter_column = $request->input('filter_column', null);  // La columna por la cual filtrar
        $filter_value = $request->input('filter_value', null);    // El valor para filtrar
        $status = $request->input('status', 'SUCCESS');    // El valor para filtrar

        try {
            $paginationResults = $this->athenaService->fetchPaginatedData($page, $limit, $filter_column, $filter_value, $status);

            // Procesar los datos para que estén en un formato útil
            $data = [];
            foreach ($paginationResults['data'] as $index => $row) {
                if ($index === 0) {
                    continue; // Omitir la primera fila de encabezado
                }

                $data[] = array_combine(
                    array_map(fn ($col) => $col['VarCharValue'] ?? '', $paginationResults['data'][0]['Data']),
                    array_map(fn ($col) => $col['VarCharValue'] ?? '', $row['Data'])
                );
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'pagination' => $paginationResults['pagination'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function topConceptsClients(){


        try {
            $concepts = "SELECT item_code, item_description, COUNT(item_code) AS item_count FROM documents WHERE status='SUCCESS' GROUP BY item_code, item_description ORDER BY item_count DESC LIMIT 5;";
            $clients = "SELECT client_ruc, client_name, COUNT(*) AS numero_facturas FROM documents WHERE status='SUCCESS' GROUP BY client_ruc, client_name ORDER BY numero_facturas DESC LIMIT 5;";

            if (!$concepts || !$clients) {
                return response()->json(['error' => 'Query is required'], 400);
            }

            $result = [
                'concepts' => $this->runAthenaQuery($concepts),
                'clients' => $this->runAthenaQuery($clients),
            ];
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
