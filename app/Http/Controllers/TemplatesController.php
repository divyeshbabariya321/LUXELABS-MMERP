<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Brand;
use App\Product;
use App\Setting;
use App\Category;
use App\Template;
use App\LogRequest;
use App\ProductTemplate;
use Plank\Mediable\Media;
use Illuminate\Support\Str;
use App\Mediables;
use Illuminate\Http\Request;
use App\Helpers\GuzzleHelper;
use Illuminate\Support\Facades\Http;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Illuminate\Support\Facades\Log;
use Exception;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $templates = Template::orderByDesc('id')->with('modifications:template_id,tag,value,row_index')->paginate(Setting::get('pagination'));

        return view('template.index', compact('templates'));
    }

    public function response(): JsonResponse
    {
        $records = Template::orderByDesc('id')->paginate(Setting::get('pagination'));
        foreach ($records as &$item) {
            $media       = $item->lastMedia(config('constants.media_tags'));
            $item->image = ($media) ? getMediaUrl($media) : '';
        }

        return response()->json([
            'code'       => 1,
            'result'     => $records,
            'pagination' => (string) $records->links(),
        ]);
    }

    public function updateBearBannerTemplate(Request $request): RedirectResponse
    {
        $template = Template::find($request->id);

        $template->name = $request->name;

        $template->save();

        $tags = [];

        $body = ['name' => $request->name, 'tags' => $tags];

        $url = config('settings.banner_api_link') . '/templates/' . $template->uid;

        $api_key = config('settings.banner_api_key');

        $headers = [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ];

        $response = GuzzleHelper::patch($url, $body, $headers);

        return redirect()->back()->with('success', 'The template is updated.');
    }

    public static function bearBannerTemplates()
    {
        $url = config('settings.banner_api_link') . '/templates';

        $api_key = config('settings.banner_api_key');

        $headers = [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ];

        $response = GuzzleHelper::get($url, $headers);

        return $response;
    }

    public function updateTemplatesFromBearBanner(Request $request)
    {
        $templates = collect(self::bearBannerTemplates());

        foreach ($templates as $key => $row) {
            $template = ['name' => $row->name, 'uid' => $row->uid, 'is_processed' => 1];

            if ($existingTemplate = Template::whereUid($row->uid)->first()) {
                $existingTemplate->update($template);
                $existingTemplate->modifications()->delete();

                $template = $existingTemplate;

                if ($row->preview_url) {
                    $media = $template->lastMedia(config('constants.media_tags'));
                    $template->detachMedia($media);
                }
            } else {
                $template = Template::create($template);
            }
            if ($row->available_modifications) {
                $available_modifications = $row->available_modifications;
            } else {
                $available_modifications = [];
            }

            if ($row->preview_url) {
                $contents = $this->getImageByCurl($row->preview_url);

                $media = MediaUploader::fromString($contents)->useFilename('template-' . time())->toDirectory('template-images')->upload();

                $template->attachMedia($media, config('constants.media_tags'));
            }

            foreach ($available_modifications as $row_index => $tag) {
                foreach ($tag as $name => $value) {
                    $modifications = ['tag' => $name, 'value' => $value, 'template_id' => $template->id, 'row_index' => $row_index];

                    $template->modifications()->create($modifications);
                }
            }
        }
        if ($request->ajax()) {
            return response()->json(['status' => 1, 'message' => 'Templates updated successfully!']);
        }

        return redirect()->back()->with('success', 'Templates are updated.');
    }

    public function createWebhook(Request $request)
    {
        $header = $request->header('Authorization', 'default');

        if ($header == 'Bearer ' . config('settings.banner_webhook_key')) {
            $this->updateTemplatesFromBearBanner();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): JsonResponse
    {
        $template = new Template;
        if ($request->auto_generate_product == 'on') {
            $request->merge(['auto_generate_product' => '1']);
        }

        $template->fill(request()->all());

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();
                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return response()->json(['code' => 1, 'message' => 'Template Created successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $template = Template::where('id', $id)->first();

        if ($template) {
            $template->delete();
        }

        return response()->json(['code' => 1, 'message' => 'Template Deleted successfully!']);
    }

    public function edit(Request $request): RedirectResponse
    {
        $template = Template::find(5);
        if ($request->auto == 'on') {
            $template->auto_generate_product = 1;
        } else {
            $template->auto_generate_product = 0;
        }
        $template->name         = $request->name;
        $template->no_of_images = $request->number;
        $template->update();

        if ($template->save()) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('template-images')->upload();

                    $template->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return redirect()->back();
    }

    public function typeIndex(Request $request)
    {
        $temps = Template::all();
        if ($request->search) {
            $templates = ProductTemplate::where('template_no', $request->search)->paginate(Setting::get('pagination'))->appends(request()->except(['page']));
        } else {
            $templates = ProductTemplate::paginate(Setting::get('pagination'));
        }

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('product-template.partials.type-list-template', compact('templates', 'temps'))->render(),
                'links' => (string) $templates->render(),
                'total' => $templates->total(),
            ], 200);
        }

        return view('product-template.type-index', compact('templates', 'temps'));
    }

    /* public function generateTempalateCategoryBrand(): JsonResponse
    {
        $templates = Template::where('auto_generate_product', 1)->get();
        foreach ($templates as $template) {
            $categories = Category::select('id')->get();
            foreach ($categories as $category) {
                $brands = Brand::select('id')->get();
                foreach ($brands as $brand) {
                    $products = Product::where('category', $category->id)->where('brand', $brand->id)->latest()->limit(50)->get();
                    foreach ($products as $product) {
                        if ($product->getMedia(config('constants.media_tags'))->count() != 0) {
                            $oldTemplate = ProductTemplate::where('template_no', $template->id)->where('type', 1)->orderByDesc('id')->first();
                            if ($oldTemplate != null) {

                                $mediable = Mediables::where('mediable_type', ProductTemplate::class)->where('mediable_id', $oldTemplate->id)->count();

                                if ($template->no_of_images == $mediable) {
                                    //check if Product Template Already Exist
                                    $temp = ProductTemplate::where('template_no', $template->id)->where('brand_id', $product->brand)->where('category_id', $product->category)->where('is_processed', 0)->where('type', 1)->count();

                                    if ($temp == 0) {
                                        $productTemplate                   = new ProductTemplate;
                                        $productTemplate->template_no      = $template->id;
                                        $productTemplate->product_title    = '';
                                        $productTemplate->brand_id         = $product->brand;
                                        $productTemplate->currency         = 'eur';
                                        $productTemplate->price            = '';
                                        $productTemplate->discounted_price = '';
                                        $productTemplate->category_id      = $product->category;
                                        $productTemplate->product_id       = '';
                                        $productTemplate->is_processed     = 0;
                                        $productTemplate->type             = 1;
                                        $productTemplate->save();
                                        $media = $product->getMedia(config('constants.media_tags'))->first();
                                        $media = Media::find($media->id);
                                        $tag   = 'template-image';
                                        try {
                                            $productTemplate->attachMedia($media, $tag);
                                        } catch (Exception $e) {
                                            continue;
                                        }
                                    }
                                } else {
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag   = 'template-image';
                                    try {
                                        $oldTemplate->attachMedia($media, $tag);
                                    } catch (Exception $e) {
                                        continue;
                                    }
                                }
                            } else {
                                //check if Product Template Already Exist
                                $temp = ProductTemplate::where('template_no', $template->id)->where('brand_id', $product->brand)->where('category_id', $product->category)->where('is_processed', 0)->where('type', 1)->count();
                                if ($temp == 0) {
                                    $productTemplate                   = new ProductTemplate;
                                    $productTemplate->template_no      = $template->id;
                                    $productTemplate->product_title    = '';
                                    $productTemplate->brand_id         = $product->brand;
                                    $productTemplate->currency         = 'eur';
                                    $productTemplate->price            = '';
                                    $productTemplate->discounted_price = '';
                                    $productTemplate->category_id      = $product->category;
                                    $productTemplate->product_id       = '';
                                    $productTemplate->is_processed     = 0;
                                    $productTemplate->type             = 1;
                                    $productTemplate->save();
                                    $media = $product->getMedia(config('constants.media_tags'))->first();
                                    $media = Media::find($media->id);
                                    $tag   = 'template-image';
                                    try {
                                        $productTemplate->attachMedia($media, $tag);
                                    } catch (Exception $e) {
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Sucess'], 200);
    } */
    public function generateTempalateCategoryBrand(): JsonResponse
    {
        $templates = Template::where('auto_generate_product', 1)->get();

        foreach ($templates as $template) {
            $uniqueCombinations = Product::select('category', 'brand')
                ->whereHas('media', function ($query) {
                    $query->where('tag', config('constants.media_tags'));
                })
                ->whereRaw('category REGEXP "^[0-9]+$"')
                ->distinct()
                ->get(['category', 'brand']);

            foreach ($uniqueCombinations as $combination) {
                $category = $combination->category;
                $brand = $combination->brand;

                // Check if template already exists for this category and brand
                $existingTemplateCount = ProductTemplate::where('template_no', $template->id)
                    ->where('brand_id', $brand)
                    ->where('category_id', $category)
                    ->where('is_processed', 0)
                    ->where('type', 1)
                    ->count();

                if ($existingTemplateCount > 0) {
                    continue;
                }

                $products = Product::where('category', $category)
                    ->where('brand', $brand)
                    ->take(50)
                    ->get();

                // Create product templates
                foreach ($products as $product) {
                    $this->createOrUpdateProductTemplate($template, $product);
                }
            }
        }

        return response()->json(['message' => 'Success'], 200);
    }

    private function createOrUpdateProductTemplate($template, $product)
    {
        $oldTemplate = ProductTemplate::where('template_no', $template->id)
            ->where('type', 1)
            ->orderBy('id', 'desc')
            ->first();

        $mediableCount = $oldTemplate ? $oldTemplate->media()->count() : 0;

        if ($template->no_of_images == $mediableCount) {
            $existingTemplate = ProductTemplate::where('template_no', $template->id)
                ->where('brand_id', $product->brand)
                ->where('category_id', $product->category)
                ->where('is_processed', 0)
                ->where('type', 1)
                ->count();

            if ($existingTemplate == 0) {
                $this->createProductTemplate($template, $product);
            }
        } else {
            if ($oldTemplate) {
                $this->attachMediaToTemplate($oldTemplate, $product);
            } else {
                $this->createProductTemplate($template, $product);
            }
        }
    }

    private function createProductTemplate($template, $product)
    {
        $productTemplate = new ProductTemplate;
        $productTemplate->template_no = $template->id;
        $productTemplate->brand_id = $product->brand;
        $productTemplate->category_id = $product->category;
        $productTemplate->currency = 'eur';
        $productTemplate->is_processed = 0;
        $productTemplate->type = 1;
        $productTemplate->save();

        $media = $product->getMedia(config('constants.media_tags'))->first();
        if ($media) {
            try {
                $productTemplate->attachMedia($media, 'template-image');
            } catch (Exception $e) {
                Log::error("Error attaching media: " . $e->getMessage());
            }
        }
    }

    private function attachMediaToTemplate($template, $product)
    {
        $media = $product->getMedia(config('constants.media_tags'))->first();
        if ($media) {
            try {
                $template->attachMedia($media, 'template-image');
            } catch (Exception $e) {
                Log::error("Error attaching media: " . $e->getMessage());
            }
        }
    }
    public function getTemplateProduct(request $request): JsonResponse
    {
        $id           = $request->input('productid');
        $productData  = product::find($id);
        $image        = $productData->getMedia(\Config('constants.media_original_tag'))->first();
        $responseData = [
            'status'            => 'success',
            'productName'       => $productData->name,
            'short_description' => Str::limit($productData->short_description, 20, $end = '...'),
            'price'             => '$' . $productData->price,
            'product_url'       => 'www.test.com',
        ];
        if ($image) {
            $responseData['product_image'] = getMediaUrl($image);
        }
        if (isset($productData)) {
            return response()->json($responseData);
        }

        return response()->json(['status' => 'failed', 'message' => 'Product not found']);
    }

    public function getImageByCurl($url)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $result   = Http::get($url);
        $response = $result->json();

        LogRequest::log($startTime, $url, 'GET', json_encode([]), $response, $result->status(), TemplatesController::class, 'report');

        return $response;
    }
}
