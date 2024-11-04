<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CropImageGetRequest extends Model
{
    protected $guarded = [];

    public function requestData()
    {
        return $this->hasmany(CropImageHttpRequestResponse::class, 'crop_image_get_request_id', 'id');
    }
}
