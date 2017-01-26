<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
            
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:250',
            'description' => 'required|string|max:250',
        ];
        $this->validate($request, $rules);

        try {
            $rol = Role::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            flash('Se incluyó el rol <strong>' . $rol->name . '</strong>.', 'success');

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                $msg = 'El rol <strong>' . $request->name . '</strong> ya existe. Elija otro nombre.';
            } else {
                $msg ='Ocurrió un error al crear el rol.  Por favor, repórtelo al departamento de Sistemas';
            }
            flash($msg, 'danger')->important();
        }
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rol = Role::findOrFail($id);
        $permisos = Permission::all();
        return view('roles.edit', compact('permisos', 'rol'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Borro todos los permisos de ese rol
        $borrarTodo = DB::delete('delete from role_has_permissions where role_id  = ?', [$id]);
        $rol = Role::findOrFail($id);
//        dd($request->all());
        foreach($request->all() as $key => $value)
        {
            if ($key != '_method' && $key != '_token')
            {
                $rol->givePermissionTo($key);
            }
        }
        flash('Los permisos se otorgaron con éxito al rol <strong>' . $rol->name . '</strong>.', 'success');
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        flash('El rol <strong>' . $role->name . '</strong> se eliminó del sistema', 'success');
        $role->delete();
        $roles = Role::all();
        return view('roles.index', compact('roles'));    }
}
