<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ObjectivesStrategicsController;
use App\Http\Controllers\ObjectivesIndividualController;
use App\Models\ObjectivesStrategics;
use App\Models\ObjectivesIndividual;
use Illuminate\Support\Facades\DB;

class PercentageController extends Controller
{
    // public function calculatePercentage(Request $request)
    // {
    //     $strategicsController = new ObjectivesStrategicsController();
    //     $strategicsResponse = $strategicsController->FindAll($request);
    //     $totalStrategics = $strategicsResponse->getData()->data->total;

    //     $individualController = new ObjectivesIndividualController();
    //     $individualsResponse = $individualController->FindAll($request);
    //     $totalIndividuals = $individualsResponse->getData()->data->total;

    //     // Calcular el total de objetivos individuales alcanzados
    //     $totalTargetedResponse = $individualController->FindAllTargeted($request);
    //     $totalTargeted = $totalTargetedResponse->getData()->data->total_targeted ?? 0;

    //     $percentage = ($totalIndividuals > 0) ? ($totalTargeted / $totalIndividuals) * 100 : 0;

    //     return response()->json([
    //         'total_strategics' => $totalStrategics,
    //         'total_individuals' => $totalIndividuals,
    //         'targeted_individuals' => $totalTargeted,
    //         'percentage' => $percentage
    //     ], 200);
    // }

    // grafica Numero de objetivos personales alineados a objetivos estratégicos
    public function countIndividualsAlignedWithStrategics()
    {
        $counts = DB::table('objectives_individuals')
            ->select(
                'objectives_individuals.strategic_id',
                DB::raw('count(*) as count'),
                DB::raw('(SELECT unique_id FROM objectives_strategics WHERE id = objectives_individuals.strategic_id) as unique_id_strategics'),
                DB::raw('(SELECT title FROM objectives_strategics WHERE id = objectives_individuals.strategic_id) as title_strategics')
            )
            ->groupBy('objectives_individuals.strategic_id')
            ->get();

        return response()->json([
            'data' => $counts,
        ], 200);
    }

    public function getTotal()
    {
        // Obtiene el total de objetivos individuales
        $totalObjectives = DB::table('objectives_individuals')->count();

        // Obtiene el total de personas registradas (supongamos que están en una tabla llamada 'users')
        $totalUsers = DB::table('users')->count();

        return response()->json([
            'total_objectives' => $totalObjectives,
            'total_users' => $totalUsers,
        ]);
    }

    public function countClosedVsApprovedIndividuals()
    {
        // Consulta para obtener el recuento de objetivos individuales cambiados de estado a "cerrados" y "aprobados"
        $counts = DB::table('objectives_individuals')
            ->select(
                DB::raw('SUM(CASE WHEN state_id = 4 THEN 1 ELSE 0 END) as closed_count'), // Suponemos que el ID del estado "cerrado" es 2
                DB::raw('SUM(CASE WHEN state_id = 2 THEN 1 ELSE 0 END) as approved_count') // Suponemos que el ID del estado "aprobado" es 1
            )
            ->first(); // Obtenemos solo una fila de resultados

        return response()->json([
            'closed_count' => $counts->closed_count,
            'approved_count' => $counts->approved_count,
        ], 200);
    }

    public function countPendingVsApprovedVsUsers()
    {
        // Consulta para obtener el recuento de objetivos individuales con estado "pendiente de aprobación"
        $pendingCount = DB::table('objectives_individuals')
            ->where('state_id', 1) // Suponemos que el ID del estado "pendiente de aprobación" es 1
            ->count();

        // Consulta para obtener el recuento de objetivos individuales con estado "aprobado"
        $approvedCount = DB::table('objectives_individuals')
            ->where('state_id', 2) // Suponemos que el ID del estado "aprobado" es 2
            ->count();

        // Obtiene el total de usuarios registrados (supongamos que están en una tabla llamada 'users')
        $totalUsers = DB::table('users')->count();

        return response()->json([
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'total_users' => $totalUsers,
        ], 200);
    }
}
