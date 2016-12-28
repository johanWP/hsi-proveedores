<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GaliciaOrdenesDePagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galicia_ordenes_de_pago', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transferencia_id')->unsigned();
            $table->integer('numero_registro')->unsigned(); // correlativo a partir de uno por cada pago de la lista
            $table->integer('importe')->unsigned(); // en centavos
            $table->char('moneda', 3)->default('001');
            $table->string('cbu', 22);
            $table->date('fecha_pago');
            $table->string('razon_social', 50);
            $table->string('direccion', 30)->nullable();
            $table->string('localidad', 20);
            $table->char('cod_postal')->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('cuit', 15);
            $table->string('numero_pago', 35);  // correspone a NUMEROPAGO en el Flexxus
            $table->char('concepto', 2)->default('01');
            $table->char('destino_comprobantes', 2)->default('02');
            $table->string('email', 70);
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
        Schema::dropIfExists('galicia_ordenes_de_pago');
    }
}
