<?php
namespace App\Services;

use App\Repositories\Interfaces\OrganizationRepositoryInterface;

class OrganizationService {

    public function __construct(
        protected OrganizationRepositoryInterface $organization_repository
    ){}

    /**
     * Creates a new Organization
     * @param Array $insert_data
     * @return Organization
     */
    public function create(Array $insert_data) {
        return $this->organization_repository->create($insert_data);
    }
}