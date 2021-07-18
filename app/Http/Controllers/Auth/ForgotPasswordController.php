<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ForgotPasswordController extends Controller
{
    public function main(){
        Session::forget('email');
        Session::forget('otp');
        Session::forget('otp_valid');
        return view('auth.password.email');
    }
}
