<?php

namespace App\Jobs;
use App\ProductColorHistory;

use Illuminate\Support\Facades\Log;
use App\ScrapedProducts;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

class UpdateProductColorFromErp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $from;

    public $to;

    public $user_id;

    public $tries = 3;

    public $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param public $params
     *
     * @return void
     */
    public function __construct(public $params)
    {
        $this->from    = $params['from'];
        $this->to      = $params['to'];
        $this->user_id = isset($params['user_id']) ? $params['user_id'] : 6;
    }

    public static function putLog($message)
    {
        Log::channel('update_color_job')->info($message);

        return true;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            self::putLog('Job update product color from erp start time : ' . date('Y-m-d H:i:s'));

            $affectedProducts = ScrapedProducts::matchedColors($this->from);

            if (! empty($affectedProducts)) {
                foreach ($affectedProducts as $affectedProduct) {
                    $affectedProduct->color = $this->to;
                    $affectedProduct->save();
                    // do entry for the history as well
                    $productColHis             = new ProductColorHistory;
                    $productColHis->user_id    = ($this->user_id) ? $this->user_id : 6;
                    $productColHis->color      = ! empty($this->to) ? $this->to : '';
                    $productColHis->old_color  = ! empty($this->from) ? $this->from : '';
                    $productColHis->product_id = $affectedProduct->id;
                    $productColHis->save();
                }
            }

            self::putLog('Job update product color from erp end time : ' . date('Y-m-d H:i:s'));

            return;
        } catch (Exception $e) {
            self::putLog('Job update product color from erp end time : ' . date('Y-m-d H:i:s') . ' => ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public function tags()
    {
        return ['supplier_products', $this->user_id];
    }
}
