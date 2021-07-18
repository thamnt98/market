<?php

use Illuminate\Database\Seeder;

class AdminCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = \App\Models\Admin::all();
        $managers = $admins->filter(function ($admin) {
            return is_null($admin->admin_id);
        });
        $staffs = $admins->filter(function ($admin) {
            return !is_null($admin->admin_id);
        });

        foreach($managers as $manager){
            $data['admin_id'] = $manager->id;
            $data['us_stock_commission'] = '0.05';
            $data['forex_commission'] = '3';
            $data['other_commission'] = '3';
            $data['staff_us_stock_commission'] = '0.02';
            $data['staff_forex_commission'] = '1';
            $data['staff_other_commission'] = '1';
            \App\Models\AdminCommission::insert($data);
        }
        $data = [];
        foreach($staffs as $staff){
            $data['admin_id'] = $staff->id;
            $data['us_stock_commission'] = '0.05';
            $data['forex_commission'] = '3';
            $data['other_commission'] = '3';
            \App\Models\AdminCommission::insert($data);
        }
    }
}
