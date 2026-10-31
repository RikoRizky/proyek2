<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PertiController;
use App\Http\Controllers\Admin\ProdiController as AdminProdiController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\DiscussionController as AdminDiscussionController;
use App\Http\Controllers\Admin\SubmissionOverviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitKerja\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/run-queue-rahasia-123', function () {
    \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]);
    return 'Sukses: ' . \Illuminate\Support\Facades\Artisan::output();
});

Route::get('/', HomeController::class)->name('home');
Route::get('/harga', function () {
    return view('home.harga');
})->name('harga');
Route::get('/diskusi', [DiscussionController::class, 'show'])->name('discussion');
Route::post('/diskusi', [DiscussionController::class, 'store'])->name('discussion.store');

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PertiRegistrationController;

Route::get('/checkout/{package}', [CheckoutController::class, 'showForm'])->name('checkout.form');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/payment/{order_id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/payment/finish', [CheckoutController::class, 'finish'])->name('checkout.finish');

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

use App\Http\Controllers\Auth\GoogleController;
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/register-perti/{token}', [PertiRegistrationController::class, 'showForm'])->name('register-perti.form');
Route::post('/register-perti/{token}', [PertiRegistrationController::class, 'process'])->name('register-perti.process');

Route::get('/sitemap.xml', function () {
    $baseUrl = config('app.url', url('/'));
    $today = date('Y-m-d');
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $xml .= '<url><loc>' . htmlspecialchars($baseUrl) . '/</loc><lastmod>' . $today . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>';
    $xml .= '<url><loc>' . htmlspecialchars($baseUrl) . '/harga</loc><lastmod>' . $today . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
    $xml .= '<url><loc>' . htmlspecialchars($baseUrl) . '/diskusi</loc><lastmod>' . $today . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>';
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'text/xml');
})->name('sitemap');
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('home');
    Route::get('analytics', AnalyticsController::class)->name('analytics');
    Route::get('transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('discussions', [AdminDiscussionController::class, 'index'])->name('discussions.index');

    // Manajemen akun: Admin CRUD
    Route::resource('users', UserController::class)->except(['show']);
    // Manajemen akun: Perti CRUD
    Route::resource('pertis', PertiController::class)->except(['show']);
    // Manajemen akun: Prodi CRUD
    Route::resource('prodis', AdminProdiController::class)->except(['show']);

    Route::get('submissions', [SubmissionOverviewController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}/view', [SubmissionOverviewController::class, 'viewer'])->name('submissions.view');
    Route::get('submissions/{submission}/inline', [SubmissionOverviewController::class, 'inline'])->name('submissions.inline');
    Route::get('submissions/{submission}/download', [SubmissionOverviewController::class, 'download'])->name('submissions.download');

    Route::resource('modules', ModuleController::class);
    Route::resource('modules.requirements', RequirementController::class);
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    
    // Broadcast Notification
    Route::get('broadcast', [App\Http\Controllers\Admin\BroadcastController::class, 'index'])->name('broadcast.index');
    Route::post('broadcast', [App\Http\Controllers\Admin\BroadcastController::class, 'send'])->name('broadcast.send');
});

Route::middleware(['auth', 'role:perti'])->prefix('perti')->name('perti.')->group(function () {
    Route::resource('prodis', App\Http\Controllers\Perti\ProdiController::class)->except(['show']);
    Route::get('prodis/{prodi}/progress', [App\Http\Controllers\Perti\ProdiProgressController::class, 'index'])->name('prodis.progress');
    Route::get('prodis/{prodi}/modul/{module}', [App\Http\Controllers\Perti\ProdiProgressController::class, 'module'])->name('prodis.modul');
    Route::post('prodis/{prodi}/modul/{module}/batch-validate', [App\Http\Controllers\Perti\ProdiProgressController::class, 'batchValidate'])->name('prodis.modul.batch-validate');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    Route::post('submissions/{submission}/validate', [App\Http\Controllers\Perti\SubmissionController::class, 'validateSubmission'])->name('submissions.validate');
    Route::get('submissions/{submission}/view', [App\Http\Controllers\Perti\SubmissionController::class, 'viewer'])->name('submissions.view');
    Route::get('submissions/{submission}/inline', [App\Http\Controllers\Perti\SubmissionController::class, 'inline'])->name('submissions.inline');
    Route::get('submissions/{submission}/download', [App\Http\Controllers\Perti\SubmissionController::class, 'download'])->name('submissions.download');
    Route::get('submissions/{submission}', [App\Http\Controllers\Perti\SubmissionController::class, 'show'])->name('submissions.show');
});

Route::middleware(['auth', 'role:prodi'])->prefix('unit')->name('unit.')->group(function () {
    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/modul/{module}', [SubmissionController::class, 'module'])->name('submissions.module');
    Route::post('modules/{module}/submissions/batch', [SubmissionController::class, 'batchStore'])->name('modules.submissions.batch');
    Route::post('requirements/{requirement}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('submissions/{submission}/view', [SubmissionController::class, 'viewer'])->name('submissions.view');
    Route::get('submissions/{submission}/inline', [SubmissionController::class, 'inline'])->name('submissions.inline');
    Route::get('submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Upgrade/Extend flow for logged in users
    Route::get('/upgrade', [CheckoutController::class, 'upgradePackages'])->name('upgrade.packages');
    Route::post('/upgrade/process', [CheckoutController::class, 'processUpgrade'])->name('upgrade.process');
});

require __DIR__.'/auth.php';
