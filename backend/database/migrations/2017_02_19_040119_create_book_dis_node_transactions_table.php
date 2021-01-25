<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookDisNodeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_dis_node_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('amount');
            $table->string('type');
            $table->bigInteger('source_id');
            $table->integer('creator_id')->unsigned();
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('book_dis_node_transactions');
    }
}
