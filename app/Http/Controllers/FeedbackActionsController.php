<?php

namespace App\Http\Controllers;

// CONTROLADOR DE ACCIONES DE RETROALIMENTACION CON FUNCIONES TIPO
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeedbackActions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FeedbackActionsController extends Controller
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

    $feeback = FeedbackActions::create([
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
            'training_id' => $feeback->unique_id,
            'title' => $feeback->title,
            'start_date' => $feeback->start_date,
            'end_date' => $feeback->end_date,
            'state_id' => $feeback->state_id,
            'msg' => 'Acciones de formacion Creado Correctamente'
        ]
    ], 200);
}


    // FUNCION PARA ACTUALIZAR UNA ACCION DE RETROALIMENTACION
    public function Update(Request $request, $uuid)
    {
        $feedback = FeedbackActions::where('unique_id', $uuid)->first();

        if (!$feedback) {
            return response()->json([
                'res' => false,
                'message' => 'Acciones de retroalimentacion no encontrada'
            ], 404);
        }

        // Actualizar los campos en base a los datos enviados en el Request
        if ($request->has('title')) {
            $feedback->title = $request->input('title');
        }
        if ($request->has('start_date')) {
        $feedback->start_date = $request->input('start_date');
    }

    if ($request->has('end_date')) {
        $feedback->end_date = $request->input('end_date');
    }

    if ($request->has('state_id')) {
        $feedback->state_id = $request->input('state_id');
    }

        // Agregar más campos para actualizar aquí si es necesario

        $feedback->save();

        return response()->json([
            'res' => true,
            'data' => [
                'feedback_id' => $feedback->unique_id,
                'title' => $feedback->title,
                'start_date' => $feedback->start_date,
                'end_date' => $feedback->end_date,
                'state_id' => $feedback->state_id,
                'msg' => 'Acciones de formación actualizada correctamente'
            ]
        ], 200);
    }

    // FUNCION PARA BUSCAR TODAS LAS ACCIONES CREADAS POR EL UNIQUE_ID DEL USUARIO
    public function FindAllByUserUniqueId(Request $request, $uuid)
    {
        // Buscar todos los objetivos individuales del usuario por su unique_id
        $objetives = FeedbackActions::where('user_id', function ($query) use ($uuid) {
            $query->select('id')
                ->from('users')
                ->where('unique_id', $uuid);
        })->get();

        return response()->json(array(
            'res' => true,
            'data' => $objetives
        ), 200);
    }


     public function findAll(Request $request)
{
    $paginate = $request->input('paginate', 10);
    $page = $request->input('page', 1);
    $column = $request->input('column', 'title');
    $direction = $request->input('direction', 'desc');
    $search = $request->input('search', []);

    $query = FeedbackActions::join('users', 'users.id', '=', 'feeback_actions.user_id')
        ->leftJoin('states_objectives', 'states_objectives.id', '=', 'feeback_actions.state_id');

    if (!empty($search)) {
        if (isset($search['title'])) {
            $query->where('feeback_actions.title', 'like', '%' . $search['title'] . '%');
        }
        if (isset($search['user_id'])) {
            $query->where('feeback_actions.user_id', $search['user_id']);
        }
    }

    $total = $query->count();

    $feeback = $query->select(
            'feeback_actions.id',
            'feeback_actions.unique_id',
            'feeback_actions.title',
            'feeback_actions.start_date',
            'feeback_actions.end_date',
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
            'feeback' => $feeback,
            'total' => $total,
        ]
    ], 200);
}

    // FUNCION PARA ELIMINAR UNA ACCION DE RETROALIMENTACION
    public function Delete($uuid)
    {
        $feedback = FeedbackActions::where('unique_id', $uuid)->first();

        if (!$feedback) {
            return response()->json([
                'res' => false,
                'message' => 'Acciones de retroalimentacion no encontrada'
            ], 404);
        }

        $feedback->delete();

        return response()->json([
            'res' => true,
            'message' => 'Acciones de retroalimentacion eliminada correctamente'
        ], 200);
    }
}

// Copyright (c) Engagement
// https://www.engagement.com.co/
// Año: 2023
// Sistema: Gestion de desempeño (GDD)
// Programador: David Tuta
