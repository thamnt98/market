<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use App\Models\AdminCommission;
use App\Models\Role;
use App\Repositories\AdminRepository;
use App\Repositories\PermissionRepository;

class DetailController extends Controller
{
    /**
     * @var AdminRepository
     */
    private $adminRepository;
    private $permissionRepository;

    /**
     * LiveListController constructor.
     * @param \App\Repositories\AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository, PermissionRepository $permissionRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function main($id)
    {
        $agent = $this->adminRepository->getAgentDetail($id);
        $commission = AdminCommission::where('admin_id', $id)->first();
        if (is_null($agent->admin_id)) {
            $agent->role = 'manager';
        } else {
            $agent->role = 'staff';
        }
        $managers = $this->adminRepository->getManagerList();
        $roles = Role::where('name', '!=', 'superAdmin')->get();
        $superManagers = $this->permissionRepository->getUsersByRoleName('superManager');
        return view('admin.agent.detail', compact('agent', 'managers', 'commission', 'roles', 'superManagers'));
    }
}
