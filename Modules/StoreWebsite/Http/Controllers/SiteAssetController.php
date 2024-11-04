<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\SiteDevelopment;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentDocument;
use App\SiteDevelopmentMasterCategory;
use App\SiteDevelopmentStatus;
use App\StoreWebsite;
use App\StoreWebsiteImage;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PDF;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;

class SiteAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $data = [];
        $data['all_store_websites'] = StoreWebsite::all();
        $data['categories'] = SiteDevelopmentCategory::all();
        $data['master_categories'] = SiteDevelopmentMasterCategory::all();
        $data['search_website'] = $request->store_webs ?? '';
        $data['master_cat'] = $request->master_cat ?? '';

        $data['search_category'] = isset($request->categories) ? $request->categories : '';
        $data['site_development_status_id'] = isset($request->site_development_status_id) ? $request->site_development_status_id : [];
        $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments', 'store_websites.id', '=', 'site_developments.website_id');
        if ($data['search_website'] != '') {
            $store_websites = $store_websites->whereIn('store_websites.id', $data['search_website']);
        }
        $data['store_websites'] = $store_websites->where('is_site_asset', 1)->groupBy('store_websites.id')->get();
        $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*')
            ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
            ->where('is_site_asset', 1);

        if ($data['search_category'] != '') {
            $site_development_categories = $site_development_categories->whereIn('site_development_categories.id', $data['search_category']);
        }

        if ($data['master_cat'] != '') {
            $site_development_categories = $site_development_categories->whereIn('site_development_categories.master_category_id', $data['master_cat']);
        }

        if (isset($request->site_development_status_id) && ! empty($request->site_development_status_id)) {
            $site_development_categories = $site_development_categories->where('site_developments.status', $data['site_development_status_id']);
        }
        $data['site_development_categories'] = $site_development_categories->groupBy('site_development_categories.id')->get();
        $data['allUsers'] = User::select('id', 'name')->get();

        return view('storewebsite::site-asset.index', $data);
    }

    /**
     * Download a listing of the images.
     *
     * @return \Illuminate\Http\Response
     */
    public function downaloadSiteAssetData(Request $request)
    {
        $store_website = json_decode($request->download_website_id);
        $media_type = $request->media_type;
        $dir = public_path().'/download_asset';
        if (! is_dir($dir)) {
            mkdir($dir);
        }
        $file_name = 'asset_'.uniqid().'.zip';
        $dir = public_path().'/download_asset/'.$file_name;

        $images = StoreWebsiteImage::leftJoin('media', 'store_website_images.media_id', '=', 'media.id')->whereIn('store_website_images.store_website_id', $store_website)->where('store_website_images.media_type', $media_type)->get();
        if (empty($images)) {
            return redirect()->to('/site-assets')->with('message', 'No Image data found');
        } else {
            $zip = new \ZipArchive;
            $zip->open($dir, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($images as $image) {
                $invoice_file = Storage::disk($image->disk)->path($image->directory.'/'.$image->filename.'.'.$image->extension);
                $zip->addFile($invoice_file, $image->filename.'.'.$image->extension);
            }
            $zip->close();

            return response()->download($dir);
        }
    }

    public function siteCheckList(Request $request): View
    {
        //dd('sdfdsf');
        $data = [];
        $data['allStatus'] = SiteDevelopmentStatus::pluck('name', 'id')->toArray();
        $data['all_store_websites'] = StoreWebsite::all()->pluck('title', 'id');
        $data['categories'] = SiteDevelopmentCategory::all()->pluck('title', 'id');
        $data['search_website'] = isset($request->store_webs) ? $request->store_webs : ['1', '2', '3', '5', '9'];
        $data['search_website_string'] = implode(',', $data['search_website']);
        $data['search_category'] = isset($request->categories) ? $request->categories : [];
        $data['site_development_status_id'] = isset($request->site_development_status_id) ? $request->site_development_status_id : [];
        $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments', 'store_websites.id', '=', 'site_developments.website_id');
        if (is_array($data['search_website'])) {
            if (isset($request->store_webs) && $request->store_webs[0] == '') {
                //$store_websites =  $store_websites->get();
            } else {
                $store_websites = $store_websites->whereIn('store_websites.id', $data['search_website']);
            }
        }

        $data['store_websites'] = $store_websites->where('is_site_list', 1)->groupBy('store_websites.id')->get();

        $site_dev = SiteDevelopment::select(DB::raw('site_development_category_id,site_developments.id as site_development_id,website_id'));

        $site_development_categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.site_development_master_category_id', 'site_developments.status', 'site_dev.website_id', 'site_dev.site_development_id')
            ->join('site_developments', function ($join) use ($request) {
                $join->on('site_development_categories.id', '=', 'site_developments.site_development_category_id');

                if (isset($request->site_development_status_id) && ! empty($request->site_development_status_id)) {
                    $join->whereIn('site_developments.status', $request->site_development_status_id);
                }
            })
            ->joinSub($site_dev, 'site_dev', function ($join) {
                $join->on('site_development_categories.id', '=', 'site_dev.site_development_category_id');
            })
            ->where('is_site_list', 1);

        if (isset($request->categories) && ! empty($request->categories)) {
            //$site_development_categories =  $site_development_categories->where('site_development_categories.id',  $data['search_category']);
            $site_development_categories = $site_development_categories->whereIn('site_development_categories.id', $data['search_category']);
        }

        $data['site_development_categories'] = $site_development_categories->leftJoin('store_development_remarks', 'store_development_remarks.store_development_id', '=', 'site_developments.id')->groupBy('site_development_categories.id')->get();
        // dd($data);
        $data['allUsers'] = User::select('id', 'name')->get();

        return view('storewebsite::site-check-list.index', $data);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $site_development_category_id = $request->get('site_development_category_id', 0);
        $store_website_id = $request->get('store_website_id', 0);
        $site_development_id = $request->get('site_development_id', 0);
        $subject = $request->get('subject', null);
        $message = '';
        $loggedUser = $request->user();

        if ($store_website_id > 0 && ! empty($subject)) {
            $store_website = StoreWebsite::find($store_website_id);

            if (! empty($store_website)) {
                $site_dev_documents = new SiteDevelopmentDocument;
                $site_dev_documents->fill(request()->all());
                $site_dev_documents->created_by = Auth::id();
                $site_dev_documents->save();

                if ($request->hasfile('files')) {
                    foreach ($request->file('files') as $files) {
                        $media = MediaUploader::fromSource($files)
                            ->toDirectory('site_development_document/'.floor($store_website->id / config('constants.image_per_folder')))
                            ->upload();
                        $site_dev_documents->attachMedia($media, config('constants.media_tags'));
                    }

                    $message = '[ '.$loggedUser->name.' ] - #DEVTASK-'.$store_website->id.' - '.$store_website->subject." \n\n".'New attchment(s) called '.$subject.' has been added. Please check and give your comment or fix it if any issue.';

                    // MessageHelper::sendEmailOrWebhookNotification([$store_website->assigned_to, $store_website->team_lead_id, $store_website->tester_id], $message);
                }

                return response()->json(['code' => 200, 'success' => 'Done!'], 200);
            }

            return response()->json(['code' => 500, 'error' => 'Oops, There is no record in database'], 500);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, Please fillup required fields'], 500);
        }
    }

    public function getDocument(Request $request): JsonResponse
    {
        // $id = $request->get("id", 0);

        $site_development_category_id = $request->get('site_development_category_id', 0);
        $store_website_id = $request->get('store_website_id', 0);
        $site_development_id = $request->get('site_development_id', 0);

        if (($site_development_category_id > 0) && ($store_website_id > 0)) {
            $devDocuments = SiteDevelopmentDocument::where(
                [
                    'site_development_category_id' => $site_development_category_id,
                    'store_website_id' => $store_website_id,
                ]
            )->with(['creator'])->latest()->get();

            $html = view('storewebsite::site-check-list.partials.document-list', compact('devDocuments'))->render();

            return response()->json(['code' => 200, 'data' => $html], 200);
        } else {
            return response()->json(['code' => 500, 'error' => 'Oops, id is required field'], 500);
        }
    }

    /**
     * Download a listing of the images.
     *
     * @return \Illuminate\Http\Response
     */
    public function downaloadSiteCheckListData(Request $request)
    {
        $site_developments = SiteDevelopment::query();

        if (isset($request->website_id) && ! empty($request->website_id)) {
            $site_developments = $site_developments->where('website_id', $request->website_id);
        }

        if (isset($request->status) && ! empty($request->status)) {
            $site_developments = $site_developments->whereIn('status', $request->status);
        }
        $site_developments = $site_developments->with(['lastRemark', 'category', 'site_development_status'])
            ->get();

        $pdf = PDF::loadView('storewebsite::site-check-list.partials.export-pdf-data', [
            'site_developments' => $site_developments,
            'title' => 'Site Check List Download',
        ]);

        return $pdf->download('Site-Check-List.pdf');
    }
}
