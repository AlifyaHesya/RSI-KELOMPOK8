<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\KodeAkses;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

/** UC-01 Registrasi & Login, UC-02 Logout. */
class AuthController extends Controller
{
    public function tampilkanLogin()
    {
        return view('auth.login');
    }

    public function masuk(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['login' => ['required', 'string'], 'password' => ['required']],
            ['required' => ':attribute wajib diisi'],
            ['login' => 'Username / email', 'password' => 'Kata sandi']
        );

        $kolom = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([$kolom => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Username/email atau kata sandi salah'])->onlyInput('login');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function pilihPeran()
    {
        return view('auth.pilih-role', ['roles' => Role::cases()]);
    }

    public function validasiKodeAkses(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:MHS,PIC'],
            'kode_akses' => ['required_unless:role,MHS', 'nullable', 'string'],
        ], ['kode_akses.required_unless' => 'Kode akses wajib diisi untuk PIC']);

        if ($data['role'] === Role::Pic->value) {
            if (! KodeAkses::cekKodeAkses($data['kode_akses'], Role::Pic)) {
                return back()->withErrors(['kode_akses' => 'Kode akses tidak valid'])->withInput();
            }

            $request->session()->put('kode_akses_valid', $data['role']);
        }

        return redirect()->route('register.form', $data['role']);
    }

    public function tampilkanFormRegistrasi(Request $request, string $role)
    {
        $roleEnum = Role::tryFrom($role) ?? abort(404);

        if (! $this->bolehMendaftar($request, $roleEnum)) {
            return redirect()->route('register')
                ->withErrors(['kode_akses' => 'Masukkan kode akses terlebih dahulu']);
        }

        return view('auth.register', ['role' => $roleEnum]);
    }

    public function registrasi(Request $request, string $role): RedirectResponse
    {
        $roleEnum = Role::tryFrom($role) ?? abort(404);

        if (! $this->bolehMendaftar($request, $roleEnum)) {
            return redirect()->route('register')
                ->withErrors(['kode_akses' => 'Masukkan kode akses terlebih dahulu']);
        }

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'password_confirmation' => ['required', 'same:password'],
        ];

        if ($roleEnum === Role::Mahasiswa) {
            $rules['nim'] = ['required', 'string', 'max:20', 'unique:mahasiswa,nim'];
            $rules['program_studi'] = ['required', 'string', 'max:100'];
        } elseif ($roleEnum === Role::Pic) {
            $rules['biro'] = ['required', 'string', 'max:100'];
        }

        $data = $request->validate($rules, [
            'required' => ':attribute wajib diisi',
            'email.unique' => 'Email sudah digunakan',
            'username.unique' => 'Username sudah digunakan',
            'nim.unique' => 'NIM sudah terdaftar',
            'password_confirmation.same' => 'Kata sandi tidak cocok',
        ], [
            'name' => 'Nama lengkap', 'nim' => 'NIM', 'program_studi' => 'Program studi', 'biro' => 'Biro/departemen', 'email' => 'Email',
            'username' => 'Username', 'password' => 'Kata sandi', 'password_confirmation' => 'Konfirmasi kata sandi',
        ]);

        DB::transaction(function () use ($data, $roleEnum) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $data['password'], // di-hash otomatis oleh cast 'hashed'
                'role' => $roleEnum,
            ]);

            if ($roleEnum === Role::Mahasiswa) {
                $user->mahasiswa()->create([
                    'nim' => $data['nim'],
                    'program_studi' => $data['program_studi'],
                ]);
            } elseif ($roleEnum === Role::Pic) {
                $user->pic()->create(['biro' => $data['biro']]);
            }
        });

        $request->session()->forget('kode_akses_valid');

        return redirect()->route('login')->with('status', 'Registrasi berhasil');
    }

    public function keluar(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function bolehMendaftar(Request $request, Role $role): bool
    {
        return $role === Role::Mahasiswa
            || $request->session()->get('kode_akses_valid') === $role->value;
    }
}
