<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use Exception;

class RejectController extends Controller
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

    public function main($id)
    {
        try {
            $order = $this->depositRepository->findOrders($id);
            if ($order === null) {
                throw new Exception('Find order fail');
            }
            $this->depositRepository->update(['status' => config('deposit.status.no')], $id);
            return redirect()->back()->with('success', 'Bạn đã reject thành công');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Reject thất bại');
        }
    }
}
