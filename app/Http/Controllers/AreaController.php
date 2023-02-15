<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class AreaController extends Controller
{
    public function Create (Request $request){
        $validate = Area::where('description', $request->all()['description'])->first();

        if(isset($validate) == true){
            return response()->json(array(
                'data' => 'Ya existe un area con la misma descripción',
                'res' => false
            ), 200);
        }

        $area = Area::create([
            'unique_id' => Str::uuid()->toString(),
            'description' => $request->all()['description'],
            'company_id' => $request->all()['company_id'],
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'roles' => $area->unique_id,
                'msg' => 'Área Creada Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];


        $companies = Area::leftjoin('companies', 'companies.id', '=', 'areas.company_id');
        if(count($search) > 0){
            if(isset($search['description'])){
                $companies = $companies->where('areas.description', 'like', '%'.$search['description'].'%');
            }
            if(isset($search['company_id'])){
                $companies = $companies->where('areas.company_id', $search['company_id']);
            }
        }
        $companies = $companies->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'areas.unique_id',  'areas.description', 'companies.businessName as company'
        ]);


        $counts = Area::leftjoin('companies', 'companies.id', '=', 'areas.company_id');
        if(count($search) > 0){
            if(isset($search['description'])){
                $counts = $counts->where('areas.description', 'like', '%'.$search['description'].'%');
            }
            if(isset($search['company_id'])){
                $counts = $counts->where('areas.company_id', $search['company_id']);
            }
        }
        $counts = $counts->get(['areas.unique_id']);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'areas' => $companies,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $companies = Area::where('unique_id', $uuid)->first();

        return response()->json(array(
            'res'=> true,
            'data' => $companies
        ), 200);
    }

    public function Update (Request $request, $uuid){
        $area = Area::where('unique_id', $uuid)
        ->update([
            'description' => $request->all()['description'],
            'company_id' => $request->all()['company_id'],
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Area Actualizado Correctamente'
        ), 200);
    }

}
