<?php

namespace App\Http\Controllers\Admin\Account;

use App\Helper\MT5Helper;
use App\Http\Controllers\Controller;
use App\Repositories\LiveAccountRepository;
use App\Repositories\WithdrawalRepository;
use Illuminate\Support\Facades\Auth;

class DetailLiveAccountController extends Controller
{

     /**
     * @var LiveAccountRepository
     */
    private $liveAccountRepository;
    private $withdrawalRepository;

    /**
     * LiveListController constructor.
     */
    public function __construct(LiveAccountRepository $liveAccountRepository, WithdrawalRepository $withdrawalRepository)
    {
        $this->liveAccountRepository = $liveAccountRepository;
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function main($id)
    {
        $account = $this->liveAccountRepository->find($id);
        $canEdit = Auth::user()->hasPermissionTo('account.edit');
        $withdrawals = $this->withdrawalRepository->getWithdrawalByLogin($account->login);
        $groups = MT5Helper::getGroups();
        return view('admin.account.detaillive', compact('account', 'withdrawals', 'canEdit', 'groups'));
    }
}
