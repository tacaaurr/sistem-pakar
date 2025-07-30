<?php

use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\GejalaController;
use App\Http\Controllers\TingkatDepresiController;
use App\Models\Diagnosa;
use App\Models\TingkatDepresi;
use App\Models\KondisiUser;
use App\Models\Gejala;
use App\Models\User;
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

// --- Rute Umum (Bisa Diakses Tanpa Login) ---

// Halaman utama / landing page
Route::get('/', function () {
    return view('landing');
});

// Rute untuk form diagnosa
Route::get('/form', function () {
    $data = [
        'gejala' => Gejala::all(),
        'kondisi_user' => KondisiUser::all()
    ];
    return view('form', $data);
});

// Rute untuk FAQ
Route::get('/form-faq', function () {
    $data = [
        'gejala' => Gejala::all(),
        'kondisi_user' => KondisiUser::all()
    ];
    return view('faq', $data);
})->name('cl.form');

// Rute resource untuk SPK (DiagnosaController)
// Ini akan mencakup: index, create, store, show, edit, update, destroy
// Pastikan DiagnosaController punya method-method ini jika semua route resource dibutuhkan
Route::resource('/spk', DiagnosaController::class);

// Rute untuk hasil diagnosa berdasarkan ID
Route::get('/spk/result/{diagnosa_id}', [DiagnosaController::class, 'diagnosaResult'])->name('spk.result');

// Rute bawaan autentikasi Laravel (login, register, reset password, dll.)
Auth::routes();


// --- Rute yang Membutuhkan Login (Middleware 'auth') ---

Route::middleware('auth')->group(function () {
    // Redirect /home ke /dashboard setelah login
    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    // Dashboard admin/user yang sudah login
    Route::get('/dashboard', function () {
        $data = [
            'gejala' => Gejala::all(),
            'kondisi_user' => KondisiUser::all(),
            'user' => User::all(),
            'tingkat_depresi' => TingkatDepresi::all()
        ];
        return view('admin.dashboard', $data);
    })->name('dashboard'); // Beri nama rute untuk kemudahan navigasi

    // Daftar admin
    Route::get('/dashboard/admin', function () {
        $data = [
            'user' => User::all()
        ];
        return view('admin.list_admin', $data);
    })->name('admin.list'); // Beri nama rute

    // Form tambah admin
    Route::get('/dashboard/add_admin', function () {
        return view('admin.add_admin');
    })->name('admin.add'); // Beri nama rute

    // Rute resource untuk Gejala (hanya bisa diakses setelah login)
    Route::resource('/gejala', GejalaController::class);

    // Rute resource untuk Tingkat Depresi (hanya bisa diakses setelah login)
    Route::resource('/depresi', TingkatDepresiController::class);

    // --- Tambahan Rute untuk Profil Pengguna ---
    // Saya asumsikan halaman profil akan diakses via GET
    // dan akan menampilkan view bernama 'profile.blade.php'
    // Kamu bisa sesuaikan URL '/profile' atau nama view 'profile' ini.
    Route::get('/profile', function () {
        // Jika kamu ingin menampilkan data user yang sedang login, kamu bisa panggil:
        // $user = Auth::user(); // Pastikan 'use Illuminate\Support\Facades\Auth;' di atas
        // return view('profile', compact('user'));
        return view('profile'); // Cukup return view dulu untuk memastikan halaman tampil
    })->name('user.profile'); // Beri nama rute untuk kemudahan panggilan link

});

// --- Catatan Tambahan ---
// Jika ada rute spesifik untuk admin yang terkait dengan SPK (DiagnosaController),
// misalnya untuk mengelola diagnosa, Anda bisa menambahkan rute terpisah di dalam middleware('auth'):
/*
Route::middleware('auth')->group(function () {
    // ... rute yang sudah ada ...

    // Contoh: Rute untuk mengelola diagnosa dari sisi admin
    Route::get('/admin/spk', [DiagnosaController::class, 'adminIndex'])->name('admin.spk.index');
    // Atau jika Anda butuh semua resource method untuk admin SPK:
    // Route::resource('/admin/spk', DiagnosaController::class)->except(['index']); // Karena index sudah ada di luar middleware
});
*/