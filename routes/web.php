<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DiscountPlanController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Canteen\DeliveryController;
use App\Http\Controllers\Canteen\FoodController;
use App\Http\Controllers\Canteen\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DocumentController;
use App\Http\Controllers\Student\ReserveController;
use App\Http\Controllers\Student\WalletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PSpell\Config;
use Symfony\Component\HttpKernel\Profiler\Profile;

Route::middleware('guest')->controller(AuthController::class)->group(function(){
    Route::get('/register' , 'showRegisterStudentForm')->name('auth.student.showRegisterForm');
    Route::post('/register' , 'registerStudent')->name('auth.student.register');
    Route::get('/login' , 'showLoginForm')->name('login');
    Route::post('/login' , 'login')->name('auth.login');
});

Route::get('/logout',[AuthController::class , 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth' , 'is_student'])->prefix('student')->group(function() {
    Route::get('/' , [AuthController::class , 'studentDashboard'])->name('student.dashboard');

    Route::get('/documents' , [DocumentController::class , 'index'])->name('student.documents.index');
    Route::post('/documents' , [DocumentController::class , 'store'])->name('student.documents.store');
    Route::get('/documents/{document}' , [DocumentController::class , 'show'])->name('student.documents.show');
    Route::delete('/documents/{document}' , [DocumentController::class , 'destroy'])->name('student.documents.delete');
    Route::post('/documents/scholarship' , [DocumentController::class , 'scholarshipRequest'])->name('student.documents.scholarship');


    Route::get('/reserve',[ReserveController::class , 'canteens'])->name('student.reserves.reserve.canteens');
    Route::get('/reserve/{canteen}',[ReserveController::class , 'menus'])->name('student.reserves.reserve.menus');
    Route::get('/reserve/{canteen}/menus/{menu}',[ReserveController::class , 'showNormalMenu'])->name('student.reserves.reserve.Normalmenu');
    Route::post('/reserve/{canteen}/menus/{menu}',[ReserveController::class , 'storeNormalReserve'])->name('student.reserves.reserve.store');
    Route::get('/reserve/{canteen}/menus/{menu}/daily',[ReserveController::class , 'showDailyReserveMenu'])->name('student.reserves.reserve.dailymenu');
    Route::post('/reserve/{canteen}/menus/{menu}/daily',[ReserveController::class , 'storeDailyReserve'])->name('student.reserves.reserve.storeDaily');
    Route::get('/reserves',[ReserveController::class , 'showAllReserves'])->name('student.reserves.all');
    Route::get('/reserves/{reserve}',[ReserveController::class , 'showReserveFoods'])->name('student.reserves.details');
    Route::delete('/reserves/{reserve}',[ReserveController::class , 'cancelReservation'])->name('student.reserves.cancel');
    Route::get('/reserves/{reserve}/delivere',[ReserveController::class , 'delivereMeal'])->name('student.reserves.delivere');

    Route::get('/reserves/{reserve}/feedback', [ReserveController::class, 'showFeedbackPage'])->name('student.reserves.feedback.show');
    Route::post('/reserves/{reserve}/feedback', [ReserveController::class, 'storeFeedback'])->name('student.reserves.feedback.store');

    Route::get('/wallet' , [WalletController::class , 'index'])->name('student.wallet.index');
    Route::post('/wallet/charge' , [WalletController::class , 'chargeWallet'])->name('student.wallet.charge');
    Route::post('/wallet/post' , [WalletController::class , 'transferMoney'])->name('student.wallet.transfer');

    
    Route::get('/profile', [ProfileController::class, 'show'])->name('student.profile.show');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('student.profile.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('student.profile.avatar.update');


    });

Route::middleware(['auth' , 'is_mensa'])->prefix('mensa')->group(function() {
    Route::get('/' , [AuthController::class , 'mensaDashboard'])->name('mensa.dashboard');

    Route::get('/foods' , [FoodController::class,'index'])->name('mensa.foods.index');
    Route::post('/foods' , [FoodController::class,'store'])->name('mensa.foods.store');
    Route::delete('/foods/{food}' , [FoodController::class,'destroy'])->name('mensa.foods.destroy');

    Route::get('/menus/add' , [MenuController::class , 'create'])->name('mensa.menus.create');
    Route::post('/menus' , [MenuController::class , 'store'])->name('mensa.menus.store');
    Route::get('/menus' , [MenuController::class , 'showAllMenus'])->name('mensa.menus.showAll');
    Route::get('/menus/{menu}' , [MenuController::class , 'showStatisticsOfMenu'])->name('mensa.menus.show');
    Route::post('/menus/{menu}' , [MenuController::class , 'defineDailySaleForFood'])->name('mensa.menus.storeDaily');
    Route::get('/menus/{menu}/delivere/{reserve?}' , [DeliveryController::class , 'showDeliveryPage'])->name('mensa.delivery.show');
    Route::post('/menus/{menu}/delivere' , [DeliveryController::class , 'DelieverReserve'])->name('mensa.delivery.store');

    Route::get('/menus/{menu}/feedbacks', [MenuController::class, 'showFeedbacksOfAMenu'])->name('mensa.menus.feedbacks.show');
    Route::post('/menus/{menu}/feedbacks/generate-ai', [MenuController::class, 'generateAiSummary'])->name('mensa.menus.feedbacks.ai');
});

Route::middleware(['auth' , 'is_ersu'])->prefix('ersu')->group(function() {
    Route::get('/' , [AuthController::class , 'adminDashboard'])->name('admin.dashboard');

    Route::get('/mensa' , [AuthController::class , 'showcanteens'])->name('admin.canteens.index');
    Route::post('/mensa' , [AuthController::class , 'registerMensa'])->name('admin.canteens.store');
    Route::put('/mensa/{canteen}', [ProfileController::class, 'updateCanteen'])->name('admin.canteens.update');
    Route::put('/mensa/{canteen}/password', [ProfileController::class, 'updatePasswordCanteen'])->name('admin.canteens.password');


    Route::get('/discounts' , [DiscountPlanController::class , 'index'])->name('admin.discounts.index');
    Route::post('/discounts' , [DiscountPlanController::class , 'store'])->name('admin.discounts.store');
    Route::put('/discounts/{discountPlan}', [DiscountPlanController::class , 'update'])->name('admin.discounts.edit');
    Route::delete('/discounts/{discountPlan}', [DiscountPlanController::class , 'destroy'])->name('admin.discounts.delete');

    Route::get('/documents' , [DocumentTypeController::class , 'index'])->name('admin.documents.index');
    Route::post('/documents' , [DocumentTypeController::class , 'store'])->name('admin.documents.store');
    Route::delete('/documents/{documentType}', [DocumentTypeController::class , 'destroy'])->name('admin.documents.delete');

    Route::get('/scholarships' , [ScholarshipController::class , 'index'])->name("admin.scholarships.index");
    Route::get('/scholarships/{scholarshipApplication}' , [ScholarshipController::class , 'show'])->name("admin.scholarships.show");
    Route::get('/scholarship/{document}' , [ScholarshipController::class , 'viewDoc'])->name('admin.scholarships.document');
    Route::post('/scholarships/{scholarshipApplication}/approve' , [ScholarshipController::class , 'assignScholarship'])->name("admin.scholarships.approve");
    Route::post('/scholarships/{scholarshipApplication}/reject' , [ScholarshipController::class , 'rejectScholarship'])->name("admin.scholarships.reject");

    Route::get('/categories' , [CategoryController::class , 'index'])->name('admin.categories.index');
    Route::post('/categories' , [CategoryController::class , 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class , 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class , 'destroy'])->name('admin.categories.delete');

    Route::get('/configs' , [ConfigController::class , 'index'])->name('admin.configs.index');
    Route::put('/configs' , [ConfigController::class , 'updateAll'])->name('admin.configs.update');



});


Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role->value;
        
        return match($role) {
            'student' => redirect()->route('student.dashboard'),
            'mensa'   => redirect()->route('mensa.dashboard'),
            'admin'    => redirect()->route('admin.dashboard'),
            default   => abort(403),
        };
    }
    return app(AuthController::class)->welcome(); 
    
})->name('welcome');