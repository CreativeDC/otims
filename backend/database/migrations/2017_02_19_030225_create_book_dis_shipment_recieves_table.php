<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookDisShipmentRecievesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_dis_shipment_recieves', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('total_safe');
            $table->bigInteger('total_general');
            $table->bigInteger('damaged');
            $table->bigInteger('lost');
            $table->timestamps();
            $table->integer('book_dis_shipments_id')->unsigned();
            $table->integer('receiver_id')->unsigned()->nullable();

            $table->foreign('book_dis_shipments_id')->references('id')->on('book_dis_shipments');
            $table->foreign('receiver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('book_dis_shipment_recieves');
    }
}
