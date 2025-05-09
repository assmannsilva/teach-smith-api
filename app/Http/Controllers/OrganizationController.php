<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrganizationController extends Controller
{
    public function store(
        StoreOrganizationRequest $request,
        OrganizationService $organization_service
    ) {
        $organization = null;
        try {
            $organization = $organization_service->create(
                $request->input('name'),
                $request->file('logo')
            );
            return response()->json([
                'message' => 'Organization created successfully',
                'data' => $organization
            ], 201);

        } catch (Throwable $th) {
            Log::error('Failed to create organization', [
                'exception' => $th,
            ]);

            $organization_service->deleteLogoImage($organization?->logo_path);

            return response()->json([
                'message' => 'Failed to create organization',
                'error' => 'An error occurred while creating the organization.'
            ], 500);
        }
    }
}