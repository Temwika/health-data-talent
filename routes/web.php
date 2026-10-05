<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PageController;
use App\Models\AuditLog;
use App\Models\Candidate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
| Admin CV download (Filament handles everything else under /admin)
*/
Route::middleware('auth')->get('admin/candidates/{candidate}/cv', function (Candidate $candidate) {
    abort_unless($candidate->cv_path && Storage::disk('local')->exists($candidate->cv_path), 404);
    AuditLog::record('cv.downloaded', $candidate, 'Downloaded CV of '.$candidate->name);
    return Storage::disk('local')->download($candidate->cv_path, $candidate->cv_original_name, [
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control' => 'no-store, private',
    ]);
})->name('admin.candidates.cv');
