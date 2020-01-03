<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->double('premium');
            $table->double('smart');
            $table->double('premium_real')->default(0);
            $table->double('smart_real')->default(0);
            $table->integer('qty_premium')->default(0);
            $table->integer('qty_smart')->default(0);
            $table->integer('qty_premium_real')->default(0);
            $table->integer('qty_smart_real')->default(0);
            $table->integer('month');
            $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuotas');
    }
}
