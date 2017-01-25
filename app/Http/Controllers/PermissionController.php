<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\QueryException;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permisos = Permission::all();
        return view('permisos.index', compact('permisos'));
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
            $permiso = Permission::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            flash('Se incluyó el permiso <strong>' . $permiso->name . '</strong>.', 'success');

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                $msg = 'El permiso <strong>' . $request->name . '</strong> ya existe. Elija otro nombre';
            } else {
                $msg ='Ocurrió un error al importar los usuarios';
            }
            flash($msg, 'danger');
        }
        $permisos = Permission::all();
        return view('permisos.index', compact('permisos'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permiso)
    {
        flash('El permiso <strong>' . $permiso->name . '</strong> se eliminó del sistema', 'success');
        $permiso->delete();
        $permisos = Permission::all();
        return view('permisos.index', compact('permisos'));
    }
}
