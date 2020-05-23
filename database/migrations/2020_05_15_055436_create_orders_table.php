<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->date('delivery_date');
            $table->enum('status', ['В обработке', 'Выполнен', 'Отменён'])->default('В обработке');
            $table->text('comment');
            $table->bigInteger('user_id')->unsigned()->nullable(true);
            $table->bigInteger('address_id')->unsigned()->nullable(true);
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
