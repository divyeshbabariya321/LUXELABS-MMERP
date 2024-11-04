<?php

namespace App\Http\Controllers;

use App\Brand;
use App\BroadcastImage;
use App\BroadcastMessage;
use App\BroadcastMessageNumber;
use App\CallBusyMessage;
use App\CallRecording;
use App\Category;
use App\ChatMessage;
use App\ColorNamesReference;
use App\CommunicationHistory;
use App\Customer;
use App\Email;
use App\ErpLeads;
use App\ErpLeadsBrand;
use App\ErpLeadsCategory;
use App\ErpLeadSendingHistory;
use App\ErpLeadStatus;
use App\ErpLeadStatusHistory;
use App\Helpers;
use App\Helpers\DevelopmentHelper;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Image;
use App\Jobs\SendEmail;
use App\LeadProductPriceCountLogs;
use App\Leads;
use App\Mails\Manual\OrderStatusChangeMail;
use App\Message;
use App\MessageQueue;
use App\Models\DataTableColumn;
use App\Product;
use App\ReplyCategory;
use App\Setting;
use App\Status;
use App\StatusChange;
use App\StoreWebsite;
use App\StoreWebsiteProductPrice;
use App\Task;
use App\User;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('orderby') == '') {
            $orderby = 'asc';
        } else {
            $orderby = 'desc';
        }

        switch ($request->input('sortby')) {
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'city':
                $sortby = 'city';
                break;
            case 'assigned_user':
                $sortby = 'assigned_user';
                break;
            case 'rating':
                $sortby = 'rating';
                break;
            case 'communication':
                $sortby = 'communication';
                break;
            case 'status':
                $sortby = 'status';
                break;
            case 'created_at':
                $sortby = 'created_at';
                break;
            default:
                $sortby = 'communication';
        }

        $term = $request->input('term');
        $brand = $request->input('brand');
        $rating = $request->input('rating');

        $type = false;
        $leads = ((new Leads)->newQuery()->with('customer'));

        if ($request->type == 'multiple') {
            $type = true;
        }

        if ($request->brand[0] != null) {
            $implode = implode(',', $request->brand);
            $leads->where('multi_brand', 'LIKE', "%$implode%");

            $brand = $request->brand;
        }

        if ($request->rating[0] != null) {
            $leads->whereIn('rating', $request->rating);

            $rating = $request->rating;
        }

        $category = request()->get('multi_category', null);

        if (! is_null($category) && $category != '' && $category != 1) {
            $leads->where('multi_category', 'LIKE', '%"'.$category.'"%');
        }

        $status = request()->get('status', null);

        if (! is_null($status) && $status != '') {
            $leads->where('status', '=', $status);
        }

        if (helpers::getadminorsupervisor()) {
            if ($sortby != 'communication') {
                $leads = $leads->orderBy($sortby, $orderby);
            }
        } else {
            if (helpers::getmessagingrole()) {
                $leads = $leads->oldest();
            } else {
                $leads = $leads->oldest()->where('assigned_user', '=', Auth::id());
            }
        }
        if (! empty($term)) {
            $leads = $leads->whereHas('customer', function ($query) use ($term) {
                return $query->where('name', 'LIKE', "%$term%");
            })->where(function ($query) use ($term) {
                return $query
                    ->orWhere('client_name', 'like', '%'.$term.'%')
                    ->orWhere('id', 'like', '%'.$term.'%')
                    ->orWhere('contactno', $term)
                    ->orWhere('city', 'like', '%'.$term.'%')
                    ->orWhere('instahandler', $term)
                    ->orWhere('assigned_user', Helpers::getUserIdByName($term))
                    ->orWhere('assigned_user', Helpers::getUserIdByName($term))
                    ->orWhere('userid', Helpers::getUserIdByName($term))
                    ->orWhere('status', (new Status)->getIDCaseInsensitive($term));
            });
        }
        $leads_array = $leads->whereNull('deleted_at')->get()->toArray();
        if ($sortby == 'communication') {
            if ($orderby == 'asc') {
                $leads_array = array_values(Arr::sort($leads_array, function ($value) {
                    return $value['communication']['created_at'];
                }));

                $leads_array = array_reverse($leads_array);
            } else {
                $leads_array = array_values(Arr::sort($leads_array, function ($value) {
                    return $value['communication']['created_at'];
                }));
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($leads_array, $perPage * ($currentPage - 1), $perPage);

        $leads_array = new LengthAwarePaginator($currentItems, count($leads_array), $perPage, $currentPage);
        $leads = $leads->whereNull('deleted_at')->paginate(Setting::get('pagination'));

        if ($request->ajax()) {
            $html = view('leads.lead-item', ['leads_array' => $leads_array, 'leads' => $leads, 'orderby' => $orderby, 'term' => $term, 'brand' => http_build_query(['brand' => $brand]), 'rating' => http_build_query(['rating' => $rating]), 'type' => $type])->render();

            return response()->json(['html' => $html]);
        }

        $category_select = Category::attr(['name' => 'multi_category', 'class' => 'form-control select-multiple', 'id' => 'multi_category'])->selected()->renderAsDropdown();
        $status = array_flip((new status)->all());

        return view('leads.index', compact('leads', 'leads_array', 'term', 'orderby', 'brand', 'rating', 'type', 'category_select', 'status'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $status = new status;
        $data['status'] = $status->all();
        $users = User::oldest()->get()->toArray();
        $data['users'] = $users;
        $brands = Brand::oldest()->get()->toArray();
        $data['brands'] = $brands;
        $data['products_array'] = [];

        $data['category_select'] = Category::attr(['name' => 'multi_category', 'class' => 'form-control', 'id' => 'multi_category'])
            ->selected()
            ->renderAsDropdown();

        $customer_suggestions = [];
        $customers = (new Customer)->newQuery()
            ->latest()->select('name')->get()->toArray();

        foreach ($customers as $customer) {
            array_push($customer_suggestions, $customer['name']);
        }

        $data['customers'] = Customer::all();

        $data['customer_suggestions'] = $customer_suggestions;

        return view('leads.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadRequest $request)
    {
        $data = $request->except('_token');
        $customer = Customer::find($request->customer_id);

        $lead = null;
        if ($request->type == 'product-lead') {

            foreach ($request->selected_product as $product_id) {
                $product = Product::find($product_id);
                $lead = ErpLeads::create([
                    'customer_id' => $request->customer_id,
                    'product_id' => $product_id,
                    'brand_id' => $product->brand,
                    'store_website_id' => 15,
                    'brand_segment' => ! empty($product->brands->brand_segment) ? $product->brands->brand_segment : '',
                    'category_id' => $product->category,
                    'color' => $product->color,
                    'size' => $product->size_value,
                    'type' => 'new_erp_lead',
                    'lead_status_id' => 1,
                ]);

                if ($request->hasfile('image')) {
                    foreach ($request->file('image') as $image) {
                        $media = MediaUploader::fromSource($image)->upload();
                        $lead->attachMedia($media, config('constants.media_tags'));
                    }
                }
            }
        } else {
            $data['client_name'] = $customer->name;
            $data['contactno'] = $customer->phone;
            $data['userid'] = Auth::id();
            $data['selected_product'] = json_encode($request->input('selected_product'));
            $data['multi_brand'] = $request->input('multi_brand') ? json_encode($request->input('multi_brand')) : null;
            $data['multi_category'] = $request->input('multi_category');
            $data['multi_category'] = json_encode($request->input('multi_category'));

            $lead = Leads::create($data);
            if ($request->hasfile('image')) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)
                        ->toDirectory('leads/'.floor($lead->id / config('constants.image_per_folder')))
                        ->upload();
                    $lead->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        if ($request->ajax()) {
            $message = 'Lead created successfully';

            return response()->json(['lead' => $lead, 'message' => $message]);
        }

        return redirect()->route('leads.create')
            ->with('success', 'Lead created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $leads = Leads::find($id);
        $status = new status;
        $data = $status->all();
        $sales_persons = Helpers::getUsersArrayByRole('Sales');
        $leads['statusid'] = $data;
        $users = User::all()->toArray();
        $leads['users'] = $users;
        $brands = Brand::all()->toArray();
        $leads['brands'] = $brands;
        $leads['selected_products_array'] = json_decode($leads['selected_product']);
        $leads['products_array'] = [];
        $leads['recordings'] = CallRecording::where('lead_id', $leads->id)->get()->toArray();
        $leads['customers'] = Customer::all();
        $tasks = Task::where('model_type', 'leads')->where('model_id', $id)->get()->toArray();
        $reply_categories = ReplyCategory::all();

        $leads['multi_brand'] = is_array(json_decode($leads['multi_brand'], true)) ? json_decode($leads['multi_brand'], true) : [];
        $data['category_select'] = Category::attr(['name' => 'multi_category', 'class' => 'form-control', 'id' => 'multi_category'])
            ->selected($leads->multi_category)
            ->renderAsDropdown();
        $leads['remark'] = $leads->remark;

        $messages = Message::all()->where('moduleid', '=', $leads['id'])->where('moduletype', '=', 'leads')->sortByDesc('created_at')->take(10)->toArray();
        $leads['messages'] = $messages;

        if (! empty($leads['selected_products_array'])) {
            foreach ($leads['selected_products_array'] as $product_id) {
                $skuOrName = $this->getProductNameSkuById($product_id);

                $data['products_array'][$product_id] = $skuOrName;
            }
        }

        $users_array = Helpers::getUserArray(User::all());

        $selected_categories = $leads['multi_category'];
        $media_tags = config('constants.media_tags');

        return view('leads.show', compact('leads', 'id', 'data', 'tasks', 'sales_persons', 'selected_categories', 'users_array', 'reply_categories', 'media_tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, int $id): RedirectResponse
    {
        $leads = Leads::find($id);

        if ($request->type != 'customer') {
            $leads->customer_id = $request->customer_id;
            $leads->client_name = $request->get('client_name');
            $leads->contactno = $request->get('contactno');
            $leads->city = $request->get('city');
            $leads->source = $request->get('source');
            $leads->rating = $request->get('rating');
            $leads->solophone = $request->get('solophone');
            $leads->userid = $request->get('userid');
            $leads->email = $request->get('email');
            $leads->address = $request->get('address');
            $leads->leadsourcetxt = $request->get('leadsourcetxt');
            $leads->created_at = $request->created_at;
            $leads->whatsapp_number = $request->whatsapp_number;
        }

        if ($request->status != $leads->status) {
            $lead_status = (new status)->all();
            StatusChange::create([
                'model_id' => $id,
                'model_type' => Leads::class,
                'user_id' => Auth::id(),
                'from_status' => array_search($leads->status, $lead_status),
                'to_status' => array_search($request->status, $lead_status),
            ]);
        }

        $leads->status = $request->get('status');
        $leads->comments = $request->get('comments');
        $leads->assigned_user = $request->get('assigned_user');

        $leads->multi_brand = $request->input('multi_brand') ? json_encode($request->get('multi_brand')) : null;
        $leads->multi_category = $request->get('multi_category');

        $leads->selected_product = json_encode($request->input('selected_product'));

        $leads->save();

        $messages = Message::where('moduletype', 'leads')->where('moduleid', $leads->id)->get();

        foreach ($messages as $message) {
            $message->customer_id = $leads->customer_id;
            $message->save();
        }

        $chats = ChatMessage::where('lead_id', $leads->id)->get();

        foreach ($chats as $chat) {
            $chat->customer_id = $leads->customer_id;
            $chat->save();
        }

        $count = 0;
        foreach ($request->oldImage as $old) {
            if ($old > 0) {
                self::removeImage($old);
            } elseif ($old == -1) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)
                        ->toDirectory('leads/'.floor($leads->id / config('constants.image_per_folder')))
                        ->upload();
                    $leads->attachMedia($media, config('constants.media_tags'));
                }
            } elseif ($old == 0) {
                $count++;
            }
        }

        if ($count > 0) {
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)
                        ->toDirectory('leads/'.floor($leads->id / config('constants.image_per_folder')))
                        ->upload();
                    $leads->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        return redirect()->back()->with('success', 'Lead has been updated');
    }

    public function sendPrices(Request $request, GuzzleClient $client): Response
    {
        $params = [
            'number' => null,
            'user_id' => Auth::id() ?? 6,
            'approved' => 0,
            'status' => 8,
        ];
        if ($request->lead_id) {
            $params['lead_id'] = $request->lead_id;
        }
        $isQueue = false;
        if ($request->is_queue > 0) {
            $isQueue = true;
            $params['is_queue'] = 1;
        }

        $customer = Customer::find($request->customer_id);
        $product_names = '';

        $params['customer_id'] = $customer->id;
        Log::channel('customer')->info('Lead send price started : '.$customer->id);
        $cnt = 'IN';
        $website = StoreWebsite::find(15);
        foreach ($request->selected_product as $product_id) {
            // count adding for leads
            $store_website_product_price = [];
            $store_website_product_price['product_id'] = $product_id;
            $productData = Product::find($product_id);
            $getPrice = $productData->getPrice($website, 'IN', null, true, null, null, null, null, null, null, null, $product_id, $customer->id);
            $getDuty = $productData->getDuty('IN');
            $store_website_product_price['default_price'] = $getPrice['original_price'];
            $store_website_product_price['duty_price'] = (float) $getDuty['duty'];
            $store_website_product_price['segment_discount'] = (float) $getPrice['segment_discount'];
            $store_website_product_price['override_price'] = $getPrice['total'];
            $store_website_product_price['status'] = 1;
            $store_website_product_price['store_website_id'] = 15;
            StoreWebsiteProductPrice::insert($store_website_product_price);

            $product = Product::find($product_id);
            $brand_name = $product->brands->name ?? '';
            $special_price = (int) $product->price_special_offer > 0 ? (int) $product->price_special_offer : $product->price_inr_special;
            $dutyPrice = $product->getDuty($cnt);
            $discountPrice = $product->getPrice($website, $cnt, null, true, $dutyPrice, null, null, null, null, null, null, $product_id, $customer->id);
            if (! empty($discountPrice['total']) && $discountPrice['total'] > 0) {
                $special_price = $discountPrice['total'];
                $brand = $product->brands;
                if ($brand) {
                    if (! empty($brand->euro_to_inr)) {
                        $special_price = (float) $brand->euro_to_inr * (float) trim($discountPrice['total']);
                    } else {
                        $special_price = (float) Setting::get('euro_to_inr') * (float) trim($discountPrice['total']);
                    }
                }
            }

            if (is_numeric($special_price)) {
                $special_price = ceil($special_price / 10) * 10;
            }

            $condition = [
                'product_id' => $product_id,
                'customer_id' => $customer->id,
            ];

            $fields = [
                'product_id' => $product_id,
                'customer_id' => $customer->id,
                'original_price' => $getPrice['original_price'] ?? '',
                'promotion_per' => $getPrice['promotion_per'] ?? '',
                'promotion' => $getPrice['promotion'] ?? '',
                'segment_discount' => $getPrice['segment_discount'] ?? '',
                'segment_discount_per' => $getPrice['segment_discount_per'] ?? '',
                'total_price' => $getPrice['total'] ?? '',
                'before_iva_product_price' => $getPrice['before_iva_product_price'] ?? '',
                'euro_to_inr_price' => $special_price,
                'log' => $getPrice['last_log'] ?? '',
            ];
            LeadProductPriceCountLogs::updateOrCreate($condition, $fields);

            if ($request->has('dimension')) {
                $product_names .= "$brand_name $product->name".' ('."Length: $product->lmeasurement cm, Height: $product->hmeasurement cm & Depth: $product->dmeasurement cm) \n";
                $params['message'] = 'The products with their respective dimensions are: : '.$product_names.'.';
                $chat_message = ChatMessage::create($params);
            } else {
                if ($request->has('detailed')) {
                    $params['message'] = 'The product images for : : '.$brand_name.' '.$product->name.' are.';
                    $chat_message = ChatMessage::create($params);
                    $chat_message->attachMedia($product->getMedia(config('constants.attach_image_tag')), config('constants.media_tags'));
                } else {
                    $auto_message = "$brand_name $product->name".' - '."$special_price";
                    $params['message'] = ''; //$auto_message;
                    $chat_message = ChatMessage::create($params);
                    $mediaImage = $product->getMedia(config('constants.attach_image_tag'))->first();
                    $textImage = null;
                    if ($mediaImage) {
                        // define seperator
                        if (! defined('DSP')) {
                            define('DSP', DIRECTORY_SEPARATOR);
                        }
                        // add text message and create image
                        $textImage = self::createProductTextImage(
                            $mediaImage->getAbsolutePath(),
                            'instant_message_'.$chat_message->id,
                            $auto_message,
                            '545b62',
                            '40',
                            true
                        );

                        if (! empty($textImage)) {
                            $mediaPrice = MediaUploader::fromSource($textImage)
                                ->toDirectory('chatmessage/'.floor($chat_message->id / config('constants.image_per_folder')))->upload();
                            $chat_message->attachMedia($mediaPrice, config('constants.media_tags'));
                            $chat_message->save();
                        }
                    }
                    // send message now
                    // uncomment this one to send message immidiatly
                    if (! $isQueue) {
                        app(WhatsAppController::class)->sendRealTime($chat_message, 'customer_'.$customer->id, $client, $textImage);
                    }
                }
            }

            if (! $isQueue) {
                $autoApprove = DevelopmentHelper::needToApproveMessage();
                Log::channel('customer')->info('Send price started : '.$chat_message->id);

                if ($autoApprove && ! empty($chat_message->id)) {
                    // send request if auto approve
                    $approveRequest = new Request;
                    $approveRequest->setMethod('GET');
                    $approveRequest->request->add(['messageId' => $chat_message->id]);

                    app(WhatsAppController::class)->approveMessage('customer', $approveRequest);
                }
            }
        }

        if ($request->has('dimension') || $request->has('detailed')) {
            if (! $isQueue) {
                app(WhatsAppController::class)->sendRealTime($chat_message, 'customer_'.$customer->id, $client);
            }
        }

        $histories = CommunicationHistory::where('model_id', $customer->id)->where('model_type', Customer::class)->where('type', 'initiate-followup')->where('is_stopped', 0)->get();

        foreach ($histories as $history) {
            $history->is_stopped = 1;
            $history->save();
        }

        CommunicationHistory::create([
            'model_id' => $customer->id,
            'model_type' => Customer::class,
            'type' => 'initiate-followup',
            'method' => 'whatsapp',
        ]);

        return response('success');
    }

    public function removeImage($old_image)
    {
        if ($old_image != 0) {
            $results = Media::where('id', $old_image)->get();

            $results->each(function ($media) {
                Image::trashImage($media->basename);
                $media->delete();
            });
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $lead = Leads::find($id);
        $lead_status = (new status)->all();
        StatusChange::create([
            'model_id' => $id,
            'model_type' => Leads::class,
            'user_id' => Auth::id(),
            'from_status' => array_search($lead->status, $lead_status),
            'to_status' => array_search($request->status, $lead_status),
        ]);

        $lead->status = $request->status;
        $lead->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $leads = Leads::findOrFail($id);
        $leads->delete();

        return redirect()->to('leads')->with('success', 'Lead has been archived');
    }

    public function permanentDelete(Leads $leads): RedirectResponse
    {
        $leads->forceDelete();

        return redirect()->to('leads')->with('success', 'Lead has been  deleted');
    }

    public function getProductNameSkuById($product_id)
    {
        $product = new Product;

        $product_instance = $product->find($product_id);

        return $product_instance->name ? $product_instance->name : $product_instance->sku;
    }

    public function imageGrid(): View
    {
        $leads_array = Leads::whereNull('deleted_at')->where('status', '!=', 1)->get()->toArray();
        $leads = Leads::whereNull('deleted_at')->where('status', '!=', 1)->get();
        $new_leads = [];

        foreach ($leads_array as $key => $lead) {
            if ($leads[$key]->getMedia(config('constants.media_tags'))->first() !== null) {
                $new_leads[$key]['id'] = $lead['id'];
                $new_leads[$key]['image'] = $leads[$key]->getMedia(config('constants.media_tags'));
                $new_leads[$key]['status'] = $lead['status'];
                $new_leads[$key]['rating'] = $lead['rating'];
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');

        if (count($new_leads) > $perPage) {
            $currentItems = array_slice($new_leads, $perPage * ($currentPage - 1), $perPage);
        } else {
            $currentItems = $new_leads;
        }

        $new_leads = new LengthAwarePaginator($currentItems, count($new_leads), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('leads.image-grid')->withLeads($new_leads);
    }

    public function saveLeaveMessage(Request $request)
    {
        $callBusyMessage = new CallBusyMessage;
        $callBusyMessage->lead_id = $request->input('lead_id');
        $callBusyMessage->message = $request->input('message');
        $callBusyMessage->save();
    }

    /**
     * Create images with text from product
     *
     * @param  mixed  $path
     * @param  mixed  $name
     * @param  mixed  $text
     * @param  mixed  $color
     * @param  mixed  $fontSize
     * @param  mixed  $abs
     */
    public static function createProductTextImage($path, $name = '', $text = '', $color = '545b62', $fontSize = '40', $abs = false)
    {
        $text = wordwrap(strtoupper($text), 24, "\n");
        $img = \IImage::make($path);
        $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        // use callback to define details
        $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
            $font->file(public_path('/fonts/HelveticaNeue.ttf'));
            $font->size($fontSize);
            $font->color('#'.$color);
            $font->align('top');
        });

        $name = ! empty($name) ? $name.'_watermarked' : time().'_watermarked';

        if (! File::isDirectory(public_path().'/uploads/chat-price-image/')) {
            File::makeDirectory(public_path().'/uploads/chat-price-image/', 0777, true, true);
        }

        $path = 'uploads/chat-price-image/'.$name.'.jpg';

        $img->save(public_path($path));

        if ($abs) {
            return public_path($path);
        }

        return url('/').'/'.$path;
    }

    public function erpLeads(Request $request)
    {
        $brands = Brand::orderBy('name')->get()->toArray();
        $erpLeadStatus = ErpLeadStatus::orderBy('name')->get()->toArray();
        $erpLeadTypes = ErpLeads::select('id', 'type')->where('type', '!=', '')->whereNotNull('type')->groupBy('type')->get()->toArray();

        $source = ErpLeads::with('erpLeadStatus')
            ->leftJoin('products', 'products.id', '=', 'erp_leads.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_leads.customer_id')
            ->leftJoin('store_websites as SW', 'SW.id', 'c.store_website_id')
            ->leftJoin('erp_lead_status as els', 'els.id', 'erp_leads.lead_status_id')
            ->leftJoin('categories as cat', 'cat.id', 'erp_leads.category_id')
            ->leftJoin('brands as br', 'br.id', 'erp_leads.brand_id')
            ->orderByDesc('erp_leads.id')
            ->select([
                'erp_leads.*',
                'products.sku as product_sku',
                'products.name as product_name',
                'cat.title as cat_title',
                'br.name as brand_name',
                'els.name as status_name',
                'c.name as customer_name',
                'c.id as customer_id',
                'c.whatsapp_number as customer_whatsapp_number',
                'c.email as customer_email',
                'SW.website',
            ]);

        if ($s = request('lead_customer')) {
            $source = $source->where('c.name', 'like', '%'.$s.'%');
        }
        if ($s = request('lead_brand')) {
            $source = $source->whereIn('erp_leads.brand_id', $s);
        }
        if ($s = request('brand_id')) {
            $leadIds = ErpLeadsBrand::whereIn('brand_id', $s)->pluck('erp_lead_id')->toArray();
            $source = $source->whereIn('erp_leads.id', $leadIds);
        }
        if ($s = request('lead_status')) {
            $source = $source->whereIn('erp_leads.lead_status_id', $s);
        }
        if ($s = request('lead_category')) {
            $leadIds = ErpLeadsCategory::leftJoin('categories', 'categories.id', '=', 'erp_leads_categories.category_id')
                ->where('title', 'like', '%'.$s.'%')
                ->pluck('erp_lead_id')
                ->toArray();
            $source = $source->whereIn('erp_leads.id', $leadIds);
        }
        if ($s = request('lead_color')) {
            $source = $source->where('erp_leads.color', '=', $s);
        }
        if ($s = request('lead_shoe_size')) {
            $source = $source->where('erp_leads.size', '=', $s);
        }
        if ($s = request('lead_type')) {
            $source = $source->whereIn('erp_leads.type', $s);
        }
        if ($s = request('brand_segment')) {
            $source = $source->where('erp_leads.brand_segment', '=', $s);
        }

        $total = $source->count();
        $source2 = clone $source;
        $allLeadCustomersId = $source2->select('erp_leads.customer_id')->pluck('customer_id', 'customer_id')->toArray();

        $source = $source->paginate(Setting::get('pagination'));

        $tempLeadIds = [];
        foreach ($source as $single) {
            $tempLeadIds[] = $single->id;
        }

        $listErpLeadsCategories = [];
        $listCategories = [];

        $listErpLeadsBrands = [];
        $listBrands = [];
        if ($tempLeadIds) {
            $temp = ErpLeadsCategory::whereIn('erp_lead_id', $tempLeadIds)->where('category_id', '>', 0)->get(['erp_lead_id', 'category_id']);
            foreach ($temp as $value) {
                $listErpLeadsCategories[$value->erp_lead_id][] = $value->category_id;
            }
            $listCategories = Category::orderBy('title')->pluck('title', 'id')->toArray();

            $temp = ErpLeadsBrand::whereIn('erp_lead_id', $tempLeadIds)->where('brand_id', '>', 0)->get(['erp_lead_id', 'brand_id']);
            foreach ($temp as $key => $value) {
                $listErpLeadsBrands[$value->erp_lead_id][] = $value->brand_id;
            }
            $listBrands = Brand::orderBy('name')->pluck('name', 'id')->toArray();
        }

        $source->getCollection()->transform(function ($value) use ($listErpLeadsCategories, $listCategories, $listErpLeadsBrands, $listBrands) {
            $value['media_url'] = null;
            $media = $value->getMedia(config('constants.media_tags'))->first();
            if ($media) {
                $value['media_url'] = getMediaUrl($media);
            }

            if (empty($value['media_url']) && $value['product_id']) {
                //
            }

            $temp = $value['cat_title'] ? [$value['cat_title']] : [];
            if (isset($listErpLeadsCategories[$value['id']]) && $listErpLeadsCategories[$value['id']]) {
                foreach ($listErpLeadsCategories[$value['id']] as $catId) {
                    if (isset($listCategories[$catId]) && $listCategories[$catId]) {
                        $temp[] = $listCategories[$catId];
                    }
                }
            }
            $value['cat_title'] = implode(', ', array_unique($temp));

            $temp = $value['brand_name'] ? [$value['brand_name']] : [];
            if (isset($listErpLeadsBrands[$value['id']]) && $listErpLeadsBrands[$value['id']]) {
                foreach ($listErpLeadsBrands[$value['id']] as $brandId) {
                    if (isset($listBrands[$brandId]) && $listBrands[$brandId]) {
                        $temp[] = $listBrands[$brandId];
                    }
                }
            }
            $value['brand_name'] = implode(', ', array_unique($temp));

            return $value;
        });

        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'erp-leads')->first();

        $dynamicColumnsToShowel = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowel = json_decode($hideColumns, true);
        }

        return view('leads.erp.index', [
            //'shoe_size_group' => $shoe_size_group,
            //'clothing_size_group' => $clothing_size_group,
            'brands' => $brands,
            'erpLeadStatus' => $erpLeadStatus,
            'erpLeadTypes' => $erpLeadTypes,
            'recordsTotal' => $total,
            'sourceData' => $source,
            'allLeadCustomersId' => $allLeadCustomersId,
            'statusErpLeadsSave' => Setting::getErpLeadsCronSave(),
            'dynamicColumnsToShowel' => $dynamicColumnsToShowel,
        ]);
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'erp-leads')->first();

        if ($userCheck) {
            $column = DataTableColumn::find($userCheck->id);
            $column->section_name = 'erp-leads';
            $column->column_name = json_encode($request->column_el);
            $column->save();
        } else {
            $column = new DataTableColumn;
            $column->section_name = 'erp-leads';
            $column->column_name = json_encode($request->column_el);
            $column->user_id = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = ErpLeadStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function filterErpLeads()
    {
        echo 'filter';
        print_r($_POST);
    }

    public function erpLeadsResponse(Request $request): JsonResponse
    {
        $source = ErpLeads::leftJoin('products', 'products.id', '=', 'erp_leads.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_leads.customer_id')
            ->leftJoin('erp_lead_status as els', 'els.id', 'erp_leads.lead_status_id')
            ->leftJoin('categories as cat', 'cat.id', 'erp_leads.category_id')
            ->leftJoin('brands as br', 'br.id', 'erp_leads.brand_id')
            ->orderByDesc('erp_leads.id')
            ->select(['erp_leads.*', 'products.sku as product_sku', 'products.name as product_name', 'cat.title as cat_title', 'br.name as brand_name', 'els.name as status_name', 'c.name as customer_name', 'c.id as customer_id', 'c.whatsapp_number as customer_whatsapp_number', 'c.email as customer_email']);

        if ($request->get('lead_customer')) {
            $source = $source->where('c.name', 'like', '%'.$request->get('lead_customer').'%');
        }

        if ($request->get('lead_brand')) {
            $source = $source->whereIn('erp_leads.brand_id', $request->get('lead_brand'));
        }

        if ($request->get('lead_status')) {
            $source = $source->whereIn('erp_leads.lead_status_id', $request->get('lead_status'));
        }

        if ($request->get('lead_category')) {
            $source = $source->where('cat.title', 'like', '%'.$request->get('lead_category').'%');
        }

        if ($request->get('lead_color')) {
            $source = $source->where('erp_leads.color', '=', $request->get('lead_color'));
        }

        if ($request->get('lead_shoe_size')) {
            $source = $source->where('erp_leads.size', '=', $request->get('lead_shoe_size'));
        }

        if ($request->get('lead_type')) {
            $source = $source->where('erp_leads.type', 'like', '%'.$request->get('lead_type').'%');
        }

        if ($request->get('brand_segment')) {
            $source = $source->where('erp_leads.brand_segment', '=', $request->get('brand_segment'));
        }

        $total = $source->count();
        $source2 = clone $source;
        $allLeadCustomersId = $source2->select('erp_leads.customer_id')->pluck('customer_id', 'customer_id')->toArray();

        $source = $source->offset($request->get('start', 0));
        $source = $source->limit($request->get('length', 10));
        $source = $source->get();

        foreach ($source as $key => $value) {
            $source[$key]->media_url = null;
            $media = $value->getMedia(config('constants.media_tags'))->first();
            if ($media) {
                $source[$key]->media_url = getMediaUrl($media);
            }
        }

        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $source,
            'allLeadCustomersId' => $allLeadCustomersId,
        ]);
    }

    public function blockcustomerlead(Request $request): JsonResponse
    {
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $is_blocked_lead = ! $customer->is_blocked_lead;

            $lead_product_freq = (isset($request->lead_product_freq)) ? $request->lead_product_freq : '';
            if ($request->column == 'delete') {
                $customer->is_blocked_lead = $is_blocked_lead;
            }
            if ($request->column == 'update') {
                $customer->lead_product_freq = $lead_product_freq;
            }

            $customer->save();
            $message = 'Leads for Customer are blocked';

            return response()->json([
                'status' => 200,
                'message' => $message,
            ]);
        }
    }

    public function erpLeadsCreate(): View
    {
        $customerList = []; //Customer::pluck("name","id")->toArray();
        $brands = Brand::all();
        $category = Category::attr(['name' => 'category_id', 'class' => 'form-control', 'id' => 'category_id'])->selected()->renderAsDropdown();
        $colors = ColorNamesReference::pluck('erp_name', 'erp_name')->toArray();
        $status = ErpLeadStatus::pluck('name', 'id')->toArray();

        return view('leads.erp.create', compact('customerList', 'brands', 'category', 'colors', 'status'));
    }

    public function manageLeadsCategory(Request $request): View
    {
        $category_id = ErpLeadsCategory::where('erp_lead_id', $request->get('lead_id'))->where('category_id', '!=', '')->pluck('category_id')->toArray();

        $categories = Category::all()->toArray();

        return view('leads.erp.create_category', compact('categories', 'category_id'));
    }

    public function manageLeadsBrand(Request $request): View
    {
        $brand_ids = ErpLeadsBrand::where('erp_lead_id', $request->get('lead_id'))->where('brand_id', '!=', '')->pluck('brand_id')->toArray();
        $brands = Brand::all()->toArray();

        return view('leads.erp.create_brand', compact('brands', 'brand_ids'));
    }

    public function saveLeadsBrands(Request $request): JsonResponse
    {
        $input = $request->all();
        $message = 'Successsfully Added';

        ErpLeadsBrand::where('erp_lead_id', $input['lead_id'])->delete();

        foreach ($input['brand_ids'] as $brand) {
            ErpLeadsBrand::create([
                'erp_lead_id' => $input['lead_id'],
                'brand_id' => $brand,
            ]);
        }

        return response()->json(['code' => 200, 'message' => $message]);
    }

    public function saveLeadsCategories(Request $request): JsonResponse
    {
        $input = $request->all();
        $message = 'Successfully Added';
        ErpLeadsCategory::where('erp_lead_id', $input['lead_id'])->delete();
        foreach ($input['categories'] as $category) {
            ErpLeadsCategory::create([
                'erp_lead_id' => $input['lead_id'],
                'category_id' => $category,
            ]);
        }

        return response()->json(['code' => 200, 'message' => $message]);
    }

    public function erpLeadsEdit(): View
    {
        $id = request()->get('id', 0);
        $erpLeads = ErpLeads::where('id', $id)->first();
        if ($erpLeads) {
            $customerList = [$erpLeads->customer_id => $erpLeads->customer->name]; //Customer::pluck("name","id")->toArray();
            $brands = Brand::pluck('name', 'id')->toArray();
            $category = Category::attr(['name' => 'category_id', 'class' => 'form-control', 'id' => 'category_id'])->selected($erpLeads->category_id)->renderAsDropdown();
            $products = Product::where('id', $erpLeads->product_id)->get()->pluck('name', 'id')->toArray();
            $colors = ColorNamesReference::pluck('erp_name', 'erp_name')->toArray();
            $status = ErpLeadStatus::pluck('name', 'id')->toArray();

            return view('leads.erp.edit', compact('erpLeads', 'customerList', 'brands', 'category', 'products', 'colors', 'status'));
        }
    }

    public function erpLeadsStore(Request $request): JsonResponse
    {
        $id = request()->get('id', 0);
        $productId = request()->get('product_id', 0);

        $customer = Customer::where('id', request()->get('customer_id', 0))->first();
        if (! $customer) {
            $message = 'Please select valid customer';

            return response()->json(['code' => 0, 'data' => [], 'message' => $message]);
        }

        $product = Product::where('id', $productId)->first();
        $productId = null;
        if ($product) {
            $productId = $product->id;
        }
        $params = request()->all();
        $params['product_id'] = $productId;
        if (isset($params['brand_segment'])) {
            $params['brand_segment'] = implode(',', (array) $params['brand_segment']);
        }

        if ($product) {
            if (empty($params['brand_id'])) {
                $params['brand_id'] = $product->brand;
                if (empty($params['brand_segment'])) {
                    $brand = Brand::where('id', $product->brand)->first();
                    if ($brand) {
                        $params['brand_segment'] = $brand->brand_segment;
                    }
                }
            }

            if (empty($params['category_id'])) {
                $params['category_id'] = $product->category;
            }
        }

        if (empty($params['color'])) {
            $params['color'] = $customer->color;
        }

        if (empty($params['size'])) {
            $params['size'] = $customer->size;
        }

        $params['type'] = 'WHATSAPP';
        $params['store_website_id'] = 15;
        $erpLeads = ErpLeads::where('id', $id)->first();
        if (! $erpLeads) {
            $erpLeads = new ErpLeads;
        }
        $erpLeads->fill($params);
        $erpLeads->save();

        $count = 0;
        if ($request->oldImage) {
            foreach ($request->oldImage as $old) {
                if ($old > 0) {
                    self::removeImage($old);
                } elseif ($old == -1) {
                    if ($request->hasFile('image')) {
                        foreach ($request->file('image') as $image) {
                            $media = MediaUploader::fromSource($image)->upload();
                            $erpLeads->attachMedia($media, config('constants.media_tags'));
                        }
                    }
                } elseif ($old == 0) {
                    $count++;
                }
            }
        }

        if ($count > 0) {
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $media = MediaUploader::fromSource($image)->upload();
                    $erpLeads->attachMedia($media, config('constants.media_tags'));
                }
            }
        }

        foreach ($request->get('product_media_list', []) as $id) {
            $media = Media::find($id);
            $erpLeads->attachMedia($media, config('constants.media_tags'));
        }

        $message = 'Erp lead created successfully';

        return response()->json(['code' => 1, 'data' => [], 'message' => $message]);
    }

    public function erpLeadDelete(): JsonResponse
    {
        $id = request()->get('id', 0);

        $erpLeads = ErpLeads::where('id', $id)->first();
        if ($erpLeads) {
            $erpLeads->delete();
        }

        $message = 'Erp lead deleted successfully';

        return response()->json(['code' => 1, 'data' => [], 'message' => $message]);
    }

    public function customerSearch()
    {
        $term = request()->get('q', null);
        $search = Customer::where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")->orWhere('id', 'like', "%{$term}%")->get();

        return $search;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $customerIds = array_unique($request->get('customers', []));
        $customerArr = Customer::whereIn('id', $customerIds)->where('do_not_disturb', 0)->get();
        //Create broadcast
        $broadcast = BroadcastMessage::create(['name' => $request->name]);
        if (! empty($customerArr)) {
            $productIds = array_unique($request->get('products', []));

            // check if the data has more values for the prmotions
            $startTime = $request->get('product_start_date', '');
            $endTime = $request->get('product_end_date', '');

            $product = new Product;

            $fireQ = false;
            if (! empty($startTime)) {
                $fireQ = true;
                $product = $product->where('created_at', '>=', $startTime);
            }
            if (! empty($endTime)) {
                $fireQ = true;
                $product = $product->where('created_at', '<=', $endTime);
            }

            if ($fireQ) {
                $productQueryIds = $product->select('id')->get()->pluck('id')->toArray();
                if (! empty($productQueryIds)) {
                    $productIds = array_merge($productIds, $productQueryIds);
                }
            }

            $broadcast_image = new BroadcastImage;
            $broadcast_image->products = json_encode($productIds);
            $broadcast_image->save();
            $max_group_id = MessageQueue::max('group_id') + 1;

            $sendingData = [
                'message' => $request->get('message', ''),
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $media = MediaUploader::fromSource($image)->upload();
                $broadcast_image->attachMedia($media, config('constants.media_tags'));
                foreach ($broadcast_image->getMedia(config('constants.media_tags')) as $brod_image) {
                    $sendingData['image'][] = [
                        'key' => $brod_image->getKey(),
                        'url' => getMediaUrl($brod_image),
                    ];
                }
            } else {
                $sendingData['linked_images'][] = $broadcast_image->id;
            }

            $params = [
                'sending_time' => $request->get('sending_time', ''),
                'user_id' => Auth::id(),
                'phone' => null,
                'type' => 'message_all',
                'data' => json_encode($sendingData),
                'group_id' => $max_group_id,
            ];

            foreach ($customerArr as $customer) {
                $params['customer_id'] = $customer->id;
                MessageQueue::create($params);

                $message = [
                    'type_id' => $customer->id,
                    'type' => Customer::class,
                    'broadcast_message_id' => $broadcast->id,
                ];
                BroadcastMessageNumber::create($message);
            }
        }
        $message = 'Message sent successfully';

        return response()->json(['code' => 1, 'data' => [], 'message' => $message]);
    }

    public function updateErpStatus(Request $request, $id)
    {
        $lead = ErpLeads::find($id);
        if ($lead->lead_status_id != $request->status) {
            $lead_status = ErpLeadStatus::pluck('name', 'id')->toArray();
            StatusChange::create([
                'model_id' => $id,
                'model_type' => ErpLeads::class,
                'user_id' => Auth::id(),
                'from_status' => $lead_status[$lead->lead_status_id],
                'to_status' => $lead_status[$request->status],
            ]);

            $lead->lead_status_id = $request->status;
            $lead->save();
        }
    }

    public function leadAutoFillInfo(Request $request): JsonResponse
    {
        $product = Product::find($request->get('product_id'));
        $customer = Customer::find($request->get('customer_id'));
        $mediaArr = $product ? $product->getMedia(config('constants.media_tags')) : [];
        $media = [];

        foreach ($mediaArr as $value) {
            $media[] = ['url' => getMediaUrl($value), 'id' => $value->id];
        }

        $price = 0;
        if ($product) {
            $price = (int) $product->price_special_offer > 0 ? (int) $product->price_special_offer : $product->price_inr_special;
        }

        return response()->json([
            'brand' => $product ? $product->brand : '',
            'category' => $product ? $product->category : '1',
            'brand_segment' => $product && $product->brands ? $product->brands->brand_segment : '',
            'shoe_size' => $customer ? $customer->shoe_size : '',
            'gender' => $customer ? $customer->gender : '',
            'media' => $media,
            'price' => $price,
        ]);
    }

    public function erpLeadsHistory(request $request): View
    {
        $erpLeadStatus = ErpLeadStatus::all()->toArray();
        $source = ErpLeadSendingHistory::leftjoin('products', 'products.id', 'erp_lead_sending_histories.product_id')
            ->leftJoin('customers as c', 'c.id', 'erp_lead_sending_histories.customer_id')
            ->leftJoin('erp_leads', 'erp_leads.id', 'erp_lead_sending_histories.lead_id')
            ->leftJoin('erp_lead_status', 'erp_leads.lead_status_id', 'erp_lead_status.id')
            ->orderByDesc('erp_lead_sending_histories.id')
            ->select(['erp_lead_sending_histories.*', 'products.name as product_name', 'c.name as customer_name', 'c.id as customer_id', 'erp_lead_status.name as lead_status']);

        if ($request->get('lead_customer')) {
            $source = $source->where('c.name', 'like', '%'.$request->get('lead_customer').'%');
        }

        if ($request->get('product_name')) {
            $source = $source->where('products.name', 'like', '%'.$request->get('product_name').'%');
        }

        if ($request->get('lead_status')) {
            $source = $source->where('erp_leads.lead_status_id', '=', $request->get('lead_status'));
        }

        if ($request->get('created_at')) {
            $source = $source->whereDate('erp_lead_sending_histories.created_at', '=', $request->get('created_at'));
        }
        $source = $source->paginate(5);
        session()->flashInput($request->input());

        return view('leads.erp.history', [
            'sourceData' => $source,
            'erpLeadStatus' => $erpLeadStatus,
        ]);
    }

    public function erpLeadsStatusCreate(Request $request): RedirectResponse
    {
        $status = new ErpLeadStatus;
        $status->name = $request->add_status;
        $status->save();

        return redirect()->back()->with('success', 'Status Added Successsfully');
    }

    public function erpLeadsStatusUpdate(Request $request): JsonResponse
    {
        $statusModal = ErpLeadStatus::where('id', $request->status_id)->first()->name;

        $template = "Greetings from Solo Luxury Ref: order number $request->id we have updated your order with status : $statusModal.";
        $erp_leads = ErpLeads::find($request->id);

        $history = new ErpLeadStatusHistory;
        $history->lead_id = $request->id;
        $history->old_status = $erp_leads->lead_status_id;
        $history->new_status = $request->status_id;
        $history->user_id = Auth::id();
        $history->save();

        $erp_leads->lead_status_id = $request->status_id;
        $erp_leads->store_website_id = 15;
        $erp_leads->type = 'erp-lead-status-update';
        $erp_leads->save();

        $message = 'Status Updated Successsfully';

        return response()->json(['code' => 200, 'message' => $message, 'template' => $template]);
    }

    public function erpLeadStatusChange(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $status = $request->get('status');

        if (! empty($id) && ! empty($status)) {
            $order = ErpLeads::find($id);

            if ($order->customer->email) {
                if (isset($request->sendmessage) && $request->sendmessage == '1') {
                    //Sending Mail on changing of order status
                    try {
                        $emailClass = (new OrderStatusChangeMail($order))->build();

                        $email = Email::create([
                            'model_id' => $order->id,
                            'model_type' => ErpLeads::class,
                            'from' => $emailClass->fromMailer,
                            'to' => $order->customer->email,
                            'subject' => $emailClass->subject,
                            'message' => $emailClass->render(),
                            'template' => 'erp-lead-status-update',
                            'additional_data' => $order->id,
                            'status' => 'pre-send',
                            'is_draft' => 0,
                        ]);

                        SendEmail::dispatch($email)->onQueue('send_email');
                    } catch (Exception $e) {
                        Log::info('Sending mail issue at the ordercontroller #2215 ->'.$e->getMessage());
                    }
                } else {
                    $emailClass = (new OrderStatusChangeMail($order))->build();

                    $email = Email::create([
                        'model_id' => $order->id,
                        'model_type' => ErpLeads::class,
                        'from' => $emailClass->fromMailer,
                        'to' => $order->customer->email,
                        'subject' => $emailClass->subject,
                        'message' => $emailClass->render(),
                        'template' => 'erplead-status-update',
                        'additional_data' => $order->id,
                        'status' => 'pre-send',
                        'is_draft' => 0,
                    ]);

                    SendEmail::dispatch($email)->onQueue('send_email');
                }
            }
        }
        $message = 'Status Changed Successsfully';

        return response()->json(['Sucess', 200, 'message' => $message]);
    }

    public function enableDisable()
    {
        Setting::set('erp_leads_cron_save', request('status', 0), 'int');

        return respJson(200, 'Updated successfully.');
    }
}
