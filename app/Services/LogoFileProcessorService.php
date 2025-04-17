<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;

class LogoFileProcessorService {

    const IMAGE_LOGOS_STORE_PATH = 'logos/';
    /**
     * Transform the uploaded file into a logo image.
     * @param UploadedFile $file_request
     * @return string $storage_path
     */
    public function processLogoImage(UploadedFile $file_request): String
    {
        $image = Image::read($file_request)
        ->resize(null, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->toPng();

        $file_name = "logo_".time().".png";
        $storage_path = storage_path(self::IMAGE_LOGOS_STORE_PATH.$file_name);
        $image->save($storage_path, 100, Format::PNG);
        return $storage_path;
    }
}