<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsgorollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('csgoroll', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_request_id');
            $table->string('full_name');
            $table->string('price_difference')->nullable();
            $table->string('conversion_price_buff')->nullable();
            $table->string('csgoroll')->nullable();
            $table->string('buff')->nullable();
            $table->string('rate')->nullable();
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
        Schema::dropIfExists('csgoroll');
    }
}
