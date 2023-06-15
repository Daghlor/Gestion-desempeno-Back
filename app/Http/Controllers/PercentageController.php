<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ObjectivesIndividualController;
use App\Http\Controllers\ObjectivesStrategicsController;

class PercentageController extends Controller
{
    public function calculatePercentage()
    {
        $objectivesIndividualController = new ObjectivesIndividualController();
        $objectivesStrategicsController = new ObjectivesStrategicsController();

        $totalIndividuals = $objectivesIndividualController->getTotalObjectivesIndividuals();
        $totalStrategics = $objectivesStrategicsController->getTotalObjectivesStrategics();

        $percentage = ($totalIndividuals / $totalStrategics) * 100;

        return response()->json(array(
            'percentage' => $percentage,
        ), 200);
    }
}
