<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {

            $table->integer('age')->after('profile_pic');
            $table->string('gender')->after('age');
            $table->date('dob')->nullable()->after('gender')->comment('Date Of Birth Of User');
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
            $table->dropColumn('age');
            $table->dropColumn('gender');
            $table->dropColumn('dob');
        });
    }
}
