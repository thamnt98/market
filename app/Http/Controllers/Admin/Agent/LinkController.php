<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function main()
    {
        $ibId = Auth::user()->ib_id;
        return view('admin.agent.customer_link', compact('ibId'));
    }
}
