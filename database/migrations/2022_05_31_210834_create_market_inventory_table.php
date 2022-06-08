<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('csgoempire')->nullable();
            $table->string('buff')->nullable();
            $table->string('csgoroll')->nullable();
            $table->string('csgoempire_default')->nullable();
            $table->string('buff_default')->nullable();
            $table->string('csgoroll_default')->nullable();
            $table->double('buff_sort', 8, 2)->nullable();
            $table->string('tick')->nullable();
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
        Schema::dropIfExists('market_inventory');
    }
}
