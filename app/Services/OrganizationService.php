<?php
namespace App\Services;

use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use Illuminate\Http\UploadedFile;

class OrganizationService {

    public function __construct(
        protected OrganizationRepositoryInterface $organization_repository,
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

        return $this->organization_repository->create([
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
     * Finds the user from a crypted state from the external provider
     * @param string $crypted_encoded_state
     * @return Organization
     */
    public function findByCriptedState(string $crypted_encoded_state) : Organization
    {
        $state_decrypted = \json_decode(\base64_decode($crypted_encoded_state),\true);
        if(!$state_decrypted) throw new ErrorException("teste");

        return $this->organization_repository->find($state_decrypted["organization_id"]);
    }

    
}