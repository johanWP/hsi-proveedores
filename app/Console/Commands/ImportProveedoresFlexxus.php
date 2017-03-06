<?php

namespace App\Console\Commands;

use App\Notifications\Bienvenida;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImportProveedoresFlexxus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexxus:proveedores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa proveedores nuevos desde la BD Flexxus';

    protected $tabla = 'WEB_PROVEEDORES_JOCKEY';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $error = 0; $creados = 0;
        try
        {
            $query = "select count(*) as total from " . $this->tabla . " WHERE TRIM(CUIT) != ''";
            $registros = collect(DB::connection('firebird')->select($query))
                ->first();
            $this->info('Total de proveedores: '. $registros->TOTAL);
            $bar = $this->output->createProgressBar($registros->TOTAL);
            $query = "SELECT * FROM " . $this->tabla ." WHERE TRIM(CUIT) != ''";
            $proveedores = DB::connection('firebird')->select($query);
            foreach ($proveedores as $fila)
            {
                if ($this->crearUsuario($fila))
                {
//                    $this->crearPerfil($fila);
                    $creados++;
                } else {
                    $error++;
                }
                $bar->advance();
            }
            $this->info("\r\n" . $creados. ' usuarios se actualizaron / crearon.');
            $this->error($error . ' usuarios no tienen email y no se crearon.');
        } catch (\Illuminate\Database\QueryException $e)
        {
            $this->error($e->getMessage());
            Log::error($e->getMessage());
            //TODO: enviar mail cuando falla la importación
        }
    }

    /**
     * Inserta un nuevo usuario o actualiza el usuario existente, basado en el CUIT
     * @param array $data
     * @return bool
     */
    private function crearUsuario($data)
    {
        try
        {
            $req = collect($data)->toArray();
            $v = Validator::make($req, [
                'CUIT'          => 'required|alpha_dash',
                'RAZONSOCIAL'   => 'required|max:255',
                'EMAIL'         => 'required|email'
            ]);

            if (! $v->fails())
            {
                $cuit = str_replace('-', '', $data->CUIT);
                $user = User::where('cuit', $cuit)->first();
                if (count($user) > 0)
                {
                    $user->update([
                        'name' => utf8_encode($data->RAZONSOCIAL),
                        'cuit' => utf8_encode($cuit),
                        'email' => utf8_encode($data->EMAIL)
                    ]);
                } else {
                    $user = User::create([
                        'name' => utf8_encode($data->RAZONSOCIAL),
                        'cuit' => utf8_encode($cuit),
                        'email' => utf8_encode($data->EMAIL)
                    ]);
                    $user->save();
                    
//                    $reset_token = strtolower(str_random(64));


//                    DB::table('password_resets')->insert([
//                        'email' => $user->email,
//                        'token' => bcrypt($reset_token),
//                        'created_at' => \Carbon\Carbon::now(),
//                    ]);

                    $user->notify(new Bienvenida($user));
//                    $user->notify(new Bienvenida($user, $reset_token));

                    if (! $user->hasRole('proveedor'))
                    {
                        $user->assignRole('proveedor');
                    }
                }
                return $user;

            } else {
                return false;
            }
        } catch ( \Illuminate\Validation\ValidationException $e) {
            Log::error('Firebird: Error al importar usuario. ', ['origen' => $data['EMAIL']]);
            return false;
        }

    }

/*
    private function crearPerfil($data)
    {
        $cuit = str_replace('-', '', $data->CUIT);
        $user = User::where('cuit', $cuit)->first();

        $profile = Profile::updateOrCreate(
            ['cuit' => $cuit],
            [
                'user_id' => $user->id,
                'cuit' => $cuit ,
                'codigo_particular' => $data-> CODIGOPARTICULAR,
                'razon_social' => utf8_encode($data->RAZONSOCIAL),
                'nombre_fantasia' => utf8_encode($data->NOMBREFANTASIA),
                'direccion' => utf8_encode($data->DIRECCION) ,
                'barrio' => utf8_encode($data->BARRIO) ,
                'provincia' => utf8_encode($data->PROVINCIA) ,
                'localidad' => utf8_encode($data->LOCALIDAD) ,
                'cp' => $data->CP ,
                'telefono' => $data->TELEFONO ,
                'fax' => $data->FAX ,
                'pagina_web' => $data->PAGINAWEB ,
                'condicion_iva' => $data->CONDICIONIVA ,
                'iibb' => $data->IIBB ,
                'agente_iibb' => $data->AGENTEIIBB ,
                'clase_proveedor' => $data->CLASEPROVEEDOR ,
                'limite_credito' => $data->LIMITECREDITO ,
                'activo' => $data->ACTIVO ,
                'nombre_contacto' => utf8_encode($data->NOMBRECONTACTO) ,
                'cargo_contacto' => utf8_encode($data->CARGOCONTACTO) ,
                'direccion_contacto' => utf8_encode($data->DIRECCIONCONTACTO) ,
                'email_contacto' => $data->EMAILCONTACTO ,
                'dni_contacto' => $data->DNICONTACTO ,
                'celular_contacto' => $data->CELULARCONTACTO ,
                'fecha_alta' => $data->FECHAALTA ,
            ]
        );
    }
*/
}
