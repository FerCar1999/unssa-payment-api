<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{

    protected $connection = 'mysql';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_id');
            $table->dateTime('date_time_transaction')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 6, 2, true);
            $table->string('code');
            $table->string('career');
            $table->string('cycle');
            $table->string('complete_name');
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
