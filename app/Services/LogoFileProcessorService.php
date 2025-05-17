<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class LogoFileProcessorService {

    const IMAGE_LOGOS_STORE_PATH = 'logos/';
    const IMAGE_LOGO_SIZE = 300;

    /**
     * Retrieve the storage path for logo images.
     * @return String
     */
    private function getStoragePath(): String
    {
        return storage_path(self::IMAGE_LOGOS_STORE_PATH);
    }

    /**
     * Transform the uploaded file into a logo image.
     * @param UploadedFile $file_request
     * @return string $storage_path
     */
    public function processLogoImage(UploadedFile $file_request): String
    {
        $resized = Image::read($file_request)
        ->resize(self::IMAGE_LOGO_SIZE, self::IMAGE_LOGO_SIZE, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })
        ->toPng();

        $canvas = Image::create(self::IMAGE_LOGO_SIZE, self::IMAGE_LOGO_SIZE)
        ->fill('rgba(255, 255, 255, 0)') // transparente
        ->place($resized, 'center')
        ->toPng();

        $file_name = "logo_".time().".png";
        $storage_path = $this->getStoragePath().$file_name;
        
        $canvas->save($storage_path);

        return $storage_path;
    }

    /**
     * Delete the logo image from storage.
     * @param String $logo_path
     * @return bool
     */
    public function deleteLogoImage(String $logo_file_name): bool
    {
        $logo_path = $this->getStoragePath().$logo_file_name;
        if (file_exists($logo_path)) return unlink($logo_path);
        return false;
    }

}