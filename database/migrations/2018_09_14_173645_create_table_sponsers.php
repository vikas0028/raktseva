<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSponsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_sponsers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id');
            $table->integer('fund_type_id');
            $table->integer('mobile');
            $table->string('sponser_name');
            $table->string('address',255);
            $table->string('designation',100);
            $table->text('profile_pic');
            $table->integer('fund_amount');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_sponsers');
    }
}
