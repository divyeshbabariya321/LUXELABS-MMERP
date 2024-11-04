<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Media;
use App\Mediables;
use Illuminate\Database\Eloquent\Model;

class CroppedImageReference extends Model
{
    public function media(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'original_media_id');
    }

    public function newMedia(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'new_media_id');
    }

    public function getDifferentWebsiteImage($original_media_id)
    {
        return $this->where('original_media_id', $original_media_id)->get();
    }

    public function differentWebsiteImages(): HasMany
    {
        return $this->hasMany(self::class, 'original_media_id', 'original_media_id');
    }

    public function getDifferentWebsiteName($media_id)
    {
        $media = Mediables::select('tag')->where('media_id', $media_id)->first();
        if (! $media) {
            return 'Default';
        }
        if ($media->tag == 'gallery') {
            return 'Default';
        } else {
            $colorCode = str_replace('gallery_', '', $media->tag);
            $site      = StoreWebsite::select('title')->where('cropper_color', $colorCode)->first();
            if ($site) {
                return $site->title;
            } else {
                return 'Default';
            }
        }
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getProductIssueStatus($id)
    {
        $task = DeveloperTask::where('task', 'LIKE', '%' . $id . '%')->first();
        if ($task != null) {
            if ($task->status == 'done') {
                return '<p>Issue Resolved</p><button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="' . $task->id . '" title="Load messages"><img src="/images/chat.png" alt=""></button>';
            } else {
                return '<p>Issue Pending</p><button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="' . $task->id . '" title="Load messages"><img src="/images/chat.png" alt=""></button>';
            }
        } else {
            return 'No Issue Yet';
        }
    }

    public function httpRequestData(): HasOne
    {
        return $this->hasOne(CropImageGetRequest::class, 'product_id', 'product_id')->latest();
    }
}
