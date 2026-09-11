<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\HasilTryout;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AnalitikController extends Controller
{
    public function index(Request $r)
    {
        $guruId = optional(auth()->user()->guru)->id ?? \App\Models\Guru::where('user_id', auth()->id())->value('id');
        $tryouts = \App\Models\Tryout::where('guru_id', $guruId)->pluck('id');

        // Rata-rata per kelas
        $perKelas = Kelas::withCount('siswa')->get()->map(function ($k) use ($tryouts) {
            $avg = HasilTryout::whereIn('tryout_id', $tryouts)
                ->whereIn('siswa_id', $k->siswa->pluck('id'))
                ->whereNotNull('waktu_submit')
                ->avg('nilai');
            return ['kelas' => $k->nama, 'avg' => $avg ? round($avg, 1) : 0, 'jumlah' => $k->siswa_count];
        });

        // Distribusi nilai (all hasil guru)
        $allNilai = HasilTryout::whereIn('tryout_id', $tryouts)->whereNotNull('waktu_submit')->pluck('nilai');
        $dist = [
            '0-50' => $allNilai->filter(fn($v) => $v < 50)->count(),
            '50-70' => $allNilai->filter(fn($v) => $v >= 50 && $v < 70)->count(),
            '70-85' => $allNilai->filter(fn($v) => $v >= 70 && $v < 85)->count(),
            '85-100' => $allNilai->filter(fn($v) => $v >= 85)->count(),
        ];

        // Siswa perlu evaluasi: avg < 70 atau nilai terakhir < 70
        $perlu = collect();
        $siswaList = Siswa::with(['user', 'kelas'])->get();
        foreach ($siswaList as $s) {
            $hasil = HasilTryout::where('siswa_id', $s->id)->whereIn('tryout_id', $tryouts)->whereNotNull('waktu_submit')->get();
            if ($hasil->isEmpty()) continue;
            $avg = $hasil->avg('nilai');
            $last = $hasil->sortByDesc('waktu_submit')->first();
            if ($avg < 70 || ($last && $last->nilai < 70)) {
                $perlu->push(['siswa' => $s, 'avg' => round($avg, 1), 'last' => $last->nilai, 'kelas' => $s->kelas->nama ?? '-']);
            }
        }
        $perlu = $perlu->sortBy('avg')->values();

        // Chart data
        $chartKelasLabels = $perKelas->pluck('kelas')->toArray();
        $chartKelasValues = $perKelas->pluck('avg')->toArray();
        $distLabels = array_keys($dist);
        $distValues = array_values($dist);

        return view('guru.analitik.index', compact('perKelas', 'dist', 'perlu', 'chartKelasLabels', 'chartKelasValues', 'distLabels', 'distValues'));
    }
}
