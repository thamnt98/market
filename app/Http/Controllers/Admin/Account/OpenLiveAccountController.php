<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Mail\OpenLiveAccountSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Repositories\LiveAccountRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OpenLiveAccountController extends Controller
{

    /**
     * @var LiveAccountRepository
     */
    private $liveAccountRepository;
    private $userRepository;

    /**
     * LiveListController constructor.
     */
    public function __construct(LiveAccountRepository $liveAccountRepository, UserRepository $userRepository)
    {
        $this->liveAccountRepository = $liveAccountRepository;
        $this->userRepository = $userRepository;
    }

    public function main(Request $request)
    {
        $data = $request->except('_token');
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        try {
            DB::beginTransaction();
            $user = $this->userRepository->find($data['customer']);
            $result = $this->liveAccountRepository->openLiveAccount($user, $data);
            if (empty($result)) {
                return redirect()->back()->with('error', "Mở thất bại");
            }
            Mail::to($user->email)->send(new OpenLiveAccountSuccess($user, $result));
            DB::commit();
            return redirect()->back()->with('success', 'Bạn đã mở tài khoản thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Mở thất bại');
        }
    }

    public function validateData($data)
    {
        $leverages = array_keys(config('mt4.leverage'));
        return Validator::make(
            $data,
            [
                'customer' => 'required',
                'group' => ['required'],
                'leverage' => ['required', Rule::in($leverages)],
            ]
        );
    }
}
