<?php

namespace App\Http\Controllers;

use App\Product;
use Exception;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use seo2websites\PriceComparisonScraper\PriceComparisonScraper;
use seo2websites\PriceComparisonScraper\PriceComparisonScraperSites;

class PriceComparisionController extends Controller
{
    /**
     * @SWG\Get(
     *   path="/price_comparision/{type}",
     *   tags={"Price Comparision"},
     *   summary="Price Comparision",
     *   operationId="price-comparision",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     *
     * @param  mixed  $name
     */
    public function index($name): JsonResponse
    {
        if (empty($name)) {
            return response()->json([
                'message' => 'No Name Found',
            ]);
        }

        $sites = PriceComparisonScraperSites::where('name', 'LIKE', '%'.$name.'%')->first();
        if (! $sites) {
            return response()->json([
                'message' => 'No Site Found',
            ]);
        } else {
            return response()->json([
                'name' => $sites->name,
                'url' => $sites->url,
                'shoes' => $sites->url_cat_shoes,
                'bags' => $sites->url_cat_bags,
                'clothing' => $sites->url_cat_clothing,
                'accessories' => $sites->url_cat_accessories,
            ]);
        }
    }

    /**
     * @SWG\Post(
     *   path="/price_comparision/store",
     *   tags={"Price Comparision"},
     *   summary="Store Price Comparision",
     *   operationId="store-price-comparision",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function storeComparision(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'sku' => 'required',
            'product_url' => 'required',
            'country_code' => 'required',
            'currency' => 'required',
            'price' => 'required',
            'shipping' => 'required',
            'checkout_price' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $name = $request->name;

        $site = PriceComparisonScraperSites::where('name', 'LIKE', '%'.$name.'%')->first();

        if (! $site) {
            return response()->json([
                'message' => 'No Site Found',
            ]);
        } else {
            $category = $request->category;
            $sku = $request->sku;
            $product_url = $request->product_url;
            $country_code = $request->country_code;
            $currency = $request->currency;
            $price = $request->price;
            $shipping = $request->shipping;
            $checkout_price = $request->checkout_price;

            //validation
            $empty = [];
            if (empty($category) || empty($sku) || empty($product_url) || empty($country_code) || empty($currency) || empty($price) || empty($shipping) || empty($checkout_price)) {
                if (empty($category)) {
                    array_push($empty, 'category');
                }

                if (empty($sku)) {
                    array_push($empty, 'sku');
                }

                if (empty($product_url)) {
                    array_push($empty, 'product_url');
                }

                if (empty($currency)) {
                    array_push($empty, 'currency');
                }

                if (empty($country_code)) {
                    array_push($empty, 'country_code');
                }

                if (empty($price)) {
                    array_push($empty, 'price');
                }

                if (empty($shipping)) {
                    array_push($empty, 'shipping');
                }

                if (empty($checkout_price)) {
                    array_push($empty, 'checkout_price');
                }

                $message = implode(' , ', $empty);

                return response()->json([
                    'message' => 'Cannot be empty '.$message,
                ]);
            } else {
                $checkIfExist = PriceComparisonScraper::where('price_comparison_site_id', $site->id)->where('category', $request->category)->where('product_url', $request->product_url)->where('sku', $request->sku)->where('country_code', $request->country_code)->where('currency', $request->currency)->where('price', $request->price)->where('shipping', $request->shipping)->where('checkout_price', $request->checkout_price)->first();

                try {
                    if (! $checkIfExist) {
                        $priceSave = new PriceComparisonScraper;
                        $priceSave->price_comparison_site_id = $site->id;
                        $priceSave->category = $request->category;
                        $priceSave->sku = $request->sku;
                        $priceSave->product_url = $request->product_url;
                        $priceSave->country_code = $request->country_code;
                        $priceSave->currency = $request->currency;
                        $priceSave->price = $request->price;
                        $priceSave->shipping = $request->shipping;
                        $priceSave->checkout_price = $request->checkout_price;
                        $priceSave->save();
                    } else {
                        $checkIfExist->price_comparison_site_id = $site->id;
                        $checkIfExist->category = $request->category;
                        $checkIfExist->sku = $request->sku;
                        $checkIfExist->product_url = $request->product_url;
                        $checkIfExist->country_code = $request->country_code;
                        $checkIfExist->currency = $request->currency;
                        $checkIfExist->price = $request->price;
                        $checkIfExist->shipping = $request->shipping;
                        $checkIfExist->checkout_price = $request->checkout_price;
                        $checkIfExist->save();
                    }

                    return response()->json([
                        'message' => 'Saved SuccessFully',
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'message' => 'Something Went Wrong',
                    ]);
                }
            }
        }
    }

    /**
     * @SWG\Post(
     *   path="/price_comparision/details",
     *   tags={"Price Comparision"},
     *   summary="Send Price Comparision",
     *   operationId="send-price-comparision",
     *
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=406, description="not acceptable"),
     *   @SWG\Response(response=500, description="internal server error"),
     *
     *      @SWG\Parameter(
     *          name="mytest",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     * )
     */
    public function sendDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required',
            'country' => 'required',
            'lang_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        //checking if we getting proper request
        $message = $this->generate_erp_response('price_compare.failed.validation', 0, 'Please Send Both SKU and Country', request('lang_code'));
        if (empty($request->sku) || empty($request->country)) {
            return response()->json([
                'status' => 'failed',
                'message' => $message,
            ]);
        }
        $internationCountriesCount = 5;

