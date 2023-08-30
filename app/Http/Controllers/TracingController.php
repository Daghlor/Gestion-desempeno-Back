<?php

// ESTE ES EL CONTROLADOR DE SEGUIMIENTOS DONDE ESTAN LAS FUNCIONES DE TIPO CRUD Y OTRAS FUNCIONES

namespace App\Http\Controllers;

use App\Models\ObjectivesIndividual;
use App\Models\Tracing;
use App\Models\User;
use App\Models\RolesUsers;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TracingController extends Controller
{
    // FUNCION PARA CREAR O REGISTRAR UN SEGUIMIENTO

    public function create(Request $request)
    {
        // Obtén el usuario autenticado
        $user = auth()->user();

        // Obtén el rol del usuario
        $userRole = $user->roles->first()->description;

        // Asegúrate de que el usuario tenga el rol adecuado (Administrador o Super Administrador)
        if ($userRole === 'Administrador' || $userRole === 'Super Administrador') {
            // Crea un nuevo seguimiento con los datos del formulario
            $tracing = new Tracing([
                'unique_id' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'individual_id' => $request->input('individual_id'),
                'plans_id' => $request->input('plans_id'),
                'weight' => $request->input('weight'),
                'comment' => $request->input('comment'), // Comentario del jefe
            ]);

            $tracing->save();

            return response()->json([
                'res' => true,
                'data' => [
                    'tracing' => $tracing->unique_id,
                    'user_role' => $userRole, // Indica el rol del usuario
                    'msg' => 'Seguimiento Creado Correctamente por el Jefe'
                ]
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'msg' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }
    }

    public function addEmployeeComment(Request $request, $uuid)
    {
        // Obtén el usuario autenticado
        $user = auth()->user();

        // Verifica si el usuario tiene el rol de Empleado
        if ($user->roles->contains('description', 'Empleado')) {
            // Busca el seguimiento por su unique_id
            $tracing = Tracing::where('unique_id', $uuid)->first();

            // Verifica si el seguimiento existe
            if ($tracing) {
                // Agrega el comentario del empleado
                $tracing->comment_employee = $request->input('comment_employee');
                $tracing->weight = $request->input('weight'); // Agrega el peso ingresado por el empleado
                $tracing->save();

                return response()->json([
                    'res' => true,
                    'data' => [
                        'tracing' => $tracing->unique_id,
                        'user_role' => 'Empleado',
                        'comment_employee' => $tracing->comment_employee,
                        'weight' => $tracing->weight, // Devuelve el peso ingresado por el empleado
                        'msg' => 'Comentario del Empleado agregado correctamente'
                    ]
                ], 200);
            } else {
                return response()->json([
                    'res' => false,
                    'msg' => 'El seguimiento no existe.'
                ], 404);
            }
        } else {
            return response()->json([
                'res' => false,
                'msg' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }
    }





    // FUNCION PARA BUSCAR O TRAER EL SEGUIMIENTO DE UN USUARIO QUE YA HAYA TENIDO UN SEGUIMIENTO
    public function FindUserTracing(Request $request)
    {
        $paginate = $request->all()['paginate'];
        $page = $request->all()['page'];
        $column = $request->all()['column'];
        $direction = $request->all()['direction'];
        $search = $request->all()['search'];

        $objetives = ObjectivesIndividual::get(['user_id', 'id']);
        $arrayDataValidate = [];
        $arrayData = [];

        for ($i = 0; $i < count($objetives); $i++) {
            if (!in_array($objetives[$i]->user_id, $arrayDataValidate)) {
                array_push($arrayDataValidate, $objetives[$i]->user_id);
            }
        }

        $users = User::whereIn('id', $arrayDataValidate)
            ->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->orderBy($column, $direction)
            ->get([
                DB::raw("CONCAT(users.name,' ', users.lastName) AS name"), 'users.identify', 'users.email', 'users.company_id', 'users.unique_id'
            ]);

        $counts = User::whereIn('id', $arrayDataValidate)
            ->get(['users.identify']);

        return response()->json(array(
            'res' => true,
            'data' => [
                'users' => $users,
                'total' => count($counts)
            ]
        ), 200);
    }

    // FUNCION PARA BUSCAR O TRAER TODOS LOS SEGUIMIENTOS
    public function FindAll(Request $request)
    {
        $paginate = $request->input('paginate', 10);
        $page = $request->input('page', 1);
        $column = $request->input('column', 'created_at');
        $direction = $request->input('direction', 'desc');
        $search = $request->input('search', []);

        $query = Tracing::join('users', 'users.id', '=', 'tracings.user_id')
            ->join('roles_users', 'roles_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'roles_users.rol_id')
            ->select([
                'tracings.unique_id',
                'tracings.comment',
                'tracings.comment_employee', // Agrega el comentario del empleado
                'users.name AS userName',
                'users.lastName AS userLastName',
                'roles.description AS userRoleDescription',
                'tracings.created_at'
            ]);

        if (!empty($search)) {
            if (isset($search['individual_id'])) {
                $query->where('tracings.individual_id', $search['individual_id']);
            }
        }

        $total = $query->count();

        $tracings = $query->orderBy($column, $direction)
            ->limit($paginate)
            ->offset(($page - 1) * $paginate)
            ->get();

        return response()->json([
            'res' => true,
            'data' => [
                'seguimientos' => $tracings,
                'total' => $total
            ]
        ], 200);
    }


    // FUNCION PARA BUSCAR UN SOLO SEGUIMIENTO POR SU UNIQUE_ID
    public function FindOne(Request $request, $uuid)
    {
        $user = User::where('unique_id', $uuid)->first(['id', 'name', 'lastName', 'identify', 'phone', 'email']);
        $objetives = ObjectivesIndividual::where('objectives_individuals.user_id', $user->id)
            ->join('objectives_strategics', 'objectives_strategics.id', '=', 'objectives_individuals.strategic_id')
            ->join('states_objectives', 'states_objectives.id', '=', 'objectives_individuals.state_id')
            ->get([
                'objectives_individuals.id', 'objectives_individuals.unique_id', 'objectives_individuals.title',
                'objectives_individuals.objetive', 'objectives_strategics.title as title_strategics',
                'objectives_individuals.weight', 'objectives_individuals.strategic_id', 'objectives_individuals.state_id',
                'objectives_individuals.created_at', 'states_objectives.description as state', 'objectives_individuals.plans_id',
            ]);

        for ($i = 0; $i < count($objetives); $i++) {
            $objetives[$i]['tracing'] = Tracing::where('individual_id', $objetives[$i]->id)->orderBy('created_at', 'desc')->get();

            $totalPoints = 100; // Inicializa los puntos disponibles para cada objetivo individual

            // Recorre los seguimientos y resta sus pesos de los puntos totales
            foreach ($objetives[$i]['tracing'] as $tracing) {
                $totalPoints -= $tracing->weight;
            }

            // Agrega el total de puntos disponibles para este objetivo individual
            $objetives[$i]['totalPoints'] = $totalPoints;
        }

        return response()->json([
            'res' => true,
            'data' => $objetives,
            'user' => $user
        ], 200);
    }
}
