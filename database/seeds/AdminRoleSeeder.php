<?php

use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $permissionRepository;

    public function __construct(\App\Repositories\PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function run()
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        $superAdmin = \App\Models\Admin::where('role', 2)->first();
        $manager = \App\Models\Admin::where('role', 2)->whereNull('admin_id')->get();
        $staff = \App\Models\Admin::where('role', 2)->whereNotNull('admin_id')->get();
        $roleData = [
            'guard_name' => 'web',
            'name' => 'superAdmin',
            'display_name' => 'Super Administrator',
            'allowed_scope' => 1,
        ];
        $role = $this->permissionRepository->createOrUpdateSuperAdminRole($roleData);
        $permissions = $this->permissionRepository->getAllPermission(1);
        $role->syncPermissions($permissions);
        $this->permissionRepository->syncPermisionAndRoleForUsers([$superAdmin], $permissions, 'superAdmin');

        $roleData = [
            'guard_name' => 'web',
            'name' => 'admin',
            'display_name' => 'Admin',
            'allowed_scope' => 2,
        ];
        $role = $this->permissionRepository->createOrUpdateSuperAdminRole($roleData);
        $permissions = $this->permissionRepository->getAllPermission(4);
        $role->syncPermissions($permissions);

        $roleData = [
            'guard_name' => 'web',
            'name' => 'superManager',
            'display_name' => 'Super Manager',
            'allowed_scope' => 2,
        ];
        $role = $this->permissionRepository->createOrUpdateSuperAdminRole($roleData);
        $permissions = $this->permissionRepository->getAllPermission(2);
        $role->syncPermissions($permissions);

        $roleData = [
            'guard_name' => 'web',
            'name' => 'standardManager',
            'display_name' => 'Standard Manager',
            'allowed_scope' => 2,
        ];
        $role = $this->permissionRepository->createOrUpdateSuperAdminRole($roleData);
        $permissions = $this->permissionRepository->getAllPermission(2);
        $role->syncPermissions($permissions);
        $this->permissionRepository->syncPermisionAndRoleForUsers($manager, $permissions, 'standardManager');
        $roleData = [
            'guard_name' => 'web',
            'name' => 'standardStaff',
            'display_name' => 'Standard Staff',
            'allowed_scope' => 2,
        ];
        $role = $this->permissionRepository->createOrUpdateSuperAdminRole($roleData);
        $permissions = $this->permissionRepository->getAllPermission(3);
        $role->syncPermissions($permissions);
        $this->permissionRepository->syncPermisionAndRoleForUsers($staff, $permissions, 'standardStaff');
    }
}