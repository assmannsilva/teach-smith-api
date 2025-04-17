<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Services\LogoFileProcessorService;
use App\Services\OrganizationService;

class OrganizationController extends Controller
{

    public function store(
        StoreOrganizationRequest $request,
        LogoFileProcessorService $logo_file_processor_service,
        OrganizationService $organization_service
    ) {
        
        $logo_file_processor_service->
        $organization_service->create([
            
        ]);
    }
}
