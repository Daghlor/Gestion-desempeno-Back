<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeedbackActions;
use Illuminate\Support\Str;

class FeedbackActionsController extends Controller
{
    public function Create(Request $request)
    {
        $title = $request->input('title'); // Obtener el título del Request

        if (!$title) {
            return response()->json(array(
                'res' => false,
                'message' => 'Hace falta el título'
            ), 400);
        }

        $feeback = FeedbackActions::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $title, // Utilizar el título obtenido del Request
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(array(
            'res' => true,
            'data' => [
                'training_id' => $feeback->unique_id,
                'title' => $feeback->title,
                'msg' => 'Accion de Retroalimentación Creada Correctamente'
            ]
        ), 200);
    }


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

        // Agregar más campos para actualizar aquí si es necesario

        $feedback->save();

        return response()->json([
            'res' => true,
            'data' => [
                'feedback_id' => $feedback->unique_id,
                'title' => $feedback->title,
                'msg' => 'Acciones de retroalimentacion actualizada correctamente'
            ]
        ], 200);
    }

    public function FindAll(Request $request)
    {
        $feedbackActions = FeedbackActions::all();

        return response()->json([
            'res' => true,
            'data' => $feedbackActions
        ], 200);
    }

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
