<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentLinkController extends Controller
{
    public function main()
    {
        $adminId = Auth::user()->id;
        return view('admin.agent.link', compact('adminId'));
    }
}
