<?php

namespace App\Http\Controllers\Admin\Email;

use App\Http\Controllers\Controller;
use App\Mail\EmailMarketing;
use App\Mail\OpenLiveAccountSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SendEmailMarketingController extends Controller
{
    public function main(Request $request)
    {
        $status = 200;
        $data = $request->except('_token');
        $validateData = $this->validateData($data);
        try {
            if ($validateData->fails()) {
                $status = 400;
                $message = $validateData->errors();
            } else {
                Mail::bcc($data['users'])->queue(new EmailMarketing($data['template_email'], $data['title']));
                $message = 'Gửi email thành công';
            }
        } catch (\Exception $e) {
            $status = 417;
            $message = 'Gửi email thất bại ';
        }
        return json_encode([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function validateData($data)
    {
        return Validator::make(
            $data,
            [
                'template_email' => 'required',
                'title' => 'required|max:255',
                'users' => 'required|array',
            ]
        );
    }
}
