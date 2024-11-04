<?php

namespace App\Jobs;
use App\SearchAttachedImagesLog;
use App\Product;
use App\Http\Controllers\WhatsAppController;
use App\ChatMessage;

use Illuminate\Support\Facades\Auth;
use App\SuggestedProduct;
use App\SuggestedProductList;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Helpers\CompareImagesHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use App\Mediables;
use Plank\Mediable\Media;

class SearchAttachedImagesNew implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $first_time;

    protected $is_matched;

    protected $suggested_product;

    public $tries = 5;

    public $backoff = 5;

    public function __construct(protected $id, protected $url, protected $req_data)
    {
        $this->first_time        = true;
        $this->is_matched        = false;
        $this->suggested_product = false;
    }

    public function handle(): void
    {
        try {
            set_time_limit(0);

            $log_comment  = 'searchAttachedImage : ';
            $id           = $this->id;
            $ref_file     = str_replace('|', '/', $this->url);
            $ref_file     = str_replace("'", '', $ref_file);
            $params       = $this->req_data;
            $customer_id  = false;
            $chat_message = false;
            if (isset($params['customer_id'])) {
                $customer_id               = $params['customer_id'];
                $log_params['customer_id'] = $customer_id;
            } else {
                $chat_message = ChatMessage::where('id', $id)->first();
                if ($chat_message) {
                    $log_comment = $log_comment . 'Chat message found with id : ' . $chat_message->id . ', ';
                }
            }
            if (@file_get_contents($ref_file)) {
                $log_comment = $log_comment . 'File contents found for ' . $ref_file . ', ';

                $log_comment = $log_comment . 'Create image started for ' . $ref_file . ', ';
                $i1          = CompareImagesHelper::createImage($ref_file);
                $log_comment = $log_comment . 'Create image finished for ' . $ref_file . ', ';

                $log_comment = $log_comment . 'Resize image started for ' . $ref_file . ', ';
                $i1          = CompareImagesHelper::resizeImage($i1, $ref_file);
                $log_comment = $log_comment . 'Resize image finished for ' . $ref_file . ', ';

                $log_comment = $log_comment . 'Image filter started for ' . $ref_file . ', ';
                imagefilter($i1, IMG_FILTER_GRAYSCALE);
                $log_comment = $log_comment . 'Image filter finished for ' . $ref_file . ', ';

                $log_comment = $log_comment . 'Color mean value started for ' . $ref_file . ', ';
                $colorMean1  = CompareImagesHelper::colorMeanValue($i1);
                $log_comment = $log_comment . 'Color mean value finished for ' . $ref_file . ', ';

                $log_comment = $log_comment . 'Bits started for ' . $ref_file . ', ';
                $bits1       = CompareImagesHelper::bits($colorMean1);
                $log_comment = $log_comment . 'Bits finished for ' . $ref_file . ', ';

                $bits = implode($bits1);
                Media::whereNotNull('bits')->where('bits', '!=', 0)->where('bits', '!=', 1)->where('directory', 'like', '%product/%')->orderBy('id')->chunk(1000, function ($medias) use ($bits, $chat_message, $customer_id, $log_comment) {
                    if ($medias) {
                        $log_comment = $log_comment . 'Medias found with count : ' . count($medias) . ', ';
                    }
                    foreach ($medias as $k => $m) {
                        $hammeringDistance = 0;
                        $m_bits            = $m->bits;
                        for ($a = 0; $a < 64; $a++) {
                            if ($bits[$a] != $m_bits[$a]) {
                                $hammeringDistance++;
                            }
                        }
                        if ($hammeringDistance < 10) {
                            $this->is_matched = true;
                            if ($this->first_time) {
                                $this->suggested_product = SuggestedProduct::create([
                                    'total'           => 0,
                                    'customer_id'     => $chat_message ? $chat_message->customer_id : $customer_id,
                                    'chat_message_id' => $chat_message ? $chat_message->id : null,
                                ]);
                                $this->first_time = false;
                                $log_comment      = $log_comment . 'Suggested product created with customer_id : ' . ($chat_message ? $chat_message->customer_id : $customer_id . ' and chat_message_id : ' . ($chat_message ? $chat_message->id : 0 . ', '));
                            }

                            $mediable = Mediables::where('media_id', $m->id)->where('mediable_type', Product::class)->first();

                            if ($mediable) {
                                $log_comment               = $log_comment . 'Mediables found for media_id : ' . $m->id . ', ';
                                $log_params['customer_id'] = $chat_message ? $chat_message->customer_id : $customer_id;
                                SuggestedProductList::create([
                                    'customer_id'           => $chat_message ? $chat_message->customer_id : $customer_id,
                                    'product_id'            => $mediable->mediable_id,
                                    'media_id'              => $m->id,
                                    'chat_message_id'       => $chat_message ? $chat_message->id : null,
                                    'suggested_products_id' => $this->suggested_product !== null ? $this->suggested_product->id : null,
                                ]);
                                $log_comment = $log_comment . 'Suggested product list created for customer_id : ' . ($chat_message ? $chat_message->customer_id : $customer_id . ' and product_id : ' . $mediable->mediable_id . ', ');
                            }
                        }
                    }
                });
            }

            $user = Auth::user();
            if ($this->is_matched) {
                $log_comment = $log_comment . 'Image find process is completed, ';
                $msg         = 'Your image find process is completed.';
            } else {
                $log_comment = $log_comment . 'Image find process is completed, No results found, ';
                $msg         = 'Your image find process is completed, No results found';
            }

            $log_comment = $log_comment . 'Send with third party API initiated for number : ' . $user->phone . ', whatsapp number : ' . $user->whatsapp_number . ' and message : ' . $msg . ', ';
            app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);
            $log_comment = $log_comment . 'Send with third party API completed, ';

            $log_comment           = $log_comment . ' . ';
            $log_params['comment'] = $log_comment;

            if (! empty($log_params['comment']) && $log_params['comment'] != '') {
                $log_added = SearchAttachedImagesLog::create($log_params);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['search_images', $this->id];
    }
}
