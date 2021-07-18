<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use App\Repositories\LiveAccountRepository;
use App\Repositories\UserRepository;
use App\Repositories\WithdrawalRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LiveAccountRepository
     */
    private $liveAccountRepository;
    private $withdrawalRepository;
    private $depositRepository;

    /**
     * ListController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        LiveAccountRepository $liveAccountRepository,
        WithdrawalRepository $withdrawalRepository,
        DepositRepository $depositRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->liveAccountRepository = $liveAccountRepository;
        $this->withdrawalRepository = $withdrawalRepository;
        $this->depositRepository = $depositRepository;
    }

    public function main($id)
    {
        try {
            DB::beginTransaction();
            $this->userRepository->delete($id);
            $message = $this->liveAccountRepository->deleteLiveAccountByUserId($id);
            if (!empty($message)) {
                return redirect()->back()->with('error', $message);
            }
            $this->withdrawalRepository->deleteWithdrawalByUserId($id);
            $this->depositRepository->deleteDepositByUserId($id);
            DB::commit();
            return redirect()->back()->with('success', 'Bạn đã xóa thành công');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Xóa thất bại');
        }
    }
}
