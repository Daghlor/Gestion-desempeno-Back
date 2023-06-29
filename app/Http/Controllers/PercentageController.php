<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\ObjectivesStrategicsController;
use App\Http\Controllers\ObjectivesIndividualController;

class PercentageController extends Controller
{
    public function calculatePercentage(Request $request, $strategicId)
    {
        $strategicsController = new ObjectivesStrategicsController();
        $strategicsResponse = $strategicsController->FindAll($request);
        $totalStrategics = $strategicsResponse->getData()->data->total;

        $individualController = new ObjectivesIndividualController();
        $individualsResponse = $individualController->FindAll($request);
        $totalIndividuals = $individualsResponse->getData()->data->total;

        $totalTargetedResponse = $individualController->FindTargeted($request, $strategicId);
        $totalTargeted = $totalTargetedResponse->getData()->data->total_targeted ?? 0;


        $percentage = ($totalIndividuals > 0) ? ($totalTargeted / $totalIndividuals) * 100 : 0;

        return response()->json([
            'total_strategics' => $totalStrategics,
            'total_individuals' => $totalIndividuals,
            'targeted_individuals' => $totalTargeted,
            'percentage' => $percentage
        ], 200);
    }
}
