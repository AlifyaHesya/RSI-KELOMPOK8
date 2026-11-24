<?php

use App\Enums\Role;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengingatController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RekapLaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('katalog.index'));

/* ---------- UC-01 / UC-02: Auth ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'tampilkanFormLupaKataSandi'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'kirimTautanReset'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'tampilkanFormAturUlangKataSandi'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'aturUlangKataSandi'])->name('password.update');
    Route::get('/login', [AuthController::class, 'tampilkanLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'masuk']);

    Route::get('/register', [AuthController::class, 'pilihPeran'])->name('register');
    Route::post('/register/kode-akses', [AuthController::class, 'validasiKodeAkses'])->name('register.kode');
    Route::get('/register/{role}', [AuthController::class, 'tampilkanFormRegistrasi'])->name('register.form');
    Route::post('/register/{role}', [AuthController::class, 'registrasi'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'keluar'])->middleware('auth')->name('logout');

Route::get('/dashboard', fn () => redirect()->route(match (auth()->user()->role) {
    Role::Mahasiswa => 'katalog.index',
    Role::Pic => 'pic.proker.index',
}))->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.dibaca');
    Route::patch('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
});

/* ---------- UC-03/04/05/08: Katalog (publik) ---------- */
Route::get('/proker', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/proker/{programKerja}', [KatalogController::class, 'show'])->name('katalog.show');

/* ---------- Mahasiswa: UC-06, UC-07 ---------- */
Route::middleware(['auth', 'role:MHS'])->group(function () {
    Route::get('/proker/{programKerja}/daftar', [PendaftaranController::class, 'periksaPendaftaran'])->name('pendaftaran.periksa');
    Route::post('/proker/{programKerja}/daftar', [PendaftaranController::class, 'kirimDataPendaftaran'])->name('pendaftaran.simpan');
    Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'tampilkanKonfirmasi'])->name('pendaftaran.konfirmasi');

    Route::get('/proker/{programKerja}/pengingat', [PengingatController::class, 'tampilkanPilihanWaktu'])->name('pengingat.form');
    Route::post('/proker/{programKerja}/pengingat', [PengingatController::class, 'aktifkanPengingat'])->name('pengingat.simpan');
});

/* ---------- PIC: UC-09 s.d. UC-13 ---------- */
Route::middleware(['auth', 'role:PIC'])->prefix('pic')->name('pic.')->group(function () {
    Route::resource('proker', ProgramKerjaController::class)
        ->parameters(['proker' => 'programKerja'])
        ->except(['show']);
    Route::patch('proker/{programKerja}/buka', [ProgramKerjaController::class, 'ubahStatusPendaftaran'])->name('proker.buka');

    Route::get('pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('pengajuan/{pengajuan}/berkas', [PengajuanController::class, 'unggahBerkas'])->name('pengajuan.berkas');
    Route::post('pengajuan/{pengajuan}/ajukan', [PengajuanController::class, 'ajukanPengajuan'])->name('pengajuan.ajukan');
    Route::post('pengajuan/{pengajuan}/konfirmasi', [PengajuanController::class, 'setujuiPengajuan'])->name('pengajuan.konfirmasi');

    Route::get('rekap', [RekapLaporanController::class, 'index'])->name('rekap.index');
    Route::get('rekap/{programKerja}', [RekapLaporanController::class, 'tampilkanOpsiDokumen'])->name('rekap.opsi');
    Route::get('rekap/{programKerja}/peserta', [RekapLaporanController::class, 'tampilkanPilihanFormatRekap'])->name('rekap.peserta');
    Route::get('rekap/{programKerja}/peserta/unduh', [RekapLaporanController::class, 'unduhRekapPeserta'])->name('rekap.peserta.unduh');
    Route::get('rekap/{programKerja}/laporan', [RekapLaporanController::class, 'tampilkanFormLaporan'])->name('rekap.laporan');
    Route::post('rekap/{programKerja}/laporan', [RekapLaporanController::class, 'simpanLaporan'])->name('rekap.laporan.simpan');
    Route::get('laporan/{laporan}/unduh', [RekapLaporanController::class, 'unduhLaporanKegiatan'])->name('laporan.unduh');
});

