<?php

namespace App\Jobs;

use App\GoogleScreencast;
use App\Models\MagentoFrontendDocumentation;
use App\UiDevice;
use App\UiDeviceHistory;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadGoogleDriveScreencast
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  privateGoogleScreencast|MagentoFrontendDocumentation  $googleScreencast
     * @param  private  $uploadedFile
     * @param  null|private  $permissionForAll
     * @param  null|private  $updatable
     * @return void
     */
    public function __construct(private GoogleScreencast|MagentoFrontendDocumentation $googleScreencast, private $uploadedFile, private $permissionForAll = null, private $updatable = null) {}

    /**
     * Execute the job.
     * Sample file link
     * https://docs.google.com/document/d/1O2nIeK9SOjn6ZKujfHdTkHacHnscjRKOG9G2OOiGaPU/edit
     */
    public function handle(): void
    {
        $client = new Client;
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        try {
            $createFile = $this->uploadScreencast(config('settings.google_screencast_folder'), $this->googleScreencast->read, $this->googleScreencast->write);
            $screencastId = $createFile->id;

            $this->googleScreencast->google_drive_file_id = $screencastId;
            $this->googleScreencast->save();

            if ($this->updatable != null) {
                $this->updateData($this->updatable, $screencastId);
            }
        } catch (Exception $e) {
            echo 'Message: '.$e->getMessage();
            dd($e);
        }
    }

    public function uploadScreencast($folderId, $googleFileUsersRead, $googleFileUsersWrite)
    {
        try {
            $client = new Client;
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile([
                'name' => $this->uploadedFile->getClientOriginalName(),
                'parents' => [$folderId],
            ]);
            $content = file_get_contents($this->uploadedFile->getRealPath());
            $file = $driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $this->uploadedFile->getClientMimeType(),
                'uploadType' => 'multipart',
                'fields' => 'id,parents,mimeType']);
            $index = 1;
            $driveService->getClient()->setUseBatch(true);

            if ($this->permissionForAll == 'anyone') {
                $batch = $driveService->createBatch();
                $userPermission = new Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ]);
                $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $results = $batch->execute();

                $batch = $driveService->createBatch();
                $userPermission = new Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'writer',
                ]);
                $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'user'.$index);
                $results = $batch->execute();
            } else {
                $batch = $driveService->createBatch();
                $googleFileUsersRead = explode(',', $googleFileUsersRead);
                foreach ($googleFileUsersRead as $email) {
                    $userPermission = new Drive\Permission([
                        'type' => 'user',
                        'role' => 'reader',
                        'emailAddress' => $email,
                    ]);

                    $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                    $batch->add($request, 'user'.$index);
                    $index++;
                }
                $results = $batch->execute();

                $batch = $driveService->createBatch();
                $googleFileUsersWrite = explode(',', $googleFileUsersWrite);

                foreach ($googleFileUsersWrite as $email) {
                    $userPermission = new Drive\Permission([
                        'type' => 'user',
                        'role' => 'writer',
                        'emailAddress' => $email,
                    ]);

                    $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                    $batch->add($request, 'user'.$index);
                    $index++;
                }
                $results = $batch->execute();
            }

            return $file;
        } catch (Exception $e) {
            echo 'Error Message: '.$e;
        }
    }

    public function updateData($data, $file_id)
    {
        try {
            foreach ($data as $class => $id) {
                $model = $class::find($id);

                switch ($class) {
                    case UiDevice::class:
                    case UiDeviceHistory::class:
                        $model->message = config('settings.google_drive_file_url').$file_id.'/view?usp=share_link';

                        $model->save();
                        break;

                    default:
                        break;
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => '500', 'message' => 'Error updating data'.$e->getMessage()]);
        }
    }
}
