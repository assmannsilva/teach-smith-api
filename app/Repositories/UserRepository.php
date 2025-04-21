<?php
namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface {

    protected $modelClass = User::class;

    /**
     * Find an user by its ID.
     * @param String $id
     * @return Organization
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(String $id) : User
    {
        return $this->findById($id);
    }

    /**
     * Creates a new User
     * @param Array $insert_data
     * @return User
     */
    public function create(Array $insert_data) : User
    {
        return User::create($insert_data);
    }

}