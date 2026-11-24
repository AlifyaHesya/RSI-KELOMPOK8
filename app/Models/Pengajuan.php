<?php

namespace App\Models;

use App\Enums\StatusPengajuan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Pengajuan program kerja dan kelengkapan berkas PIC. */
class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    protected $primaryKey = 'id_pengajuan';

    public const BERKAS_WAJIB = [
        'berkas_proposal' => 'Proposal kegiatan',
        'berkas_rab' => 'Rincian anggaran (RAB)',
        'berkas_surat_pengantar' => 'Surat pengantar',
    ];

    protected $fillable = [
        'id_pic', 'id_proker', 'nama_proker', 'berkas_proposal', 'berkas_rab',
        'berkas_surat_pengantar', 'link_bukti', 'berkas_lengkap', 'status_pengajuan', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'berkas_lengkap' => 'boolean',
            'status_pengajuan' => StatusPengajuan::class,
        ];
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(Pic::class, 'id_pic', 'id_pic');
    }

    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'id_proker', 'id_proker');
    }

    public function dokumenKurang(): array
    {
        return collect(self::BERKAS_WAJIB)
            ->filter(fn (string $label, string $kolom) => blank($this->{$kolom}))
            ->values()
            ->all();
    }

    public function cekBerkasLengkap(): bool
    {
        return $this->dokumenKurang() === [];
    }

    public function ubahStatus(StatusPengajuan $status, ?string $catatan = null): bool
    {
        $this->status_pengajuan = $status;
        $this->catatan = $catatan;

        return $this->save();
    }

    public function bisaDiubahPic(): bool
    {
        return in_array($this->status_pengajuan, [StatusPengajuan::Draft, StatusPengajuan::Ditolak], true);
    }
}
