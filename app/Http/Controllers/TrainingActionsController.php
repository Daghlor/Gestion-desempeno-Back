<?php

// CONTROLADOR DE LAS ACCIONES DE FORMACION CON FUNCIONES TIPO CRUD
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingActions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TrainingActionsController extends Controller
{
    // FUNCION QUE CREA UNA ACCION DE FORMACION
    public function Create(Request $request)
    {
        $title = $request->input('title');

        if (!$title) {
            return response()->json(array(
                'res' => false,
                'message' => 'Hace falta el título'
            ), 400);
        }

        $training = TrainingActions::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $title,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(array(
            'res' => true,
            'data' => [
                'training_id' => $training->unique_id,
                'title' => $training->title,
                'msg' => 'Acciones de formacion Creado Correctamente'
            ]
        ), 200);
    }

    // FUNCION QUE ACTUALIZA UNA ACCION DE FORMACION
    public function Update(Request $request, $uuid)
    {

        $training = TrainingActions::where('unique_id', $uuid)->first();

        if (!$training) {
            return response()->json([
                'res' => false,
                'message' => 'Acciones de formación no encontrada'
            ], 404);
        }

        if ($request->has('title')) {
            $training->title = $request->input('title');
        }

        $training->save();

        return response()->json([
            'res' => true,
            'data' => [
                'training_id' => $training->unique_id,
                'title' => $training->title,
                'msg' => 'Acciones de formación actualizada correctamente'
            ]
        ], 200);
    }

    // FUNCION QUE BUSCA TODAS LAS ACCIONES CREADAS POR EL UNIQUE_ID DE UN USUARIO
    public function FindAllByUserUniqueId(Request $request, $uuid)
    {
        // Buscar todos los objetivos individuales del usuario por su unique_id
        $objetives = TrainingActions::where('user_id', function ($query) use ($uuid) {
            $query->select('id')
                ->from('users')
                ->where('unique_id', $uuid);
        })->get();

        return response()->json(array(
            'res' => true,
            'data' => $objetives
        ), 200);
    }

    // FUNCION PARA BUSCAR TODAS LAS ACCIONES DE FORMACION
    public function FindAll(Request $request)
    {
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $training = TrainingActions::join('users', 'users.id', '=', 'training_actions.user_id');
        if (count($search) > 0) {
            if (isset($search['title'])) {
                $training = $training->where('training_actions.title', 'like', '%' . $search['title'] . '%');
            }
            if (isset($search['user_id'])) {
                $training = $training->where('training_actions.user_id', $search['user_id']);
            }
        }

        $training = $training->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->orderBy($column, $direction)
            ->get([
                'training_actions.id', 'training_actions.unique_id', 'training_actions.title', DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"),
            ]);

        $total = TrainingActions::count();

        return response()->json([
            'res' => true,
            'data' => [
                'titles' => $training,
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
