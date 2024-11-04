<?php

namespace App\Providers;

use App\Brand;
use App\CallBusyMessage;
use App\Category;
use App\ChatMessage;
use App\Email;
use App\Exceptions\Handler;
use App\Helpers\TranslationLambdaHelper;
use App\Models\GoogleDocsCategory;
use App\Observers\BrandObserver;
use App\Observers\CallBusyMessageObserver;
use App\Observers\ChatMessageIndexObserver;
use App\Observers\ChatMessageObserver;
use App\Observers\EmailObserver;
use App\Observers\MediaObserver;
use App\Observers\ReplyCategoryObserver;
use App\Observers\ScrappedCategoryMappingObserver;
use App\ReplyCategory;
use App\ScrapedProducts;
use Facebook\Facebook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Plank\Mediable\Media;
use Studio\Totem\Totem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('defaultClient', function () {
            return Http::timeout(60);
        });

        //Force assets to ssl
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
        //
        Schema::defaultStringLength(191);

        // Custom blade view directives
        Blade::directive('icon', function ($expression) {
            return "<?php echo icon($expression); ?>";
        });

        Totem::auth(function ($request) {
            return Auth::check();
        });

        if (in_array(app('request')->ip(), config('debugip.ips'))) {
            config(['app.debug' => true]);
            config(['debugbar.enabled' => true]);
        }

        Validator::extend('valid_base', function ($attribute, $value, $parameters, $validator) {
            if (base64_decode($value, true) !== false) {
                return true;
            } else {
                return false;
            }
        }, 'image is not valid base64 encoded string.');

        CallBusyMessage::observe(CallBusyMessageObserver::class);

        Paginator::useBootstrap();

        Facades\View::composer(['googledocs.index', 'development.flagtask', 'development.issue', 'task-module.show', 'task-module.*'], function (View $view) {
            $google_docs_category = GoogleDocsCategory::get()->pluck('name', 'id')->toArray();
            if (count($google_docs_category) > 0) {
                $view->with('googleDocCategory', $google_docs_category);
            } else {
                $view->with('googleDocCategory', []);
            }
        });

        /**
         * @param  mixed|null  $data
         * @param  mixed  $message
         * @param  \Throwable|null  $e
         * @param  int  $code
         * @param  array  $headers
         * @return mixed|null
         */
        Response::macro(name: 'jsonResponse', macro: function (mixed $message = '', bool $success = true, mixed $data = null, ?\Throwable $e = null, int $code = Response::HTTP_OK, $statusMessages = [], array $headers = []) {
            $response = [];
            $response['success'] = $success;

            if ($data) {
                $response['data'] = $data;
            }
            if ($message) {
                $response['message'] = $message;
            }
            if (count($statusMessages)) {
                $response['status_messages'] = $statusMessages;
            }
            if ($e && config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ];
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json($response, $code, $headers);
        });

        Builder::macro('whereLike', function ($columns, $search) {
            $this->where(function ($query) use ($columns, $search) {
                foreach (Arr::wrap($columns) as $column) {
                    $query->orWhere($column, $search);
                }
            });

            return $this;
        });

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page'): LengthAwarePaginator {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage)->values(),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        // $arrReplyCategoriesViewRouteList = [
        //     "development.issue.index",
        //     "livechat.get.tickets",
        //     "livechat.tickets.update-ticket",
        //     "livechat.tickets.approve-ticket",
        //     "livechat.tickets.ticket-data"
        // ];
        // Facades\View::composer('*', function ($view) {
        //     $reply_categories = \App\ReplyCategory::where('parent_id', 0)->orderBy('name')->get();
        //     $view->with('reply_categories', $reply_categories);
        // });

        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

        $this->bootEvent();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Facebook::class, function ($app) {
            return new Facebook(config('facebook.config'));
        });
        $this->app->bind(\Illuminate\Contracts\Debug\ExceptionHandler::class, Handler::class);
        $this->app->singleton(ScrapedProducts::class);
        $this->app->singleton('translation-lambda-helper', TranslationLambdaHelper::class);
    }

    public function bootEvent(): void
    {
        Brand::observe(BrandObserver::class);
        Email::observe(EmailObserver::class);
        Media::observe(MediaObserver::class);
        ChatMessage::observe(ChatMessageObserver::class);
        ChatMessage::observe(ChatMessageIndexObserver::class);
        Category::observe(ScrappedCategoryMappingObserver::class);
        ReplyCategory::observe(ReplyCategoryObserver::class);
    }
}
