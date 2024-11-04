<?php

namespace App\Jobs;
use App\ProductUpdatedAttributeHistory;

use App\Product;
use App\Helpers\StatusHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttributeAssignment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param mixed $params
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->data = $params['data'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = $this->data['user_id'];
        if ($this->data['attribute_id'] == StatusHelper::$unknownSize) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('size', $this->data['find_size']);
            $find_products = $find_products->get();

            $attribute_arr = [];
            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $new_value = '';
                    $old_value = $fp_value->size;
                    if (isset($this->data['replace_size']) && is_array($this->data['replace_size'])) {
                        $new_value = implode(',', $this->data['replace_size']);
                    }
                    $fp_value->size                         = $new_value;
                    $fp_value->updated_attribute_job_status = 1;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value'      => $old_value,
                        'new_value'      => $new_value,
                        'attribute_name' => 'size',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (! empty($attribute_arr)) {
                $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownMeasurement) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('lmeasurement', $this->data['find_lmeasurement']);
            $find_products->where('hmeasurement', $this->data['find_hmeasurement']);
            $find_products->where('dmeasurement', $this->data['find_dmeasurement']);
            $find_products = $find_products->get();

            $attribute_arr = [];
            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $old_value_lmeasurement = $fp_value->lmeasurement;
                    $old_value_hmeasurement = $fp_value->hmeasurement;
                    $old_value_dmeasurement = $fp_value->dmeasurement;

                    $fp_value->lmeasurement                 = $this->data['replace_lmeasurement'];
                    $fp_value->hmeasurement                 = $this->data['replace_hmeasurement'];
                    $fp_value->dmeasurement                 = $this->data['replace_dmeasurement'];
                    $fp_value->updated_attribute_job_status = 1;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value'      => $old_value_lmeasurement,
                        'new_value'      => $this->data['replace_lmeasurement'],
                        'attribute_name' => 'lmeasurement',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];

                    $attribute_arr[] = [
                        'old_value'      => $old_value_hmeasurement,
                        'new_value'      => $this->data['replace_hmeasurement'],
                        'attribute_name' => 'hmeasurement',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];

                    $attribute_arr[] = [
                        'old_value'      => $old_value_dmeasurement,
                        'new_value'      => $this->data['replace_dmeasurement'],
                        'attribute_name' => 'dmeasurement',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (! empty($attribute_arr)) {
                $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownCategory) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('category', $this->data['find_category']);
            $find_products = $find_products->get();

            $attribute_arr = [];
            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $old_value                              = $fp_value->category;
                    $fp_value->category                     = $this->data['replace_category'];
                    $fp_value->updated_attribute_job_status = 1;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value'      => $old_value,
                        'new_value'      => $this->data['replace_category'],
                        'attribute_name' => 'category',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (! empty($attribute_arr)) {
                $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownColor) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            if ($this->data['find_color'] == 'NULL') {
                $find_products->where(function ($query) {
                    $query->where('color', null)
                        ->orWhere('color', '');
                });
            } else {
                $find_products->where('color', $this->data['find_color']);
            }
            $find_products = $find_products->get();
            $attribute_arr = [];
            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $old_value                              = $fp_value->color;
                    $new_value                              = ($this->data['replace_color'] != 'NULL') ? $this->data['replace_color'] : null;
                    $fp_value->color                        = $new_value;
                    $fp_value->updated_attribute_job_status = 1;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();

                    $attribute_arr[] = [
                        'old_value'      => $old_value,
                        'new_value'      => $new_value,
                        'attribute_name' => 'color',
                        'attribute_id'   => $this->data['attribute_id'],
                        'product_id'     => $fp_value->id,
                        'user_id'        => $userId,
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                }
            }

            if (! empty($attribute_arr)) {
                $productUpdatedAttributeHistory = ProductUpdatedAttributeHistory::insert($attribute_arr);
            }
        }
    }

    public function failed()
    {
        $userId = $this->data['user_id'];
        if ($this->data['attribute_id'] == StatusHelper::$unknownSize) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('size', $this->data['find_size']);
            $find_products = $find_products->get();

            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $fp_value->updated_attribute_job_status = 2;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();
                }
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownMeasurement) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('lmeasurement', $this->data['find_lmeasurement']);
            $find_products->where('hmeasurement', $this->data['find_hmeasurement']);
            $find_products->where('dmeasurement', $this->data['find_dmeasurement']);
            $find_products = $find_products->get();

            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $fp_value->updated_attribute_job_status = 2;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();
                }
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownCategory) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('category', $this->data['find_category']);
            $find_products = $find_products->get();

            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $fp_value->updated_attribute_job_status = 2;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();
                }
            }
        } elseif ($this->data['attribute_id'] == StatusHelper::$unknownColor) {
            $find_products = Product::where('status_id', $this->data['attribute_id']);
            $find_products->where('color', $this->data['find_color']);
            $find_products = $find_products->get();

            if (isset($find_products) && ! empty($find_products)) {
                foreach ($find_products as $fp_key => $fp_value) {
                    $fp_value->updated_attribute_job_status = 2;
                    $fp_value->updated_attribute_job_attempt_count += 1;
                    $fp_value->save();
                }
            }
        }
    }
}
