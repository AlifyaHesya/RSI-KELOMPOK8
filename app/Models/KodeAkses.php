<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;

class KodeAkses extends Model
{
    protected $table = 'kode_akses';

    protected $fillable = ['role', 'kode', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    public static function cekKodeAkses(string $kodeAkses, Role $role): bool
    {
        return static::query()
            ->where('role', $role->value)
            ->where('kode', $kodeAkses)
            ->where('aktif', true)
            ->exists();
    }
}
