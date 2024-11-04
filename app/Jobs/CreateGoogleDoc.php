<?php

namespace App\Jobs;

use App\GoogleDoc;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateGoogleDoc
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param private GoogleDoc $googleDoc
     * @param  null|private  $permissionForAll
     * @return void
     */
    public function __construct(private GoogleDoc $googleDoc, private $permissionForAll = null) {}

    /**
     * Execute the job.
     * Sample doc link
     * https://docs.google.com/document/d/1O2nIeK9SOjn6ZKujfHdTkHacHnscjRKOG9G2OOiGaPU/edit
     */
    public function handle(): void
    {
        $client = new Client;
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $docMimeType = $this->googleDoc->type == 'ppt' ? 'application/vnd.google-apps.presentation' : 'application/vnd.google-apps.document';
        try {
            $createFile = $this->createDriveFile(config('settings.google_shared_folder'), $this->googleDoc->read, $this->googleDoc->write, $docMimeType);
            $spreadsheetId = $createFile->id;

            $this->googleDoc->docId = $spreadsheetId;
            $this->googleDoc->save();
        } catch (Exception $e) {
            echo 'Message: '.$e->getMessage();
            dd($e);
        }
    }

    public function moveFileToFolder($fileId, $folderId)
    {
        try {
            $client = new Client;
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $emptyFileMetadata = new DriveFile;
            // Retrieve the existing parents to remove
            $file = $driveService->files->get($fileId, ['fields' => 'parents']);

            $previousParents = implode(',', $file->parents);

            // Move the file to the new folder
            $file = $driveService->files->update($fileId, $emptyFileMetadata, [
                'addParents' => $folderId,
                'removeParents' => $previousParents,
                'fields' => 'id, parents', ]);

            return $file->parents;
        } catch (Exception $e) {
            echo 'Error Message: '.$e;
        }
    }

    public function createDriveFile($folderId, $googleDocUsersRead, $googleDocUsersWrite, $docMimeType)
    {
        try {
            $client = new Client;
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile([
                'name' => $this->googleDoc->name,
                'parents' => [$folderId],
                'mimeType' => $docMimeType,
            ]);

            $file = $driveService->files->create($fileMetadata, [
                'fields' => 'id,parents,mimeType',
            ]);
            $index = 1;
            $driveService->getClient()->setUseBatch(true);

            if ($this->permissionForAll == 'anyone') {
                $batch = $driveService->createBatch();
                $userPermission = new Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ]);
                $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'full-access');
                $results = $batch->execute();

                $batch = $driveService->createBatch();
                $userPermission = new Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'writer',
                ]);
                $request = $driveService->permissions->create($file->id, $userPermission, ['fields' => 'id']);
                $batch->add($request, 'full-access');
                $results = $batch->execute();
            } else {
                $batch = $driveService->createBatch();
                $googleDocUsersRead = explode(',', $googleDocUsersRead);

                foreach ($googleDocUsersRead as $email) {
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
                $googleDocUsersWrite = explode(',', $googleDocUsersWrite);

                foreach ($googleDocUsersWrite as $email) {
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
            dd($e);
        }
    }

    public function insertPermission($fileId, $value, $type, $role)
    {
        $service = $this->service;
        $newPermission = new \Google_Service_Drive_Permission;
        $newPermission->setType($type);
        $newPermission->setRole($role);

        try {
            return $service->permissions->create($fileId, $newPermission);
        } catch (Exception $e) {
            echo 'An error occurred: '.$e->getMessage();
        }

        return null;
    }
}
