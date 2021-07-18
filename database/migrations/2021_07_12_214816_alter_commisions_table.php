<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCommisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_commissions', function (Blueprint $table) {
            $table->float('us_stock_commission')->default(0.05)->change();
            $table->float('forex_commission')->default(3)->change();
            $table->float('other_commission')->default(4)->change();
            $table->float('staff_us_stock_commission')->default(0.02)->change();
            $table->float('staff_forex_commission')->default(1)->change();
            $table->float('staff_other_commission')->default(1.33)->change();
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
