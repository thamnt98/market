<?php

namespace App\Repositories;

use App\Models\Role;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class RoleRepository extends EloquentBaseRepository implements RepositoryInterface
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Role::class;
    }
}
