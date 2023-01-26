<?php

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\Tracing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class TracingController extends Controller
{
    public function Create (Request $request){
        $tracing = Tracing::create([
            'unique_id' => Str::uuid()->toString(),
            'comment' => $request->all()['comment'],
            'user_id' => auth()->user()->id,
            'individual_id' => $request->all()['individual_id'],
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'roles' => $tracing->unique_id,
                'msg' => 'Seguimiento Creado Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];


        $tracings = Tracing::leftjoin('users', 'users.id', '=', 'tracings.user_id');
        if(count($search) > 0){
            if(isset($search['individual_id'])){
                $tracings = $tracings->where('tracings.individual_id', $search['individual_id']);
            }
        }
        $tracings = $tracings->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'tracings.unique_id',  'tracings.comment', DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"), 'tracings.created_at'
        ]);


        $counts = Tracing::leftjoin('users', 'users.id', '=', 'tracings.user_id');
        if(count($search) > 0){
            if(isset($search['individual_id'])){
                $counts = $counts->where('tracings.individual_id', $search['individual_id']);
            }
        }
        $counts = $counts->get(['tracings.unique_id']);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'areas' => $tracings,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $tracings = Tracing::where('unique_id', $uuid)->first();
      
        return response()->json(array(
            'res'=> true,
            'data' => $tracings
        ), 200);
    }

}
