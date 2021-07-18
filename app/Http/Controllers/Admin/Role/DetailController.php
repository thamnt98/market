<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;

class DetailController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * DetailController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function main($id)
    {
        $roleData = $this->permissionRepository->getRoleDataById($id);
        $permissions = $this->permissionRepository->getPermissionInTreeStructure();
        return view('admin.role.edit', compact('roleData', 'permissions'));
    }
}
