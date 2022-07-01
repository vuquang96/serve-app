<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbMarketInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cb_market_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('buff')->nullable();
            $table->string('csgoroll')->nullable();
            $table->double('buff_rate')->nullable();
            $table->double('csgoroll_rate')->nullable();
            $table->double('rate', 8, 2)->nullable();
            $table->double('buff_sort', 8, 2)->nullable();
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
        Schema::dropIfExists('cb_market_inventory');
    }
}
