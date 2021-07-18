<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id');
            $table->float('us_stock_commission')->nullable();
            $table->float('forex_commission')->nullable();
            $table->float('other_commission')->nullable();
            $table->float('staff_us_stock_commission')->nullable();
            $table->float('staff_forex_commission')->nullable();
            $table->float('staff_other_commission')->nullable();
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
        Schema::dropIfExists('admin_commissions');
    }
}
