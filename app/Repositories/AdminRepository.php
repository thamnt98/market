<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\AdminCommission;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class AdminRepository extends EloquentBaseRepository implements RepositoryInterface
{
    const STATUS_NO_ACTIVE = 2;
    /**
     * @inheritDoc
     */
    public function model()
    {
        return Admin::class;
    }

    public function isActive($email)
    {
        return $this->where('email', $email)->first()->status;
    }

    public function login($credentials)
    {
        return Auth::attempt($credentials);
    }

    public function findAgent($agent_id)
    {
        return $this->where('id', $agent_id)->first();
    }

    /**
     * list agent and agent manager
     * @param array $search
     * @return mixed
     */
    public function getAgentList()
    {
        $query = $this->where('role', config('role.staff'));
        $user = Auth::user();
        if ($user->role == config('role.staff')) {
            $query = $query->where('admin_id', $user->id);
        }
        return $query->get(['id', 'name', 'email', 'phone_number', 'ib_id', 'status', 'admin_id']);
    }

    public function getManagerList()
    {
        return $this->whereNull('admin_id')->get(['id', 'name']);
    }

    public function activeAgent($id, $status)
    {
        return $this->update(['status' => $status], $id);
    }

    public function getAgentDetail($id)
    {
        return $this->find($id);
    }

    public function updateAgent($id, $data)
    {
        $user = $this->where('id', $id)->first();
        if ($data['role'] == 'standardStaff') {
            $this->where('admin_id', $user->id)->update(['admin_id' => $data['admin_id']]);
        }
        if (in_array($data['role'], ['standardManager', 'superManager', 'admin'])) {
            $data['admin_id'] = null;
        }
        else{
            Admin::where('admin_id', $id)->update(['admin_id' => 1]);
            Admin::where('super_manager_id', $id)->update(['super_manager_id' => null]);
        }
        $role = $data['role'];
        unset($data['role']);
        $agent = $this->update($data, $id);
        $permissions = Role::findByName($role)->permissions;
        $agent->syncPermissions($permissions);
        $agent->syncRoles($role);
        unset($data['role']);
        $commission = [
            'us_stock_commission' => $data['us_stock_commission'],
            'forex_commission' => $data['forex_commission'],
            'other_commission' => $data['other_commission'],
            'staff_us_stock_commission' => $data['staff_us_stock_commission'],
            'staff_forex_commission' => $data['staff_forex_commission'],
            'staff_other_commission' => $data['staff_other_commission'],
        ];
        AdminCommission::where('admin_id', $id)->update($commission);

    }

    public function changePassword($data)
    {
        return $this->where('email', $data['email'])->update(['password' => $data['password']]);
    }

    /**
     * get list agent of manager
     * @param int agent_id id of agent
     */
    public function getListAgentOfManager($search, $agent_id)
    {
        $query = $this->where('admin_id', $agent_id)
            ->where(function ($q) {
                $q->where('role', config('role.staff'));
            });
        if (!empty($search)) {
            if (isset($search['email']) && !is_null($search['email'])) {
                $query = $query->where('email', 'like', '%' . $search['email'] . '%');
            }
            if (isset($search['ib_id']) && !is_null($search['ib_id'])) {
                $query = $query->where('ib_id', 'like', '%' . $search['ib_id'] . '%');
            }
        }
        $query = $query->orderBy('created_at', 'desc');
        return $query->paginate(20);
    }

    /**
     * list agent admin
     * @return mixed
     */
    public function listAgentAdmin($search)
    {
        $query = $this->where('role', config('role.staff'));
        if (!empty($search)) {
            if (isset($search['email']) && !is_null($search['email'])) {
                $query = $query->where('email', 'like', '%' . $search['email'] . '%');
            }
            if (isset($search['ib_id']) && !is_null($search['ib_id'])) {
                $query = $query->where('ib_id', 'like', '%' . $search['ib_id'] . '%');
            }
        }
        $admin = Auth::user();
        if ($admin->role == config('role.admin')) {
            if (empty($search)) {
                $query = $query->where('admin_id', null)->orWhere('id', 1);
            }
        } else {
            $query = $query->where(function ($q) use ($admin){
                return $q->orWhere('admin_id', $admin->id)->orWhere('super_manager_id', $admin->id);
            });
        }
        $query = $query->orderBy('created_at', 'desc');
        return $query->paginate(20);
    }

    /**
     * count agent manager
     * @param int admin_id id of admin
     * @return count staff of manager
     */
    public function countAgentManager($admin_id = null)
    {
        $agentManagers = $this->where('admin_id', $admin_id)
            ->where(function ($q) {
                $q->where('role', config('role.staff'));
            })
            ->get();
            return count($agentManagers);
    }

    public function countManager($superManagerId)
    {
        $managers = $this->where('super_manager_id', $superManagerId)
            ->where(function ($q) {
                $q->where('role', config('role.staff'));
            })
            ->get();
        return count($managers);
    }

    /**
     * count agent no active
     * @param int admin_id id of admin
     * @return count agent no active
     */
    public function countStatusNoActive($admin_id = null)
    {
        $admin = Auth::user();
        if ($admin->role == config('role.admin')) {
            $agentNoActive = $this->where('status', self::STATUS_NO_ACTIVE)
                ->where(function ($q) {
                    $q->where('role', config('role.staff'));
                })
                ->get();
        } else {
            $agentNoActive = $this->where(function ($q) use ($admin_id) {
                    return $q->where('status', self::STATUS_NO_ACTIVE)
                        ->where('role', config('role.staff'))
                        ->orWhere('admin_id', $admin_id)
                        ->orWhere('super_manager_id', $admin_id);
                })
                ->get();
        }
        return count($agentNoActive);
    }

    /**
     * Total agent
     */
    public function totalAgent()
    {
        $admin = Auth::user();
        if ($admin->role == config('role.admin')) {
            $totalAgents = $this->where('role', config('role.staff'))->get();
        } else {
            $totalAgents = $this->where('role', config('role.staff'))
                ->where(function ($q) use ($admin) {
                   return $q->orWhere('admin_id', $admin->id)
                    ->orWhere('super_manager_id', $admin->id);
                })
                ->get();
        }
        return count($totalAgents);
    }

    /**
     * list status no active
     */
    public function listStatusNoActive($search)
    {
        $admin = Auth::user();
        if ($admin->role == config('role.admin')) {
            $agentNoActive = $this->where('status', self::STATUS_NO_ACTIVE)
                ->where(function ($q) {
                    $q->where('role', config('role.staff'));
                });
        } else {
            $agentNoActive = $this->where('admin_id', $admin->id)
                ->where(function ($q) {
                    $q->where('status', self::STATUS_NO_ACTIVE);
                    $q->where('role', config('role.staff'));
                });
        }
        if (!empty($search)) {
            if (isset($search['email']) && !is_null($search['email'])) {
                $agentNoActive = $agentNoActive->where('email', 'like', '%' . $search['email'] . '%');
            }
            if (isset($search['ib_id']) && !is_null($search['ib_id'])) {
                $agentNoActive = $agentNoActive->where('ib_id', 'like', '%' . $search['ib_id'] . '%');
            }
        }
        $agentNoActive = $agentNoActive->orderBy('created_at', 'desc');
        return $agentNoActive->paginate(20);
    }
}
