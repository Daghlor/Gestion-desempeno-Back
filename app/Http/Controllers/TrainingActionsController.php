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

        $training = TrainingActions::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $request->all()['title'],
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
}
