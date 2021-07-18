<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UpdateProfileController extends Controller
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

    public function main(Request $request)
    {
        $data = $request->except('_token');
        $id = Auth::user()->id;
        $validateData = $this->validateData($id, $data);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        try {
            $this->adminRepository->update($data, $id);
            return redirect()->back()->with('success', 'Bạn đã cập nhật thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Cập nhật thất bại');
        }
    }

    public function validateData($id, $data)
    {
        return Validator::make(
            $data,
            [
                'name' => ['required', 'max:255'],
                'phone_number' =>  'required|regex:/[0-9]{10,11}/',
                'ib_id' => 'required|unique:admins,ib_id,' . $id .  '|regex:/[0-9]{6}/',
            ]
        );
    }
}
