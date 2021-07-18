<?php

namespace App\Repositories;

use \Prettus\Repository\Eloquent\BaseRepository as EloquentBaseRepository;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\PasswordReset;

/**
 * Class AdminRepository
 * @package App\Repositories
 */
class PasswordResetRepository extends EloquentBaseRepository implements RepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model()
    {
        return PasswordReset::class;
    }
}
