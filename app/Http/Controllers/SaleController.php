<?php

namespace App\Http\Controllers;
use App\Supplier;
use App\Stage;
use App\Colors;
use App\Brand;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Image;
use App\Product;
use App\Sale;
use App\Setting;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;
use Spatie\Permission\Models\Role;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::latest()->paginate(Setting::get('pagination'));

        $users = $this->getUserArray(User::all());

        return view('sales.index', compact('sales', 'users'));
    }

    public function create(): View
    {
        $data = [];

        $data['date_of_request'] = '';
        $data['sale_persons'] = $this->getUserArray($this->getUsersByRoleName());
        $data['sales_person_name'] = '';
        $data['client_name'] = '';
        $data['client_phone'] = '';
        $data['instagram_handle'] = '';
        $data['description'] = '';
        $data['selected_product'] = '';
        $data['selected_products_array'] = [];
        $data['products_array'] = [];

        $data['allocated_to'] = '';
        $data['users'] = $this->getUserArray(User::all());
        $data['finished_at'] = '';
        $data['check_1'] = '';
        $data['check_2'] = '';
        $data['check_3'] = '';
        $data['sent_to_client'] = '';
        $data['remark'] = '';
        $data['modify'] = 0;
        $data['img_url'] = '';
        $data['img_id'] = '';

        return view('sales.form', $data);
    }

    public function show(Sale $sale): View
    {
        $data = $sale->toArray();
        $data['sale_persons'] = $this->getUserArray($this->getUsersByRoleName());
        $data['users'] = $this->getUserArray(User::all());

        $data['selected_products_array'] = json_decode($sale->selected_product);
        $data['products_array'] = [];
        $data['sale'] = $sale;

        if (! empty($data['selected_products_array'])) {
            foreach ($data['selected_products_array'] as $product_id) {
                $skuOrName = $this->getProductNameSkuById($product_id);
                $data['products_array'][$product_id] = $skuOrName;
            }
        }
        $data['media_tags'] = config('constants.media_tags');

        return view('sales.show', $data);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = new Sale;

        $data = $request->except('_token', 'image');
        $data['author_id'] = Auth::id();
        $data['date_of_request'] = date('Y-m-d');
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['selected_product'] = json_encode($request->input('selected_product'));

        $sale->insert($data);

        $sale_id = DB::getPdo()->lastInsertId();

        if ($request->has('image')) {
            $sale_instance = $sale->find($sale_id);
            $media = MediaUploader::fromSource($request->file('image'))
                ->toDirectory('sale/'.floor($sale_instance->id / config('constants.image_per_folder')))
                ->upload();
            $sale_instance->attachMedia($media, config('constants.media_tags'));
        }

        ActivityConroller::create($sale_id, 'sales', 'create');

        return redirect()->route('sales-item.index');
    }

    public function edit(Sale $sale): View
    {
        $data = $sale->toArray();
        $data['sale_persons'] = $this->getUserArray($this->getUsersByRoleName());
        $data['users'] = $this->getUserArray(User::all());

        $data['selected_products_array'] = json_decode($sale->selected_product);
        $data['products_array'] = [];

        if (! empty($data['selected_products_array'])) {
            foreach ($data['selected_products_array'] as $product_id) {
                $skuOrName = $this->getProductNameSkuById($product_id);
                $data['products_array'][$product_id] = $skuOrName;
            }
        }

        $data['modify'] = 1;

        $image = $sale->getMedia(config('constants.media_tags'))->first();

        if (empty($image)) {
            // nothing
            $data['img_url'] = '';
            $data['img_id'] = '';
        } else {
            $data['img_url'] = getMediaUrl($image);
            $data['img_id'] = $image->id;
        }

        return view('sales.form', $data);
    }

    public function update(Sale $sale, UpdateSaleRequest $request): RedirectResponse
    {

        ActivityConroller::create($sale->id, 'sales', 'update');
        NotificaitonContoller::store('Sale Updated', '', '', $sale->id, $sale->author_id);

        $sale->sales_person_name = $request->input('sales_person_name');
        $sale->client_name = $request->input('client_name');
        $sale->client_phone = $request->input('client_phone');
        $sale->instagram_handle = $request->input('instagram_handle');
        $sale->description = $request->input('description');
        $sale->selected_product = json_encode($request->input('selected_product'));
        $sale->allocated_to = $request->input('allocated_to');
        $sale->finished_at = $request->input('finished_at');
        $sale->check_1 = $request->input('check_1') ? $request->input('check_1') : 0;
        $sale->check_2 = $request->input('check_2') ? $request->input('check_2') : 0;
        $sale->check_3 = $request->input('check_3') ? $request->input('check_3') : 0;
        $sale->sent_to_client = $request->input('sent_to_client');
        $sale->remark = $request->input('remark');

        self::replaceImage($request, $sale);

        $sale->update();

        return redirect()->route('sales-item.index');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $sale->delete();

        return redirect()->route('sales-item.index')
            ->with('success', 'Sale deleted successfully');
    }

    public function selectionGrid(Sale $sale): View
    {
        $products = Product::latest()->paginate(Setting::get('pagination'));
        $roletype = 'Sale';
        $sale_id = $sale->id;

        $selected_products = json_decode($sale->selected_product, true) ?? [];

        $attach_image_tag = config('constants.attach_image_tag');
        $pending_products_count = Product::getPendingProductsCount($roletype);
        $brands = Brand::getAll();
        $colors = (new Colors)->all();
        $stage = new Stage;
        $suppliers = Supplier::getProductSuppliers();

        return view('partials.grid', compact('products', 'roletype', 'sale_id', 'selected_products', 'attach_image_tag', 'pending_products_count', 'brands', 'colors', 'suppliers', 'stage'));
    }

    public static function attachProduct($model_id, $product_id)
    {
        $sale = Sale::findOrFail($model_id);

        $selected_product = json_decode($sale->selected_product, true) ?? [];

        if (! in_array($product_id, $selected_product)) {
            array_push($selected_product, $product_id);
            $action = 'Attached';
        } else {
            if (($key = array_search($product_id, $selected_product)) !== false) {
                unset($selected_product[$key]);
                $action = 'Attach';
            }
        }

        $sale->selected_product = json_encode($selected_product);
        $sale->save();

        return $action;
    }

    public function getUsersByRoleName($roleName = 'Sales')
    {
        $roleID = Role::findByName($roleName);

        $users = User::select('users.id', 'users.name')
            ->where('m.role_id', '=', $roleID->id)
            ->leftJoin('model_has_roles as m', 'm.model_id', '=', 'users.id')
            ->distinct()
            ->get();

        return $users;
    }

    public function getUserArray($users)
    {
        $userArray = [];

        foreach ($users as $user) {
            $userArray[((string) $user->id)] = $user->name;
        }

        return $userArray;
    }

    public function searchProduct(Request $request)
    {
        $q = $request->input('q');

        $results = Product::select('id', 'name', 'sku', 'brand')
            ->where('id', 'LIKE', '%'.$q.'%')
            ->orWhere('sku', 'LIKE', '%'.$q.'%')
            ->orWhere('name', 'LIKE', '%'.$q.'%')
            ->offset(0)
            ->limit(15)
            ->get();

        return $results;
    }

    public function getProductNameSkuById($product_id)
    {
        $product = new Product;

        $product_instance = $product->find($product_id);

        return $product_instance->name ? $product_instance->name : $product_instance->sku;
    }

    public function replaceImage($request, $sale)
    {
        if ($request->input('oldImage') != 0) {
            $results = Media::where('id', $request->input('oldImage'))->get();

            $results->each(function ($media) {
                Image::trashImage($media->basename);
                $media->delete();
            });

            if (! empty($request->file('image'))) {
                $media = MediaUploader::fromSource($request->file('image'))
                    ->toDirectory('sale/'.floor($sale->id / config('constants.image_per_folder')))
                    ->upload();
                $sale->attachMedia($media, config('constants.media_tags'));
            }
        }
    }
}
