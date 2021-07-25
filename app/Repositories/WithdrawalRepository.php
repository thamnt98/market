<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use \Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\WithdrawalFund;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class WithdrawalRepository extends EloquentBaseRepository implements RepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model()
    {
        return WithdrawalFund::class;
    }

    protected $liveAccountRepository;

    public function __construct(Application $app, LiveAccountRepository $liveAccountRepository)
    {
        parent::__construct($app);
        $this->liveAccountRepository = $liveAccountRepository;
    }
    public function getWithdrawalByLogin($login)
    {
        return $this->where('login', $login)->get();
    }

    public function getWithdrawalListBySearch($search){
        $query = $this;
        $logins = $this->liveAccountRepository->getAccountListBySearch([], false);
        $query = $this->whereIn('login', $logins);
        if (isset($search['email']) && !is_null($search['email'])) {
            $query = $query->join('users', 'withdrawal_funds.user_id', '=', 'users.id')
                ->where('users.email', 'like', '%' . $search['email'] . '%');
        }
        if (isset($search['login']) && !is_null($search['login'])) {
            $query = $query->where('withdrawal_funds.login', 'like', '%' . $search['login'] . '%');
        }
        if (isset($search['start_date']) && !is_null($search['start_date'])) {
            $query = $query->whereDate('withdrawal_funds.created_at', '>=', $search['start_date']);
        }
        if (isset($search['end_date']) && !is_null($search['end_date'])) {
            $query = $query->whereDate('withdrawal_funds.created_at', '<=', $search['end_date']);
        }
        return $query->orderBy('withdrawal_funds.created_at', 'desc')->paginate(20, 'withdrawal_funds.*');
    }

    public function deleteWithdrawalByUserId($userId)
    {
        $this->where('user_id', $userId)->delete();
    }

    /**
     * find withdrawalfun
     * @param int $id
     * @return mix
     */
    public function findWithDrawalFun($id)
    {
        $result = $this->where('id', $id)->first();
        if($result){
            return $result;
        }
        return null;
    }

}