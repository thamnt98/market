<?php

namespace App\Http\Controllers\Admin\Agent;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{

    /**
     * @var AdminRepository
     */
    private $adminRepository;

    /**
     * LiveListController constructor.
     * @param \App\Repositories\AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function main(Request $request)
    {
        $search = $request->except('_token');
        $admin = Auth::user();
        $agents = $this->adminRepository->listAgentAdmin($search);
        $agentNoActives = $this->adminRepository->countStatusNoActive($admin->id);
        $agentManagers = $this->adminRepository->countAgentManager();
        $totalAgents = $this->adminRepository->totalAgent();
        $data = [];
        foreach($agents as $agent){
            $agent['count'] = $this->adminRepository->countAgentManager($agent['id']);
            $data[] = $agent;
        }
        return view('admin.agent.list', compact('data', 'search', 'agentNoActives', 'agentManagers', 'admin', 'totalAgents', 'agents'));
    }

    /**
     * list staff mannager
     */
    public function listStaffManager(Request $request)
    {
        $agentId = $request->id;
        $agentSearch = $this->adminRepository->findAgent($agentId);
        if(!$agentSearch){
            return redirect()->route('agent.list');
        }
        $search = $request->except('_token');
        $agents = $this->adminRepository->getListAgentOfManager($search, $agentId);
        return view('admin.agent.manager-staff', compact('agents', 'search', 'agentSearch', 'agentId'));
    }

    /**countStatusNoActive
     * list staff No Active
     */
    public function listStaffNoActive(Request $request)
    {
        $search = $request->except('_token');
        $admin = Auth::user();
        $agents = $this->adminRepository->listStatusNoActive($search);
        return view('admin.agent.agent-no-status', compact('agents', 'search', 'admin'));
    }
}
