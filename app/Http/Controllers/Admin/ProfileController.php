<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
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

    public function main(){
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }
}
