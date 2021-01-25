<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BookDisNodeStaffGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_dis_node_staffs_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->timestamps();
            $table->integer('active');
            $table->integer('node_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('node_id')->references('id')->on('book_dis_nodes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_dis_node_staffs_groups', function (Blueprint $table) {
            //
        });
    }
}
