<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBloggerEmailTemplateRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\BloggerEmailTemplate;

class BloggerEmailTemplateController extends Controller
{
    protected $data;

    public function index(): View
    {
        $template = BloggerEmailTemplate::first();
        if (! $template) {
            $template = BloggerEmailTemplate::create([]);
        }
        $this->data['template'] = $template;

        return view('blogger.email-template', $this->data);
    }

    public function update(BloggerEmailTemplate $bloggerEmailTemplate, UpdateBloggerEmailTemplateRequest $request): RedirectResponse
    {
        $bloggerEmailTemplate->fill($request->all())->save();

        return redirect()->back()->withSuccess('Template Successfully Updated');
    }
}
