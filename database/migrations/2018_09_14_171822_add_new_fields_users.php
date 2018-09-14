<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->integer('country_id')->after('id');
            $table->integer('state_id')->after('country_id');
            $table->integer('city_id')->after('state_id');
            $table->integer('role_id')->after('city_id')->comment('Role id defined the role of user like its admin or donor or reciver is ');
            $table->text('profile_pic')->after('password')->comment('my comment');
            $table->string('blood_group')->after('profile_pic')->comment('my comment');
            $table->string('address')->after('profile_pic')->comment('my comment');
            $table->integer('lat')->after('address')->comment('my comment');
            $table->integer('long')->after('lat')->comment('my comment');
            $table->integer('status')->after('long')->comment('my comment');
            $table->integer('remeber_token')->after('status')->comment('my comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('country_id');
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->dropColumn('role_id');
            $table->dropColumn('profile_pic');
            $table->dropColumn('blood_group');
            $table->dropColumn('address');
            $table->dropColumn('lat');
            $table->dropColumn('long');
            $table->dropColumn('status');
            $table->dropColumn('remeber_token');
        });
    }
}
