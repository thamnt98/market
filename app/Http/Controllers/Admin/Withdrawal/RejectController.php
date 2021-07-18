<?php

namespace App\Http\Controllers\Admin\Withdrawal;

use App\Http\Controllers\Controller;
use App\Repositories\WithdrawalRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class RejectController extends Controller
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

    public function main($id)
    {
        try {
            $withdrawal = $this->withdrawalRepository->findWithDrawalFun($id);
            if (!$withdrawal) {
                return new Exception('find withdrawal fail');
            }
            $this->withdrawalRepository->update(['status' => config('deposit.status.no')], $id);
            DB::commit();
            return redirect()->back()->with('success', 'Reject thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Reject fail');
        }
    }
}
