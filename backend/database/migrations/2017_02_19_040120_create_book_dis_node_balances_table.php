<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookDisNodeBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_dis_node_balances', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('total');
            $table->bigInteger('previous_total');
            $table->bigInteger('amount_added');
            $table->string('type');
            $table->integer('active');

            $table->integer('node_id')->unsigned();
            $table->integer('transaction_id')->unsigned();
            $table->integer('creator_id')->unsigned();

            $table->foreign('node_id')->references('id')->on('book_dis_nodes');
            $table->foreign('transaction_id')->references('id')->on('book_dis_node_transactions');
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
        Schema::drop('book_dis_node_balances');
    }
}