        //getting product
        $product = Product::getProductBySKU($request->sku);
        if (! $product) {
            $sku = explode('-', $request->sku);
            $product = Product::whereIn('sku', $sku)->first();
        }
        if ($product) {
            //getting product category
            $category = $product->product_category;
            if ($category) {
                $categoryArray = []; //storing the category in array
                //storing in category array
                $categoryArray[] = $category->title;
                //checking if category is parent or child
                $isParentCategory = $category->isParent($category->id);
                //if not parent category
                if (! $isParentCategory) {
                    //getting category parent
                    $parent = $category->parent;
                    //storing data in category array
                    $categoryArray[] = $parent->title;
                }

                //search in Price comparision table using array

                $outputArray = []; //output array
                $idArray = [];
                $priceComparisonId = [];

                //getting local data
                $resultWithCountries = PriceComparisonScraper::whereIn('category', $categoryArray)
                    ->where('country_code', $request->country)
                    ->where('currency', 'EUR')
                    ->groupBy('price_comparison_site_id')
                    ->take(3)
                    ->get();

                //storing locat data for output
                foreach ($resultWithCountries as $resultWithCountry) {
                    $percentage = $resultWithCountry->getTheDiffrence();
                    $priceComparisonId[] = $resultWithCountry->price_comparison_site_id;
                    $data['name'] = ($resultWithCountry->price_comparison_site) ? $resultWithCountry->price_comparison_site->name : 'N/A';
                    $data['currency'] = $resultWithCountry->currency;
                    $data['price'] = $resultWithCountry->addPrice($product->price, $percentage);
                    $data['country_code'] = $resultWithCountry->country_code;
                    $outputArray[] = $data;
                    $idArray[] = $resultWithCountry->id;
                }

                //if we dont get any local price
                if (count($idArray) == 0) {
                    $resultWithoutCountries = PriceComparisonScraper::whereIn('category', $categoryArray)->where('currency', 'EUR')->groupBy('price_comparison_site_id')->take(5)->get();
                } else {
                    //exclude the price and site which are already included
                    $resultWithoutCountries = PriceComparisonScraper::whereIn('category', $categoryArray)
                        ->whereNotIn('id', $idArray)
                        ->where('currency', 'EUR')
                        ->whereNotIn('price_comparison_site_id', $priceComparisonId)
                        ->groupBy('price_comparison_site_id')
                        ->take($internationCountriesCount)
                        ->get();
                }

                //getting international results
                foreach ($resultWithoutCountries as $resultWithoutCountry) {
                    $percentage = $resultWithoutCountry->getTheDiffrence();
                    $data['name'] = ($resultWithoutCountry->price_comparison_site) ? $resultWithoutCountry->price_comparison_site->name : 'N/A';
                    $data['currency'] = $resultWithoutCountry->currency;
                    $data['price'] = $resultWithoutCountry->addPrice($product->price, $percentage);
                    $data['country_code'] = $resultWithoutCountry->country_code;
                    $outputArray[] = $data;
                }

                //checking when we dont have any output
                if (count($outputArray) == 0) {
                    $message = $this->generate_erp_response('price_compare.failed.no_price_comparision', 0, 'No Price Comparision Found', request('lang_code'));

                    return response()->json([
                        'status' => 'failed',
                        'message' => $message,
                    ]);
                } else {

                    return response()->json([
                        'status' => 'success',
                        'results' => $outputArray,
                    ]);
                }
            } else {
                $message = $this->generate_erp_response('price_compare.failed', 0, 'No Category Found', request('lang_code'));

                //if not category found response
                return response()->json([
                    'status' => 'failed',
                    'message' => $message,
                ]);
            }
        } else {
            //not product found with sku response
            return response()->json([
                'status' => 'failed',
                'message' => 'No Product Found For This SKU',
            ]);
        }
    }

    public function testPriceComparison()
    {
        // Setup Chrome options
        $options = new ChromeOptions;
        $options->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
        ]);

        // Setup capabilities
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        // Start the session
        $driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);

        try {
            // Open a webpage
            $driver->get('https://www.farfetch.com/AF/shopping/men/balenciaga-10xl-sneakers-item-24460050.aspx');
            $content = $driver->getPageSource();
            dd($content);
            //            echo $driver->getTitle();
        } finally {
            // Close the session
            $driver->quit();
        }

    }
}
