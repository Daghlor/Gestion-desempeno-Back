<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StatesObjectives;

class stateIndividualObjectives extends Controller
{
    public function index(Request $request)
    {
        try {
            // Consulta todos los estados de objetivos individuales
            $states = StatesObjectives::all();

            return response()->json([
                'success' => true,
                'data' => $states,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar los estados.',
            ], 500);
        }
    }
}
