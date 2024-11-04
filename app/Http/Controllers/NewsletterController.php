<?php

namespace App\Http\Controllers;

use App\Language;
use App\Newsletter;
use App\NewsletterProduct;
use App\Product;
use App\Services\CommonGoogleTranslateService;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    const GALLERY_TAG_NAME = 'gallery';

    public function index(Request $request): View
    {
        $title = 'Newsletter';
        $store_websites = null;

        return view('newsletter.index', compact(['title', 'store_websites']));
    }

    public function reviewTranslate(Request $request, $language = '')
    {
        $title = 'Newsletter - Review Translate:'.$language;
        $languagesList = Language::pluck('name', 'name')->toArray();
        if (! empty($languagesList) && $language == '') {
            $first = reset($languagesList);

            return redirect()->route('newsletters.review.translate', ['language' => $first]);
        }

        $languages = Language::pluck('locale', 'code')->toArray();
        $storeWebsites = StoreWebsite::all()->pluck('website', 'id');

        return view('newsletter.review-translate', compact(['title', 'storeWebsites', 'languagesList']));
    }

    public function records(Request $request): JsonResponse
    {
        $records = Newsletter::join('newsletter_products as np', 'newsletters.id', 'np.newsletter_id')
            ->leftJoin('users as u', 'u.id', 'newsletters.updated_by')
            ->leftJoin('mailinglists as m', 'm.id', 'newsletters.mail_list_id');

        $keyword = request('keyword');

        if (! empty($keyword)) {
            $records = $records->where(function ($q) use ($keyword) {
                $q->where('np.product_id', 'LIKE', "%$keyword%");
            });
        }

        if ($request->language != null) {
            $records = $records->where('newsletters.language', $request->language);
            $records = $records->where('newsletters.is_flagged_translation', 1);
        }

        $dateFrom = request('date_from');
        if ($dateFrom != null) {
            $records = $records->where('newsletters.created_at', '>=', $dateFrom);
        }

        $dateTo = request('date_to');
        if ($dateTo != null) {
            $records = $records->where('newsletters.created_at', '<=', $dateTo);
        }

        $sent_at = request('send_at');
        if ($sent_at != null) {
            $records = $records->whereDate('newsletters.sent_at', '=', $sent_at);
        }

        $records = $records->groupBy('newsletters.id')->select(['newsletters.*', 'u.name as updated_by_name', 'm.name as mailinglist_name'])->latest()->paginate();

        $items = [];

        foreach ($records->items() as &$rec) {
            $images = [];
            if (! $rec->newsletterProduct->isEmpty()) {
                foreach ($rec->newsletterProduct as $nwP) {
                    if ($nwP->product) {
                        $media = $nwP->product->getMedia(config('constants.attach_image_tag'))->first();
                        if ($media) {
                            $images[] = [
                                'url' => getMediaUrl($media),
                                'id' => $nwP->id,
                                'product_id' => $nwP->product->id,
                            ];
                        }
                    }
                }
            }
            $rec->product_images = $images;
            $rec->store_websiteName = ($rec->storeWebsite) ? $rec->storeWebsite->website : '';
            $rec->mailinglist_template_name = ($rec->mailinglistTemplate) ? $rec->mailinglistTemplate->name : '';
            if ($request->language != null) {
                $rec->original_newsletter = Newsletter::where('id', $rec->translated_from)->first();
            }
            $items[] = $rec;
        }

        return response()->json(['code' => 200, 'data' => $items, 'total' => $records->total(), 'pagination' => (string) $records->render()]);
    }

    public function save(Request $request): RedirectResponse
    {
        $productIds = json_decode($request->get('images'), true);

        $errorMessage = [];
        $needToSave = [];

        if (! empty($productIds)) {
            foreach ($productIds as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $needToSave[] = $product->id;
                } else {
                    $errorMessage[] = "Product not found : {$productId}";
                }
            }
        }

        if (count($needToSave) > 0) {
            $newsletter = new Newsletter;
            $newsletter->subject = 'DRAFT';
            $newsletter->language = 'English';
            $newsletter->updated_by = auth()->user()->id;
            if ($newsletter->save()) {
                foreach ($needToSave as $ns) {
                    $nProduct = new NewsletterProduct;
                    $nProduct->product_id = $ns;
                    $nProduct->newsletter_id = $newsletter->id;
                    $nProduct->save();
                }
            }
        }

        if (count($errorMessage) > 0) {
            return redirect()->route('newsletters.index')->withError('There was some issue for given products : '.implode('<br>', $errorMessage));
        }

        return redirect()->route('newsletters.index')->withSuccess('You have successfully added newsletter products!');
    }

    public function store(Request $request): JsonResponse
    {
        $post = $request->all();

        $validator = Validator::make($post, [
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $id = $request->get('id', 0);

        $records = Newsletter::find($id);

        if (! $records) {
            $records = new Newsletter;
        }

        $records->fill($post);
        $records->save();

        return response()->json(['code' => 200, 'data' => $records]);
    }

    /**
     * Edit Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $newsletter = Newsletter::where('id', $id)->first();

        if ($newsletter) {
            $newsletter->newsletterProduct;

            return response()->json(['code' => 200, 'data' => $newsletter]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong row id!']);
    }

    /**
     * delete Page
     *
     * @param  Request  $request  [description]
     * @param  mixed  $id
     */
    public function delete(Request $request, $id): JsonResponse
    {
        $newsletter = Newsletter::where('id', $id)->first();

        if ($newsletter) {
            $newsletter->newsletterProduct()->delete();
            $newsletter->delete();

            return response()->json(['code' => 200]);
        }

        return response()->json(['code' => 500, 'error' => 'Wrong row id!']);
    }

    public function deleteImage($id): JsonResponse
    {
        $newsletterProduct = NewsletterProduct::find($id);

        if ($newsletterProduct) {
            $newsletterProduct->delete();
        }

        return response()->json(['code' => 200, 'message' => 'Deleted successfully']);
    }

    public function preview(Request $request, $id)
    {
        $newsletter = Newsletter::find($id);

        if ($newsletter) {
            $template = $newsletter->mailinglistTemplate;
            if ($template) {
                $products = $newsletter->products;
                if (! $products->isEmpty()) {
                    foreach ($products as $product) {
                        if ($product->hasMedia(config('constants.attach_image_tag'))) {
                            foreach ($product->getMedia(config('constants.attach_image_tag')) as $image) {
                                $product->images[] = getMediaUrl($image);
                            }
                        }
                    }
                }

                echo view($template->mail_tpl, compact('products', 'newsletter'));
            }
        }

        echo 'No Preview found';
        exit;
    }

    public function translate($id, Request $request): JsonResponse
    {
        $newsletter = Newsletter::find($id);

        if ($newsletter) {
            if (is_null($newsletter->translated_from)) {
                if (is_null($newsletter->language)) {
                    $newsletter->language = 'English';
                    $newsletter->save();
                }
                $products = [];
                if (! $newsletter->products->isEmpty()) {
                    $products = $newsletter->products->pluck('id')->toArray();
                }

                $languages = Language::where('status', 1)->get();
                foreach ($languages as $l) {
                    if (strtolower($newsletter->language) != strtolower($l->name)) {
                        $newsletterExist = Newsletter::where('translated_from', $newsletter->id)->where('store_website_id', $newsletter->store_website_id)->where('language', $l->name)->first();
                        if (! $newsletterExist) {
                            $newNewsletter = new Newsletter;
                        } else {
                            $newNewsletter = Newsletter::find($newsletterExist->id);
                        }

                        $subject = GoogleTranslateController::translateProducts(
                            new CommonGoogleTranslateService,
                            $l->locale,
                            [$newsletter->subject]
                        );
                        $newNewsletter->subject = ! empty($subject) ? $subject : $newsletter->subject;
                        $newNewsletter->language = $l->name;
                        $newNewsletter->translated_from = $newsletter->id;
                        $newNewsletter->store_website_id = $newsletter->store_website_id;
                        $newNewsletter->sent_at = $newsletter->sent_at;
                        $newNewsletter->sent_on = $newsletter->sent_on;
                        $newNewsletter->mail_list_id = $newsletter->mail_list_id;
                        $newNewsletter->mail_list_temp_id = $newsletter->mail_list_temp_id;
                        $newNewsletter->updated_by = auth()->user()->id;
                        $newNewsletter->is_flagged_translation = 1;
                        $newNewsletter->save();
                        activity()->causedBy(auth()->user())->performedOn($newsletter)->log('newsletter '.$newsletter->id.' translated to '.$l->name);
                        if (! empty($products)) {
                            $newNewsletter->products()->sync($products);
                        }
                    }
                }

                return response()->json(['code' => 200, 'message' => 'Newsletter translated successfully']);
            } else {
                return response()->json(['code' => 500, 'message' => 'This Newsletter is already translated from other']);
            }
        }

        return response()->json(['code' => 500, 'message' => 'Newsletter Not found!']);
    }

    public function getTranslatedTextScore(Request $request, $id): JsonResponse
    {
        $newsletter = Newsletter::where('id', $id)->first();
        if ($newsletter) {
            $originalData = Newsletter::where('id', $newsletter->translated_from)->first();
            if ($originalData) {
                $subjectScore = app('translation-lambda-helper')->getTranslateScore($originalData->subject, $newsletter->subject);

                $newsletter->subject_score = ($subjectScore != 0) ? $subjectScore : 0.1;
                $newsletter->save();

                return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
            }
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong Newslatter id!']);
        }
    }

    public function getMultiTranslatedTextScore(Request $request): JsonResponse
    {
        $newsletters = Newsletter::whereIn('id', $request->ids)->whereNull('subject_score')->whereNotNull('translated_from')->get();
        if (! empty($newsletters) && count($newsletters) > 0) {
            foreach ($newsletters as $news) {
                $originalData = Newsletter::where('id', $news->translated_from)->first();
                if ($originalData) {
                    $subjectScore = app('translation-lambda-helper')->getTranslateScore($originalData->subject, $news->subject);

                    $news->subject_score = ($subjectScore != 0) ? $subjectScore : 0.1;
                    $news->save();
                }
            }

            return response()->json(['code' => 200, 'success' => 'Success', 'message' => 'Success']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Wrong Newslatter id!']);
        }
    }
}
