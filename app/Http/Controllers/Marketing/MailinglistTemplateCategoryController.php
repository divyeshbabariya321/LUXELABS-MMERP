<?php

namespace App\Http\Controllers\Marketing;

use Illuminate\Http\JsonResponse;
use App\StoreWebsite;
use App\MailinglistTemplate;
use App\Http\Controllers\Controller;
use App\MailinglistTemplateCategory;
use App\Http\Requests\StoreMailingListTemplateCategoryRequest;

class MailinglistTemplateCategoryController extends Controller
{
    public function store(StoreMailingListTemplateCategoryRequest $request): JsonResponse
    {
        $logged_user = $request->user();

        $cid = MailinglistTemplateCategory::create([
            'title'   => $request->name,
            'user_id' => $logged_user->id,
        ]);
        $storeWebSites = StoreWebsite::get();
        $data          = [
            'store_website_id' => 0,
            'category_id'      => $cid->id,
        ];
        MailinglistTemplate::insert($data);
        foreach ($storeWebSites as $s) {
            $data = [
                'store_website_id' => $s->id,
                'category_id'      => $cid->id,
            ];
            MailinglistTemplate::insert($data);
        }

        return response()->json([
            'status' => true,
        ]);
    }
}
