<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilAudit extends Model
{
    use HasFactory;

    protected $table = 'hasil_audit';

    protected $fillable = [
        'peta_id',
        'auditor_id',
        'komentar_1',
        'komentar_2',
        'komentar_3',
        'pengendalian',
        'mitigasi',
        'status_konfirmasi_auditee',
        'status_konfirmasi_auditor',
        'unit_kerja',
        'kode_risiko',
        'kegiatan',
        'level_risiko',
        'risiko_residual',
        'skor_total',
        'tahun_anggaran',
        'nama_pemonev',
        'nip_pemonev',
        'file_lampiran',
    ];

    protected $casts = [
        'skor_total' => 'integer',
    ];

    /**
     * Relasi ke tabel petas (risks)
     */
    public function peta()
    {
        return $this->belongsTo(Peta::class, 'peta_id');
    }

    /**
     * Relasi ke tabel users (auditor)
     */
    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /**
     * ✅ Accessor: Ambil label strategi mitigasi saja
     * Jika mitigasi tersimpan sebagai JSON {"strategi":..., "kepada":..., "kategori":...}
     * → kembalikan nilai "strategi"
     * Jika string biasa (Accept Risk) → kembalikan apa adanya
     */
    public function getMitigasiLabelAttribute(): string
    {
        if (!$this->mitigasi) return '-';

        if (str_starts_with(trim($this->mitigasi), '{')) {
            $decoded = json_decode($this->mitigasi, true);
            return $decoded['strategi'] ?? $this->mitigasi;
        }

        // Format lama: "Share Risk → Bagian Keuangan (Unit Kerja Lain)"
        // Ambil bagian sebelum " → " jika ada
        if (str_contains($this->mitigasi, ' → ')) {
            return trim(explode(' → ', $this->mitigasi)[0]);
        }

        return $this->mitigasi;
    }

    /**
     * ✅ Accessor: Ambil info "kepada siapa" dari mitigasi
     * Hanya ada nilainya jika strategi = Share Risk atau Transfer Risk
     * Mengembalikan null jika Accept Risk / tidak ada info kepada
     */
    public function getMitigasiKepadaAttribute(): ?string
    {
        if (!$this->mitigasi) return null;

        // Format baru: JSON {"strategi":..., "kepada":..., "kategori":...}
        if (str_starts_with(trim($this->mitigasi), '{')) {
            $decoded = json_decode($this->mitigasi, true);
            if (!isset($decoded['kepada']) || $decoded['kepada'] === '') return null;
            $kepada = $decoded['kepada'];
            $kategori = $decoded['kategori'] ?? '';
            return $kategori ? "{$kepada} ({$kategori})" : $kepada;
        }

        // Format lama: "Share Risk → Bagian Keuangan (Unit Kerja Lain)"
        if (str_contains($this->mitigasi, ' → ')) {
            $parts = explode(' → ', $this->mitigasi, 2);
            $strategi = trim($parts[0]);
            // Hanya tampilkan jika bukan Accept Risk
            if (in_array($strategi, ['Share Risk', 'Transfer Risk'])) {
                return trim($parts[1]);
            }
        }

        return null;
    }
}
