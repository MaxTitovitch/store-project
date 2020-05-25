<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('percent');
            $table->date('date_start');
            $table->date('date_end');
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('sale_category_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('sale_category_id')->references('id')->on('sale_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
