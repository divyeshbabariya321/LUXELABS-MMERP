<?php

namespace App\Models;
use App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FaqPlatformDetails extends Model
{
    use HasFactory;

    public function getFaqPlatformId($id, $store_website_id, $storeValue, $type)
    {
        $result = $this->where(
            [
                'reply_id'         => $id,
                'type'             => $type,
                'store_website_id' => $store_website_id,
                'store_code'       => $storeValue,
            ])->first();

        if (! empty($result->platform_id)) {
            return $result->platform_id;
        }

        return false;
    }
}
