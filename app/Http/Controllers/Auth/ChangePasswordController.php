<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpViaMail;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{

    protected $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
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
            Session::put('admin_email', $data['email']);
            return view('auth.password.otp');
        }
        if (isset($data['otp'])) {
            if (Session::get('otp') != $data['otp']) {
                Session::put('otp_valid', 'OTP is invalid');
                return view('auth.password.otp');
            }
            Session::forget('otp_valid');
            return view('auth.password.forgot');
        }
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            $errors = $validateData->errors();
            return view('auth.password.forgot', compact('errors', 'data'));
        }
        $data['email'] = Session::get('admin_email');
        $data['password'] = Hash::make($data['password']);
        try {
            DB::beginTransaction();
            $this->adminRepository->changePassword($data);
            DB::commit();
            Session::forget('admin_email');
            return redirect('/login')->with('success', 'You changed password successfully');
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
                'email' => 'required|email|exists:admins',
            ]
        );
    }

    public function validateData($data)
    {
        return Validator::make(
            $data,
            [
                'password' => 'required|regex:/[A-z0-9]{8,}/',
                'password_confirmation' => 'required|same:password',
            ]
        );
    }
}
