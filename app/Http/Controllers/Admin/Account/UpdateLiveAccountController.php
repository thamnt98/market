<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Repositories\LiveAccountRepository;

class UpdateLiveAccountController extends Controller
{

    /**
     * @var LiveAccountRepository
     */
    private $liveAccountRepository;

    /**
     * LiveListController constructor.
     */
    public function __construct(LiveAccountRepository $liveAccountRepository)
    {
        $this->liveAccountRepository = $liveAccountRepository;
    }

    public function main($id, Request $request)
    {
        $data = $request->except('_token');
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        try {
            DB::beginTransaction();
            $result = $this->liveAccountRepository->updateLiveAccount($id, $data);
            if (!($result)) {
                return redirect()->back()->with('error', $result);
            }
            DB::commit();
            return redirect()->back()->with('success', 'Bạn đã cập nhật tài khoản thành công');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Cập nhật thất bại');
        }
    }

    public function validateData($data)
    {
        $leverages = array_keys(config('mt4.leverage'));
        return Validator::make(
            $data,
            [
                'group' => ['required'],
                'leverage' => ['required', Rule::in($leverages)],
                'phone' => 'required|regex:/[0-9]{10,11}/',
                'ib_id'            => 'bail|required|regex:/[0-9]{6}/',
            ],
            [
                'ib_id.regex' => 'The IB ID has only 6 digits',
            ]
        );
    }
}
