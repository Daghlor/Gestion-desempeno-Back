<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class EmploymentController extends Controller
{
    public function Create (Request $request){
        $validate = Employment::where('description', $request->all()['description'])->first();

        if(isset($validate) == true){
            return response()->json(array(
                'data' => 'Ya existe un cargo con la misma descripción',
                'res' => false
            ), 200);
        }

        $employment = Employment::create([
            'unique_id' => Str::uuid()->toString(),
            'description' => $request->all()['description'],
            'company_id' => !$request->all()['company_id'] ? null : $request->all()['company_id']
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => [
                'roles' => $employment->unique_id,
                'msg' => 'Cargo Creado Correctamente'
            ]
        ), 200);
    }

    public function FindAll (Request $request){
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $employment = Employment::orderBy('description', 'asc')
        ->leftjoin('companies', 'companies.id', '=', 'employments.company_id')
        ->limit($paginate)
        ->offset(($page-1)*$paginate)
        ->orderBy($column, $direction)
        ->get([
            'employments.id', 'employments.unique_id', 'employments.description', 'employments.company_id', 'companies.businessName as company_name'
        ]);

        $counts = Employment::leftjoin('companies', 'companies.id', '=', 'employments.company_id')->get(['employments.id']);

        

        return response()->json(array(
            'res'=> true,
            'data' => [
                'employments' => $employment,
                'total' => count($counts)
            ]
        ), 200);
    }

    public function FindAllPublic (Request $request){
        $employment = Employment::orderBy('description', 'asc')
        ->where('company_id', null)
        ->get([
            'employments.id', 'employments.description'
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => $employment,
        ), 200);
    }

    public function FindOne (Request $request, $uuid){
        $employment = Employment::where('unique_id', $uuid)->first();

        $employment->company = null;
        if($employment->company_id){
            $employment->company = Company::where('id', $employment->company_id)->first();
        }

        return response()->json(array(
            'res'=> true,
            'data' => $employment,
        ), 200);
    }

    public function Update (Request $request, $uuid){
        $validate = Employment::where('description', $request->all()['description'])->first();

        if(isset($validate) == true && $validate->unique_id != $uuid){
            return response()->json(array(
                'data' => 'Ya existe un cargo con la misma descripción',
                'res' => false
            ), 200);
        }

        Employment::where('unique_id', $uuid)->update([
            'description' => $request->all()['description'],
            'company_id' => $request->all()['company_id'],
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Cargo Actualizado Correctamente'
        ), 200);
    }

    public function Delete (Request $request, $uuid){
        Employment::where('unique_id', $uuid)->update([
            'state_id' => 2,
        ]);

        return response()->json(array(
            'res'=> true,
            'data' => 'Información Eliminada Correctamente'
        ), 200);
    }

}
