<?php

namespace App\Http\Controllers\Admin\Account;

use App\Helper\MT4Connect;
use App\Http\Controllers\Controller;
use App\Repositories\LiveAccountRepository;
use Illuminate\Support\Facades\DB;

class DeleteLiveAccountController extends Controller
{

    /**
     * @var MT4Connect
     */
    private $mt4Connect;
    private $liveAccountRepository;

    /**
     * LiveListController constructor.
     */
    public function __construct(MT4Connect $mt4Connect, LiveAccountRepository $liveAccountRepository)
    {
        $this->mt4Connect = $mt4Connect;
        $this->liveAccountRepository = $liveAccountRepository;
    }

    public function main($login)
    {
        try {
            DB::beginTransaction();
            $this->liveAccountRepository->deleteByLogin($login);
            $message = $this->mt4Connect->deleteLiveAccount($login);
            if (!empty($message)) {
                return redirect()->back()->with('error', $message);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Bạn đã xóa thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Xóa thất bại');
        }
    }
}
