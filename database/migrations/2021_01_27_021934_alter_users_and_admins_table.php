<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAndAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('ib_id')->nullable();
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->tinyInteger('role')->default(2);
            $table->bigInteger('ib_id')->nullable();
        });
        Schema::table('live_accounts', function (Blueprint $table) {
            $table->bigInteger('ib_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ib_id');
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('ib_id');
        });
        Schema::table('live_accounts', function (Blueprint $table) {
            $table->dropColumn('ib_id');
        });
    }
}
