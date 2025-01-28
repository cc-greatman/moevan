<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function() {
    // User Routes (moevan.com)
    Route::domain(env('USER_DOMAIN'))->group(function () {

        //Email Verify Action Route
        Route::get('user/email/verify/{token}/{email}', 'Auth\EmailVerifyController@verifyAccount')->name('user.email.verify.perform');

        //Sync Route
        Route::get('sync', 'Controller@syncServiceVariations');
        Route::get('load', 'Controller@postData');
        Route::get('token', 'User\MonnifyController@generateToken');
        Route::get('account', 'User\MonnifyController@reserveAccount');
        Route::get('/refresh-csrf', function() {
            return response()->json(['csrf_token' => csrf_token()]);
        });

        Route::name('webhook.')->prefix('webhook')->group(function () {
            Route::post('transfer-status', 'WebhookController@handle');
            Route::post('manual-status', 'WebhookController@handleIpay');
            Route::post('data-status', 'WebhookController@handleData');
        });

        Route::name('cron.')->prefix('cron')->group(function () {
            Route::get('sync-dstv', 'CronController@syncDstv');
            Route::get('sync-gotv', 'CronController@syncGotv');
            Route::get('sync-star', 'CronController@syncStar');
            Route::get('sync-show', 'CronController@syncShow');
        });

        //Landing Page Route
        Route::get('/', 'User\ViewController@home')->name('home');

        //User Authentication Routes
        Route::name('user.')->group(function() {
            Route::group(['middleware' => ['guest']], function() {

                //Registration Routes
                Route::get('register', 'Auth\RegisterController@show')->name('register.show');
                Route::post('register', 'Auth\RegisterController@register')->name('register.perform');

                //Login Routes
                Route::get('login', 'Auth\LoginController@show')->name('login.show');
                Route::post('login', 'Auth\LoginController@login')->name('login.perform');

                //Google Login Routes
                Route::get('login/google', 'Auth\SocialiteController@redirectToGoogle')->name('login.google');
                Route::get('login/google/callback', 'Auth\SocialiteController@handleGoogleCallback');

                Route::get('login/facebook', 'Auth\SocialiteController@redirectToFacebook')->name('login.facebook');
                Route::get('login/facebook/callback', 'Auth\SocialiteController@handleFacebookCallback');

                //Forgot Password Routes
                Route::get('forgot-password', 'Auth\ResetPassController@index')->name('forgot-password.show');
                Route::post('forgot-password', 'Auth\ResetPassController@sendLink')->name('forgot-password.perform');
                Route::get('reset-password/{token}', 'Auth\ResetPassController@show')->name('reset-password.show');
                Route::post('reset-password', 'Auth\ResetPassController@perform')->name('reset-password.perform');
            });
        });

        //User Action Routes
        Route::name('user.')->prefix('user')->group(function() {

            Route::middleware(['auth:web', 'single.login.session', 'ensure.subdomain'])->group(function () {

                //Monnify Routes
                Route::get('/get-customer-details', 'User\MonnifyController@getCustomerDetails');
                Route::post('/process-monnify-payment', 'User\MonnifyController@processPayment');

                //Email Verification Routes
                Route::name('email.')->prefix('email')->group(function() {
                    Route::get('verify', 'Auth\EmailVerifyController@show')->name('verify.show');
                    Route::post('verify/resend', 'Auth\EmailVerifyController@resend')->name('verify.resend');
                });

                Route::group(['middleware' => ['is.email.verified']], function() {

                    //Logout Routes
                    Route::get('logout', 'Auth\LogoutController@perform')->name('logout');

                    Route::get('success', 'Controller@success')->name('trx.success');
                    Route::get('error', 'Controller@error')->name('trx.error');
                    Route::get('processing', 'Controller@warning')->name('trx.warning');

                    //Dashboard Routes
                    Route::get('dashboard', 'User\ViewController@index')->name('dashboard');

                    //Data Routes
                    Route::name('data.')->prefix('data')->group(function() {
                        Route::get('buy', 'User\ViewController@buyData')->name('show');
                        Route::post('buy/perform', 'User\InternetController@processDataSub')->name('buy.perform');
                        Route::get('/plans', 'User\InternetController@getDataPlans')->name('plans');
                        Route::get('/types', 'User\InternetController@getDataTypes')->name('types');
                        Route::get('/search/transactions', 'User\TransactionsController@searchData')->name('search.transactions');
                    });

                    //Airtime Routes
                    Route::name('airtime.')->prefix('airtime')->group(function() {
                        Route::get('buy', 'User\ViewController@buyAirtime')->name('show');
                        Route::post('buy', 'User\AirtimeController@processTopUp')->name('buy.process');
                        Route::get('/search/transactions', 'User\TransactionsController@searchAirtime')->name('search.transactions');
                    });

                    Route::get('business-registration', 'User\ViewController@businessReg')->name('business.view');

                    //Utility Routes
                    Route::name('utility.')->prefix('utility')->group(function() {

                        //Electricity Routes
                        Route::name('electric.')->prefix('electric')->group(function() {
                            Route::get('buy', 'User\ViewController@buyElectric')->name('show');
                            Route::get('test', 'Controller@verifyMeter');
                            Route::get('token', 'User\ElectricityController@showToken')->name('show.token');
                            Route::post('buy', 'User\ElectricityController@processRecharge')->name('perform');
                            Route::get('/search/transactions', 'User\TransactionsController@searchElectric')->name('search.transactions');
                        });

                        //Electricity Routes
                        Route::name('cable.')->prefix('cable')->group(function() {
                            Route::get('buy', 'User\ViewController@buyCable')->name('show');
                            Route::get('get/{cableName}', 'User\CableController@getSubscriptionPlans')->name('plans.get');
                            Route::post('buy', 'User\CableController@performTransaction')->name('perform');
                            Route::get('/search/transactions', 'User\TransactionsController@searchCable')->name('search.transactions');
                            Route::post('/validate-smartcard', 'User\ProxyController@validateSmartcard')->name('validate.smartcard');
                        });
                    });

                    //Top-Up Routes
                    Route::name('top-up.')->prefix('wallet')->group(function() {

                        //Paystack Routes
                        Route::name('paystack.')->prefix('paystack')->group(function() {
                            Route::get('top-up', 'User\ViewController@fundPaystack')->name('show');
                        });

                        //Monnify Routes
                        Route::name('fund.')->prefix('fund')->group(function() {
                            Route::get('monnify', 'User\ViewController@viewMonnify')->name('show');
                            Route::get('manual', 'User\MonnifyController@viewManual')->name('manual.show');
                            Route::get('automated-manual', 'User\ViewController@autoManual')->name('auto.manual');
                            Route::post('manual/perform', 'User\MonnifyController@processManual')->name('manual.perform');
                        });
                        Route::get('/search/transactions', 'User\TransactionsController@searchWallet')->name('search.transactions');
                    });


                    //Profile Settings Routes
                    Route::name('profile.')->prefix('profile')->group(function() {
                        Route::get('general', 'User\ViewController@profile')->name('show');
                        Route::get('kyc/verification', 'User\ViewController@kycVerification')->name('kyc.show');
                        Route::get('bonus/withdraw', 'User\ViewController@bonus')->name('bonus.show');
                        Route::post('bonus/withdraw', 'User\ProfileController@withdrawBonus')->name('withdraw.bonus.perform');
                        Route::post('profile/update/picture', 'User\ProfileController@updateProfilePicture')->name('picture.update');
                        Route::post('profile/update/details', 'User\ProfileController@updateProfileInfo')->name('profile.update');
                        Route::post('profile/update/password', 'User\ProfileController@updateUserPassword')->name('password.update');
                        Route::post('account/upgrade', 'User\ProfileController@updateUserStatus')->name('account.upgrade');
                        Route::post('kyc/verify', 'User\ProfileController@kycVerification')->name('kyc.perform');
                    });

                    //Transaction History Routes
                    Route::name('transactions.')->prefix('transactions')->group(function() {
                        Route::get('general', 'User\TransactionsController@main')->name('general.show');
                        Route::get('data', 'User\TransactionsController@data')->name('data.show');
                        Route::get('airtime', 'User\TransactionsController@airtime')->name('airtime.show');
                        Route::get('wallet', 'User\TransactionsController@wallet')->name('wallet.show');
                        Route::get('electric', 'User\TransactionsController@electric')->name('electric.show');
                        Route::get('cable', 'User\TransactionsController@cable')->name('cable.show');
                    });

                    //Transaction History Routes
                    Route::name('receipt.')->prefix('view/receipt')->group(function() {
                        Route::get('data/{id}', 'User\TransactionsController@viewReceipt')->name('data.show');
                    });

                    //Pricing Routes
                    Route::get('pricing', 'User\ViewController@pricing')->name('pricing.view');
                });

            });

        });

    });

    Route::domain(env('ADMIN_DOMAIN'))->middleware('ensure.subdomain')->group(function () {

        //Admin Routes
        Route::name('admin.')->group(function() {

            Route::get('/', 'Auth\LoginController@adminShow')->name('login.show');
            Route::post('login', 'Auth\LoginController@adminLogin')->name('login.perform');

                //Pass Admin Middleware
            Route::middleware(['auth:admin', 'ensure.subdomain'])->group(function () {
                Route::get('dashboard', 'Admin\ViewController@dashboard')->name('show.dashboard');

                //Manage User Routes
                Route::name('user.')->prefix('manage/user')->group(function () {
                    Route::get('referrals', 'Admin\ViewController@viewReferrals')->name('referrals');
                    Route::get('topup', 'Admin\ViewController@viewTopup')->name('topup');
                    Route::get('debit', 'Admin\ViewController@viewDebit')->name('debit');
                    Route::get('view/perform/{id}', 'Admin\ViewController@viewPerformTopUp')->name('view.perform');
                    Route::post('login/{id}', 'Admin\UserController@userLogin')->name('login');
                    Route::post('delete/{id}', 'Admin\UserController@deleteUser')->name('delete');
                    Route::get('view-perform/{id}', 'Admin\ViewController@viewPerformDebit')->name('perform');
                    Route::post('topup/perform/{id}', 'Admin\UserController@userFund')->name('topup.perform');
                    Route::post('debit/perform/{id}', 'Admin\UserController@userDebit')->name('debit.perform');
                });

                //Manage Setting Routes
                Route::name('setting.')->prefix('settings/site')->group(function () {
                    Route::get('rolling_message', 'Admin\ViewController@viewRolling')->name('rolling');
                    Route::get('rolling_update/{id}', 'Admin\ViewController@messageEdit')->name('rolling.view');
                    Route::get('data_switches', 'Admin\ViewController@viewData')->name('data');
                    Route::get('general_switches', 'Admin\ViewController@viewGeneral')->name('general');

                    Route::get('/airtime/manual', 'Admin\EditController@switchAirtimeManual')->name('airtime.manual');
                    Route::get('/cable/manual', 'Admin\EditController@switchCableManual')->name('cable.manual');
                    Route::get('/electricity/manual', 'Admin\EditController@switchElectricityManual')->name('electricity.manual');

                    Route::get('/airtime/main', 'Admin\EditController@switchAirtimeMain')->name('airtime.main');
                    Route::get('/cable/main', 'Admin\EditController@switchCableMain')->name('cable.main');
                    Route::get('/electricity/main', 'Admin\EditController@switchElectricityMain')->name('electricity.main');
                    Route::get('/popup/main', 'Admin\EditController@switchPopupMain')->name('popup.main');

                    Route::get('/reseller/special/on', 'Admin\EditController@switchOnResellerData')->name('reseller.switch.on');
                    Route::get('/reseller/special/off', 'Admin\EditController@switchOffResellerData')->name('reseller.switch.off');
                    Route::get('/agent/special/on', 'Admin\EditController@switchOnAgentData')->name('agent.switch.on');
                    Route::get('/agent/special/off', 'Admin\EditController@switchOffAgentData')->name('agent.switch.off');
                    Route::post('/manual_disbursment/on/{id}', 'Admin\EditController@switchOnDataStatus')->name('status.switch.on');
                    Route::post('/manual_disbursment/off/{id}', 'Admin\EditController@switchOffDataStatus')->name('status.switch.off');
                    Route::post('/main_switch/on/{id}', 'Admin\EditController@switchOnDataActive')->name('active.switch.on');
                    Route::post('/main_switch/off/{id}', 'Admin\EditController@switchOffDataActive')->name('active.switch.off');
                    Route::post('rolling_update/{id}', 'Admin\EditController@messageEdit')->name('rolling.update');
                });

                    //Export to Excel Routes
                Route::name('export.')->prefix('export')->group(function () {
                    Route::get('wallet/{filename}', 'ExportController@exportWallet')->name('wallet');
                    Route::get('data/{filename}', 'ExportController@exportData')->name('data');
                    Route::get('airtime/{filename}', 'ExportController@exportAirtime')->name('airtime');
                    Route::get('cable/{filename}', 'ExportController@exportCable')->name('cable');
                    Route::get('electric/{filename}', 'ExportController@exportElectric')->name('electric');
                });

                //View Transactions
                Route::name('price.')->prefix('price/edit')->group(function () {
                    Route::get('data/plans', 'Admin\AgentDataPriceController@getDataPlans')->name('data.plans.get');
                    Route::get('data', 'Admin\ViewController@viewDataPrices')->name('data.edit');
                    Route::get('/specific/data', 'Admin\ViewController@viewAgentPrice')->name('specific.data.edit');
                    Route::get('data', 'Admin\ViewController@viewDataPrices')->name('data.edit');
                    Route::get('data/agent', 'Admin\AgentDataPriceController@index')->name('agent.list');
                    Route::get('data/agent/{id}/edit', 'Admin\AgentDataPriceController@edit')->name('agent.edit');
                    Route::put('data/agent/{id}', 'Admin\AgentDataPriceController@update')->name('agent.update');
                    Route::get('data/reseller', 'Admin\ViewController@viewResellerPrices')->name('data.edit.reseller');
                    Route::get('cable', 'Admin\ViewController@viewCablePrices')->name('cable.edit');
                    Route::post('data', 'Admin\EditController@editData')->name('data.edit.perform');
                    Route::post('data/agent', 'Admin\EditController@editAgentData')->name('data.edit.agent.perform');
                    Route::post('data/reseller', 'Admin\EditController@editResellerData')->name('data.edit.reseller.perform');
                    Route::post('cable', 'Admin\EditController@editCable')->name('cable.edit.perform');
                });

                //Data Routes
                Route::name('data.')->prefix('data')->group(function() {
                    Route::get('/special_data_on', 'Admin\EditController@switchOnData')->name('switch.on');
                    Route::get('/special_data_off', 'Admin\EditController@switchOffData')->name('switch.off');
                    Route::get('/plans', 'User\InternetController@getAdminDataPlans')->name('plans');
                    Route::get('/types', 'User\InternetController@getAdminDataTypes')->name('types');
                    Route::get('/reseller/types', 'User\InternetController@getAdminDataTypesR')->name('types.reseller');
                    Route::get('/agent/types', 'User\InternetController@getAdminDataTypesA')->name('types.agent');
                    Route::post('refund/user/{id}', 'Admin\UserController@refundUser')->name('refund');
                });

                //View Transactions
                Route::name('transactions.')->prefix('transactions/view')->group(function () {
                    Route::get('wallet', 'Admin\ViewController@viewWalletTransactions')->name('wallet.view');
                    Route::get('airtime', 'Admin\ViewController@viewAirtimeTransactions')->name('airtime.view');
                    Route::get('data', 'Admin\ViewController@viewDataTransactions')->name('data.view');
                    Route::get('cable', 'Admin\ViewController@viewCableTransactions')->name('cable.view');
                    Route::get('electric', 'Admin\ViewController@viewElectricTransactions')->name('electric.view');
                    Route::get('manual/transactions', 'Admin\ViewController@viewManualTransactions')->name('manual.transactions');
                    Route::get('manual/transactions/{transaction}', 'Admin\ViewController@showManualTransactions')->name('manual.transactions.show');
                    Route::post('manual/transactions/{transaction}/approve', 'Admin\UserController@approveManualTransactions')->name('manual.transactions.approve');
                    Route::post('manual/transactions/{transaction}/reject', 'Admin\UserController@disapproveManualTransactions')->name('manual.transactions.disapprove');
                });

                //Search Transactions
                Route::name('search.')->prefix('search')->group(function() {
                    Route::get('user/topup', 'Admin\UserController@searchTopup')->name('topup');
                    Route::get('user/debit', 'Admin\UserController@searchDebit')->name('debit');
                    Route::get('user/referrals', 'Admin\UserController@searchReferral')->name('referral');
                    Route::get('data/transactions', 'Admin\UserController@searchData')->name('data');
                    Route::get('airtime/transactions', 'Admin\UserController@searchAirtime')->name('airtime');
                    Route::get('electric/transactions', 'Admin\UserController@searchElectric')->name('electric');
                    Route::get('cable/transactions', 'Admin\UserController@searchCable')->name('cable');
                    Route::get('wallet/transactions', 'Admin\UserController@searchWallet')->name('wallet');
                });

                Route::get('logout', 'Auth\LogoutController@logout')->name('logout.perform');
            });
        });

    });

});
