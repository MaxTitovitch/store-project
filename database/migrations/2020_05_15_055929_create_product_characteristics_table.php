<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_characteristics', function (Blueprint $table) {
            $table->id();
            $table->boolean('boolean_value')->nullable(true);
            $table->double('number_value')->nullable(true);
            $table->string('string_value')->nullable(true);
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('characteristic_id')->unsigned();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('product_characteristics');
    }
}
