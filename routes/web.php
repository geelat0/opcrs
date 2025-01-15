<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntriesController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuccesIndicatorController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::middleware(['auth_check'])->group(function () {
    Route::middleware(['2fa'])->group(function () {

            Route::get('/permissions', [PermissionController::class, 'editPermissions'])->name('roles.permissions.edit')->middleware('permission:manage_permissions');
            Route::post('/permissions/update', [PermissionController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware('permission:manage_permissions');
            Route::get('roles/permissions/fetch', [PermissionController::class, 'fetchPermissions'])->name('roles.permissions.fetch')->middleware('permission:manage_permissions');
            Route::post('/permissions/store', [PermissionController::class, 'store'])->name('roles.permissions.store')->middleware('permission:manage_permissions');


            Route::get('/login_in', [LogController::class, 'login_in'])->middleware('permission:manage_history');
            Route::get('/list', [LogController::class, 'list'])->name('list')->middleware('permission:manage_history');

            Route::get('user', [UserController::class, 'user_create']);

            Route::post('user/store', [UserController::class, 'store'])->name('user.store')->middleware('permission:manage_users');
            Route::post('users/store', [UserController::class, 'UserStore'])->name('users.store')->middleware('permission:manage_users');
            Route::post('users/update', [UserController::class, 'update'])->name('users.update')->middleware('permission:manage_users');
            Route::get('users/list', [UserController::class, 'list'])->name('user.list')->middleware('permission:manage_users');
            Route::post('users/destroy', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:manage_users');
            Route::post('temp-password', [UserController::class, 'temp_password'])->name('users.temp-password')->middleware('permission:manage_users');
            Route::post('proxy', [UserController::class, 'proxy'])->name('users.gen-proxy')->middleware('permission:manage_users');
            Route::post('users/change-status', [UserController::class, 'changeStatus'])->name('users.change-status')->middleware('permission:manage_users');
            Route::get('getDivision', [UserController::class, 'getDivision'])->name('getDivision');

            Route::get('roles', [RoleController::class, 'roles'])->middleware('permission:manage_roles');
            Route::get('role/list', [RoleController::class, 'list'])->name('role.list')->middleware('permission:manage_roles');
            Route::get('role/data', [RoleController::class, 'getRole'])->name('get.role')->middleware('permission:manage_roles');
            Route::post('role/store', [RoleController::class, 'store'])->name('role.store')->middleware('permission:manage_roles');
            Route::post('role/update', [RoleController::class, 'update'])->name('role.update')->middleware('permission:manage_roles');
            Route::post('/role/destroy', [RoleController::class, 'destroy'])->name('role.destroy')->middleware('permission:manage_roles');

            Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
            Route::post('/logs_clear', [LogController::class, 'clear'])->name('logs.clear');


        });

        Route::get('dash-home', [DashboardController::class, 'index'])->name('dash-home');
        Route::get('dash-home/Loginlist', [DashboardController::class, 'Loginlist'])->name('Loginlist');
        Route::get('dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter')->middleware('permission:view_dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard-data', [DashboardController::class, 'fetchDashboardData'])->name('fetch.dashboard.data');


        Route::get('profile', [ProfileController::class, 'index']);
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('password.change');
        Route::get('/two-factor', [ProfileController::class, 'two_factor'])->name('two_factor');
        Route::post('/twofaEnable', [ProfileController::class, 'twofaEnable'])->name('twofaEnable');
        Route::post('/twofaDisabled', [ProfileController::class, 'twofaDisabled'])->name('twofaDisabled');

        Route::get('change-password', [AuthController::class, 'ChangePassForm'])->name('change-password');

        Route::get('outcome', [OutcomeController::class, 'index'])->name('organization_outcome')->middleware('permission:manage_organizational_outcome');
        Route::get('organiztional/outcome/list', [OutcomeController::class, 'list'])->name('org.list')->middleware('permission:manage_organizational_outcome');
        Route::post('organizational/outcome/store', [OutcomeController::class, 'store'])->name('org.store')->middleware('permission:manage_organizational_outcome');
        Route::post('organizational/outcome/update', [OutcomeController::class, 'update'])->name('org.update')->middleware('permission:manage_organizational_outcome');
        Route::post('organizational/outcome/destroy', [OutcomeController::class, 'destroy'])->name('org.destroy')->middleware('permission:manage_organizational_outcome');
        Route::get('organizational/outcome/getOrg', [OutcomeController::class, 'getOrg'])->name('org.getOrg')->middleware('permission:manage_organizational_outcome');

        Route::get('indicator', [IndicatorController::class, 'index'])->name('indicator')->middleware('permission:manage_indicator');
        Route::get('indicator/list', [IndicatorController::class, 'list'])->name('indicator.list')->middleware('permission:manage_indicator');
        Route::get('indicator_create', [IndicatorController::class, 'create'])->name('indicator.create')->middleware('permission:manage_indicator');
        Route::get('indicator_edit', [IndicatorController::class, 'edit'])->name('indicator.edit')->middleware('permission:manage_indicator');
        Route::get('indicator/getDivision', [IndicatorController::class, 'getDivision'])->name('indicator.getDivision');
        Route::post('indicator/store', [IndicatorController::class, 'store'])->name('indicator.store')->middleware('permission:manage_indicator');
        Route::match(['post', 'put'], 'indicator/update', [IndicatorController::class, 'update'])->name('indicator.update')->middleware('permission:manage_indicator');
        Route::match(['post', 'put'], 'indicator/update_2', [IndicatorController::class, 'update_nonSuperAdmin'])->name('indicator.update_nonSuperAdmin')->middleware('permission:manage_indicator');
        Route::match(['post', 'put'], 'indicator/update_v2', [IndicatorController::class, 'update_nonSuperAdminV2'])->name('indicator.update_nonSuperAdminV2')->middleware('permission:manage_indicator');
        Route::get('indicator_view', [IndicatorController::class, 'view'])->name('indicator.view')->middleware('permission:manage_indicator');
        Route::post('indicator/destroy', [IndicatorController::class, 'destroy'])->name('indicator.destroy')->middleware('permission:manage_indicator');
        Route::get('getIndicator', [IndicatorController::class, 'getIndicator'])->name('getIndicator')->middleware('permission:manage_indicator');
        Route::get('/getIndicatorById/{id}', [IndicatorController::class, 'getIndicatorById'])->name('indicator.getById');


        Route::get('accomplishment', [EntriesController::class, 'index'])->name('entries')->middleware('permission:manage_entries');
        Route::get('accomplishment_create', [EntriesController::class, 'create'])->name('create')->middleware('permission:manage_entries');
        Route::get('accomplishment_view', [EntriesController::class, 'view'])->name('view')->middleware('permission:manage_entries');
        Route::get('accomplishment_edit', [EntriesController::class, 'edit'])->name('edit')->middleware('permission:manage_entries');
        Route::post('accomplishment/store', [EntriesController::class, 'store'])->name('entries.store')->middleware('permission:manage_entries');
        Route::post('accomplishment/update', [EntriesController::class, 'update'])->name('entries.update')->middleware('permission:manage_entries');
        Route::post('accomplishment/destroy', [EntriesController::class, 'destroy'])->name('entries.destroy')->middleware('permission:manage_entries');
        Route::get('accomplishment/list', [EntriesController::class, 'list'])->name('entries.list')->middleware('permission:manage_entries');
        Route::get('accomplishment/completed_list', [EntriesController::class, 'completed_list'])->name('entries.completed_list')->middleware('permission:manage_entries');
        Route::get('accomplishment/getIndicator', [EntriesController::class, 'getIndicator'])->name('entries.getIndicator')->middleware('permission:manage_entries');

        Route::get('getMeasureDetails', [EntriesController::class, 'getMeasureDetails'])->name('entries.getMeasureDetails')->middleware('permission:manage_entries');



        Route::get('generate', [ReportController::class, 'index'])->name('generate')->middleware('permission:generate_report');
        Route::post('/generate-pdf', [ReportController::class, 'generatePDF'])->name('generate.pdf')->middleware('permission:generate_report_pdf');
        Route::post('/generate-word', [ReportController::class, 'generateWord'])->name('generate.word')->middleware('permission:generate_report_doc');
        Route::get('pdf', [ReportController::class, 'pdf'])->name('show.pdf')->middleware('permission:generate_report');
        Route::get('/export', [ReportController::class, 'exportMultipleSheets'])->name('export')->middleware('permission:generate_report_excel');


});



Route::middleware(['guest'])->group(function () {

    Route::get('/', [AuthController::class, 'index']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('register', [UserController::class, 'create']);

    Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');

});

Route::get('auth/otp', [AuthController::class, 'OTP'])->name('auth.otp');
Route::post('auth/otp/check', [AuthController::class, 'check'])->name('auth.otp.check');
Route::get('/test', [TestController::class, 'index'])->name('test');
Route::get('/test_outcome', [TestController::class, 'test_outcome'])->name('test_outcome');















