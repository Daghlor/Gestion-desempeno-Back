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

        $feeback = FeedbackActions::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $request->all()['title'],
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(array(
            'res' => true,
            'data' => [
                'training_id' => $feeback->unique_id,
                'title' => $feeback->title,
                'msg' => 'Acciones de retroalimentacion Creado Correctamente'
            ]
        ), 200);
    }
}
