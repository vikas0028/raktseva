<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldAndChangeCreatetedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_request_blood', function(Blueprint $table) {
            $table->integer('created_by')->default('0')->change();
            $table->integer('updated_by')->default('0')->change();
            $table->integer('user_id')->default('0')->change();
            $table->integer('is_expiry')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
