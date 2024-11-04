<?php

namespace App\Console\Commands\Manual;

use App\CronJob;
use App\CronJobReport;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordChangeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:change-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change all passwords and output the new passwords';

    /**
     * Create a new command instance.
     */

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            // Get all users
            $users = User::all();

            // Delete all existing sessions
            $folder = storage_path('framework/sessions');

            //Get a list of all of the file names in the folder.
            $files = glob($folder.'/*');

            //Loop through the file list.
            foreach ($files as $file) {
                //Make sure that this is a file and not a directory.
                if (is_file($file)) {
                    //Use the unlink function to delete the file.
                    unlink($file);
                }
            }

            // Loop over users
            if ($users !== null) {
                foreach ($users as $user) {
                    // Generate new password
                    $newPassword = Str::random(12);

                    // Set hash password
                    $hashPassword = Hash::make($newPassword);

                    // Update password
                    $user->password = $hashPassword;
                    $user->save();

                    // Output new ones
                    echo $user->name."\t".$user->email."\t".$newPassword."\n";
                }
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
