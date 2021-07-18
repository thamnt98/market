<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class CreateController extends Controller
{
    public function main()
    {
        $ibIds = Admin::whereNotNull('ib_id')->pluck('ib_id', 'email');
        return view('admin.user.create', compact('ibIds'));
    }
}
