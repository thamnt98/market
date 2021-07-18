<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpViaMail;
use App\Models\AdminCommission;
use App\Repositories\AdminRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HandleRegisterController extends Controller
{

    protected $adminRepository;
    protected $userRepository;
    protected $permissionRepository;

    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository, PermissionRepository $permissionRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function main(Request $request)
    {
        $data = $request->all();
        if (!Session::has('email')) {
            $validateEmail = $this->validateEmail($data['email']);
            if ($validateEmail->fails()) {
                return redirect()->back()->withErrors($validateEmail->errors())->withInput();
            }
            $otp = rand(100000, 999999);
            Mail::to($data['email'])->send(new SendOtpViaMail($otp));
            Session::put('email', $data['email']);
            Session::put('otp', $otp);
            return view('auth.otp');
        }
        if (isset($data['otp'])) {
            if (Session::get('otp') != $data['otp']) {
                Session::put('otp_valid', 'OTP is invalid');
                return view('auth.otp');
            }
            Session::forget('otp_valid');
            return view('auth.register');
        }
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            $errors = $validateData->errors();
            return view('auth.register', compact('errors','data'));
        }
        $data['password'] = Hash::make($data['password']);
        $data['ib_id'] = rand(100000, 999999);
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
        try {
            DB::beginTransaction();
            $data['admin_id'] = Session::get('admin_id');
            $data['status'] = 2;
            $admin = $this->adminRepository->create($data);
            Session::forget('email');
            Session::forget('otp');
            Session::forget('otp_valid');
            $data['application_type'] = 1;
            $this->userRepository->create($data);
            $adminCommission['admin_id'] = $admin->id;
            $adminCommission['us_stock_commission'] = '0.05';
            $adminCommission['forex_commission'] = '3';
            $adminCommission['other_commission'] = '3';
            $roleName = 'standardStaff';
            if(is_null($data['admin_id'])){
                $roleName = 'standardManager';
                $adminCommission['staff_us_stock_commission'] = '0.02';
                $adminCommission['staff_forex_commission'] = '1';
                $adminCommission['staff_other_commission'] = '1';
            }
            AdminCommission::insert($adminCommission);
            $this->permissionRepository->syncPermissionForUserByRoleName($admin, $roleName);
            DB::commit();
            return redirect('/login')->with('success', 'You registered successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function validateEmail($email)
    {
        return Validator::make(
            ['email' => $email],
            [
                'email' => 'required|email|unique:admins|unique:users',
            ]
        );
    }

    public function validateData($data)
    {
        return Validator::make(
            $data,
            [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'password' => 'required|regex:/[A-z0-9]{8,}/',
                'password_confirmation' => 'required|same:password',
                'phone_number' => 'required|regex:/[0-9]{10,11}/',
            ]
        );
    }
}
