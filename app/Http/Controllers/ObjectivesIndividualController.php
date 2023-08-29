<?php

// ESTE ES EL CONTROLADOR DE OBEJTIVOS INDIVIDUALES DONDE ESTAN LAS FUNCIONES DE TIPO CRUD

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\ObjectivesStrategics;
use App\Models\StatesObjectives;
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

    public function UpdateState(Request $request, $uuid)
    {
        // Obtén el estado deseado desde la solicitud
        $newStateId = $request->input('new_state_id');

        // Busca el objetivo individual por su UUID
        $objective = ObjectivesIndividual::where('unique_id', $uuid)->first();

        // Verifica si se encontró el objetivo
        if (!$objective) {
            return response()->json([
                'res' => false,
                'message' => 'Objetivo individual no encontrado.',
            ], 404);
        }

        // Actualiza el estado del objetivo individual
        $objective->state_id = $newStateId;
        $objective->save();

        return response()->json([
            'res' => true,
            'message' => 'Estado del objetivo individual actualizado correctamente.',
        ], 200);
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
            if (isset($search['nameUser'])) {
                $objetives = $objetives->where('users.name', 'like', '%' . $search['nameUser'] . '%');
            }
        }
        $objetives = $objetives->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->orderBy($column, $direction)
            ->get([
                'objectives_individuals.unique_id',  'objectives_individuals.objetive', 'objectives_individuals.weight',
                'objectives_individuals.title', 'objectives_strategics.title as title_strategics', 'users.identify',
                DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"),  'states_objectives.description as state',
                'states_objectives.id as state_id'
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

    public function FindAllByUserUniqueId(Request $request, $uuid)
    {
        // Buscar todos los objetivos individuales del usuario por su unique_id
        $objetives = ObjectivesIndividual::where('user_id', function ($query) use ($uuid) {
            $query->select('id')
                ->from('users')
                ->where('unique_id', $uuid);
        })->get();

        // Obtener los títulos de los objetivos estratégicos asociados a cada objetivo individual
        foreach ($objetives as $objetivo) {
            $strategic = ObjectivesStrategics::find($objetivo->strategic_id);
            if ($strategic) {
                $objetivo->title_strategics = $strategic->title;
            } else {
                $objetivo->title_strategics = null;
            }
        }

        foreach ($objetives as $objetivo) {
            $state = StatesObjectives::find($objetivo->state_id);
            if ($state) {
                $objetivo->title_state = $state->description;
            } else {
                $objetivo->title_state = null;
            }
        }

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


    public function FindAllTargeted(Request $request)
    {
        // Obtener una lista de los objetivos estratégicos que deseas incluir en la gráfica
        $strategicUniqueIds = $request->input('strategic_unique_ids');

        // Verificar si se proporcionaron identificadores estratégicos
        if (empty($strategicUniqueIds)) {
            return response()->json([
                'res' => false,
                'message' => 'No se proporcionaron identificadores estratégicos.',
            ], 400);
        }

        // Inicializar arrays para almacenar los datos de cada objetivo estratégico
        $chartData = [];
        $chartLabels = [];

        // Iterar sobre los identificadores estratégicos
        foreach ($strategicUniqueIds as $uniqueId) {
            // Obtener el objetivo estratégico correspondiente al unique_id
            $strategicObjective = ObjectivesStrategics::where('unique_id', $uniqueId)->first();

            // Verificar si se encontró el objetivo estratégico
            if (!$strategicObjective) {
                // Puedes manejar esto de acuerdo a tus requerimientos, por ejemplo, saltar este objetivo o reportar un error.
                continue;
            }

            // Obtener los datos necesarios para el gráfico de este objetivo estratégico
            $targetedObjectives = ObjectivesIndividual::where('strategic_id', $strategicObjective->id)->get();
            $totalTargeted = $targetedObjectives->count();

            // Agregar los datos al array de gráfico
            $chartData[] = $totalTargeted;

            // Agregar etiquetas (puedes usar el título del objetivo estratégico)
            $chartLabels[] = $strategicObjective->title;
        }

        return response()->json([
            'res' => true,
            'data' => [
                'chartData' => $chartData,
                'chartLabels' => $chartLabels,
            ],
        ], 200);
    }
}
