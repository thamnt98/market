<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Auth;

class HandleLoginController extends Controller
{

    protected $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function main(Request $request)
    {
        $data = $request->except('_token');
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        $isActive = $this->adminRepository->isActive($data['email']);
        if($isActive == 2){
            return back()->with('error', 'Your account is not active yet')->withInput();
        }
        $login = $this->adminRepository->login($data);
        if ($login) {
            return redirect()->route('dashboard');
        } else {
            return back()->with('error', 'Invalid email or password')->withInput();
        }
    }

    public function validateData($data)
    {
        return Validator::make(
            $data,
            [
                'email' => 'required|email|exists:admins',
                'password' => 'required|regex:/[A-z0-9]{8,}/',
            ]
        );
    }
}
