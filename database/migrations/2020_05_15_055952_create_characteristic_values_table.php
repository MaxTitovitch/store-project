<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_values', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->bigInteger('characteristic_id')->unsigned();
            $table->timestamps();

            $table->foreign('characteristic_id')->references('id')->on('characteristics')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characteristic_values');
    }
}
