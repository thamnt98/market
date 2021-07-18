<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;

class ActiveController extends Controller
{

    /**
     * @var AdminRepository
     */
    private $adminRepository;

    /**
     * LiveListController constructor.
     * @param AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function main($id, Request  $request)
    {
        $status = $request->status;
        $result = $this->adminRepository->activeAgent($id, $status);
        if ($result) {
            return redirect()->back()->with('success', 'Bạn đã ' . ($status == 1 ? 'kích hoạt ' : 'hủy kích hoạt ') . 'thành công');
        } else {
            return redirect()->back()->with('error', 'Active thất bại ');
        }
    }
}
