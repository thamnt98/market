<?php

namespace App\Http\Controllers\Admin\Withdrawal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\WithdrawalRepository;

class ListController extends Controller
{

    /**
     * @var WithdrawalRepository
     */
    private $withdrawalRepository;

    /**
     * ListController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(WithdrawalRepository $withdrawalRepository)
    {
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function main(Request $request)
    {
        $data = $request->except('_token');
        $withdrawals = $this->withdrawalRepository->getWithdrawalListBySearch($data);
        return view('admin.withdrawal.list', compact('withdrawals', 'data'));
    }
}
