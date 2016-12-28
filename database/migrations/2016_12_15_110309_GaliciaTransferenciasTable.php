<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GaliciaTransferenciasTable extends Migration
{
    /**
     * Esta tabla guarda la cabecera que se genera para el archivo de transferencias del
     * Banco Galicia.  El detalle de las transferencias está en la tabla galicia_ordenes_de_pago
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galicia_transferencias', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha_proceso');
            $table->char('numero_cuenta', 14);
            $table->string('archivo');  // ruta al archivo generado
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('galicia_transferencias');
    }}
