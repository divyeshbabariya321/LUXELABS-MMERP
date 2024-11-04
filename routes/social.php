<?php

use App\Http\Controllers\SocialController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\Social\SocialAdsController;
use App\Http\Controllers\Social\SocialPostController;
use App\Http\Controllers\SocialAccountPostController;
use App\Http\Controllers\Social\SocialAdsetController;
use App\Http\Controllers\Social\SocialConfigController;
use App\Http\Controllers\SocialAccountCommentController;
use App\Http\Controllers\Social\SocialCampaignController;
use App\Http\Controllers\Social\SocialAdCreativeController;
use App\Http\Middleware\HttpClientTimeout;

Route::middleware([HttpClientTimeout::class])->group(function () {
    Route::get('inbox', [SocialAccountController::class, 'inbox'])->name('direct-message');
    Route::post('send-message', [SocialAccountController::class, 'sendMessage'])->name('message.send');
    Route::post('list-message', [SocialAccountController::class, 'listMessage'])->name('message.list');
    Route::get('{account_id}/posts', [SocialAccountPostController::class, 'index'])->name('account.posts');
    Route::get('{post_id}/comments', [SocialAccountCommentController::class, 'index'])->name('account.comments');
    Route::get('{post_id}/comments/sync', [SocialAccountCommentController::class, 'sync'])->name('account.comments.sync');
    Route::post('delete-post', [SocialPostController::class, 'deletePost'])->name('post.postdelete');
    Route::post('reply-comments', [SocialAccountCommentController::class, 'replyComments'])->name('account.comments.reply');
    Route::post('dev-reply-comment', [SocialAccountCommentController::class, 'devCommentsReply'])->name('dev.reply.comment');
    Route::get('email-replise/{id}', [SocialAccountCommentController::class, 'getEmailreplies']);
    Route::get('all-comments', [SocialAccountCommentController::class, 'allcomments'])->name('all-comments');
    Route::any('get-post/page', [SocialController::class, 'pagePost'])->name('get-post.page');
    Route::get('{msg_id}/get-translated-text-score', [SocialAccountController::class, 'getTranslatedTextScore'])->name('message.get-translated-text-score');
    Route::get('comments/{comment_id}/get-translated-text-score', [SocialAccountCommentController::class, 'getTranslatedTextScore'])->name('account.comments.get-translated-text-score');

    Route::prefix('config')->group(function () {
        Route::get('/', [SocialConfigController::class, 'index'])->name('config.index');
        Route::post('store', [SocialConfigController::class, 'store'])->name('config.store');
        Route::post('ad/store', [SocialConfigController::class, 'adStore'])->name('config.adStore');
        Route::get('{id}/update', [SocialConfigController::class, 'update'])->name('config.update');
        Route::post('edit', [SocialConfigController::class, 'edit'])->name('config.edit');
        Route::post('delete', [SocialConfigController::class, 'destroy'])->name('config.delete');
        Route::get('adsmanager', [SocialConfigController::class, 'getadsAccountManager'])->name('config.adsmanager');
        Route::get('fbtoken', [SocialConfigController::class, 'getfbToken'])->name('config.fbtoken');
    });


    Route::get('posts/{id}', [SocialPostController::class, 'index'])->name('post.index');
    Route::prefix('post')->group(function () {
        Route::post('store', [SocialPostController::class, 'store'])->name('post.store');
        Route::post('edit', [SocialPostController::class, 'edit'])->name('post.edit');
        Route::post('delete', [SocialPostController::class, 'destroy'])->name('post.delete');
        Route::get('create/{id}', [SocialPostController::class, 'create'])->name('post.create');
        Route::get('getimage/{id}', [SocialPostController::class, 'getImage'])->name('post.getimage');
        Route::post('history', [SocialPostController::class, 'history'])->name('post.history');
        Route::post('translationapproval', [SocialPostController::class, 'translationapproval'])->name('post.translationapproval');
        Route::post('approvepost', [SocialPostController::class, 'approvepost'])->name('post.approvepost');
        Route::get('grid', [SocialPostController::class, 'grid'])->name('post.grid');
        Route::get('page', [SocialController::class, 'index'])->name('post.page');
        Route::post('page/create', [SocialController::class, 'createPost'])->name('post.page.create');
        // Add previe post route. DEVTASK-24790
        Route::post('preview', [SocialPostController::class, 'previewPost'])->name('post.preview');
        Route::get('view/{id}', [SocialPostController::class, 'view'])->name('post.view');
        Route::get('{id}/get-translated-text-score', [SocialPostController::class, 'getTranslatedTextScore'])->name('post.get-translated-text-score');
    });

    Route::get('adsets', [SocialAdsetController::class, 'index'])->name('adset.index');
    Route::prefix('adset')->group(function () {
        Route::post('store', [SocialAdsetController::class, 'store'])->name('adset.store');
        Route::post('edit', [SocialAdsetController::class, 'edit'])->name('adset.edit');
        Route::post('delete', [SocialAdsetController::class, 'destroy'])->name('adset.delete');
        Route::get('create', [SocialAdsetController::class, 'create'])->name('adset.create');
        Route::post('history', [SocialAdsetController::class, 'history'])->name('adset.history');
        Route::get('get-optimization-goals/{id}', [SocialAdsetController::class, 'getOptimizationGoals'])->name('adset.optimizationgoals');
    });


    Route::get('campaigns', [SocialCampaignController::class, 'index'])->name('campaign.index');
    Route::prefix('campaign')->group(function () {
        Route::post('store', [SocialCampaignController::class, 'store'])->name('campaign.store');
        Route::post('edit', [SocialCampaignController::class, 'edit'])->name('campaign.edit');
        Route::post('delete', [SocialCampaignController::class, 'destroy'])->name('campaign.delete');
        Route::get('create', [SocialCampaignController::class, 'create'])->name('campaign.create');
        Route::post('history', [SocialCampaignController::class, 'history'])->name('campaign.history');
    });

    Route::get('adsets', [SocialAdsetController::class, 'index'])->name('adset.index');
    Route::prefix('adset')->group(function () {
        Route::post('store', [SocialAdsetController::class, 'store'])->name('adset.store');
        Route::post('edit', [SocialAdsetController::class, 'edit'])->name('adset.edit');
        Route::post('delete', [SocialAdsetController::class, 'destroy'])->name('adset.delete');
        Route::get('create', [SocialAdsetController::class, 'create'])->name('adset.create');
        Route::post('history', [SocialAdsetController::class, 'history'])->name('adset.history');
    });

    Route::get('adcreatives', [SocialAdCreativeController::class, 'index'])->name('adcreative.index');
    Route::prefix('adcreative')->group(function () {
        Route::post('store', [SocialAdCreativeController::class, 'store'])->name('adcreative.store');
        Route::post('edit', [SocialAdCreativeController::class, 'edit'])->name('adcreative.edit');
        Route::post('delete', [SocialAdCreativeController::class, 'destroy'])->name('adcreative.delete');
        Route::get('create', [SocialAdCreativeController::class, 'create'])->name('adcreative.create');
        Route::get('getconfigPost', [SocialAdCreativeController::class, 'getpost'])->name('adcreative.getpost');
        Route::post('history', [SocialAdCreativeController::class, 'history'])->name('adcreative.history');
        Route::get('report', [SocialController::class, 'adCreativereport'])->name('adCreative.report');
        Route::post('report/paginate', [SocialController::class, 'adCreativepaginateReport'])->name('adCreative.paginate');
    });

    Route::prefix('ads')->group(function () {
        Route::get('/', [SocialAdsController::class, 'index'])->name('ad.index');
        Route::post('store', [SocialAdsController::class, 'store'])->name('ads.store');
        Route::post('edit', [SocialAdsController::class, 'edit'])->name('ad.edit');
        Route::post('delete', [SocialAdsController::class, 'destroy'])->name('ad.delete');
        Route::get('create', [SocialAdsController::class, 'create'])->name('ads.create');
        Route::post('history', [SocialAdsController::class, 'history'])->name('ad.history');
        Route::get('getconfigPost', [SocialAdsController::class, 'getpost'])->name('ad.getpost');
        Route::get('getAdsets', [SocialAdsController::class, 'getAdsets'])->name('ad.getAdsets');
    });

    Route::prefix('ad')->group(function () {
        Route::get('report', [SocialController::class, 'report'])->name('report');
        Route::get('report-history', [SocialController::class, 'reportHistory'])->name('report.history');
        Route::get('schedules', [SocialController::class, 'getSchedules'])->name('ads.schedules');
        Route::post('schedules', [SocialController::class, 'getSchedules'])->name('ads.schedules.p');
        Route::get('schedules/calendar', [SocialController::class, 'getAdSchedules'])->name('ads.schedules.calendar');
        Route::post('schedules/', [SocialController::class, 'createAdSchedule'])->name('ads.schedules.create');
        Route::post('schedules/attach-images/{id}', [SocialController::class, 'attachMedia'])->name('ads.schedules.attach_images');
        Route::post('schedules/attach-products/{id}', [SocialController::class, 'attachProducts'])->name('ads.schedules.attach_products');
        Route::post('schedules/', [SocialController::class, 'createAdSchedule'])->name('ads.schedules.attach_image');
        Route::get('schedules/{id}', [SocialController::class, 'showSchedule'])->name('ads.schedules.show');
        Route::get('insight/{adId}', [SocialController::class, 'getAdInsights'])->name('ad.insight');
        Route::post('report/paginate', [SocialController::class, 'paginateReport'])->name('report.paginate');
        Route::get('report/{ad_id}/{status}/{token}/', [SocialController::class, 'changeAdStatus'])->name('report.ad.status');
        Route::get('campaign/create', [SocialController::class, 'createCampaign'])->name('ad.campaign.create');
        Route::post('campaign/store', [SocialController::class, 'storeCampaign'])->name('ad.campaign.store');
        Route::get('adset/create', [SocialController::class, 'createAdset'])->name('ad.adset.create');
        Route::post('adset/store', [SocialController::class, 'storeAdset'])->name('ad.adset.store');
        Route::get('create', [SocialController::class, 'createAd'])->name('ad.create');
        Route::post('store', [SocialController::class, 'storeAd'])->name('ad.store');
    });

    Route::get('get-hashtags', [SocialPostController::class, 'getHashtags'])->name('posts.get_hashtags');
});
