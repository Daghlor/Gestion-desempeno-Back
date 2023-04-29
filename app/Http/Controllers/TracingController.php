<?php

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\Tracing;
use App\Models\User;
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
                'tracing' => $tracing->unique_id,
                'msg' => 'Seguimiento Creado Correctamente'
            ]
        ), 200);
    }

    public function FindUserTracing (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $objetives = ObjectivesIndividual::get(['user_id', 'id']);
        $arrayDataValidate = [];
        $arrayData = [];

        for ($i=0; $i < count($objetives); $i++) { 
            if(!in_array($objetives[$i]->user_id, $arrayDataValidate)){
                array_push($arrayDataValidate, $objetives[$i]->user_id);
            }
        }

        $users = User::whereIn('id', $arrayDataValidate)
        ->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            DB::raw("CONCAT(users.name,' ', users.lastName) AS name"), 'users.identify', 'users.email', 'users.company_id', 'users.unique_id'
        ]);

        $counts = User::whereIn('id', $arrayDataValidate)
        ->get(['users.identify']);
        
        return response()->json(array(
            'res'=> true,
            'data' => [
                'users' => $users,
                'total' => count($counts)
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
                'tracings' => $tracings,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $user = User::where('unique_id', $uuid)->first(['id', 'name', 'lastName', 'identify', 'phone', 'email']);
        $objetives = ObjectivesIndividual::where('objectives_individuals.user_id', $user->id)
        ->join('objectives_strategics', 'objectives_strategics.id', '=', 'objectives_individuals.strategic_id')
        ->join('states_objectives', 'states_objectives.id', '=', 'objectives_individuals.state_id')
        ->get([
            'objectives_individuals.id', 'objectives_individuals.unique_id', 'objectives_individuals.title', 
            'objectives_individuals.objetive', 'objectives_strategics.title as title_strategics',
            'objectives_individuals.weight', 'objectives_individuals.strategic_id', 'objectives_individuals.state_id', 
            'objectives_individuals.created_at', 'states_objectives.description as state'
        ]);

        for ($i=0; $i < count($objetives); $i++) { 
            $objetives[$i]['tracing'] = Tracing::where('individual_id', $objetives[$i]->id)->orderBy('created_at', 'desc')->get();
        }
      
        return response()->json(array(
            'res'=> true,
            'data' => $objetives,
            'user' => $user
        ), 200);
    }

}
