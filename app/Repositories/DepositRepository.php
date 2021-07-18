<?php

namespace App\Repositories;

use App\Models\Order;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class DepositRepository extends EloquentBaseRepository implements RepositoryInterface
{
    const VN_TO_USD = 23000;
    /**
     * @inheritDoc
     */
    public function model()
    {
        return Order::class;
    }

    public function getDepositListBySearch($search)
    {
        $query = $this;
        if (isset($search['email']) && !is_null($search['email'])) {
            $query = $query->join('users', 'orders.user_id', '=', 'users.id')
                ->where('email', 'like', '%' . $search['email'] . '%');
        }
        if (isset($search['start_date']) && !is_null($search['start_date'])) {
            $query = $query->whereDate('orders.created_at', '>=', $search['start_date']);
        }
        if (isset($search['end_date']) && !is_null($search['end_date'])) {
            $query = $query->whereDate('orders.created_at', '<=', $search['end_date']);
        }
        return $query->orderBy('orders.created_at', 'desc')->paginate(20, ['orders.id', 'orders.user_id', 'orders.bank_name', 'orders.status',
            'orders.type', 'orders.amount_money', 'orders.created_at', 'orders.login', 'orders.usd']);
    }

    public function deleteDepositByUserId($userId)
    {
        $this->where('user_id', $userId)->delete();
    }

    /**
     * find orders
     * @param int $id
     * @return mix
     */
    public function findOrders($id)
    {
        $order = $this->where('id', $id)->first();
        if($order){
            return $order;
        }
        return null;
    }

    /**
     * change money
     */
    public function changeMoneyUsd($money)
    {
        $money = $money/(self::VN_TO_USD);
        return round($money, 2);
    }
}
