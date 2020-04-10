<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_item_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreign('venta_id')->references('id')->on('ventas');
            $table->foreign('place_id')->references('id')->on('categoria_secundarias');
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
        Schema::dropIfExists('quote_item_packages');
    }
}
