<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChnageFieldsWithDefaultUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->integer('country_id')->default('0')->change();
            $table->integer('state_id')->default('0')->change();
            $table->integer('city_id')->default('0')->change();
            $table->integer('role_id')->default('0')->change();
            $table->string('name')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->text('profile_pic')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->integer('lat')->default('0')->change();
            $table->integer('long')->default('0')->change();
            $table->integer('status')->default('1')->change();
            $table->integer('remeber_token')->nullable()->change();
            $table->string('blood_group')->nullable()->change();
            $table->integer('device_token')->nullable()->change();
            $table->integer('device_type')->nullable()->change();
            $table->string('device_details')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
