<?php

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\ObjectivesStrategics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ObjectivesIndividualController extends Controller
{
    public function Create (Request $request){
        $individual = ObjectivesIndividual::create([
            'unique_id' => Str::uuid()->toString(),
            'objetive' => $request->all()['objetive'],
            'weight' => $request->all()['weight'],
            'user_id' => auth()->user()->id,
            'state_id' => 1,
            'strategic_id' => $request->all()['strategic_id'],
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'objetive' => $individual->unique_id,
                'msg' => 'Objetivo Individual Creado Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $objetives = ObjectivesIndividual::join('users', 'users.id', '=', 'objectives_individuals.user_id')
        ->join('states_objectives', 'states_objectives.id', '=', 'objectives_individuals.state_id');

        if(count($search) > 0){
            if(isset($search['objetive'])){
                $objetives = $objetives->where('objectives_individuals.objetive', 'like', '%'.$search['objetive'].'%');
            }
            if(isset($search['user_id'])){
                $objetives = $objetives->where('objectives_individuals.user_id', $search['user_id']);
            }
            if(isset($search['areas_id'])){
                $objetives = $objetives->where('objectives_individuals.areas_id', $search['areas_id']);
            }
            if(isset($search['strategic_id'])){
                $objetives = $objetives->where('objectives_individuals.strategic_id', $search['strategic_id']);
            }
        }
        $objetives = $objetives->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'objectives_individuals.unique_id',  'objectives_individuals.objetive', 'objectives_individuals.weight',
            DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"), 'users.identify', 'states_objectives.description as state'
        ]);


        $counts = ObjectivesIndividual::join('states', 'states.id', '=', 'objectives_individuals.state_id');
        if(count($search) > 0){
            if(isset($search['objetive'])){
                $counts = $counts->where('objectives_individuals.objetive', 'like', '%'.$search['objetive'].'%');
            }
            if(isset($search['user_id'])){
                $counts = $counts->where('objectives_individuals.user_id', $search['user_id']);
            }
            if(isset($search['areas_id'])){
                $counts = $counts->where('objectives_individuals.areas_id', $search['areas_id']);
            }
            if(isset($search['strategic_id'])){
                $counts = $counts->where('objectives_individuals.strategic_id', $search['strategic_id']);
            }
        }
        $counts = $counts->get(['objectives_individuals.unique_id']);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'objetives' => $objetives,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $objetives = ObjectivesIndividual::where('objectives_individuals.unique_id', $uuid)->first();
        $objetives->user = User::where('id', $objetives->user_id)->first(['unique_id', 'name', 'lastName', 'identify', 'phone', 'email']);
        $objetives->objectivesStrategics = ObjectivesStrategics::where('id', $objetives->strategic_id)->first();
    
        return response()->json(array(
            'res'=> true,
            'data' => $objetives
        ), 200);
    }

}
