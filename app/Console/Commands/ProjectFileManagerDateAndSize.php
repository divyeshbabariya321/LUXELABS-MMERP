<?php

namespace App\Console\Commands;

use App\Http\Controllers\WhatsAppController;
use App\ProjectFileManager;
use App\ProjectFileManagerHistory;
use App\Setting;
use App\User;
use Illuminate\Console\Command;

class ProjectFileManagerDateAndSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:filemanagementdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Its For Local Part where we run this on local and send the data to whatsapp and server';

    /**
     * Create a new command instance.
     */
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fileInformation = ProjectFileManager::all();
        $param = [];

        foreach ($fileInformation as $val) {
            $path = base_path().DIRECTORY_SEPARATOR.(str_replace('./', '', $val->name));

            if (is_dir($path)) {
                if (file_exists($path)) {
                    $old_size = $val->size;

                    $limit_data = Setting::get('project_file_managers');

                    if ($limit_data) {
                        $limit_rec = $limit_data;
                    } else {
                        $limit_rec = 10;
                    }

                    $increase_size = (($old_size * $limit_rec) / 100);

                    $id = $val->id;
                    $name = $val->name;

                    $io = popen('/usr/bin/du -sk '.$path, 'r');
                    $size = fgets($io, 4096);
                    $new_size = substr($size, 0, strpos($size, "\t"));

                    $new_size = round($new_size, 2);
                    pclose($io);
                    if ($old_size != $new_size) {
                        ProjectFileManager::where(['id' => $id])->update(['size' => $new_size]);

                        $param = [
                            'project_id' => $id,
                            'name' => $name,
                            'old_size' => $old_size.'MB',
                            'new_size' => $new_size.'MB',
                        ];

                        ProjectFileManagerHistory::create($param);
                    }

                    $both_size = ($old_size + $increase_size);

                    if ($new_size >= $both_size) {
                        $message = 'Project Directory Size increase in Path = '.$name.','.' OldSize = '.$old_size.'MB'.' And '.'NewSize = '.$new_size.'MB';

                        $users = User::get();
                        foreach ($users as $user) {
                            if ($user->isAdmin()) {
                                app(WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, $message);
                                $this->info('message successfully send');
                            }
                        }

                        ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 1]);
                    } else {
                        ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 0]);
                    }
                }
            } else {
                if (file_exists($path)) {
                    $old_size = $val->size;
                    $limit_data = Setting::get('project_file_managers');

                    if ($limit_data) {
                        $limit_rec = $limit_data;
                    } else {
                        $limit_rec = 10;
                    }

                    $increase_size = (($old_size * $limit_rec) / 100);
                    $id = $val->id;
                    $name = $val->name;

                    $new_size = filesize($path) / 1024;
                    $new_size = round($new_size, 2);

                    if ($old_size != $new_size) {
                        $updatesize = ProjectFileManager::where(['id' => $id])->update(['size' => $new_size]);

                        $param = [
                            'project_id' => $id,
                            'name' => $name,
                            'old_size' => $old_size.'MB',
                            'new_size' => $new_size.'MB',
                        ];

                        ProjectFileManagerHistory::create($param);
                    }

                    $both_size = ($old_size + $increase_size);

                    if ($new_size > $both_size) {
                        $message = 'Project Directory Size increase in Path = '.$name.','.' OldSize = '.$old_size.'MB'.' And '.'NewSize = '.$new_size.'MB';

                        $users = User::get();
                        foreach ($users as $user) {
                            if ($user->isAdmin()) {
                                app(WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, $message);
                                $this->info('message successfully send');
                            }
                        }
                        $updatesize = ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 1]);
                    } else {
                        $updatesize = ProjectFileManager::where(['id' => $id])->update(['display_dev_master' => 0]);
                    }

                    if (is_numeric($new_size)) {
                        $new_size = number_format($new_size / 1024, 2, '.', '');
                    }

                    $fileInformation->size = $new_size;
                }
            }
        }
        $this->info('success');
    }
}
