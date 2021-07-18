<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{

    protected $permissionRepository;

    public function __construct(\App\Repositories\PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        $data = [
            [
                'guard_name' => 'web',
                'name' => 'user.*',
                'display_name' => 'Quản lý khách hàng ',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'user.show',
                'display_name' => 'Xem danh sách khách hàng ',

                'parent_id' => 1,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'user.create',
                'display_name' => 'Thêm mới khách hàng ',

                'parent_id' => 1,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'user.edit',
                'display_name' => 'Chỉnh sửa khách hàng ',

                'parent_id' => 1,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'user.delete',
                'display_name' => 'Xóa khách hàng ',

                'parent_id' => 1,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'user.link',
                'display_name' => 'Cung cấp link giới thiệu cho khách hàng ',
                'parent_id' => 1,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'account.*',
                'display_name' => 'Quản lý tài khoản MT5',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'account.show',
                'display_name' => 'Xem danh sách tài khoản MT5 ',

                'parent_id' => 7,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'account.create',
                'display_name' => 'Tạo mới tài khoản MT5 ',

                'parent_id' => 7,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'account.edit',
                'display_name' => 'Chỉnh sửa tài khoản MT5',

                'parent_id' => 7,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'agent.*',
                'display_name' => 'Quản lý agent ',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'agent.show',
                'display_name' => 'Xem danh sách agent',

                'parent_id' => 11,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'agent.edit',
                'display_name' => 'Chỉnh sửa agent',

                'parent_id' => 11,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'agent.approve',
                'display_name' => 'Xác nhận agent',

                'parent_id' => 11,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'agent.link',
                'display_name' => 'Cung cấp link giới thiệu cho agent ',
                'parent_id' => 11,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'deposit.*',
                'display_name' => 'Quản lý nạp tiền ',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'deposit.show',
                'display_name' => 'Xem danh sách nạp tiền ',

                'parent_id' => 16,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'deposit.create',
                'display_name' => 'Nạp tiền ',

                'parent_id' => 16,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'deposit.approve',
                'display_name' => 'Xác thực nạp tiền cho khách ',

                'parent_id' => 16,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'withdrawal.*',
                'display_name' => 'Quản lý rút tiền ',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'withdrawal.show',
                'display_name' => 'Xem danh sách rút tiền ',

                'parent_id' => 20,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'withdrawal.create',
                'display_name' => 'Rút tiền ',

                'parent_id' => 20,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'withdrawal.approve',
                'display_name' => 'Xác thực rút tiền cho khách ',

                'parent_id' => 20,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'report.*',
                'display_name' => 'Thống kê',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'email.*',
                'display_name' => 'Quản lý gửi email marketing ',

                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'email.create',
                'display_name' => 'Tạo mới email marketing',

                'parent_id' => 25,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'email.send',
                'display_name' => 'Gửi email marketing ',
                'parent_id' => 25,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'role.*',
                'display_name' => 'Quản lý phân quyền ',
                'parent_id' => null,
                'level' => 1
            ],
            [
                'guard_name' => 'web',
                'name' => 'role.show',
                'display_name' => 'Xem danh sách role ',
                'parent_id' => 28,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'role.create',
                'display_name' => 'Tạo mới môt role ',
                'parent_id' => 28,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'role.edit',
                'display_name' => 'Chỉnh sửa role ',
                'parent_id' => 28,
                'level' => 2
            ],
            [
                'guard_name' => 'web',
                'name' => 'role.delete',
                'display_name' => 'Xóa role',
                'parent_id' => 28,
                'level' => 2
            ],
        ];
        $this->permissionRepository->createMultiplePermission($data);
    }
}
