<?php

namespace App\Console\Commands;

use App\CronJob;
use App\CronJobReport;
use App\ResourceCategory;
use App\ResourceImage;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Webklex\PHPIMAP\ClientManager;

class RecieveResourceImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recieves Resource Image and Category From Email';

    const CATEGORY_IMAGES = '/category_images';

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
            $cm = new ClientManager;
            $oClient = $cm->make([
                'host' => config('settings.imap_host_resourceimage'),
                'port' => config('settings.imap_port_resourceimage'),
                'encryption' => config('settings.imap_encryption_resourceimage'),
                'validate_cert' => config('settings.imap_validate_cert_resourceimage'),
                'username' => config('settings.imap_username_resourceimage'),
                'password' => config('settings.imap_password_resourceimage'),
                'protocol' => config('settings.imap_protocol_resourceimage'),
            ]);

            $oClient->connect();

            $folder = $oClient->getFolder('INBOX');

            $message = $folder->query()->unseen()->setFetchBody(true)->get()->all();
            if (count($message) == 0) {
                echo 'No New Mail Found';
                echo '<br>';
                exit();
            }

            foreach ($message as $messages) {
                if (session()->has('resource.image')) {
                    session()->forget('resource.image');
                }
                $subject = $messages->getSubject();
                $subject = strtolower($subject);
                $subject = explode(' ', $subject);
                //Getting Category
                foreach ($subject as $value) {
                    $category = ResourceCategory::where('parent_id', 0)->where('title', $value)->first();
                    if ($category != null) {
                        $categoryId = $category->id;
                        break;
                    } else {
                        $categoryId = '';
                    }
                }
                //Getting Sub Category
                foreach ($subject as $value) {
                    $subCategory = ResourceCategory::where('parent_id', '!=', 0)->where('title', $value)->first();
                    if ($subCategory != null) {
                        $subCategoryId = $subCategory->id;
                        break;
                    } else {
                        $subCategoryId = '';
                    }
                }
                //Fetching Images

                if ($messages->hasAttachments()) {
                    $aAttachment = $messages->getAttachments();
                    $aAttachment->each(function ($oAttachment) {
                        $name = $oAttachment->getName();
                        if (! file_exists(public_path(self::CATEGORY_IMAGES))) {
                            mkdir(public_path(self::CATEGORY_IMAGES), 0777, true);
                        }
                        $oAttachment->save(public_path(self::CATEGORY_IMAGES), $name);

                        session()->push('resource.image', $name);
                    });

                    $images = json_encode(session()->get('resource.image'));
                }

                //Getting URL
                $body = $messages->getHTMLBody(true);

                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $body, $match);

                if ($match != null && $match[0] != null && $match[0][0] != null) {
                    $url = $match[0][0];
                } else {
                    $url = '';
                }

                $description = strip_tags($body);

                $resourceimg = new ResourceImage;
                $resourceimg->cat_id = $categoryId;
                $resourceimg->sub_cat_id = $subCategoryId;
                $resourceimg->images = $images;
                $resourceimg->url = $url;
                $resourceimg->description = $description;
                $resourceimg->created_by = 'Email Reciever';
                $resourceimg->is_pending = 1;
                $resourceimg->save();
                echo 'Resource Image Saved';
                session()->forget('resource.image');
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
