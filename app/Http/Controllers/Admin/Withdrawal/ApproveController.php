<?php

namespace App\Http\Controllers\Admin\Withdrawal;

use App\Helper\MT5Helper;
use App\Http\Controllers\Controller;
use App\Repositories\WithdrawalRepository;
use App\Helper\MT4Connect;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class ApproveController extends Controller
{
    const SUBTRACT = '-';

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

    public function main($id, Request $request)
    {
        try{
            $amount = $request->amount;
            $withdrawal = $this->withdrawalRepository->findWithDrawalFun($id);
            if(!$withdrawal){
                return new Exception('find withdrawal fail');
            }
            $login = $withdrawal->login;
            $data = [
                'Account' => $login,
                'Amount' => $amount,
                'Comment' => 'Withdrawal to Bank'
            ];
            $result = MT5Helper::makeWithdrawal($data);
            if (is_null($result->ERR_MSG)) {
                DB::beginTransaction();
                $this->withdrawalRepository->update(['status' => config('deposit.status.yes'), 'amount' => $amount], $id);
                DB::commit();
                return redirect()->back()->with('success', 'You are approve success');
            }
            return redirect()->back()->with('error', 'Approve fail');
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', 'Approve fail');
        }
    }

}
