<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookDisShipmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_dis_shipment_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->bigInteger('total');

            $table->integer('grade_id')->unsigned()->nullable();
            $table->integer('title_id')->unsigned()->nullable();
            $table->integer('language_id')->unsigned()->nullable();
            $table->integer('book_dis_shipments_id')->unsigned();
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('book_dis_meta_grades');
            $table->foreign('title_id')->references('id')->on('book_dis_meta_titles');
            $table->foreign('language_id')->references('id')->on('book_dis_title_languages');
            $table->foreign('book_dis_shipments_id')->references('id')->on('book_dis_shipments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('book_dis_shipment_details');
    }
}
