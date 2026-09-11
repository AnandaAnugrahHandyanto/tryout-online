<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use App\Models\HasilTryout;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    private function ortu()
    {
        $u = auth()->user();
        return OrangTua::with('siswa.user', 'siswa.kelas')->where('user_id', $u->id)->firstOrFail();
    }

    private function selectedSiswa(Request $r, $ortu)
    {
        $list = $ortu->siswa;
        if ($list->isEmpty()) return null;
        if ($r->siswa_id) {
            $found = $list->firstWhere('id', (int) $r->siswa_id);
            if ($found) return $found;
        }
        return $list->first();
    }

    private function buildData($siswa)
    {
        if (!$siswa) return null;

        $hasilAll = HasilTryout::with('tryout.mapel')
            ->where('siswa_id', $siswa->id)->whereNotNull('waktu_submit')
            ->orderByDesc('waktu_submit')->get();

        $last = $hasilAll->first();
        $prev = $hasilAll->skip(1)->first();

        // KPI
        $nilaiTerakhir = $last?->nilai;
        $jumlahTryout = $hasilAll->count();
        $delta = null;
        if ($last && $prev) $delta = round($last->nilai - $prev->nilai, 1);

        $ranking = null;
        $rankingTotal = null;
        if ($last) {
            $ranking = HasilTryout::where('tryout_id', $last->tryout_id)->whereNotNull('waktu_submit')->where('nilai', '>', $last->nilai)->count() + 1;
            $rankingTotal = HasilTryout::where('tryout_id', $last->tryout_id)->whereNotNull('waktu_submit')->count();
        }

        // progress 30 hari: (avg last 30d vs previous)
        $progressPct = $delta !== null ? ($delta >= 0 ? '+' . $delta : (string) $delta) : null;

        // Chart 5 terakhir (reverse biar kiri lama -> kanan baru)
        $chartSlice = $hasilAll->take(5)->reverse()->values();
        $chartLabels = $chartSlice->map(fn($h) => $h->tryout->nama ?? 'Tryout #' . $h->tryout_id)->toArray();
        $chartValues = $chartSlice->map(fn($h) => (float) $h->nilai)->toArray();
        // fallback demo if <2 data -> keep as is

        // Progress per mapel (avg per mapel)
        $perMapel = $hasilAll->groupBy(fn($h) => $h->tryout->mapel->nama ?? 'Umum')
            ->map(function ($group, $nama) {
                $avg = round($group->avg('nilai'), 1);
                $color = $avg >= 85 ? 'green' : ($avg >= 70 ? 'yellow' : 'red');
                return ['nama' => $nama, 'avg' => $avg, 'count' => $group->count(), 'color' => $color];
            })->values();

        // Perlu perhatian: mapel avg <70 atau trend turun (last 2 point turun)
        $perlu = [];
        foreach ($perMapel as $pm) {
            if ($pm['avg'] < 70) $perlu[] = $pm['nama'] . ' (' . $pm['avg'] . ') perlu latihan';
        }
        // trend check per mapel: last 2 of that mapel
        foreach ($perMapel as $pm) {
            $ofMapel = $hasilAll->filter(fn($h) => ($h->tryout->mapel->nama ?? 'Umum') === $pm['nama'])->take(2)->values();
            if ($ofMapel->count() === 2 && $ofMapel[0]->nilai < $ofMapel[1]->nilai - 3) {
                $perlu[] = $pm['nama'] . ' turun ' . round($ofMapel[1]->nilai - $ofMapel[0]->nilai, 1) . ' poin';
            }
        }
        $perlu = array_values(array_unique($perlu));

        return compact('hasilAll', 'last', 'prev', 'nilaiTerakhir', 'jumlahTryout', 'delta', 'ranking', 'rankingTotal', 'progressPct', 'chartLabels', 'chartValues', 'perMapel', 'perlu');
    }

    public function dashboard(Request $r)
    {
        $ortu = $this->ortu();
        $siswa = $this->selectedSiswa($r, $ortu);
        $data = $this->buildData($siswa);
        return view('orang-tua.dashboard', array_merge(compact('ortu', 'siswa'), $data ?? []));
    }

    public function riwayat(Request $r)
    {
        $ortu = $this->ortu();
        $siswa = $this->selectedSiswa($r, $ortu);
        $data = $this->buildData($siswa);
        // enrich riwayat with ranking per row
        $riwayat = collect();
        if ($siswa && $data) {
            foreach ($data['hasilAll'] as $h) {
                $rank = HasilTryout::where('tryout_id', $h->tryout_id)->whereNotNull('waktu_submit')->where('nilai', '>', $h->nilai)->count() + 1;
                $total = HasilTryout::where('tryout_id', $h->tryout_id)->whereNotNull('waktu_submit')->count();
                $h->rank = $rank;
                $h->rankTotal = $total;
                $riwayat->push($h);
            }
        }
        return view('orang-tua.riwayat', compact('ortu', 'siswa', 'riwayat') + ($data ?? []));
    }

    public function analisis(Request $r)
    {
        $ortu = $this->ortu();
        $siswa = $this->selectedSiswa($r, $ortu);
        $data = $this->buildData($siswa);

        $analisis = collect();
        if ($siswa && $data) {
            foreach ($data['perMapel'] as $pm) {
                $status = $pm['avg'] >= 80 ? 'Tinggi' : ($pm['avg'] >= 65 ? 'Sedang' : 'Perlu Perhatian');
                $rekom = $pm['avg'] >= 80 ? 'Pertahankan, coba soal sulit' : ($pm['avg'] >= 65 ? 'Latihan rutin 30 menit/hari' : 'Fokus remedial + bimbingan guru');
                $analisis->push(array_merge($pm, compact('status', 'rekom')));
            }
        }
        return view('orang-tua.analisis', compact('ortu', 'siswa', 'analisis') + ($data ?? []));
    }

    public function ranking(Request $r)
    {
        $ortu = $this->ortu();
        $siswa = $this->selectedSiswa($r, $ortu);
        $data = $this->buildData($siswa);

        $tryoutId = $r->tryout_id ?? $data['last']->tryout_id ?? null;
        $ranking = collect();
        $myHasil = null;
        if ($tryoutId) {
            $ranking = HasilTryout::with('siswa.user')
                ->where('tryout_id', $tryoutId)->whereNotNull('waktu_submit')
                ->orderByDesc('nilai')->orderBy('waktu_pengerjaan_menit')
                ->limit(20)->get();
            $myHasil = $siswa ? HasilTryout::where('tryout_id', $tryoutId)->where('siswa_id', $siswa->id)->first() : null;
        }
        // list tryout for filter
        $tryoutList = $siswa ? HasilTryout::with('tryout')->where('siswa_id', $siswa->id)->whereNotNull('waktu_submit')->get()->pluck('tryout')->unique('id') : collect();

        return view('orang-tua.ranking', compact('ortu', 'siswa', 'ranking', 'myHasil', 'tryoutId', 'tryoutList') + ($data ?? []));
    }
}
