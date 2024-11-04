<?php

use App\Http\Middleware\HttpClientTimeout;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserDeliveredController;
use Modules\UserManagement\Http\Controllers\UserManagementController;

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

Route::prefix('user-management')->middleware('auth', HttpClientTimeout::class)->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('user-management.index');

    Route::get('planned-user-and-availability', [UserManagementController::class, 'plannedUserAndAvailability'])->name('user-management.planned-user-and-availability');

    Route::get('/feedback-category/store', [UserManagementController::class, 'addFeedbackCategory'])->name('user.feedback-category');
    Route::post('/feedback-category/delete', [UserManagementController::class, 'deleteFeedbackCategory'])->name('delete.user.feedback-category');
    Route::get('/feedback-status/store', [UserManagementController::class, 'addFeedbackStatus'])->name('user.feedback-status');
    Route::get('/feedback-status/update', [UserManagementController::class, 'updateFeedbackStatus'])->name('user.feedback-status.update');
    Route::get('/feedback-table/data', [UserManagementController::class, 'addFeedbackTableData'])->name('user.feedback-table-data');

    // Route::get('/userfeedback','UserManagementController@cat_name');
    // Route::post('/userfeedback','UserManagementController@cat_name')->name('user-management.insert');
    Route::get('/get/user-list/', [UserManagementController::class, 'getUserList'])->name('get-user-list');
    Route::get('/request-list', [UserManagementController::class, 'permissionRequest'])->name('user-management.permission.request');

    Route::post('/request-delete', [UserManagementController::class, 'deletePermissionRequest'])->name('user-management.permission.delete.request.');

    Route::post('/task-activity', [UserManagementController::class, 'taskActivity'])->name('user-management.task.activity');
    Route::post('today-task-history', [UserManagementController::class, 'todayTaskHistory'])->name('user-management.today.task.history');
    Route::post('modifiy-permission', [UserManagementController::class, 'modifiyPermission'])->name('user-management.modifiy.permission');
    Route::get('/edit/{id}', [UserManagementController::class, 'edit'])->name('user-management.edit');
    Route::get('/role/{id}', [UserManagementController::class, 'getRoles'])->name('user-management.get-role');
    Route::post('/role/{id}', [UserManagementController::class, 'submitRoles'])->name('user-management.submit-role');
    Route::get('/permission/{id}', [UserManagementController::class, 'getPermission'])->name('user-management.get-permission');
    Route::post('/permission/{id}', [UserManagementController::class, 'submitPermission'])->name('user-management.submit-permission');
    Route::post('/add-permission', [UserManagementController::class, 'addNewPermission'])->name('user-management.add-permission');
    Route::get('/show/{id}', [UserManagementController::class, 'show'])->name('user-management.show');
    Route::patch('/update/{id}', [UserManagementController::class, 'update'])->name('user-management.update');
    Route::post('/{id}/activate', [UserManagementController::class, 'activate'])->name('user-management.activate');
    Route::get('track/{id}', [UserManagementController::class, 'usertrack'])->name('user-management.usertrack');
    Route::get('/user/team/{id}', [UserManagementController::class, 'createTeam'])->name('user-management.team');
    Route::post('/user/team/{id}', [UserManagementController::class, 'submitTeam'])->name('user-management.team.submit');
    Route::get('/user/teams/{id}', [UserManagementController::class, 'getTeam'])->name('user-management.team.info');
    Route::post('/user/teams/{id}', [UserManagementController::class, 'editTeam'])->name('user-management.team.edit');
    Route::post('/user/delete-team/{id}', [UserManagementController::class, 'deleteTeam'])->name('user-management.team.delete');
    Route::get('/paymentInfo/{id}', [UserManagementController::class, 'paymentInfo'])->name('user-management.payment-info');
    Route::get('payments/{id}', [UserManagementController::class, 'userPayments'])->name('user-management.payments');
    Route::post('payments/{id}', [UserManagementController::class, 'savePayments'])->name('user-management.savePayments');

    Route::get('user-avaibility/{id}', [UserManagementController::class, 'getPendingandAvalHour'])->name('user-management.pending.available.hour');

    Route::post('user-avaibility/submit-time', [UserManagementController::class, 'saveUserAvaibility'])->name('user-management.user-avaibility.submit-time');

    Route::get('user-avl-list/{id}', [UserManagementController::class, 'userAvaibilityForModal'])->name('user-management.user-avl-list');
    /*
       Pawan added for view the page for user-activities
    */
    Route::get('user-avl-view/{id}', [UserManagementController::class, 'userAvaibilityForView'])->name('user-management.user-avl-view');
    //end
    Route::post('user-avaibility/{id}', [UserManagementController::class, 'userAvaibilityUpdate'])->name('user-management.update-user-avaibility');
    Route::post('approve-user/{id}', [UserManagementController::class, 'approveUser'])->name('user-management.approve-user');
    Route::post('/add-new-method', [UserManagementController::class, 'addPaymentMethod'])->name('user-management.add-payment-method');
    Route::get('/task/user/{id}', [UserManagementController::class, 'userTasks'])->name('user-management.tasks');
    Route::post('/reply/add', [UserManagementController::class, 'addReply'])->name('user-management.reply.add');
    Route::get('/reply/delete', [UserManagementController::class, 'deleteReply'])->name('user-management.reply.delete');
    Route::get('/records', [UserManagementController::class, 'records'])->name('user-management.records');
    Route::get('/user-details/{id}', [UserManagementController::class, 'GetUserDetails'])->name('user-management.user-details');
    Route::get('task-hours/{id}', [UserManagementController::class, 'getPendingandAvalHour'])->name('user-management.task-hours');
    Route::get('/system-ips', [UserManagementController::class, 'systemIps']);
    Route::get('{id}/get-database', [UserManagementController::class, 'getDatabase'])->name('user-management.get-database');
    Route::post('{id}/create-database', [UserManagementController::class, 'createDatabaseUser'])->name('user-management.create-database');
    Route::post('{id}/assign-database-table', [UserManagementController::class, 'assignDatabaseTable'])->name('user-management.assign-database-table');
    Route::post('{id}/delete-database-access', [UserManagementController::class, 'deleteDatabaseAccess'])->name('user-management.delete-database-access');
    Route::post('{id}/choose-database', [UserManagementController::class, 'chooseDatabase'])->name('user-management.choose-database');
    Route::post('/update-status', [UserManagementController::class, 'updateStatus']);

    Route::post('/user-generate-file-store', [UserManagementController::class, 'userGenerateStorefile'])->name('user-management.gent-file-store');

    Route::get('/user-generate-file-listing/{userid}', [UserManagementController::class, 'userPemfileHistoryListing'])->name('user-management-pem-history-list');
    Route::get('/user-pemfile-history-logs/{pemfileHistoryId}', [UserManagementController::class, 'userPemfileHistoryLogs'])->name('user-management-pem-history-logs');
    Route::post('/disable-pem-file/{id}', [UserManagementController::class, 'disablePemFile'])->name('user-management-disable-pem-file');
    Route::post('/delete-pem-file/{id}', [UserManagementController::class, 'deletePemFile'])->name('user-management-delete-pem-file');
    Route::get('/download-pem-file/{id}', [UserManagementController::class, 'downloadPemFile'])->name('user-management-download-pem-file');

    Route::prefix('update')->group(function () {
        Route::post('task-plan-flag', [UserManagementController::class, 'updateTaskPlanFlag'])->name('user-management.update.flag-for-task-plan');
    });

    Route::prefix('user-schedules')->group(function () {
        Route::get('index', [UserManagementController::class, 'userSchedulesIndex'])->name('user-management.user-schedules.index');
        Route::get('report', [UserManagementController::class, 'userSchedulesReport'])->name('user-management.user-schedules.report');
        Route::any('load-data', [UserManagementController::class, 'userSchedulesLoadData'])->name('user-management.user-schedules.load-data');
        Route::post('/add-new-request', [UserManagementController::class, 'addNewRequest'])->name('user-management.add-new-request');
        Route::post('/update-request', [UserManagementController::class, 'updateRequest'])->name('user-management.update-request');
        Route::post('/statuscolor', [UserManagementController::class, 'statuscolor'])->name('user-management.statuscolor');
        Route::get('/countdevtask/{id}', [UserManagementController::class, 'taskCount']);
        Route::get('/countdevtaskadmin/{id}', [UserManagementController::class, 'taskCountAdmin']);
    });

    Route::prefix('user-delivered')->group(function () {
        Route::get('index', [UserDeliveredController::class, 'index'])->name('user-management.user-delivered.index');
        Route::any('load-data', [UserDeliveredController::class, 'loadData'])->name('user-management.user-delivered.load-data');
    });

    Route::prefix('user-availabilities')->group(function () {
        Route::post('/edit', [UserManagementController::class, 'userAvailabilitiesEdit'])->name('user-availabilities.edit');
        Route::post('/history', [UserManagementController::class, 'userAvaibilityHistoryLog'])->name('user-availabilities.history');
    });
    Route::post('/whitelist-bulk-update', [UserManagementController::class, 'whitelistBulkUpdate'])->name('user-management.whitelist-bulk-update');
    Route::get('/user-access-listing', [UserManagementController::class, 'userAccessListing'])->name('user-management.user-access-listing');

    Route::get('database-logs', [UserManagementController::class, 'databaseLogs'])->name('user-management.database-logs');
});
