<?php

namespace App\Repositories\Interfaces;

use App\Models\Organization;

interface OrganizationRepositoryInterface {
    
    /**
     * Find an organization by its ID.
     * @param String $id
     * @return Organization
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(String $id) : Organization;

    /**
     * Creates a new Organization
     * @param Array $insert_data
     * @return Organization
     */
    public function create(Array $insert_data) : Organization;
}