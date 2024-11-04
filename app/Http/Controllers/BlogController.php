<?php

namespace App\Http\Controllers;
use App\Http\Controllers;

use App\Http\Requests\UpdateBlogRequest;
use App\Http\Requests\StoreBlogRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use App\Tag;
use App\User;
use Illuminate\Support\Facades\Response;
use DataTables;
use App\Models\Blog;
use App\StoreWebsite;
use App\Models\BlogTag;
use App\Models\BlogHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DataTableColumn;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $blogs = Blog::query();

            if ($request->get('user_id')) {
                $blogs->where('user_id', $request->get('user_id'));
            }
            if ($request->get('storeWebsiteId')) {
                $blogs->where('store_website_id', $request->get('storeWebsiteId'));
            }
            if ($request->get('keyword')) {
                $keyword = request('keyword', '');
                $blogs   = $blogs->where(function ($q) use ($keyword) {
                    $q->where('blogs.idea', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.keyword', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.header_tag', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.title_tag', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.canonical_url', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.meta_desc', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.url_structure', 'LIKE', '%' . $keyword . '%')->orWhere('blogs.url_xml', 'LIKE', '%' . $keyword . '%');
                });
            }
            if (! empty($request->get('date'))) {
                $blogs->whereDate('created_at', $request->get('date'));
            }

            $blogs->with('user', 'blogsTag');
            $blogs = $blogs->orderByDesc('id')->get();

            return Datatables::of($blogs)
                ->addIndexColumn()
                ->addColumn('userName', function ($row) {
                    $user = $row->user ? $row->user->name : 'N/A';

                    return $user;
                })
                ->addColumn('no_index', function ($row) {
                    if ($row->no_index === 1) {
                        return 'Yes';
                    } elseif ($row->no_index === 0) {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('no_follow', function ($row) {
                    if ($row->no_follow === 1) {
                        return 'Yes';
                    } elseif ($row->no_follow === 0) {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('google', function ($row) {
                    if ($row->google == 'yes') {
                        return 'Yes';
                    } elseif ($row->google == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('strong_tag', function ($row) {
                    if ($row->strong_tag == 'yes') {
                        return 'Yes';
                    } elseif ($row->strong_tag == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('italic_tag', function ($row) {
                    if ($row->italic_tag == 'yes') {
                        return 'Yes';
                    } elseif ($row->italic_tag == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('store_website_id', function ($row) {
                    $website = StoreWebsite::where('id', $row->store_website_id)->first();
                    if (empty($website)) {
                        return '';
                    } else {
                        return $website->website;
                    }
                })
                ->addColumn('bing', function ($row) {
                    if ($row->bing == 'yes') {
                        return 'Yes';
                    } elseif ($row->bing == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('checkmobile_friendliness', function ($row) {
                    if ($row->checkmobile_friendliness == 'yes') {
                        return 'Yes';
                    } elseif ($row->checkmobile_friendliness == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('internal_link', function ($row) {
                    if ($row->internal_link == 'yes') {
                        return 'Yes';
                    } elseif ($row->internal_link == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    $createdDate = $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : 'N/A';

                    return $createdDate;
                })
                ->addColumn('facebook_date', function ($row) {
                    $facebookDate = $row->facebook_date ? Carbon::parse($row->facebook_date)->format('Y-m-d') : 'N/A';

                    return $facebookDate;
                })
                ->addColumn('google_date', function ($row) {
                    $googleDate = $row->google_date ? Carbon::parse($row->google_date)->format('Y-m-d') : 'N/A';

                    return $googleDate;
                })
                ->addColumn('instagram_date', function ($row) {
                    $instaDate = $row->instagram_date ? Carbon::parse($row->instagram_date)->format('Y-m-d') : 'N/A';

                    return $instaDate;
                })
                ->addColumn('twitter_date', function ($row) {
                    $twitterDate = $row->twitter_date ? Carbon::parse($row->twitter_date)->format('Y-m-d') : 'N/A';

                    return $twitterDate;
                })
                ->addColumn('bing_date', function ($row) {
                    $bingDate = $row->bing_date ? Carbon::parse($row->bing_date)->format('Y-m-d') : 'N/A';

                    return $bingDate;
                })
                ->addColumn('content', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" data-id="' . $row->id . '" id="ViewContent" data-blog-id="' . $row->id . '" class="ViewContent"><i class="fa fa-eye"></i></a>&nbsp;';

                    return $actionBtn;
                })
                ->addColumn('publish_blog_date', function ($row) {
                    $publishDate = ! empty($row->publish_blog_date) ? Carbon::parse($row->publish_blog_date)->format('Y-m-d') : 'N/A';

                    return $publishDate;
                })
                ->addColumn('plaglarism', function ($row) {
                    if ($row->plaglarism == 'yes') {
                        return 'Yes';
                    } elseif ($row->plaglarism == 'no') {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="javascript:void(0)" data-id="' . $row->id . '" data-blog-id="' . $row->id . '" id="BlogEditModal" class="btn btn-xs pull-left BlogEditData"><i class="fa fa-edit"></i></a>&nbsp;
                    <a href="edit/' . $row->id . '"  data-id="' . $row->id . '" data-blog-id="' . $row->id . '" class="delete-blog btn btn-xs pull-left"><i class="fa fa-trash"></i></a>&nbsp;';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'userName', 'plaglarism', 'facebook_date', 'google_date', 'instagram_date', 'twitter_date', 'strong_tag', 'italic_tag', 'checkmobile_friendliness', 'internal_link', 'content'])
                ->make(true);
        }

        $users          = User::get();
        $store_website  = StoreWebsite::all();
        $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'blogs-listing')->first();

        $dynamicColumnsToShowb = [];
        if (! empty($datatableModel->column_name)) {
            $hideColumns           = $datatableModel->column_name ?? '';
            $dynamicColumnsToShowb = json_decode($hideColumns, true);
        }

        return view('blogs.index', compact('users', 'store_website', 'dynamicColumnsToShowb'));
    }

    public function columnVisbilityUpdate(Request $request): RedirectResponse
    {
        $userCheck = DataTableColumn::where('user_id', auth()->user()->id)->where('section_name', 'blogs-listing')->first();

        if ($userCheck) {
            $column               = DataTableColumn::find($userCheck->id);
            $column->section_name = 'blogs-listing';
            $column->column_name  = json_encode($request->column_blogs);
            $column->save();
        } else {
            $column               = new DataTableColumn();
            $column->section_name = 'blogs-listing';
            $column->column_name  = json_encode($request->column_blogs);
            $column->user_id      = auth()->user()->id;
            $column->save();
        }

        return redirect()->back()->with('success', 'column visiblity Added Successfully!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Illuminate\View\View
    {
        $users   = User::get();
        $allTag  = Tag::get()->toArray();
        $tagName = array_column($allTag, 'tag');

        $tagName = implode(',', $tagName);
        $tagName = "['" . str_replace(',', "','", $tagName) . "']";

        return view('blogs.create', compact('users', 'tagName'));
    }

    public function viewAllHistory(Request $request)
    {
        if ($request->ajax()) {
            $blogsHistory = BlogHistory::query();
            if ($request->get('user_id')) {
                $blogsHistory->where('user_id', $request->get('user_id'));
            }
            if (! empty($request->get('date'))) {
                $blogsHistory->whereDate('created_at', $request->get('date'));
            }

            $blogsHistory->with('user');

            return Datatables::of($blogsHistory)
                ->addIndexColumn()
                ->addColumn('no_index', function ($row) {
                    if ($row->no_index === 1) {
                        return 'Yes';
                    } elseif ($row->no_index === 0) {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('no_follow', function ($row) {
                    if ($row->no_follow === 1) {
                        return 'Yes';
                    } elseif ($row->no_follow === 0) {
                        return 'No';
                    } else {
                        return '';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    $createdDate = $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : 'N/A';

                    return $createdDate;
                })
                ->addColumn('userName', function ($row) {
                    $user = $row->user ? $row->user->name : 'N/A';

                    return $user;
                })
                ->rawColumns(['userName'])
                ->make(true);
        }

        $users = User::get();

        return view('blogs.view-all-history', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request): RedirectResponse
    {

        $blog = Blog::create($request->all());
        if (! empty($blog)) {
            $blogId = $blog->id;
            if (! empty($blog->url_xml) && ! empty($blog->store_website_id)) {
                $this->createSitemap($blog->store_website_id);
            }

            $blogHistory = BlogHistory::create([
                'blog_id'       => $blog->id,
                'plaglarism'    => $blog->plaglarism,
                'user_id'       => ! empty(Auth::user()->id) ? Auth::user()->id : null,
                'internal_link' => $blog->internal_link,
                'external_link' => $blog->external_link,
                'create_time'   => Carbon::now()->format('Y-m-d'),
                'no_index'      => $blog->no_index,
                'no_follow'     => $blog->no_follow,

            ]);

            return redirect()->route('blog.index')->with('message', 'Blog has been create successfully!');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

    public function createSitemap($websiteId)
    {
        $storeWebsite = StoreWebsite::where('id', $websiteId)->first();
        if (! empty($storeWebsite)) {
            $blogData = Blog::where('store_website_id', $websiteId)->whereNotNull('url_xml')->orderByDesc('id')->get();
            if (! empty($blogData)) {
                $FilePath = public_path('sitemap/web_' . $websiteId);

                if (! file_exists($FilePath)) {
                    mkdir($FilePath, 0777, true);
                }
                $output = view('Sitemap.blog')->with(compact('blogData'))->render();
                File::put($FilePath . '/blog.xml', $output);
                response($output)->header('Content-Type', 'application/xml');

                return true;
            }
        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param int   $id
     * @param mixed $titleTags
     * @param mixed $blogId
     *
     * @return \Illuminate\Http\Response
     */
    public function titleTag($titleTags, $blogId)
    {
        foreach ($titleTags as $titleTag) {
            $checkTagExistsOrNot = Tag::where('tag', $titleTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $titleTag,
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'title_tag',
                    'tag_id'  => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'title_tag',
                    'tag_id'  => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }

    public function headerTag($headerTags, $blogId)
    {
        foreach ($headerTags as $headerTag) {
            $checkTagExistsOrNot = Tag::where('tag', $headerTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $headerTag,
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'header_tag',
                    'tag_id'  => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'header_tag',
                    'tag_id'  => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }

    public function italicTag($italicTags, $blogId)
    {
        foreach ($italicTags as $italicTag) {
            $checkTagExistsOrNot = Tag::where('tag', $italicTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $italicTag,
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'italic_tag',
                    'tag_id'  => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'italic_tag',
                    'tag_id'  => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }

    public function strongTag($strongTags, $blogId)
    {
        foreach ($strongTags as $strongTag) {
            $checkTagExistsOrNot = Tag::where('tag', $strongTag)->first();

            if (empty($checkTagExistsOrNot)) {
                $tagAdd = Tag::create([
                    'tag' => $strongTag,
                ]);

                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'strong_tag',
                    'tag_id'  => $tagAdd->id,
                ]);
            } else {
                $blogTag = BlogTag::create([
                    'blog_id' => $blogId,
                    'type'    => 'strong_tag',
                    'tag_id'  => $checkTagExistsOrNot->id,
                ]);
            }
        }

        return true;
    }

    public function show($id)
    {
        $blog = Blog::with('user', 'blogsTag')->where('id', $id)->first();

        if (! empty($blog)) {
            $users              = User::get();
            $headerTags         = $this->headerTagGetWhenEdit($id);
            $headerTagEditValue = implode(',', $headerTags);
            $titleTags          = $this->titleTagGetWhenEdit($id);
            $titleTagEditValue  = implode(',', $titleTags);
            $italicTags         = $this->italicTagGetWhenEdit($id);
            $italicTagEditValue = implode(',', $italicTags);
            $strongTags         = $this->strongTagGetWhenEdit($id);
            $strongTagEditValue = implode(',', $strongTags);

            return view('blogs.show', compact('blog', 'headerTagEditValue', 'titleTagEditValue', 'italicTagEditValue', 'strongTagEditValue', 'users'));
        }

        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): JsonResponse
    {
        $blog = Blog::where('id', $id)->first();
        if (! empty($blog)) {
            $users = User::get();

            $store_website = StoreWebsite::all();
            $returnHTML    = view('blogs.editModal')->with('blog', $blog)->with('users', $users)->with('store_website', $store_website)->render();

            return response()->json(['status' => 'success', 'data' => ['html' => $returnHTML], 'message' => 'Blog'], 200);
        } else {
            return response()->json(['status' => 'error', 'data' => ['not found'], 'message' => 'Not Found!'], 400);
        }
    }

    public function contentView($id): JsonResponse
    {
        $blog = Blog::where('id', $id)->first();
        if (! empty($blog)) {
            $returnHTML = view('blogs.Contentview')->with('blog', $blog)->render();

            return response()->json(['status' => 'success', 'data' => ['html' => $returnHTML], 'message' => 'Blog'], 200);
        } else {
            return response()->json(['status' => 'error', 'data' => ['not found'], 'message' => 'Not Found!'], 400);
        }
    }

    public function allTagsByTagType($type)
    {
        $tags        = [];
        $allTagsType = BlogTag::where('type', $type)->get();
        if (! empty($allTagsType)) {
            foreach ($allTagsType as $value) {
                if (! empty($value->tag->tag)) {
                    $tags[] = $value->tag->tag;
                }
            }
        }

        return $tags;
    }

    public function headerTagGetWhenEdit($blogId)
    {
        $headerTag = BlogTag::query();
        $headerTag = $headerTag->with('tag')->where('type', 'header_tag')->where('blog_id', $blogId)->get();

        $headerTags = [];
        foreach ($headerTag as $value) {
            if (! empty($value->tag->tag)) {
                $headerTags[] = $value->tag->tag;
            }
        }

        return $headerTags;
    }

    public function titleTagGetWhenEdit($blogId)
    {
        $titleTag = BlogTag::query();
        $titleTag = $titleTag->with('tag')->where('type', 'title_tag')->where('blog_id', $blogId)->get();

        $titleTags = [];
        foreach ($titleTag as $value) {
            if (! empty($value->tag->tag)) {
                $titleTags[] = $value->tag->tag;
            }
        }

        return $titleTags;
    }

    public function italicTagGetWhenEdit($blogId)
    {
        $italicTag = BlogTag::query();
        $italicTag = $italicTag->with('tag')->where('type', 'italic_tag')->where('blog_id', $blogId)->get();

        $italicTags = [];
        foreach ($italicTag as $value) {
            if (! empty($value->tag->tag)) {
                $italicTags[] = $value->tag->tag;
            }
        }

        return $italicTags;
    }

    public function strongTagGetWhenEdit($blogId)
    {
        $strongTag = BlogTag::query();
        $italicTag = $strongTag->with('tag')->where('type', 'strong_tag')->where('blog_id', $blogId)->get();

        $italicTags = [];
        foreach ($italicTag as $value) {
            if (! empty($value->tag->tag)) {
                $italicTags[] = $value->tag->tag;
            }
        }

        return $italicTags;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, int $id): RedirectResponse
    {
        $blog = Blog::where('id', $id)->first();

        if (empty($blog)) {
            return redirect()->route('blog.index')->with('error', 'Blog Not Found!');
        }


        $dataUpdate = [
            'user_id'                  => $request->user_id,
            'store_website_id'         => $request->store_website_id,
            'header_tag'               => $request->header_tag,
            'title_tag'                => $request->title_tag,
            'strong_tag'               => $request->strong_tag,
            'italic_tag'               => $request->italic_tag,
            'idea'                     => $request->idea,
            'keyword'                  => $request->keyword,
            'content'                  => $request->content,
            'plaglarism'               => $request->plaglarism,
            'internal_link'            => $request->internal_link,
            'external_link'            => $request->external_link,
            'meta_desc'                => $request->meta_desc,
            'url_structure'            => $request->url_structure,
            'url_xml'                  => $request->url_xml,
            'no_follow'                => $request->no_follow,
            'publish_blog_date'        => ! empty($request->publish_blog_date) ? Carbon::parse($request->publish_blog_date)->format('Y-m-d') : null,
            'no_index'                 => $request->no_index,
            'date'                     => $request->date,
            'facebook'                 => $request->facebook,
            'facebook_date'            => $request->facebook_date,
            'instagram'                => $request->instagram,
            'instagram_date'           => $request->instagram_date,
            'twitter'                  => $request->twitter,
            'twitter_date'             => $request->twitter_date,
            'google'                   => $request->google,
            'google_date'              => $request->google_date,
            'bing'                     => $request->bing,
            'bing_date'                => $request->bing_date,
            'canonical_url'            => $request->canonical_url,
            'checkmobile_friendliness' => $request->checkmobile_friendliness,

        ];

        $blogUpdate = Blog::where('id', $id)->update($dataUpdate);

        if ($blogUpdate) {
            $blogupdateData = Blog::where('id', $id)->first();
            $oldXMlUrl      = $blog->url_xml;
            $newXmlUrl      = $blogupdateData->url_xml;

            if ($oldXMlUrl != $newXmlUrl) {
                $this->createSitemap($blogupdateData->store_website_id);
            }

            BlogHistory::create([
                'blog_id'       => $id,
                'plaglarism'    => $request->plaglarism,
                'user_id'       => ! empty(Auth::user()->id) ? Auth::user()->id : null,
                'internal_link' => $request->internal_link,
                'external_link' => $request->external_link,
                'create_time'   => Carbon::now()->format('Y-m-d'),
                'no_index'      => $request->no_index,
                'no_follow'     => $request->no_follow,

            ]);

            return redirect()->route('blog.index')->with('message', 'Blog has been successfully update!');
        } else {
            return redirect()->route('blog.index')->with('error', 'Something Went Wrong!');
        }
    }

    public function blogTagDeleteByType($blogId, $type)
    {
        return BlogTag::where('blog_id', $blogId)->where('type', $type)->delete();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $blog = Blog::where('id', $id)->first();

        if (empty($blog)) {
            return response()->json(['message' => 'Blog Not Found.']);
        }

        BlogTag::where('blog_id', $id)->delete();
        BlogHistory::where('blog_id', $id)->delete();

        $WebsiteId = $blog->store_website_id;

        $blog = Blog::where('id', $id)->delete();

        if (! empty($WebsiteId)) {
            $this->createSitemap($WebsiteId);
        }
        if ($blog) {
            return response()->json(['status' => 200, 'message' => 'Blog has been successfully deleted!']);
        } else {
            return response()->json(['status' => 200, 'message' => 'Something Went Wrong!']);
        }
    }
}
