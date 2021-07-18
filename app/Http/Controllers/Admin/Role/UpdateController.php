<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * UpdateController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function main(Request $request, $id)
    {
        $data = [
            'display_name' => $request->display_name,
            'permissions' => $request->permissions
        ];
        $validate = $this->validateData($data);
        if ($validate->fails()) {
            return back()->withErrors($validate->errors())->withInput();
        }
        $result = $this->permissionRepository->updateRoleById($id, $data);
        if ($result === null) {
            return redirect()->route('role.list')
                ->with('success', 'Update role successfully');
        }
        return redirect()->route('role.detail')
            ->with('error', 'messages.Something went wrong. Please try again later.');
    }

    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateData($data)
    {
         return Validator::make(
            $data,
            [
                'display_name' => 'required|string|max:255',
                'permissions' => 'required|array'
            ]
        );
    }
}
