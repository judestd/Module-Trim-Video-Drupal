<?php

namespace Drupal\trim_video;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\VideoEdit;

class CloudinaryService
{

    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key' => $_ENV['CLODINARY_API_KEY'],
                'api_secret' => $_ENV['CLODINARY_API_SECRET']
            ]
        ]);
    }

    public function uploadFile($file, $public_id)
    {
        $result = $this->cloudinary->uploadApi()->upload($file, [
            'public_id' => $public_id,
            'folder' => '/home',
            'resource_type' => 'video'
        ]);

        return json_decode(json_encode($result), true);
    }

    private function clearGetParam($str){
        return explode("?",$str)[0];
    }

    public function getThumbVideo($public_id) {
        $result = $this->cloudinary->image($public_id)->assetType("video")->__toString();
        return $this->clearGetParam($result).".jpg";
        // clear param get in url
    }

    public function getTrimVideo($public_id, $format, $duration = 5)
    {
        $result = $this->cloudinary
            ->video($public_id)
            ->videoEdit(VideoEdit::trim()
                ->duration($duration))
            ->toUrl()
            ->__toString() ;

        return $this->clearGetParam($result).".$format";
    }
}
