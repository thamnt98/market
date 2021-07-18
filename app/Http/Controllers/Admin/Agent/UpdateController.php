<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Admin;

class UpdateController extends Controller
{
    /**
     * @var AdminRepository
     */
    private $adminRepository;
    private $userRepository;

    /**
     * LiveListController constructor.
     * @param \App\Repositories\AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository, UserRepository  $userRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    public function main($id, Request $request)
    {
        $data = $request->except('_token');
        $validateData = $this->validateData($data, $id);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        try {
            DB::beginTransaction();
            $ibId = Admin::find($id)->ib_id;
            $this->adminRepository->updateAgent($id, $data);
            DB::commit();
            return redirect()->back()->with('success', 'Bạn đã cập nhật thành công');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Cập nhật thất bại');
        }
    }

    public function validateData($data, $id)
    {
        return Validator::make(
            $data,
            [
                'name' => ['required', 'max:255'],
                'phone_number' => 'required|regex:/[0-9]{10,11}/',
                'us_stock_commission' => 'required|numeric|min:0',
                'forex_commission' => 'required|numeric|min:0',
                'other_commission' => 'required|numeric|min:0',
                'staff_us_stock_commission' => 'required_if:role,manager|nullable|numeric|min:0',
                'staff_forex_commission' => 'required_if:role,manager|nullable|numeric|min:0',
                'staff_other_commission' => 'required_if:role,manager|nullable|numeric|min:0',
                'ib_id' => 'bail|required|regex:/[0-9]{6}/|unique:admins,ib_id,'. $id,
                'ib_id'
            ]
        );
    }
}
