<?php
namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    /**
     * Get Profile of the authenticated user.
     * @param OrganizationService $organizationService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(OrganizationService $organizationService) : JsonResponse
    {
        $user = auth()->user();
        $organization = $organizationService->getCachedAutenticatedOrganization();
        return response()->json([
            'user' => $user,
            'organization' => $organization
        ]);
    }
    /**
     * Logout the user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy() : JsonResponse
    {
        Auth::guard('web')->logout();
        return response()->json([
            'success' => \true
        ]);
    }
}

?>