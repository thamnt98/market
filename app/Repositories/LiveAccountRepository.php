<?php

namespace App\Repositories;

use App\Helper\MT4Connect;
use App\Helper\MT5Helper;
use App\Models\Admin;
use App\Models\AdminCommission;
use App\Models\LiveAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class LiveAccountRepository extends EloquentBaseRepository implements RepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function model()
    {
        return LiveAccount::class;
    }

    public function deleteLiveAccountByUserId($userId)
    {
        $logins = $this->where('user_id', $userId)->pluck('login')->toArray();
        $message = MT4Connect::deleteMultiLiveAccount($logins);
        $this->where('user_id', $userId)->delete();
        return $message;
    }

    public function deleteByLogin($login)
    {
        $this->where('login', $login)->delete();
    }

    public function openLiveAccount($user, $data)
    {
        $params = [
            "Account" => 0,
            "ManagerIndex" => 101,
            "Agent" => $user->ib_id ?? 0,
            "First" =>  $user->first_name,
            "Last" => $user->last_name,
            "Group" => $data['group'],
            "Email" => $user->email,
            "Leverage" =>   $data['leverage'],
            "Country" =>  $user->country ?? '',
            'State' => $user->state ?? '',
            'City' => $user->city ?? '',
            'ZipCode' => $user->zip_code ?? '',
            'Address' => $user->address ?? ''
        ];
        $result = MT5Helper::openAccount($params);
        if ($result['ERR_MSG'] != null || $result['Account'] == 0) {
            return [];
        }
        $data['phone_number'] = $user->phone_number;
        $data['user_id'] = $user->id;
        $data['ib_id'] = $user->ib_id;
        $data['login'] = $result['Account'];
        LiveAccount::create($data);
        $data['password'] = $result['Pwd_Master'];
        return $data;
    }

    public function updateLiveAccount($id, $data)
    {
        $account = $this->find($id);
        $data['login'] = $account->login;
        if($data['group'] != $account->group){
            $param =[
                'Group' => $data['group']
            ];
            $result = MT5Helper::updateAccount('CHANGE_GROUP', $data['login'], $param);
            if(!$result->Result) return false;
        }
        if($data['leverage'] != $account->leverage){
            $param =[
                'Leverage' => $data['leverage']
            ];
            $result = MT5Helper::updateAccount('CHANGE_LEVERAGE', $data['login'], $param);
            if(!$result->Result) return false;
        }
        $data['phone_number'] = $data['phone'];
        $this->update($data, $id);
        return true;
    }

    public function getAccountListBySearch($search, $paginate = true)
    {
        $query = $this;
        $user = Auth::user();
        if ($user->role == config('role.staff') && !$user->hasAnyDirectPermission(['all.account.show', 'all.deposit.show', 'all.withdrawal.show'])) {
            $ibIds = [$user->ib_id];
            if (is_null($user->admin_id)) {
                $ibIdsOfStaff = Admin::where('admin_id', $user->id)->pluck('ib_id')->toArray();
                $ibIds = array_merge($ibIds, $ibIdsOfStaff);
            }
            $query = $query->whereIn('live_accounts.ib_id', $ibIds);
        }
        if (!empty($search)) {
            if (isset($search['login']) && !is_null($search['login'])) {
                $query = $query->where('login', 'like', '%' . $search['login'] . '%');
            }
            if (isset($search['ib_id']) && !is_null($search['ib_id'])) {
                $query = $query->where('ib_id', 'like', '%' . $search['ib_id'] . '%');
            }
            if (isset($search['email']) && !is_null($search['email'])) {
                $query = $query
                    ->join('users', 'live_accounts.user_id', '=', 'users.id')
                    ->where('users.email', 'like', '%' . $search['email'] . '%');
            }
        }
        if($paginate){
            return $query->orderBy('live_accounts.created_at', 'desc')->paginate(20, [
                'live_accounts.id',
                'live_accounts.login',
                'live_accounts.group',
                'live_accounts.leverage',
                'live_accounts.ib_id',
                'live_accounts.user_id',
            ]);
        }
        return $query->get('live_accounts.login');
    }

    public function getLoginsByAdmin($admin, $search = null)
    {
        $staffCommission = [];
        if(is_null($admin->admin_id)) {
            $staffCommission = AdminCommission::where('admin_id', $admin->id)->get(['staff_us_stock_commission', 'staff_forex_commission', 'staff_other_commission']);
        }
        $commission = AdminCommission::where('admin_id', $admin->id)->get(['us_stock_commission', 'forex_commission', 'other_commission']);
        $commission = array_values($commission->first()->toArray());
        if (count($staffCommission)){
            $staffCommission = array_values($staffCommission->first()->toArray());
        }
        if ($search) {
            $admin = Admin::where('ib_id', trim($search))->first();
            if (!$admin)
                return [];
        }
        if ($admin->role == config('role.staff')) {
            $logins = $this->where('ib_id', $admin->ib_id)->pluck('login')->toArray();
            $result = array_fill_keys($logins, $commission);
            if (is_null($admin->admin_id)) {
                $ibIds = Admin::where('admin_id', $admin->id)->pluck('ib_id')->toArray();
                $logins = array_merge($logins, $this->whereIn('ib_id', $ibIds)->pluck('login')->toArray());
                $result += array_fill_keys($logins, $staffCommission);
            }
        } else {
            $logins = $this->pluck('login')->toArray();
            $result = array_fill_keys($logins, $staffCommission);
        }
        return $result;
    }

    /**
     * list live account
     * @param int customer_id
     * @return mix
     */
    public function getListLogin($customer_id)
    {
        $result = [];
        $logins = $this->where('user_id', $customer_id)->get();
        foreach($logins as $login){
            $result[$login->login] = $login->login;
        }
        return $result;
    }

}
