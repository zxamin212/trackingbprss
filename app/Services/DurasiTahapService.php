<?php

namespace App\Services;

use App\Models\BerkasKredit;
use App\Models\BerkasKreditHistory;

class DurasiTahapService
{
    public const TAHAP = ['diajukan', 'screening_data', 'slik', 'survey', 'komite', 'realisasi'];

    /**
     * Rata-rata durasi (hari) per tahap.
     * $kantorIds null = semua kantor; isi array untuk membatasi (mis. wilayah Area Manager).
     */
    public static function rataRata(?iterable $kantorIds = null): array
    {
        $query = BerkasKreditHistory::query()
            ->orderBy('berkas_kredit_id')
            ->orderBy('id');

        if ($kantorIds !== null) {
            $query->whereIn(
                'berkas_kredit_id',
                BerkasKredit::whereIn('kantor_id', $kantorIds)->select('id')
            );
        }

        $histories = $query->get(['berkas_kredit_id', 'status', 'created_at']);

        $akumulasi = [];

        foreach ($histories->groupBy('berkas_kredit_id') as $rows) {
            $rows = $rows->values();

            for ($i = 0; $i < $rows->count() - 1; $i++) {
                $status = $rows[$i]->status;
                $jam = $rows[$i]->created_at->diffInHours($rows[$i + 1]->created_at);

                $akumulasi[$status]['total_jam'] = ($akumulasi[$status]['total_jam'] ?? 0) + $jam;
                $akumulasi[$status]['count'] = ($akumulasi[$status]['count'] ?? 0) + 1;
            }
        }

        $hasil = [];
        foreach (self::TAHAP as $status) {
            $data = $akumulasi[$status] ?? null;
            $hasil[$status] = $data && $data['count'] > 0
                ? round($data['total_jam'] / $data['count'] / 24, 1)
                : null;
        }

        return $hasil;
    }
}