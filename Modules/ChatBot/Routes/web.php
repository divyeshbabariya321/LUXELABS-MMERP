<?php

use App\Http\Middleware\HttpClientTimeout;
use Illuminate\Support\Facades\Route;
use Modules\ChatBot\Http\Controllers\AnalyticsController;
use Modules\ChatBot\Http\Controllers\ChatBotController;
use Modules\ChatBot\Http\Controllers\DialogController;
use Modules\ChatBot\Http\Controllers\KeywordController;
use Modules\ChatBot\Http\Controllers\MessageController;
use Modules\ChatBot\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::middleware(['auth', HttpClientTimeout::class])->prefix('chatbot')->group(function () {
    Route::get('/', [ChatBotController::class, 'index']);
    Route::post('/edit-message', [ChatBotController::class, 'editMessage'])->name('chatbot.edit.message');

    Route::prefix('keyword')->group(function () {
        Route::get('/', [KeywordController::class, 'index'])->name('chatbot.keyword.list');
        Route::post('/', [KeywordController::class, 'save'])->name('chatbot.keyword.save');
        Route::post('/submit', [KeywordController::class, 'saveAjax'])->name('chatbot.keyword.saveAjax');
        Route::get('/search', [KeywordController::class, 'search'])->name('chatbot.keyword.search');
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', [KeywordController::class, 'edit'])->name('chatbot.keyword.edit');
            Route::post('/edit', [KeywordController::class, 'update'])->name('chatbot.keyword.update');
            Route::get('/delete', [KeywordController::class, 'destroy'])->name('chatbot.keyword.delete');
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', [KeywordController::class, 'destroyValue'])->name('chatbot.value.delete');
            });
        });
    });

    Route::prefix('question')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('chatbot.question.list');
        Route::post('/', [QuestionController::class, 'save'])->name('chatbot.question.save');
        Route::post('/save_dymanic_task', [QuestionController::class, 'saveDynamicTask'])->name('chatbot.question.save_dymanic_task');
        Route::post('/save_dymanic_reply', [QuestionController::class, 'saveDynamicReply'])->name('chatbot.question.save_dymanic_reply');
        Route::post('/submit', [QuestionController::class, 'saveAjax'])->name('chatbot.question.saveAjax');
        Route::get('/search', [QuestionController::class, 'search'])->name('chatbot.question.search');
        Route::get('/category', [QuestionController::class, 'getCategories'])->name('chatbot.question.category');
        Route::get('/search-category', [QuestionController::class, 'searchCategory'])->name('chatbot.question.search-category');
        Route::get('/search-suggestion', [QuestionController::class, 'searchSuggestion'])->name('chatbot.question.search-suggetion');
        Route::post('/search-suggestion-delete', [QuestionController::class, 'searchSuggestionDelete'])->name('chatbot.question.search-suggetion-delete');
        Route::post('/change-category', [QuestionController::class, 'changeCategory'])->name('chatbot.question.change-category');
        Route::get('/keyword/search', [QuestionController::class, 'searchKeyword'])->name('chatbot.question.keyword.search');
        Route::post('/reply/add', [QuestionController::class, 'addReply'])->name('chatbot.question.reply.add');
        Route::post('/reply/update', [QuestionController::class, 'updateReply'])->name('chatbot.question.reply.update');
        Route::post('/online-update/{id}', [QuestionController::class, 'onlineUpdate'])->name('chatbot.question.online-update');
        Route::post('question-error-log', [QuestionController::class, 'showLogById'])->name('chatbot.question.error_log');
        Route::post('repeat-watson', [QuestionController::class, 'repeatWatson'])->name('chatbot.question.repeat.watson');
        Route::get('suggested-response', [QuestionController::class, 'suggestedResponse'])->name('chatbot.question.suggested.response');
        Route::prefix('annotation')->group(function () {
            Route::post('/save', [QuestionController::class, 'saveAnnotation'])->name('chatbot.question.annotation.save');
            Route::get('/delete', [QuestionController::class, 'deleteAnnotation'])->name('chatbot.question.annotation.delete');
        });

        Route::prefix('{id}')->group(function () {
            Route::get('/edit', [QuestionController::class, 'edit'])->name('chatbot.question.edit');
            Route::post('/edit', [QuestionController::class, 'update'])->name('chatbot.question.update');
            Route::get('/delete', [QuestionController::class, 'destroy'])->name('chatbot.question.delete');
            Route::get('/sync-watson', [QuestionController::class, 'syncWatson'])->name('chatbot.question.sync.watson');
            Route::get('/sync-google', [QuestionController::class, 'syncGoogle'])->name('chatbot.question.sync.google');
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', [QuestionController::class, 'destroyValue'])->name('chatbot.question-example.delete');
            });
        });

        Route::prefix('autoreply')->group(function () {
            Route::post('/save', [QuestionController::class, 'saveAutoreply'])->name('chatbot.question.autoreply.save');
        });
    });

    Route::get('/bot-simulator', [QuestionController::class, 'simulator'])->name('chatbot-simulator');

    Route::prefix('dialog')->group(function () {
        Route::get('/', [DialogController::class, 'index'])->name('chatbot.dialog.list');
        Route::post('/', [DialogController::class, 'save'])->name('chatbot.dialog.save');
        Route::get('/search', [DialogController::class, 'search'])->name('chatbot.dialog.search');
        Route::prefix('{id}')->group(function () {
            Route::get('/edit', [DialogController::class, 'edit'])->name('chatbot.dialog.edit');
            Route::post('/edit', [DialogController::class, 'update'])->name('chatbot.dialog.update');
            Route::get('/delete', [DialogController::class, 'destroy'])->name('chatbot.dialog.delete');
            Route::prefix('values/{valueId}')->group(function () {
                Route::get('/delete', [DialogController::class, 'destroyValue'])->name('chatbot.dialog-response.delete');
            });
        });
        Route::get('/log', [DialogController::class, 'log'])->name('chatbot.dialog.log');
        Route::get('/local-error-log', [DialogController::class, 'localErrorLog'])->name('chatbot.dialog.local-error-log');
        Route::get('/get-response-only', [DialogController::class, 'getWebsiteResponse'])->name('chatbot.dialog.get-response-only');

        // store dialog via save response
        Route::post('dialog-save', [DialogController::class, 'saveAjax'])->name('chatbot.dialog.saveajax');
        Route::get('all-reponses/{id}', [DialogController::class, 'getAllResponse'])->name('chatbot.dialog.all-responses');
        Route::post('submit-reponse/{id}', [DialogController::class, 'submitResponse'])->name('chatbot.dialog.submit-reponse');
    });

    Route::prefix('dialog-grid')->group(function () {
        Route::get('/', [DialogController::class, 'dialogGrid'])->name('chatbot.dialog-grid.list');
    });

    Route::post('/add-entity-type', [QuestionController::class, 'addEntityType'])->name('chatbot.entity.type.create');

    // Route::prefix('rest/dialog-grid')->group(function () {
    //     Route::get('/create', 'DialogController@restCreate')->name("chatbot.rest.dialog.create");
    //     Route::post('/create', 'DialogController@restCreate')->name("chatbot.rest.dialog.create");
    //     Route::get('/status', 'DialogController@restStatus')->name("chatbot.rest.dialog.status");
    //     Route::prefix('{id}')->group(function () {
    //         Route::get('/', 'DialogGridController@restDetails')->name("chatbot.rest.dialog-grid.detail");
    //         Route::get('/delete', 'DialogController@restDelete')->name("chatbot.rest.dialog.delete");
    //     });
    // });

    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('chatbot.analytics.list');
    });

    Route::prefix('messages')->group(function () {
        Route::post('chatbot-messages-column-visbility', [MessageController::class, 'chatbotMessagesColumnVisbilityUpdate'])->name('chatbot.messages.column.update');
        Route::get('/', [MessageController::class, 'index'])->name('chatbot.messages.list');

        Route::get('/{isElastic?}', [MessageController::class, 'index'])->name('chatbot.messages.list.elastic');
        Route::get('/reindex', [MessageController::class, 'reindex'])->name('chatbot.messages.reindex');

        Route::get('/today', [MessageController::class, 'todayMessages'])->name('chatbot.messages.today-list');
        Route::get('/today-check-new', [MessageController::class, 'todayMessagesCheck'])->name('chatbot.messages.todayMessagesCheck');
        Route::get('/stop-reminder', [MessageController::class, 'stopReminder'])->name('chatbot.messages.stopReminder');
        Route::post('/approve', [MessageController::class, 'approve'])->name('chatbot.messages.approve');
        Route::post('/remove-images', [MessageController::class, 'removeImages'])->name('chatbot.messages.remove-images');
        Route::get('/attach-images', [MessageController::class, 'attachImages'])->name('chatbot.messages.attach-images');
        Route::post('/forward-images', [MessageController::class, 'forwardToCustomer'])->name('chatbot.messages.forward-images');
        Route::get('/resend-to-bot', [MessageController::class, 'resendToBot'])->name('chatbot.messages.resend-to-bot');
        Route::post('/update-read-status', [MessageController::class, 'updateReadStatus'])->name('chatbot.messages.update-read-status');
        Route::get('/update-emailaddress', [MessageController::class, 'updateEmailAddress']);
        Route::post('/send-message-reply', [MessageController::class, 'sendMessageReply'])->name('message.send-mail-reply');

        Route::post('/upload-audio', [MessageController::class, 'uploadAudio'])->name('upload-audio');

        Route::post('/update-simulator-setting', [MessageController::class, 'updateSimulator'])->name('chatbot.message.update.simulator.setting');
        Route::post('/send-suggested-replay', [MessageController::class, 'sendSuggestedMessage'])->name('chatbot.send.suggested.message');

        //        Route::get('/chat-bot-replies/{message_id}', [MessageController::class, 'chatBotReplayList'])->name('chatbot.message');
        Route::get('/message-list/{object?}/{object_id?}', [MessageController::class, 'chatBotReplayList'])->name('chatbot.message.list');
        Route::get('/simulator-messages/{object?}/{object_id?}', [MessageController::class, 'simulatorMessageList'])->name('simulator.message.list');
    });

    Route::prefix('simulate-message')->group(function () {
        Route::post('/store-intent', [MessageController::class, 'storeIntent'])->name('simulate.message.store.intent');
        Route::post('/store-replay', [MessageController::class, 'storeReplay'])->name('simulate.message.store.replay');
    });

    Route::post('/simulate-/{object?}/{object_id?}', [MessageController::class, 'chatBotReplayList'])->name('simulate.chatbot.message.list');

    Route::prefix('rest/dialog')->group(function () {
        Route::get('/create', [DialogController::class, 'restCreate'])->name('chatbot.rest.dialog.create');
        Route::get('/status', [DialogController::class, 'restStatus'])->name('chatbot.rest.dialog.status');
        Route::prefix('{id}')->group(function () {
            Route::get('/', [DialogController::class, 'restDetails'])->name('chatbot.rest.dialog.detail');
            Route::get('/delete', [DialogController::class, 'restDelete'])->name('chatbot.rest.dialog.delete');
        });
    });
});
