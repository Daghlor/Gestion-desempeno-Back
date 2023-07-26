<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingActions;
use Illuminate\Support\Str;

class TrainingActionsController extends Controller
{
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

    public function FindAll(Request $request)
    {

        $trainingActions = TrainingActions::all();

        return response()->json([
            'res' => true,
            'data' => $trainingActions
        ], 200);
    }

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
