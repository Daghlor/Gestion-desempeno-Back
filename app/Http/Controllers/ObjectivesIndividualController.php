<?php

// ESTE ES EL CONTROLADOR DE OBEJTIVOS INDIVIDUALES DONDE ESTAN LAS FUNCIONES DE TIPO CRUD

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\ObjectivesStrategics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ObjectivesIndividualController extends Controller
{
    // FUNCION PARA CREAR O REGISTRAR UN OBJETIVO INDIVIDUAL
    public function Create(Request $request)
    {
        $individual = ObjectivesIndividual::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $request->all()['title'],
            'objetive' => $request->all()['objetive'],
            'weight' => $request->all()['weight'],
            'user_id' => auth()->user()->id,
            'state_id' => 1,
            'strategic_id' => $request->all()['strategic_id'],
            'plans_id' => $request->all()['plans_id'],
        ]);

        return response()->json(array(
            'res' => true,
            'data' => [
                'objetive' => $individual->unique_id,
                'msg' => 'Objetivo Individual Creado Correctamente'
            ]
        ), 200);
    }

    // FUNCION PARA TRAER O BUSCAR TODOS LOS OBJETIVOS INDIVIDUALES QUE SE HAYAN REGISTRADO
    public function FindAll(Request $request)
    {
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $objetives = ObjectivesIndividual::join('users', 'users.id', '=', 'objectives_individuals.user_id')
            ->join('objectives_strategics', 'objectives_strategics.id', '=', 'objectives_individuals.strategic_id')
            ->join('states_objectives', 'states_objectives.id', '=', 'objectives_individuals.state_id');

        if (count($search) > 0) {
            if (isset($search['objetive'])) {
                $objetives = $objetives->where('objectives_individuals.objetive', 'like', '%' . $search['objetive'] . '%');
            }
            if (isset($search['user_id'])) {
                $objetives = $objetives->where('objectives_individuals.user_id', $search['user_id']);
            }
            if (isset($search['areas_id'])) {
                $objetives = $objetives->where('objectives_individuals.areas_id', $search['areas_id']);
            }
            if (isset($search['strategic_id'])) {
                $objetives = $objetives->where('objectives_individuals.strategic_id', $search['strategic_id']);
            }
        }
        $objetives = $objetives->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->orderBy($column, $direction)
            ->get([
                'objectives_individuals.unique_id',  'objectives_individuals.objetive', 'objectives_individuals.weight',
                'objectives_individuals.title', 'objectives_strategics.title as title_strategics', 'users.identify',
                DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"),  'states_objectives.description as state'
            ]);


        $counts = ObjectivesIndividual::join('states', 'states.id', '=', 'objectives_individuals.state_id');
        if (count($search) > 0) {
            if (isset($search['objetive'])) {
                $counts = $counts->where('objectives_individuals.objetive', 'like', '%' . $search['objetive'] . '%');
            }
            if (isset($search['user_id'])) {
                $counts = $counts->where('objectives_individuals.user_id', $search['user_id']);
            }
            if (isset($search['areas_id'])) {
                $counts = $counts->where('objectives_individuals.areas_id', $search['areas_id']);
            }
            if (isset($search['strategic_id'])) {
                $counts = $counts->where('objectives_individuals.strategic_id', $search['strategic_id']);
            }
        }
        $counts = $counts->get(['objectives_individuals.unique_id']);

        $total = count($counts);

        return response()->json(
            [
                'res' => true,
                'data' => [
                    'objetives' => $objetives,
                    'total' => $total,
                ]
            ],
            200
        );
    }


    // FUNCION PARA BUSCAR O ENCONTRAR UN OBJETIVO INDIVIDUAL POR SU UNIQUE_ID
    public function FindOne(Request $request, $uuid)
    {
        $objetives = ObjectivesIndividual::where('objectives_individuals.unique_id', $uuid)->first();
        $objetives->user = User::where('id', $objetives->user_id)->first(['unique_id', 'name', 'lastName', 'identify', 'phone', 'email']);
        $objetives->objectivesStrategics = ObjectivesStrategics::where('id', $objetives->strategic_id)->first();

        return response()->json(array(
            'res' => true,
            'data' => $objetives
        ), 200);
    }

    // FUNCION PARA BORRAR UN OBJETIVO INDIVIDUAL
    public function Delete(Request $request, $uuid)
{
    ObjectivesIndividual::where('unique_id', $uuid)->delete();

    return response()->json(array(
        'res' => true,
        'data' => 'Objetivo Individual Eliminado Correctamente'
    ), 200);
}



    public function FindTargeted(Request $request, $strategicUniqueId)
    {
        // Obtener el objetivo estratégico correspondiente al unique_id
        $strategicObjective = ObjectivesStrategics::where('unique_id', $strategicUniqueId)->first();

        // Verificar si se encontró el objetivo estratégico
        if (!$strategicObjective) {
            return response()->json([
                'res' => false,
                'message' => 'No se encontró el objetivo estratégico correspondiente al unique_id.',
            ], 404);
        }

        // Obtener los objetivos individuales relacionados con el objetivo estratégico
        $targetedObjectives = ObjectivesIndividual::where('strategic_id', $strategicObjective->id)->get();
        $totalTargeted = $targetedObjectives->count();

        return response()->json([
            'res' => true,
            'data' => [
                'targeted_objectives' => $targetedObjectives,
                'total_targeted' => $totalTargeted,
            ]
        ], 200);
    }
}
