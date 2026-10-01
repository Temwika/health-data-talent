<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
| Public site
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::view('/doctors', 'pages.doctors')->name('doctors');
Route::view('/about', 'pages.about')->name('about');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

Route::get('/employers', [EmployerController::class, 'create'])->name('employers.create');
Route::get('/employers/vacancy', [EmployerController::class, 'createVacancy'])->name('vacancies.create');
Route::get('/candidates', [CandidateController::class, 'create'])->name('candidates.create');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{vacancy:slug}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{post:slug}', [InsightController::class, 'show'])->name('insights.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/employers', [EmployerController::class, 'store'])->name('employers.store');
    Route::post('/employers/vacancy', [EmployerController::class, 'storeVacancy'])->name('vacancies.store');
    Route::post('/candidates', [CandidateController::class, 'store'])->name('candidates.store');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

/*
| Admin
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('two-factor', [Admin\TwoFactorController::class, 'challenge'])->name('2fa.challenge');
        Route::post('two-factor', [Admin\TwoFactorController::class, 'verify'])->middleware('throttle:10,1');
        Route::get('two-factor/setup', [Admin\TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('two-factor/setup', [Admin\TwoFactorController::class, 'confirm'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'twofactor'])->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('candidates', [Admin\CandidateController::class, 'index'])->name('candidates.index');
        Route::get('candidates/{candidate}', [Admin\CandidateController::class, 'show'])->name('candidates.show');
        Route::patch('candidates/{candidate}', [Admin\CandidateController::class, 'update'])->name('candidates.update');
        Route::get('candidates/{candidate}/cv', [Admin\CandidateController::class, 'cv'])->name('candidates.cv');
        Route::post('candidates/{candidate}/applications', [Admin\ApplicationController::class, 'store'])->name('applications.store');

        Route::get('employers', [Admin\OrganisationController::class, 'index'])->name('employers.index');
        Route::patch('employers/{organisation}', [Admin\OrganisationController::class, 'update'])->name('employers.update');

        Route::get('vacancies', [Admin\VacancyController::class, 'index'])->name('vacancies.index');
        Route::get('vacancies/create', [Admin\VacancyController::class, 'create'])->name('vacancies.create');
        Route::post('vacancies', [Admin\VacancyController::class, 'store'])->name('vacancies.store');
        Route::get('vacancies/{vacancy}/edit', [Admin\VacancyController::class, 'edit'])->name('vacancies.edit');
        Route::put('vacancies/{vacancy}', [Admin\VacancyController::class, 'update'])->name('vacancies.update');
        Route::patch('vacancies/{vacancy}/status', [Admin\VacancyController::class, 'status'])->name('vacancies.status');

        Route::get('applications', [Admin\ApplicationController::class, 'index'])->name('applications.index');
        Route::patch('applications/{application}', [Admin\ApplicationController::class, 'update'])->name('applications.update');

        Route::get('enquiries', [Admin\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::patch('enquiries/{enquiry}', [Admin\EnquiryController::class, 'update'])->name('enquiries.update');

        Route::resource('posts', Admin\PostController::class)->except(['show', 'destroy']);

        // Destructive actions and the audit trail are for administrators only.
        Route::middleware('role:admin')->group(function () {
            Route::delete('candidates/{candidate}', [Admin\CandidateController::class, 'destroy'])->name('candidates.destroy');
            Route::delete('employers/{organisation}', [Admin\OrganisationController::class, 'destroy'])->name('employers.destroy');
            Route::delete('vacancies/{vacancy}', [Admin\VacancyController::class, 'destroy'])->name('vacancies.destroy');
            Route::delete('posts/{post}', [Admin\PostController::class, 'destroy'])->name('posts.destroy');
            Route::get('audit', Admin\AuditController::class)->name('audit');
        });
    });
});
