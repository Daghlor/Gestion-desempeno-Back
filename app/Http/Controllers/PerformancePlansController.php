<?php

namespace App\Http\Controllers;

use App\Models\PerformancePlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerformancePlansController extends Controller
{
    public function Create (Request $request){
        $objetive = PerformancePlans::create([
            'unique_id' => Str::uuid()->toString(),
            'title' => $request->all()['title'],
            'mission' => $request->all()['mission'],
            'vision' => $request->all()['vision'],
            'totalWeight' => 0,
            'company_id' => $request->all()['company_id'],
            'user_id' => auth()->user()->id,
            'areas_id' => $request->all()['areas_id'],
            'state_id' => 1
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'objetive' => $objetive->unique_id,
                'msg' => 'Objetivo Estratégico Creado Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $objetives = PerformancePlans::join('companies', 'companies.id', '=', 'objectives_strategics.company_id')
        ->join('areas', 'areas.id', '=', 'objectives_strategics.areas_id')
        ->join('users', 'users.id', '=', 'objectives_strategics.user_id')
        ->join('states', 'states.id', '=', 'objectives_strategics.state_id');

        if(count($search) > 0){
            if(isset($search['user_id'])){
                $objetives = $objetives->where('objectives_strategics.user_id', $search['user_id']);
            }
            if(isset($search['areas_id'])){
                $objetives = $objetives->where('objectives_strategics.areas_id', $search['areas_id']);
            }
            if(isset($search['company_id'])){
                $objetives = $objetives->where('objectives_strategics.company_id', $search['company_id']);
            }
            if(isset($search['state_id'])){
                $objetives = $objetives->where('objectives_strategics.state_id', $search['state_id']);
            }
        }
        $objetives = $objetives->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'objectives_strategics.unique_id',  'objectives_strategics.title', 'objectives_strategics.mission', 'objectives_strategics.vision',
            'objectives_strategics.totalWeight', 'companies.businessName as company', DB::raw("CONCAT(users.name,' ', users.lastName) AS nameUser"),
            'users.identify', 'areas.description as area', 'states.description as state'
        ]);

        $counts = PerformancePlans::join('states', 'states.id', '=', 'objectives_strategics.state_id');
        if(count($search) > 0){
            if(isset($search['user_id'])){
                $counts = $counts->where('objectives_strategics.user_id', $search['user_id']);
            }
            if(isset($search['areas_id'])){
                $counts = $counts->where('objectives_strategics.areas_id', $search['areas_id']);
            }
            if(isset($search['company_id'])){
                $counts = $counts->where('objectives_strategics.company_id', $search['company_id']);
            }
        }
        $counts = $counts->get(['objectives_strategics.unique_id']);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'objetives' => $objetives,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $objetives = PerformancePlans::where('objectives_strategics.unique_id', $uuid)->join('companies', 'companies.id', '=', 'objectives_strategics.company_id')->first();
        // $objetives->company = Company::where('id', $objetives->company_id)->first();
        // $objetives->user = User::where('id', $objetives->user_id)->first(['unique_id', 'name', 'lastName', 'identify', 'phone', 'email']);
        // $objetives->area = Area::where('id', $objetives->areas_id)->first();
        // $objetives->objectivesIndividual = ObjectivesIndividual::where('strategic_id', $objetives->id)->get();
    
        return response()->json(array(
            'res'=> true,
            'data' => $objetives
        ), 200);
    }


    public function Delete (Request $request, $uuid){
        PerformancePlans::where('unique_id', $uuid)
        ->update([
            'state_id' => 2,
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Objetivo Estratégico Eliminado Correctamente'
        ), 200);
    }
}
