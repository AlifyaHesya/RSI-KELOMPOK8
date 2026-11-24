<?php

namespace App\Models;

use App\Enums\KategoriProker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class ProgramKerja extends Model
{
    protected $table = 'program_kerja';

    protected $primaryKey = 'id_proker';

    protected $fillable = [
        'id_pic', 'nama_proker', 'deskripsi', 'kuota', 'lokasi', 'biro', 'kategori',
        'tanggal_pelaksanaan', 'waktu_pelaksanaan', 'tanggal_buka_pendaftaran',
        'batas_pendaftaran', 'status_buka', 'dipublikasi', 'poster_path',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => KategoriProker::class,
            'tanggal_pelaksanaan' => 'date',
            'tanggal_buka_pendaftaran' => 'datetime',
            'batas_pendaftaran' => 'datetime',
            'status_buka' => 'boolean',
            'dipublikasi' => 'boolean',
        ];
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Pic::class, 'id_pic', 'id_pic');
    }

    public function pengajuan(): HasOne
    {
        return $this->hasOne(Pengajuan::class, 'id_proker', 'id_proker');
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'id_proker', 'id_proker');
    }

    public function pengingat(): HasMany
    {
        return $this->hasMany(Pengingat::class, 'id_proker', 'id_proker');
    }

    public function laporan(): HasOne
    {
        return $this->hasOne(LaporanKegiatan::class, 'id_proker', 'id_proker');
    }

    public function scopeDipublikasi(Builder $query): Builder
    {
        return $query->where('dipublikasi', true);
    }

    public function waktuMulai(): Carbon
    {
        return $this->tanggal_pelaksanaan->copy()->setTimeFromTimeString($this->waktu_pelaksanaan);
    }

    /** Status pendaftaran efektif berdasarkan publikasi, izin PIC, dan jendela waktu. */
    public function cekStatusPendaftaran(?int $idProker = null): bool
    {
        if ($idProker !== null && (int) $this->getKey() !== $idProker) {
            return false;
        }

        return $this->dipublikasi
            && $this->status_buka
            && now()->between($this->tanggal_buka_pendaftaran, $this->batas_pendaftaran);
    }

    /** Label status yang bisa ditampilkan mahasiswa, dihitung dari waktu WIB dan kuota saat ini. */
    public function labelStatusPendaftaran(): string
    {
        if (now()->lt($this->tanggal_buka_pendaftaran)) {
            return 'Dibuka '.$this->tanggal_buka_pendaftaran->translatedFormat('d M Y, H:i');
        }

        if (now()->gt($this->batas_pendaftaran)) {
            return 'Pendaftaran ditutup';
        }

        if (! $this->status_buka) {
            return 'Pendaftaran ditutup oleh PIC';
        }

        return $this->cekKuotaTersedia() ? 'Pendaftaran dibuka' : 'Kuota penuh';
    }

    public function cekKuotaTersedia(?int $idProker = null): bool
    {
        if ($idProker !== null && (int) $this->getKey() !== $idProker) {
            return false;
        }

        $jumlahTerdaftar = array_key_exists('pendaftaran_count', $this->attributes)
            ? (int) $this->attributes['pendaftaran_count']
            : $this->pendaftaran()->where('status_pendaftaran', true)->count();

        return $jumlahTerdaftar < $this->kuota;
    }

    public function ambilBatasPendaftaran(int $idProker): ?Carbon
    {
        if ((int) $this->getKey() !== $idProker) {
            return null;
        }

        return $this->batas_pendaftaran;
    }
}


