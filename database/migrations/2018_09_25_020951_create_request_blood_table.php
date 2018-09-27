<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestBloodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_request_blood', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('patient_name')->nullable();
            $table->integer('is_accepted')->default('0')->nullable();
            $table->string('quantity_donated')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('donated_hospital')->nullable();
            $table->string('address')->nullable();
            $table->text('image')->nullable();;
            $table->integer('lat')->default('0')->nullable();
            $table->integer('long')->default('0')->nullable();
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
        Schema::dropIfExists('tbl_request_blood');
    }
}
