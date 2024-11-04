<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddResourceCatResourceImgRequest;
use App\Http\Requests\AddResourceResourceImgRequest;
use App\Http\Requests\EditResourceCatResourceImgRequest;
use App\Http\Requests\SaveRemarksResourceImgRequest;
use App\Models\EmailReceiverMaster;
use App\Models\ResourceRemarksHistory;
use App\Models\ResourceStatus;
use App\Models\ResourceStatusHistory;
use App\ResourceCategory;
use App\ResourceImage;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ResourceImgController extends Controller
{
    public function index(Request $request)
    {
        $old = $request->old('parent_id');
        $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();
        $categories = ResourceCategory::where('parent_id', '=', 0)->get();
        $sub_categories = ResourceCategory::where('parent_id', '!=', 0)->get();

        $query = ResourceImage::where('is_pending', '=', 0);
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        DB::enableQueryLog();

        $query->where(function ($query) use ($request) {
            if ($request->term) {
                $query = $query->where('url', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('updated_at', 'LIKE', '%'.$request->term.'%');
            }
            if ($request->category) {
                $query = $query->orwhereIn('cat_id', $request->category);
            }
            if ($request->sub_category) {
                $query = $query->orwhereIn('sub_cat_id', $request->sub_category);
            }

            return $query;
        });

        $ResourceStatus = ResourceStatus::all();

        $allresources = $query->orderByDesc('id')->paginate(15)->appends(request()->except(['page']));

        //Email Reciever
        $emailReceivRec = EmailReceiverMaster::where('module_name', 'resource')->first();
        if ($emailReceivRec) {
            if ($emailReceivRec->configs) {
                $emailRecConfig = json_decode($emailReceivRec->configs);

                if ($emailRecConfig->cat) {
                    $emailReceivRec->sub_cat_list = ResourceCategory::where('parent_id', $emailRecConfig->cat)->get();
                }
            }
        }

        if ($request->ajax()) {
            Log::info('enter in ajax');
            LOG::info(DB::getQueryLog());

            return response()->json([
                'tbody' => view('resourceimg.partial_index', compact('allresources', 'Categories', 'categories', 'sub_categories', 'ResourceStatus'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $allresources->render(),
                'count' => $allresources->total(),
            ], 200);
        } else {
            return view('resourceimg.index', compact('Categories', 'categories', 'allresources', 'sub_categories', 'ResourceStatus', 'emailReceivRec'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
    }

    public function resourceimgStatusHistories($id): JsonResponse
    {
        $datas = ResourceStatusHistory::with(['user', 'newValue', 'oldValue'])
            ->where('resource_images_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $postId = $request->input('postId');
        $selectedStatus = $request->input('selectedStatus');

        $ResourceImage = ResourceImage::find($postId);
        $history = new ResourceStatusHistory;
        $history->resource_images_id = $postId;
        $history->old_value = $ResourceImage->status_id;
        $history->new_value = $selectedStatus;
        $history->user_id = Auth::user()->id;
        $history->save();

        $ResourceImage->status_id = $selectedStatus;
        $ResourceImage->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function statuscolor(Request $request): RedirectResponse
    {
        $status_color = $request->all();
        foreach ($status_color['color_name'] as $key => $value) {
            $bugstatus = ResourceStatus::find($key);
            $bugstatus->status_color = $value;
            $bugstatus->save();
        }

        return redirect()->back()->with('success', 'The status color updated successfully.');
    }

    public function resourceStatusCreate(Request $request): JsonResponse
    {
        try {
            $status = new ResourceStatus;
            $status->status_name = $request->status_name;
            $status->save();

            return response()->json(['code' => 200, 'message' => 'status Create successfully']);
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function searchResourceimg(Request $request): View
    {
        $query = ResourceImage::where('is_pending', '=', 0)->select();

        $query->where(function ($query) use ($request) {
            if ($request->term) {
                $query = $query->where('url', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$request->term.'%')
                    ->orWhere('updated_at', 'LIKE', '%'.$request->term.'%');
            }
            if ($request->category) {
                $query = $query->orwhereIn('cat_id', $request->category);
            }
            if ($request->sub_category) {
                $query = $query->orwhereIn('sub_cat_id', $request->sub_category);
            }
        });

        $allresources = $query->orderByDesc('id')->get();

        return view('resourceimg.partial_index', compact('allresources'));
    }

    public function addResourceCat(AddResourceCatResourceImgRequest $request): RedirectResponse
    {
        $input = $request->all();
        if (! ResourceCategory::create($input)) {
            return redirect()->back()->with('danger', 'Something went wrong, Please try again.');
        } else {
            return redirect()->back()->with('success', 'New resource category added successfully.');
        }
    }

    public function editResourceCat(ResourceCategory $category, EditResourceCatResourceImgRequest $request)
    {
        $input = $request->all();
        if ($input['type'] == 'edit') {
            if ($request->method() === 'POST') {
                if ($request->input('title')) {
                    $category = $category->find($request->input('parent_id'));
                    $category->title = $request->input('title');
                    if (! $category->save()) {
                        return redirect()->route('resourceimg.index')->with('danger', 'Something went wrong, Please try again.');
                    } else {
                        return redirect()->route('resourceimg.index')->with('success', 'Resource category updated successfully.');
                    }
                } else {
                    $old = $request->input('parent_id');
                    $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
                        ->selected($old ? $old : 1)
                        ->renderAsDropdown();
                    $category = $category->find($request->input('parent_id'));
                    $title = $category->title;

                    return view('resourceimg.editCategory', compact('Categories', 'title'));
                }
            }
        }

        if ($input['type'] == 'delete') {
            $category_instance = new ResourceCategory;
            $category = $category_instance->find($request->input('parent_id'));

            if (ResourceCategory::isParent($category->id)) {
                return redirect()->back()->with('danger', 'Can\'t delete Parent category. Please delete all the childs first');
            }

            if ($category->id == 1) {
                return redirect()->back()->with('danger', 'Can\'t be delete');
            }
            $category->delete();

            return redirect()->back()->with('success', 'deleted successfully.');
        }

        return redirect()->back()->with('danger', 'Method not allowed, Please try again.');
    }

    public function addResource(AddResourceResourceImgRequest $request): JsonResponse
    {
        $input = $request->all();
        try {
            if ($request->hasFile('image')) {
                $images = $request->file('image');
                foreach ($images as $image) {
                    $name = uniqid().time().'.'.$image->getClientOriginalExtension();
                    $destinationPath = public_path('/category_images');
                    $image->move($destinationPath, $name);
                    $input['images'][] = $name;
                }
            }

            if ($request->image2) {
                $image = $request->image2;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = uniqid().time().'.'.'png';
                $destinationPath = public_path('/category_images');
                File::put($destinationPath.'/'.$imageName, base64_decode($image));
                $input['images'][] = $imageName;
            }
            $input['images'] = ($request->hasFile('image') || $request->image2) ? json_encode($input['images']) : '';

            if (ResourceImage::create($input)) {
                return response()->json([
                    'status' => 'success',
                    'msg' => 'New Resource image added successfully.',
                ]);
            } else {
                return response()->json([
                    'errors' => [
                        '' => 'Something went wrong,Please try again.',
                    ],
                ]);
            }
        } catch (Exception) {
            return response()->json([
                'errors' => [
                    '' => 'Error while uploading file.',
                ],
            ]);
        }
    }

    public function deleteResource(Request $request)
    {
        if ($request->input('button_type') == 'delete') {
            if ($request->input('id')) {
                $ResourceImage = new ResourceImage;
                $Image = $ResourceImage->find($request->input('id'));
                $file_path = public_path().'/category_images/'.$Image->image1;
                if (isset($Image->image1) && file_exists(@$file_path)) {
                    unlink($file_path);
                }
                $file_path = public_path().'/category_images/'.@$Image->image2;
                if (isset($Image->image2) && file_exists(@$file_path)) {
                    unlink($file_path);
                }
                $Image->delete();

                return redirect()->back()->with('success', 'Resource removed successfully.');
            } else {
                return redirect()->back()->with('danger', 'Requested id not found.');
            }
        } elseif ($request->input('button_type') == 'edit') {
            $allresources = ResourceImage::getData();
            $old = $request->old('cat_id');
            $allCategoriesDropdown = Category::attr(['name' => 'cat_id', 'class' => 'form-control'])->selected($old ? $old : 1)->renderAsDropdown();

            return view('resourceimg.edit', compact('allCategoriesDropdown', 'allresources'));
        }

        return redirect()->back()->with('danger', 'Requested params not found.');
    }

    public function imagesResource($id)
    {
        $ResourceImage = new ResourceImage;
        $allresources = $ResourceImage->find($id);
        $title = '';
        if ($allresources) {
            $categories = ResourceCategory::where('id', '=', $allresources->cat_id)->get()->first();
            $parent_id = $categories->parent_id;
            $id = $categories->id;
            if ($parent_id == 0) {
                $title = $categories->title;
            } else {
                $titlestr = [];
                while ($parent_id != 0) {
                    $categories = ResourceCategory::where('id', '=', $id)->get()->first();
                    $titlestr[] = $categories->title;
                    $id = $parent_id = $categories->parent_id;
                }
                krsort($titlestr);
                $title = implode(' >> ', $titlestr);
            }
            $url = $allresources->url;
            $description = $allresources->description;

            return view('resourceimg.images', compact('allresources', 'title', 'url', 'description'));
        } else {
            return redirect()->route('resourceimg.index');
        }
    }

    /**
     * This method used for show image in resourceimg page
     */
    public function showImagesResource(Request $request)
    {
        $ResourceImage = new ResourceImage;
        $allresources = $ResourceImage->find($request->id);
        if ($allresources) {
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('resourceimg.partials.modal-show-images', compact('allresources'))->render(),
                ], 200);
            }
        } else {
            return redirect()->route('resourceimg.index');
        }
    }

    /**
     * @SWG\Post(
     *   path="/values-as-per-category",
     *   tags={"Documents"},
     *   summary="post Documents values as per category",
     *   operationId="get-document-per-category",
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
    public function getSubCategoryByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selected' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }
        $sub = ResourceCategory::where('parent_id', $request->selected)->get();

        $output = '';

        foreach ($sub as $sub_cat) {
            $output .= '<option value="'.$sub_cat['id'].'">'.$sub_cat['title'].'</option>';
        }
        echo $output;
    }

    public function pending(Request $request)
    {
        $old = $request->old('parent_id');
        $Categories = ResourceCategory::attr(['name' => 'parent_id', 'class' => 'form-control'])
            ->selected($old ? $old : 1)
            ->renderAsDropdown();
        $categories = ResourceCategory::where('parent_id', '=', 0)->get();
        $query = ResourceImage::where('is_pending', '=', 1);
        if ($request->id) {
            $query = $query->where('id', $request->id);
        }
        if ($request->term) {
            $query = $query->where('url', 'LIKE', '%'.$request->term.'%')
                ->orWhere('created_at', 'LIKE', '%'.$request->term.'%')
                ->orWhere('updated_at', 'LIKE', '%'.$request->term.'%');
        }
        $allresources = $query->orderByDesc('id')->paginate(15)->appends(request()->except(['page']));
        if ($request->ajax()) {
            Log::info('enter in ajax');

            return response()->json([
                'tbody' => view('resourceimg.partial_pending', compact('allresources', 'Categories', 'categories'))->with('i', ($request->input('page', 1) - 1) * 5)->render(),
                'links' => (string) $allresources->render(),
                'count' => $allresources->total(),
            ], 200);
        } else {
            return view('resourceimg.pending', compact('Categories', 'categories', 'allresources'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }
    }

    public function activateResourceCat(Request $request): RedirectResponse
    {
        $ids = $request->id;
        foreach ($ids as $id) {
            $resourceImage = ResourceImage::findorfail($id);
            $resourceImage->is_pending = 0;
            $resourceImage->created_by = Auth::user()->name;
            $resourceImage->update();
        }

        return redirect()->back()->with('success', 'New Resource image added successfully.');
    }

    public function saveRemarks(SaveRemarksResourceImgRequest $request): JsonResponse
    {

        $input = $request->except(['_token']);
        $input['added_by'] = Auth::user()->id;
        ResourceRemarksHistory::create($input);

        return response()->json(['code' => 200, 'data' => $input]);
    }

    public function getRemarksHistories(Request $request): JsonResponse
    {
        $datas = ResourceRemarksHistory::with(['user'])
            ->where('resource_images_id', $request->resource_images_id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function getResourcesImages(Request $request): JsonResponse
    {
        $datas = ResourceImage::where('id', $request->resource_images_id)->first();

        $html = '';
        if (! empty($datas)) {
            if (isset($datas->image1)) {
                $image1Url = url('/category_images/'.$datas->image1);

                $html .= '<div class="col-md-3"> <img onclick="OpenModel(this.id)" id="myImg1" class="myImg" src="'.$image1Url.'" alt="'.$image1Url.'" style="width: 100% !important;height: 50px !important;"> </div>';
            }

            if (isset($datas->image2)) {
                $image2Url = url('/category_images/'.$datas->image2);

                $html .= '<div class="col-md-3"> <img onclick="OpenModel(this.id)" id="myImg2" class="myImg" src="'.$image2Url.'" alt="'.$image2Url.'" style="width: 100% !important;height: 50px !important;"> </div>';
            }

            if (isset($datas->images)) {
                if ($datas->images != null) {
                    foreach (json_decode($datas->images) as $image) {
                        $imageUrl = url('/category_images/'.$image);

                        $html .= '<div class="col-md-3" style="margin-top: 15px"> <img id="myShowImg" img-id="'.$datas->id.'" src="'.$imageUrl.'" style="width: 100% !important;height: 50px !important;"> </div>';
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'html' => $html,
            'message' => 'Images get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function show($id)
    {
        $resImg = ResourceImage::find($id);

        return ['status' => true, 'data' => $resImg];
    }
}
