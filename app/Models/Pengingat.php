<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\UniqueConstraintViolationException;

class Pengingat extends Model
{
    protected $table = 'pengingat';

    protected $primaryKey = 'id_pengingat';

    protected $fillable = ['id_mahasiswa', 'id_proker', 'waktu_pengingat', 'status_pengingat', 'terkirim_pada'];

    protected function casts(): array
    {
        return [
            'waktu_pengingat' => 'datetime',
            'terkirim_pada' => 'datetime',
            'status_pengingat' => 'boolean',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'id_proker', 'id_proker');
    }

    public function simpanPengaturanPengingat(int $idProker, int $idMahasiswa, DateTimeInterface $waktuPengingat): bool
    {
        try {
            return $this->fill([
                'id_proker' => $idProker,
                'id_mahasiswa' => $idMahasiswa,
                'waktu_pengingat' => $waktuPengingat,
                'status_pengingat' => true,
                'terkirim_pada' => null, // pengaturan baru/diubah = belum terkirim
            ])->save();
        } catch (UniqueConstraintViolationException) {
            return false; // klik ganda bersamaan pada mahasiswa + proker yang sama
        }
    }

    public static function getPengingat(int $idPengingat): ?self
    {
        return static::find($idPengingat);
    }

    /** Pengiriman notifikasi dilakukan oleh scheduler `pengingat:kirim`. */
    public static function jadwalkanPengingat(int $idPengingat): bool
    {
        $pengingat = static::getPengingat($idPengingat);

        if (! $pengingat || $pengingat->waktu_pengingat->isPast()) {
            return false;
        }

        return $pengingat->update([
            'status_pengingat' => true,
            'terkirim_pada' => null,
        ]);
    }
}
