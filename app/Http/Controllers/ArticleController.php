<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|array|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        $articles = Article::paginate(100);

        return View('articles.index',
            compact('articles')
        );
    }

    /**
     * Get Broken Links Details
     * Function for display
     *
     * @return JsonResponse response
     */
    public function updateTitle(Request $request): JsonResponse
    {
        $article        = Article::findOrFail($request['id']);
        $article->title = $request['article_title'];
        $article->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Title Updated',
        ]);
    }

    /**
     * Updated Title
     * Function for display
     *
     * @return JsonResponse response
     */
    public function updateDescription(Request $request): JsonResponse
    {
        $article              = Article::findOrFail($request['id']);
        $article->description = $request['article_desc'];
        $article->save();

        return response()->json([
            'type'    => 'success',
            'message' => 'Description Updated',
        ]);
    }
}
