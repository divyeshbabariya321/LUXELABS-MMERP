<?php

namespace App\Http\Controllers;
use App\StoreSocialContent;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Exception;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $user          = $request->user();
        $used_space    = $request->user()->getMedia('instagram')->count();
        $storage_limit = 0;

        return view('instagram.media.index', compact(
            'user',
            'used_space',
            'storage_limit'
        ));
    }

    public function files(Request $request): JsonResponse
    {
        $item     = [];
        $allMedia = $request->user()->getMedia('instagram');

        foreach ($allMedia as $media) {
            $item[] = ['id' => $media->id, 'file_name' => $media->filename, 'mime_type' => $media->mime_type, 'size' => $media->size, 'thumb' => getMediaUrl($media), 'original' => getMediaUrl($media), 'sitename' => ''];
        }

        $websites = StoreSocialContent::all();

        foreach ($websites as $website) {
            $webMedia = $website->getMedia(config('constants.media_tags'));

            foreach ($webMedia as $media) {
                $item[] = ['id' => $media->id, 'file_name' => $media->filename, 'mime_type' => $media->mime_type, 'size' => $media->size, 'thumb' => getMediaUrl($media), 'original' => getMediaUrl($media), 'sitename' => $website->website->title];
            }
        }

        return response()->json($item);
    }

    public function upload(Request $request): JsonResponse
    {
        $validMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/x-flv',
            'video/mp4',
            'video/3gpp',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
        ];

        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimetypes:' . implode(',', $validMimeTypes),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('The files must be a file of type: ') . implode(', ', $validMimeTypes),
            ]);
        } else {
            try {
                $used_space    = 0;
                $storage_limit = 100;

                if ($used_space <= $storage_limit) {
                    $files = $request->file('files');
                    $user  = $request->user();
                    foreach ($files as $file) {
                        $savedMedia = MediaUploader::fromSource($file)->toDirectory('instagram-media')->upload();
                        $user->attachMedia($savedMedia, 'instagram');
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Exceed storage limit',
                    ]);
                }
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            if ($request->filled('id')) {
                $request->user()->media()->whereIn('id', $request->id)->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No media ID specified',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
