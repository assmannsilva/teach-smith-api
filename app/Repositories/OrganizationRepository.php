<?php

use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;

class OrganizationRepository implements OrganizationRepositoryInterface {

    protected $modelClass = Organization::class;

    /**
     * Find an organization by its ID.
     * @param String $id
     * @return Organization
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(String $id) : Organization
    {
        return $this->findById($id);
    }

    /**
     * Creates a new Organization
     * @param Array $insert_data
     * @return Organization
     */
    public function create(Array $insert_data) : Organization
    {
        return Organization::create($insert_data);
    }

}