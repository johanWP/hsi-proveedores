<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('cuit', 20)->unique();
            $table->string('codigo_particular', 20);
            $table->string('razon_social');
            $table->string('nombre_fantasia')->nullable()->default('No Disponible');
            $table->string('direccion')->nullable()->default('No Disponible');
            $table->string('barrio', 100)->nullable()->default('No Disponible');
            $table->string('provincia', 100)->nullable()->default('No Disponible');
            $table->string('localidad', 100)->nullable()->default('No Disponible');
            $table->string('cp')->nullable()->default('No Disponible');
            $table->string('telefono', 100)->nullable()->default('No Disponible');
            $table->string('fax', 100)->nullable()->default('No Disponible');
            $table->string('pagina_web')->nullable()->default('No Disponible');
//            $table->string('email');
            $table->string('condicion_iva', 100);
            $table->string('iibb', 50)->nullable()->default('No Disponible');
            $table->string('agente_iibb')->nullable()->default('No Disponible');
            $table->string('clase_proveedor', 100)->nullable();
            $table->float('limite_credito')->nullable()->default(0);
            $table->string('activo', 100)->nullable()->default('Activo');
            $table->string('nombre_contacto')->nullable()->default('No Disponible');
            $table->string('cargo_contacto')->nullable()->default('No Disponible');
            $table->string('direccion_contacto')->nullable()->default('No Disponible');
            $table->string('email_contacto')->nullable()->default('No Disponible');
            $table->string('dni_contacto', 100)->nullable()->default('No Disponible');
            $table->string('celular_contacto', 100)->nullable()->default('No Disponible');
            $table->dateTime('fecha_alta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}