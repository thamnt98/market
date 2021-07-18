<?php

namespace App\Http\Controllers\Admin\Email;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected  $userRepository;
    protected  $adminRepository;

    /**
     * EmailController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository  $userRepository, AdminRepository $adminRepository)
    {
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;
    }

    public function main(){
        $path = public_path('../app/Mail/templates.json');
//        $path = '/home/gemimnhr/ib.gemifx.com/app/Mail/templates.json';
        $templates = file_get_contents($path);
        $templates = json_decode($templates);
        $users = $this->userRepository->getCustomersHasMT4AccountOrNo();
        $agents = $this->adminRepository->getAgentList();
        $users['agents'] = $agents;
        return view('admin.mail.marketing', compact('templates', 'users'));
    }
}
