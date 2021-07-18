<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * @var DepositRepository
     */
    private $depositRepository;

    /**
     * ListController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(DepositRepository $depositRepository)
    {
        $this->depositRepository = $depositRepository;
    }


    public function main(Request $request)
    {
        $data = $request->except('_token');
        $orders = $this->depositRepository->getDepositListBySearch($data);
        return view('admin.deposit.list', compact('orders', 'data'));
    }
}
