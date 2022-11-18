<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('recibo_id');
            $table->dateTime('fecha_hora_transaccion')->nullable();
            $table->string('transaccion_id')->nullable();
            $table->decimal('monto', 6, 2, true);
            $table->string('carnet');
            $table->string('carrera');
            $table->string('ciclo');
            $table->string('nombre_apellido');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
