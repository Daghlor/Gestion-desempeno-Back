<?php

// CONTROLADOR DE LAS ACCIONES DE FORMACION CON FUNCIONES TIPO CRUD
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingActions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\StatesObjectives;

class TrainingActionsController extends Controller
{

// FUNCION QUE CREA UNA ACCION DE FORMACION
public function create(Request $request)
{
    $title = $request->input('title');
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    $state_id = $request->input('state_id', 1); // Asignar 1 si no se proporciona state_id

    if (!$title || !$start_date || !$end_date) {
        return response()->json([
            'res' => false,
            'message' => 'Falta información requerida'
        ], 400);
    }

    $training = TrainingActions::create([
        'unique_id' => Str::uuid()->toString(),
        'title' => $title,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'state_id' => $state_id,
        'user_id' => auth()->user()->id,
    ]);

    return response()->json([
        'res' => true,
        'data' => [
            'training_id' => $training->unique_id,
            'title' => $training->title,
            'start_date' => $training->start_date,
            'end_date' => $training->end_date,
            'state_id' => $training->state_id,
            'msg' => 'Acciones de formacion Creado Correctamente'
        ]
    ], 200);
}


   // FUNCION QUE ACTUALIZA UNA ACCION DE FORMACION
public function update(Request $request, $uuid)
{
    $training = TrainingActions::where('unique_id', $uuid)->first();

    if (!$training) {
        return response()->json([
            'res' => false,
            'message' => 'Acciones de formación no encontrada'
        ], 404);
    }

    // Verificar si se proporciona el campo "title" en la solicitud y actualizarlo si es así
    if ($request->has('title')) {
        $training->title = $request->input('title');
    }

    // Verificar si se proporcionan los campos "start_date", "end_date" y "state_id" y actualizarlos si es así
    if ($request->has('start_date')) {
        $training->start_date = $request->input('start_date');
    }

    if ($request->has('end_date')) {
        $training->end_date = $request->input('end_date');
    }

    if ($request->has('state_id')) {
        $training->state_id = $request->input('state_id');
    }

    $training->save();

    return response()->json([
        'res' => true,
        'data' => [
            'training_id' => $training->unique_id,
            'title' => $training->title,
            'start_date' => $training->start_date,
            'end_date' => $training->end_date,
            'state_id' => $training->state_id,
            'msg' => 'Acciones de formación actualizada correctamente'
        ]
    ], 200);
}


    // FUNCION QUE BUSCA TODAS LAS ACCIONES CREADAS POR EL UNIQUE_ID DE UN USUARIO
    // public function FindAllByUserUniqueId(Request $request, $uuid)
    // {
    //     // Buscar todos los objetivos individuales del usuario por su unique_id
    //     $objetives = TrainingActions::where('user_id', function ($query) use ($uuid) {
    //         $query->select('id')
    //             ->from('users')
    //             ->where('unique_id', $uuid);
    //     })->get();

    //     return response()->json(array(
    //         'res' => true,
    //         'data' => $objetives
    //     ), 200);
    // }

    public function FindAllByUserUniqueId(Request $request, $uuid)
{
    // Buscar todos los objetivos individuales del usuario por su unique_id
    $objetives = TrainingActions::where('user_id', function ($query) use ($uuid) {
        $query->select('id')
            ->from('users')
            ->where('unique_id', $uuid);
    })->get();

    // Obtener las descripciones de la tabla states_objectives y agregarlas a los objetivos
    $objetivesWithDescriptions = $objetives->map(function ($objetive) {
        $stateDescription = StatesObjectives::where('id', $objetive->state_id)->pluck('description')->first();
        $objetive->stateDescription = $stateDescription;
        return $objetive;
    });

    return response()->json(array(
        'res' => true,
        'data' => $objetivesWithDescriptions
    ), 200);
}

   public function findAll(Request $request)
{
    $paginate = $request->input('paginate', 10);
    $page = $request->input('page', 1);
    $column = $request->input('column', 'title');
    $direction = $request->input('direction', 'desc');
    $search = $request->input('search', []);

    $query = TrainingActions::join('users', 'users.id', '=', 'training_actions.user_id')
        ->leftJoin('states_objectives', 'states_objectives.id', '=', 'training_actions.state_id');

    if (!empty($search)) {
        if (isset($search['title'])) {
            $query->where('training_actions.title', 'like', '%' . $search['title'] . '%');
        }
        if (isset($search['user_id'])) {
            $query->where('training_actions.user_id', $search['user_id']);
        }
    }

    $total = $query->count();

    $trainings = $query->select(
            'training_actions.id',
            'training_actions.unique_id',
            'training_actions.title',
            'training_actions.start_date',
            'training_actions.end_date',
            DB::raw("CONCAT(users.name, ' ', users.lastName) AS nameUser"),
            'states_objectives.description AS stateDescription'
        )
        ->orderBy($column, $direction)
        ->offset(($page - 1) * $paginate)
        ->limit($paginate)
        ->get();

    return response()->json([
        'res' => true,
        'data' => [
            'trainings' => $trainings,
            'total' => $total,
        ]
    ], 200);
}

    // FUNCION PARA ELIMINAR UNA ACCION DE FORMACION
    public function Delete($uuid)
    {

        $training = TrainingActions::where('unique_id', $uuid)->first();

        if (!$training) {
            return response()->json([
                'res' => false,
                'message' => 'Acciones de formación no encontrada'
            ], 404);
        }

        $training->delete();

        return response()->json([
            'res' => true,
            'message' => 'Acciones de formación eliminada correctamente'
        ], 200);
    }
}

// Copyright (c) Engagement
// https://www.engagement.com.co/
// Año: 2023
// Sistema: Gestion de desempeño (GDD)
// Programador: David Tuta
