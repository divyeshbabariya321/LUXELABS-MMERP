<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadImagesBloggerProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use App\User;
use App\Brand;
use App\Blogger;
use App\Helpers;
use App\ReplyCategory;
use App\BloggerProduct;
use Illuminate\Http\Request;
use App\Http\Requests\CreateBloggerProductRequest;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Exception;

class BloggerProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session()->forget('active_tab');

            return $next($request);
        });
    }

    public function store(BloggerProduct $bloggerProduct, CreateBloggerProductRequest $request): RedirectResponse
    {
        $bloggerProduct->create($request->all());

        return redirect()->route('blogger.index')->withSuccess('You have successfully saved a blogger product record');
    }

    public function update(BloggerProduct $bloggerProduct, CreateBloggerProductRequest $request): RedirectResponse
    {
        $blogger = $bloggerProduct->blogger;
        if ($request->has('default_phone')) {
            $blogger->default_phone = $request->get('default_phone');
        }
        if ($request->has('whatsapp_number')) {
            $blogger->default_phone = $request->get('whatsapp_number');
        }
        $blogger->save();
        $bloggerProduct->fill($request->all())->save();

        return redirect()->route('blogger.index')->withSuccess('You have successfully updated a blogger product record');
    }

    public function show(BloggerProduct $blogger_product, Blogger $blogger, Brand $brand): View
    {
        $this->data['bloggers']         = $blogger->pluck('name', 'id');
        $this->data['brands']           = $brand->pluck('name', 'id');
        $this->data['blogger_product']  = $blogger_product;
        $this->data['reply_categories'] = ReplyCategory::all();
        $this->data['users_array']      = Helpers::getUserArray(User::all());

        return view('blogger.show', $this->data);
    }

    public function uploadImages(BloggerProduct $bloggerProduct, UploadImagesBloggerProductRequest $request): Response
    {

        $uploaded_images = [];
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $image) {
                    $media = MediaUploader::fromSource($image)->toDirectory('blogger-images')->upload();
                    array_push($uploaded_images, $media);
                    $bloggerProduct->attachMedia($media, config('constants.media_tags'));
                }
            } catch (Exception $exception) {
                return response($exception->getMessage(), $exception->getCode());
            }
        }

        return response($uploaded_images);
    }

    public function getImages(BloggerProduct $bloggerProduct): Response
    {
        return response($bloggerProduct->getMedia(config('constants.media_tags')));
    }
}
