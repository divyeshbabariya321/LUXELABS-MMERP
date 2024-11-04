<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Plank\Mediable\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3Controller extends Controller
{
    public function index(Request $request)
    {
        $files = Media::where('disk', 'like', 's3')->paginate(10);
        if ($request->ajax()) {
            return response()->view('s3-files.partials.file-list', ['files' => $files]);
        }

        return view('s3-files.index', ['files' => $files]);
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $media = Media::where('id', $request->get('mediaId', -1))->first();
            $path  = $media->directory . '/' . $media->filename . '.' . $media->extension;

            if (Storage::disk('s3')->delete($path)) {
                $media->delete();

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'File successfully deleted',
                ]);
            } else {
                return response()->json([
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'Failed to delete file',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 400,
                'status'  => 'error',
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function moveToGlacierS3(Request $request): JsonResponse
    {
        try {
            $media        = Media::where('id', $request->get('mediaId', -1))->first();
            $s3Path       = $media->directory . '/' . $media->filename . '.' . $media->extension;
            $fileContents = Storage::disk('s3')->get($s3Path);

            if ($fileContents) {
                $glacierClient = new \Aws\Glacier\GlacierClient([
                    'credentials' => [
                        'key'    => config('filesystems.disks.glacier.key'),
                        'secret' => config('filesystems.disks.glacier.secret'),
                    ],
                    'region'  => config('filesystems.disks.glacier.region'),
                    'version' => 'latest',
                ]);

                $result = $glacierClient->uploadArchive([
                    'vaultName'          => config('filesystems.disks.glacier.vault'),
                    'body'               => $fileContents,
                    'archiveDescription' => $s3Path,
                ]);

                if ($result) {
                    $media->glacier_archive_id = $result['archiveId'];
                    $media->save();

                    if (Storage::disk('s3')->delete($s3Path)) {
                        return response()->json([
                            'code'       => 200,
                            'status'     => 'success',
                            'message'    => 'File moved to Glacier and deleted from s3',
                        ]);
                    } else {
                        return response()->json([
                            'code'       => 400,
                            'status'     => 'error',
                            'message'    => 'Failed to delete file from s3',
                        ]);
                    }
                } else {
                    return response()->json([
                        'code'       => 400,
                        'status'     => 'error',
                        'message'    => 'Failed to upload to Glacier',
                    ]);
                }
            } else {
                return response()->json([
                    'code'       => 400,
                    'status'     => 'error',
                    'message'    => 'File not found on s3',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'code'       => 400,
                'status'     => 'error',
                'message'    => $th->getMessage(),
            ]);
        }
    }
}
