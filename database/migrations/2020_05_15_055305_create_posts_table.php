<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->mediumText('text');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('top_id')->unsigned()->nullable(true);
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')->onUpdate('set null');
            $table->foreign('top_id')->references('id')->on('tops')
                ->onDelete('set null')->onUpdate('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
