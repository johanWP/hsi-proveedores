<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cuit', 'codigo_particular', 'razon_social', 'nombre_fantasia',
        'direccion', 'barrio', 'provincia', 'localidad', 'cp', 'telefono',
        'fax', 'pagina_web', 'condicion_iva', 'iibb', 'agente_iibb', 'clase_proveedor',
        'limite_credito', 'activo', 'nombre_contacto', 'cargo_contacto', 'direccion_contacto',
        'email_contacto', 'dni_contacto', 'celular_contacto', 'fecha_alta'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'profiles';

    protected $dates = ['fecha_alta'];
    
    public function User()
    {
        return $this->belongsTo('App\User');
    }
}
