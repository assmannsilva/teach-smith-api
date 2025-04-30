<?php
namespace App\Repositories;

use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\BaseRepository;

class OrganizationRepository extends BaseRepository implements OrganizationRepositoryInterface {

    protected $modelClass = Organization::class;
}