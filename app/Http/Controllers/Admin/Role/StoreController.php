<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StoreController extends Controller
{

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * StoreController constructor.
     * @param PermissionRepository $permissionRepository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function main(Request $request)
    {
        $data = [
            'display_name' => $request->display_name,
            'permissions' => $request->permissions,
        ];
        $validate = $this->validateData($data);
        if ($validate->fails()) {
            return back()->withErrors($validate->errors())->withInput();
        }
        $data['name'] = Str::random(6);
        $result = $this->permissionRepository->createAndSyncPermissions($data);
        if ($result === null) {
            return redirect()->route('role.list')
                ->with('success', 'Tạo mới role thành công');
        }
        return redirect()->route('setting.role.create')
            ->with('error', 'Something went wrong. Please try again later.');
    }

    /**
     * @param $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateData($data)
    {
        $validator = Validator::make(
            $data,
            [
                'display_name' => 'required|string|max:255',
                'permissions' => 'required|array',
            ]
        );
        return $validator;
    }
}
