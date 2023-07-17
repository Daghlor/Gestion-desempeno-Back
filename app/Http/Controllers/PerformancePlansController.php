<?php

namespace App\Http\Controllers;

use App\Models\PerformancePlans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerformancePlansController extends Controller
{
    public function Create (Request $request){
        $plan = PerformancePlans::create([
            'unique_id' => Str::uuid()->toString(),
            'name' => $request->all()['name'],
            'term' => $request->all()['term'],
            'dateInit' => $request->all()['dateInit'],
            'dateEnd' => $request->all()['dateEnd'],
            'company_id' => $request->all()['company_id'],
            'state' => 1
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'plan' => $plan->unique_id,
                'msg' => 'Plan Creado Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $plan = PerformancePlans::join('companies', 'companies.id', '=', 'performance_plans.company_id');
        if(count($search) > 0){
            if(isset($search['company_id'])){
                $plan = $plan->where('performance_plans.company_id', $search['company_id']);
            }
        }
        $plan = $plan->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'performance_plans.unique_id',  'performance_plans.name', 'performance_plans.term', 'performance_plans.dateInit',
            'performance_plans.dateEnd', 'performance_plans.state', 'performance_plans.company_id', 'companies.businessName as company'
        ]);


        $counts = PerformancePlans::join('companies', 'companies.id', '=', 'performance_plans.company_id');
        if(count($search) > 0){
            if(isset($search['company_id'])){
                $plan = $plan->where('performance_plans.company_id', $search['company_id']);
            }
        }
        $counts = $counts->get(['performance_plans.unique_id']);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'plans' => $plan,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        // $objetives = PerformancePlans::where('objectives_strategics.unique_id', $uuid)->join('companies', 'companies.id', '=', 'objectives_strategics.company_id')->first();
        // // $objetives->company = Company::where('id', $objetives->company_id)->first();
        // // $objetives->user = User::where('id', $objetives->user_id)->first(['unique_id', 'name', 'lastName', 'identify', 'phone', 'email']);
        // // $objetives->area = Area::where('id', $objetives->areas_id)->first();
        // // $objetives->objectivesIndividual = ObjectivesIndividual::where('strategic_id', $objetives->id)->get();
    
        // return response()->json(array(
        //     'res'=> true,
        //     'data' => $objetives
        // ), 200);
    }


    public function Delete (Request $request, $uuid){
        // PerformancePlans::where('unique_id', $uuid)
        // ->update([
        //     'state_id' => 2,
        // ]);

        // return response()->json(array(
        //     'res'=> true,
        //     'data' => 'Objetivo Estratégico Eliminado Correctamente'
        // ), 200);
    }
}
