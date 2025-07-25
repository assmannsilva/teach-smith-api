<?php
namespace App\Services;

use App\Exceptions\InvalidStateRequestException;
use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class OrganizationService {

    public function __construct(
        protected OrganizationRepositoryInterface $organizationRepository,
        protected LogoFileProcessorService $logoFileProcessorService
    ){}

    /**
     * Creates a new Organization
     * @param String $organization_name
     * @param UploadedFile $logo_file
     * @return Organization
     */
    public function create(
        String $organization_name, 
        UploadedFile $logo_file,
    ) {
        $logo_path = $this->logoFileProcessorService->processLogoImage($logo_file);

        return $this->organizationRepository->create([
            'name' => $organization_name,
            'logo_url' => $logo_path,
        ]);
    }
    /**
     * Delete the logo image from storage
     * @param String|null $logo_path
     * @return void
     */
    public function deleteLogoImage(?string $logo_path): void
    {
        if ($logo_path) {
            $this->logoFileProcessorService->deleteLogoImage($logo_path);
        }
    }

    /**
     * Finds the organization from a crypted state from the external provider
     * @param string $crypted_encoded_state
     * @return Organization
     */
    public function findByCriptedState(string $crypted_encoded_state) : Organization
    {
        $state_decrypted = \json_decode(\base64_decode($crypted_encoded_state),\true);
        if(!$state_decrypted) throw new InvalidStateRequestException;

        return $this->organizationRepository->find($state_decrypted["organization_id"]);
    }

    /**
     * Get the cached organization in the current session
     * @param int $organization_id
     * @return Organization
     */
    public function getCachedAutenticatedOrganization() : Organization
    {
        $user = \auth()->user();
        $cache_key = 'organization_' . $user->organization_id;
        $organization = Cache::remember($cache_key, now()->addMinutes(60), function () use ($user) {
            return $user->organization;
        });
        return $organization;
    }
}