<?php

use App\Http\Controllers\Admin\DiscountPlanController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Student\DocumentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
});

Route::middleware(['auth' , 'is_mensa'])->prefix('mensa')->group(function() {
    Route::get('/' , [AuthController::class , 'mensaDashboard'])->name('mensa.dashboard');
});

Route::middleware(['auth' , 'is_ersu'])->prefix('ersu')->group(function() {
    Route::get('/' , [AuthController::class , 'adminDashboard'])->name('admin.dashboard');

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