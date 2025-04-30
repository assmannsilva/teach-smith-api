<?php
namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface {

    protected $modelClass = User::class;

    public function getExistingEmails(array $emails) : array
    {
        return $this->newQuery()
        ->whereIn('email', $emails)
        ->pluck("email")
        ->toArray();
    }
}