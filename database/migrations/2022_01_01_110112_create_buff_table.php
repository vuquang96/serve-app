<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('buff_id', 100)->nullable();
            $table->string('name')->nullable();
            $table->string('sell_min_price')->nullable();
            $table->string('buy_max_price')->nullable();
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
        Schema::dropIfExists('buff');
    }
}
