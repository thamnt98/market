<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', function() {
    return redirect('/register');
});
Route::get('/register', 'Auth\RegisterController@main')->name('register');
Route::post('/register', 'Auth\HandleRegisterController@main')->name('register');
Route::get('/login', 'Auth\LoginController@main')->name('login');
Route::post('/login', 'Auth\HandleLoginController@main')->name('login');
Route::get('/password/forgot', 'Auth\ForgotPasswordController@main')->name('password.forgot');
Route::post('/password/forgot', 'Auth\ChangePasswordController@main')->name('password.forgot');


Route::group([
    'namespace' => 'Admin',
    'middleware' => 'auth',
    'prefix' => 'admin',
], function () {
    Route::get('/logout', 'LogoutController@main')->name('logout');
    Route::get('/dashboard', 'DashboardController@main')->name('dashboard');
    Route::get('/profile', 'ProfileController@main')->name('profile');
    Route::post('/profile', 'UpdateProfileController@main')->name('profile.update');
    Route::get('/', 'DashboardController@main')->name('home');
    Route::group([
        'namespace' => 'User',
        'prefix' => 'user',
    ], function () {
        Route::get('/list', 'ListController@main')->name('user.list')->middleware('permission:user.show|all.user.show');
        Route::get('/create', 'CreateController@main')->name('user.create')->middleware('permission:user.create');
        Route::post('/create', 'StoreController@main')->name('user.store')->middleware('permission:user.create');
        Route::get('/detail/{id}', 'DetailController@main')->name('user.detail')->middleware('permission:user.show|all.user.show');
        Route::post('/update/{id}', 'UpdateController@main')->name('user.update')->middleware('permission:user.edit');
        Route::post('/delete/{id}', 'DeleteController@main')->name('user.delete')->middleware('permission:user.edit');
    });
    Route::group([
        'namespace' => 'Account',
        'prefix' => 'account',
    ], function () {
        Route::get('/live', 'LiveListController@main')->name('account.live')->middleware('permission:account.show|all.account.show');
        Route::get('/create/{id}', 'CreateLiveAccountController@main')->name('account.live.create')->middleware('permission:account.create');
        Route::post('/create', 'OpenLiveAccountController@main')->name('account.live.open')->middleware('permission:account.create');
        Route::get('/detail/{id}', 'DetailLiveAccountController@main')->name('account.live.detail')->middleware('permission:account.show|all.account.show');;
        Route::post('/detail/{id}', 'UpdateLiveAccountController@main')->name('account.live.update')->middleware('permission:account.edit');
        Route::get('/create-withdrawal', 'LiveListController@createWithdrawal')->name('account.create_withdrawal')->middleware('permission:withdrawal.create');
        Route::post('/create-withdrawal', 'LiveListController@createWithdrawalPost')->name('account.create.withdrawal')->middleware('permission:withdrawal.create');
        Route::get('/create-deposit', 'LiveListController@createDeposit')->name('account.create_deposit')->middleware('permission:deposit.create');
        Route::post('/create-deposit', 'LiveListController@createDepositPost')->name('account.create.deposit')->middleware('permission:deposit.create');
        Route::post('/get-list-login', 'LiveListController@listLogin')->name('account.list.login')->middleware('permission:deposit.create|permission:withdrawal.create');
    });
    Route::group([
        'namespace' => 'Deposit',
        'prefix' => 'deposit',
    ], function () {
        Route::get('/list', 'ListController@main')->name('deposit.list')->middleware('permission:deposit.show|all.deposit.show');
        Route::post('/approve/{id}', 'ApproveController@main')->name('deposit.approve')->middleware('permission:deposit.approve');
        Route::post('/reject/{id}', 'RejectController@main')->name('deposit.reject')->middleware('permission:deposit.approve');;
    });
    Route::group([
        'namespace' => 'Withdrawal',
        'prefix' => 'withdrawal',
    ], function () {
        Route::get('/list', 'ListController@main')->name('withdrawal.list')->middleware('permission:withdrawal.show|all.withdrawal.show');
        Route::post('/approve/{id}', 'ApproveController@main')->name('withdrawal.approve')->middleware('permission:withdrawal.approve');
        Route::post('/reject/{id}', 'RejectController@main')->name('deposit.reject')->middleware('permission:withdrawal.approve');;
    });
    Route::group([
        'namespace' => 'Agent',
        'prefix' => 'agent',
    ], function () {
        Route::get('/customer/link', 'LinkController@main')->name('customer.link')->middleware('permission:user.link');;
        Route::get('/link', 'AgentLinkController@main')->name('agent.link')->middleware('permission:agent.link');;
        Route::get('/list', 'ListController@main')->name('agent.list')->middleware('permission:agent.show');;
        Route::get('/detail/{id}', 'DetailController@main')->name('agent.detail')->middleware('permission:agent.show');
        Route::post('/detail/{id}', 'UpdateController@main')->name('agent.update')->middleware('permission:agent.edit');;
        Route::post('/active/{id}', 'ActiveController@main')->name('agent.active')->middleware('permission:agent.approve');;
        Route::get('/link-manager-agent/{id}', 'ListController@listStaffManager')->name('agent.manager-staff')->middleware('permission:agent.show');;;
        Route::get('/list-status-noactive/{id}', 'ListController@listStaffNoActive')->name('agent.list-status-noactive')->middleware('permission:agent.show');;;
    });
    Route::group([
        'namespace' => 'Report',
        'prefix' => 'report',
    ], function () {
        Route::get('/trade', 'GetTradeListController@main')->name('report.trade')->middleware('permission:report.*');;;
    });
    Route::get('/email/marketing', 'Email\EmailController@main')->name('email.marketing')->middleware('permission:email.create');
    Route::post('/email/marketing', 'Email\SendEmailMarketingController@main')->name('email.marketing.send')->middleware('permission:email.send');
    Route::group([
        'namespace' => 'Role',
        'prefix' => 'role',
    ], function () {
        Route::get('list', 'ListController@main')->name('role.list')
            ->middleware('permission:role.show');
        Route::get('/{id}/detail', 'DetailController@main')->name('role.detail')
            ->middleware('permission:role.show');
        Route::post('/{id}/update', 'UpdateController@main')->name('role.update')
            ->middleware('permission:role.edit');
        Route::get('create', 'CreateController@main')->name('role.create')
            ->middleware('permission:role.create');
        Route::post('store', 'StoreController@main')->name('role.store')
            ->middleware('permission:role.create');
    });
});