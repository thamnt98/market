<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function main(Request $request)
    {
        Session::put('admin_id', $request->admin_id);
        Session::forget('email');
        Session::forget('otp');
        Session::forget('otp_valid');
        return view('auth.email');
    }
}
