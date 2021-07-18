<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Mail\CreateUserSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Repositories\PasswordResetRepository;
use Illuminate\Support\Carbon;

class StoreController extends Controller
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    private $passwordResetRepository;

    /**
     * ListController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, PasswordResetRepository $passwordResetRepository)
    {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function main(Request $request)
    {
        $data = $request->except('_token');
        $validateData = $this->validateData($data);
        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData->errors())->withInput();
        }
        $data['password'] = Hash::make(Str::random(8));
        $user = $this->userRepository->create($data);
        if ($user) {
            $email = $user->email;
            $token = $this->createToken($email);
            Mail::to($email)->send(new CreateUserSuccess($user, $token));
            return back()->with('success', "Bạn đã thêm mới thành công");
        } else {
            return back()->with('error', "Thêm mới thất bại");
        }
    }

    public function validateData($data)
    {
        $countries = config('country');
        return Validator::make(
            $data,
            [
                'email'        => 'required|email|unique:users',
                'first_name'   => 'required|string|max:255',
                'last_name'    => 'required|string|max:255',
                'country'      => ['required', Rule::in(array_keys($countries))],
                'phone_number' => 'required|regex:/[0-9]{10,11}/',
                'ib_id'        => 'bail|required|regex:/[0-9]{6}/',
            ],
            [
                'ib_id.regex' => 'The IB ID has only 6 digits',
            ]
        );
    }

    private function createToken($email)
    {
        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $token = hash_hmac('sha256', Str::random(40), $key);
        $this->passwordResetRepository->updateOrCreate(
            ['email' => $email],
            [
                'token'      => $token,
                'email'      => $email,
                'created_at' => Carbon::now(),
            ]
        );
        return $token;
    }
}
