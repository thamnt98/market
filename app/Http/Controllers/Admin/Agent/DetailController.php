<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use App\Models\AdminCommission;
use App\Models\Role;
use App\Repositories\AdminRepository;

class DetailController extends Controller
{
    /**
     * @var AdminRepository
     */
    private $adminRepository;

    /**
     * LiveListController constructor.
     * @param \App\Repositories\AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
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
        return view('admin.agent.detail', compact('agent', 'managers', 'commission', 'roles'));
    }
}
