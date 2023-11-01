<?php

// CONTROLADOR DE TODAS LAS GRAFICAS DEL SISTEMA CON SUS FUNCIONES LOGICAS
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ObjectivesStrategicsController;
use App\Http\Controllers\ObjectivesIndividualController;
use App\Models\ObjectivesStrategics;
use App\Models\ObjectivesIndividual;
use Illuminate\Support\Facades\DB;
use App\Models\Tracing;
use App\Models\User;
use App\Models\Company;

class PercentageController extends Controller
{

    // FUNCION PARA CONTAR CUANTOS INDIVIDUALES ESTAN ALINEADOS A CIERTO ESTRATEGICO
    public function countIndividualsAlignedWithStrategics(Request $request, $companyUniqueId)
    {
        // Busca la empresa por su unique_id
        $company = Company::where('unique_id', $companyUniqueId)->first();

        if (!$company) {
            return response()->json(['error' => 'Empresa no encontrada'], 404);
        }

        // Obtiene los objetivos estratégicos relacionados con la empresa y sus conteos de objetivos individuales
        $counts = DB::table('objectives_individuals')
            ->join('objectives_strategics', 'objectives_individuals.strategic_id', '=', 'objectives_strategics.id')
            ->select(
                'objectives_individuals.strategic_id',
                DB::raw('count(*) as count'),
                DB::raw('(SELECT unique_id FROM objectives_strategics WHERE id = objectives_individuals.strategic_id) as unique_id_strategics'),
                DB::raw('(SELECT title FROM objectives_strategics WHERE id = objectives_individuals.strategic_id) as title_strategics')
            )
            ->where('objectives_strategics.company_id', $company->id)
            ->groupBy('objectives_individuals.strategic_id')
            ->get();

        return response()->json([
            'company_name' => $company->businessName, // Agrega el nombre de la empresa
            'data' => $counts,
        ], 200);
    }

    // FUNCION DE GRAFICA PARA TENER EL TOTAL DE OBJETIVOS INDIVIDUALES Y EL TOTAL DE PERSONAS REGISTRADAS
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

    // FUNCION PARA CONTAR LOS PLANES CERRADOS Y LOS PLANES APROBADOS
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

    // FUNCION PARA CONTAR LOS PLANES PENDIENTE, LOS PLANES APROBADOS Y EL TOTAL DE PERSONAS REGISTRADAS
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

    // FUNCION PARA BUSCAR LOS OBJETIVOS INDIVIDUALES DE UN USUARIO Y VER EL PORCENTAJE DE AVANCE
    public function FindOne(Request $request, $uuid)
    {
        $user = User::where('unique_id', $uuid)->first(['id', 'name', 'lastName', 'identify', 'phone', 'email']);
        $objetives = ObjectivesIndividual::where('objectives_individuals.user_id', $user->id)
            ->join('objectives_strategics', 'objectives_strategics.id', '=', 'objectives_individuals.strategic_id')
            ->join('states_objectives', 'states_objectives.id', '=', 'objectives_individuals.state_id')
            ->get([
                'objectives_individuals.id', 'objectives_individuals.unique_id', 'objectives_individuals.title',
                'objectives_individuals.objetive', 'objectives_strategics.title as title_strategics',
                'objectives_individuals.weight', 'objectives_individuals.strategic_id', 'objectives_individuals.state_id',
                'objectives_individuals.created_at', 'states_objectives.description as state', 'objectives_individuals.plans_id',
            ]);

        for ($i = 0; $i < count($objetives); $i++) {
            $objetives[$i]['tracing'] = Tracing::where('individual_id', $objetives[$i]->id)->orderBy('created_at', 'desc')->get();

            $totalPointsAvailable = 100; // Inicializa los puntos disponibles para cada objetivo individual
            $totalPointsAssigned = 0; // Inicializa los puntos asignados para cada objetivo individual

            // Recorre los seguimientos y resta sus pesos de los puntos totales disponibles
            foreach ($objetives[$i]['tracing'] as $tracing) {
                $totalPointsAvailable -= $tracing->weight;
                $totalPointsAssigned += $tracing->weight;
            }

            // Agrega los totales de puntos disponibles y asignados para este objetivo individual
            $objetives[$i]['totalPointsAvailable'] = $totalPointsAvailable;
            $objetives[$i]['totalPointsAssigned'] = $totalPointsAssigned;
        }

        return response()->json([
            'res' => true,
            'data' => $objetives,
            'user' => $user
        ], 200);
    }

    // FUNCION QUE DA EL TOTAL DE PORCENTAJE DE AVANCE DE LOS OBJETIVOS ESTRATEGICOS DE LA EMPRESA
    public function calculateResultsForStrategicObjective(Request $request, $uuid)
    {
        // Verifica si el objetivo estratégico con el UUID proporcionado existe
        $strategicObjective = ObjectivesStrategics::where('unique_id', $uuid)->first();

        if (!$strategicObjective) {
            return response()->json([
                'error' => 'El objetivo estratégico no existe',
            ], 404);
        }

        // Obtén todos los objetivos individuales alineados a este objetivo estratégico
        $individualObjectives = ObjectivesIndividual::where('strategic_id', $strategicObjective->id)->get();

        $totalResults = 0;

        // Recopila el título del objetivo estratégico
        $strategicTitle = $strategicObjective->title;

        // Recorre todos los objetivos individuales y suma sus resultados
        foreach ($individualObjectives as $individualObjective) {
            // Suma los pesos (weights) de los registros de seguimiento para este objetivo individual
            $results = Tracing::where('individual_id', $individualObjective->id)->sum('weight');

            $totalResults += $results;
        }

        // Calcula el promedio dividiendo la suma de los resultados entre el número de objetivos individuales
        $averageResult = count($individualObjectives) > 0 ? $totalResults / count($individualObjectives) : 0;

        return response()->json([
            'strategic_title' => $strategicTitle, // Agrega el título del objetivo estratégico
            'total_results' => $totalResults,
            'average_result' => $averageResult,
        ], 200);
    }
}

// Copyright (c) Engagement
// https://www.engagement.com.co/
// Año: 2023
// Sistema: Gestion de desempeño (GDD)
// Programador: David Tuta
