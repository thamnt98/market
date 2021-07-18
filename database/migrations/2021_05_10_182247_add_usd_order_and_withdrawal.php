<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsdOrderAndWithdrawal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('usd', 191)->nullable();
        });

        Schema::table('withdrawal_funds', function (Blueprint $table) {
            $table->string('currency', 20)->nullable()->change();
            $table->float('available_balance')->nullable()->change();
            $table->string('bank_account', 191)->nullable()->change();
            $table->string('bank_address', 255)->nullable()->change();
            $table->string('bank_name', 191)->nullable()->change();
            $table->float('balance')->nullable()->change();
            $table->string('account_holder', 191)->nullable()->change();
            $table->string('bank_branch_name', 191)->nullable()->change();
            $table->string('note', 191)->nullable()->change();
            $table->string('swift_code', 191)->nullable()->change();
            $table->string('iban', 20)->nullable()->change();
            $table->string('account_name', 255)->nullable()->change();
            $table->string('withdrawal_currency', 20)->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function($table)
        {
            $table->dropColumn(['usd']);
        });

    }
}
