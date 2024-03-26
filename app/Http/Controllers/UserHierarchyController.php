<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserHierarchy;

class UserHierarchyController extends Controller
{
    public function getAll()
    {
        $userHierarchies = UserHierarchy::all();
        return response()->json($userHierarchies);
    }

    public function Create(Request $request)
{
    $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        // Eliminamos la validación del parent_id
    ], [
        'user_id.required' => 'El campo user_id es requerido.',
        'user_id.exists' => 'El user_id proporcionado no existe en la tabla de usuarios.',
    ]);

    // Si el parent_id no se proporciona, establecerlo en el ID del jefe principal (user_id: 3)
    if (!$request->has('parent_id')) {
        $request->merge(['parent_id' => 3]); // Esto asume que el ID del jefe principal es siempre 3
    }

    $userHierarchy = UserHierarchy::create($request->all());
    return response()->json($userHierarchy, 201);
}


    public function show(UserHierarchy $userHierarchy)
    {
        return response()->json($userHierarchy);
    }

    public function update(Request $request, UserHierarchy $userHierarchy)
    {
        $request->validate([
            'user_id' => 'exists:users,id',
            'parent_id' => 'exists:user_hierarchy,id',
            // Puedes agregar más validaciones si es necesario
        ]);

        $userHierarchy->update($request->all());
        return response()->json($userHierarchy, 200);
    }

    public function destroy(UserHierarchy $userHierarchy)
    {
        $userHierarchy->delete();
        return response()->json(null, 204);
    }

    public function children(UserHierarchy $userHierarchy)
    {
        $children = $userHierarchy->children;
        return response()->json($children);
    }

    public function parent(UserHierarchy $userHierarchy)
    {
        $parent = $userHierarchy->parent;
        return response()->json($parent);
    }

    public function deleteAll()
    {
        // Borra todas las jerarquías
        UserHierarchy::truncate();
        return response()->json(['message' => 'Todas las jerarquías han sido borradas']);
    }
}
