<?php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\HasilTryout;
use App\Models\DetailJawaban;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TryoutController extends Controller
{
    private function siswa()
    {
        $u = auth()->user();
        return $u->siswa ?? Siswa::where('user_id', $u->id)->firstOrFail();
    }

    // Daftar tryout aktif
    public function index()
    {
        $siswa = $this->siswa();
        $tryouts = Tryout::with(['mapel', 'guru.user'])
            ->where('status', 'aktif')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->latest()
            ->get()
            ->map(function ($t) use ($siswa) {
                $t->hasil = HasilTryout::where('tryout_id', $t->id)->where('siswa_id', $siswa->id)->first();
                return $t;
            });
        // also include tryout yg sudah dikerjakan biar tetap kelihatan
        $allAktif = Tryout::with('mapel')->where('status', 'aktif')->latest()->get()->map(function ($t) use ($siswa) {
            $t->hasil = HasilTryout::where('tryout_id', $t->id)->where('siswa_id', $siswa->id)->first();
            return $t;
        });

        return view('siswa.tryout.index', ['tryouts' => $allAktif]);
    }

    // Start / resume
    public function start(Tryout $tryout)
    {
        $siswa = $this->siswa();
        if ($tryout->status !== 'aktif') abort(403, 'Tryout tidak aktif');
        // cek sudah pernah submit?
        $hasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->first();
        if ($hasil && $hasil->waktu_submit) {
            return redirect()->route('siswa.tryout.hasil', $tryout);
        }
        // jika belum ada hasil, buat baru + generate detail random order
        if (!$hasil) {
            $hasil = DB::transaction(function () use ($tryout, $siswa) {
                $h = HasilTryout::create([
                    'tryout_id' => $tryout->id,
                    'siswa_id' => $siswa->id,
                    'nilai' => 0,
                    'jumlah_benar' => 0,
                    'jumlah_salah' => 0,
                    'waktu_pengerjaan_menit' => 0,
                    'started_at' => now(),
                ]);
                $soalIds = $tryout->soal()->pluck('soal.id')->shuffle();
                // fallback jika tryout_soal kosong pakai soal via tryout->soal()
                if ($soalIds->isEmpty()) {
                    $soalIds = $tryout->soal()->pluck('soal.id');
                }
                foreach ($soalIds as $sid) {
                    DetailJawaban::create([
                        'hasil_tryout_id' => $h->id,
                        'soal_id' => $sid,
                        'jawaban_siswa' => null,
                        'benar_salah' => false,
                        'ragu' => false,
                    ]);
                }
                return $h;
            });
        } elseif (!$hasil->started_at) {
            $hasil->update(['started_at' => now()]);
        }

        return redirect()->route('siswa.tryout.exam', $tryout);
    }

    // Halaman pengerjaan
    public function exam(Request $r, Tryout $tryout)
    {
        $siswa = $this->siswa();
        $hasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->firstOrFail();
        if ($hasil->waktu_submit) return redirect()->route('siswa.tryout.hasil', $tryout);

        // server time remaining (absolute diff, Carbon 3 signed by default)
        $started = $hasil->started_at ?? $hasil->created_at;
        $elapsed = $started ? (int) abs(now()->diffInSeconds($started, true)) : 0;
        $durasiSec = $tryout->durasi_menit * 60;
        $remaining = max(0, $durasiSec - $elapsed);
        if ($remaining <= 0) {
            return $this->doSubmit($tryout, $hasil);
        }

        $details = DetailJawaban::with('soal')->where('hasil_tryout_id', $hasil->id)->get();
        // order by id (random order per siswa)
        $currentIdx = (int) $r->query('q', 1); // 1-based
        $currentIdx = max(1, min($currentIdx, $details->count()));
        $current = $details[$currentIdx - 1] ?? null;

        return view('siswa.tryout.exam', compact('tryout', 'hasil', 'details', 'current', 'currentIdx', 'remaining'));
    }

    // autosave jawaban
    public function save(Request $r, Tryout $tryout)
    {
        $r->validate(['detail_id' => 'required|exists:detail_jawaban,id', 'jawaban' => 'nullable|in:a,b,c,d', 'ragu' => 'nullable|boolean']);
        $siswa = $this->siswa();
        $hasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->firstOrFail();
        if ($hasil->waktu_submit) return response()->json(['error' => 'sudah submit'], 403);

        // cek timeout server (absolute)
        $started = $hasil->started_at ?? $hasil->created_at;
        $elapsedTmp = $started ? (int) abs(now()->diffInSeconds($started, true)) : 0;
        $remaining = $tryout->durasi_menit * 60 - $elapsedTmp;
        if ($remaining <= 0) {
            $this->doSubmit($tryout, $hasil);
            return response()->json(['timeout' => true, 'redirect' => route('siswa.tryout.hasil', $tryout)]);
        }

        $detail = DetailJawaban::where('id', $r->detail_id)->where('hasil_tryout_id', $hasil->id)->firstOrFail();
        $data = [];
        if ($r->has('jawaban')) $data['jawaban_siswa'] = $r->jawaban ?: null;
        if ($r->has('ragu')) $data['ragu'] = (bool) $r->ragu;
        if (!empty($data)) $detail->update($data);

        return response()->json(['ok' => true]);
    }

    public function toggleRagu(Request $r, Tryout $tryout)
    {
        $r->validate(['detail_id' => 'required|exists:detail_jawaban,id']);
        $siswa = $this->siswa();
        $hasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->firstOrFail();
        if ($hasil->waktu_submit) return response()->json(['error' => 'sudah submit'], 403);
        $detail = DetailJawaban::where('id', $r->detail_id)->where('hasil_tryout_id', $hasil->id)->firstOrFail();
        $detail->update(['ragu' => !$detail->ragu]);
        return response()->json(['ragu' => $detail->ragu]);
    }

    // submit
    public function submit(Tryout $tryout)
    {
        $siswa = $this->siswa();
        $hasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->firstOrFail();
        if ($hasil->waktu_submit) return redirect()->route('siswa.tryout.hasil', $tryout);
        return $this->doSubmit($tryout, $hasil);
    }

    private function doSubmit(Tryout $tryout, HasilTryout $hasil)
    {
        $details = DetailJawaban::with('soal')->where('hasil_tryout_id', $hasil->id)->get();
        $benar = 0;
        $salah = 0;
        foreach ($details as $d) {
            $kunci = $d->soal->jawaban_benar ?? null;
            $isBenar = $d->jawaban_siswa && $kunci && strtolower($d->jawaban_siswa) === strtolower($kunci);
            $d->update(['benar_salah' => $isBenar]);
            if ($d->jawaban_siswa === null) {
                $salah++;
            } elseif ($isBenar) $benar++;
            else $salah++;
        }
        $total = $details->count() ?: 1;
        $nilai = round($benar / $total * 100, 2);
        $started = $hasil->started_at ?? $hasil->created_at;
        $elapsedSubmit = $started ? (int) abs(now()->diffInSeconds($started, true)) : 0;
        $menit = (int) ceil($elapsedSubmit / 60);
        $menit = max(1, min($menit, $tryout->durasi_menit));
        $hasil->update([
            'jumlah_benar' => $benar,
            'jumlah_salah' => $salah,
            'nilai' => $nilai,
            'waktu_pengerjaan_menit' => $menit,
            'waktu_submit' => now(),
        ]);
        if (request()->expectsJson()) {
            return response()->json(['redirect' => route('siswa.tryout.hasil', $tryout)]);
        }
        return redirect()->route('siswa.tryout.hasil', $tryout)->with('success', 'Tryout selesai. Nilai: ' . $nilai);
    }

    public function hasil(Tryout $tryout)
    {
        $siswa = $this->siswa();
        $hasil = HasilTryout::with(['detailJawaban.soal'])->where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->firstOrFail();
        if (!$hasil->waktu_submit) return redirect()->route('siswa.tryout.exam', $tryout);
        $tryout->load('mapel');
        $ranking = HasilTryout::with('siswa.user')->where('tryout_id', $tryout->id)->whereNotNull('waktu_submit')->orderByDesc('nilai')->orderBy('waktu_pengerjaan_menit')->limit(10)->get();
        $myRank = HasilTryout::where('tryout_id', $tryout->id)->whereNotNull('waktu_submit')->where('nilai', '>', $hasil->nilai)->count() + 1;
        // tie handling simple: count greater nilai +1
        return view('siswa.tryout.hasil', compact('tryout', 'hasil', 'ranking', 'myRank'));
    }

    public function ranking(Tryout $tryout)
    {
        $siswa = $this->siswa();
        $myHasil = HasilTryout::where('tryout_id', $tryout->id)->where('siswa_id', $siswa->id)->first();
        $ranking = HasilTryout::with('siswa.user')->where('tryout_id', $tryout->id)->whereNotNull('waktu_submit')->orderByDesc('nilai')->orderBy('waktu_pengerjaan_menit')->limit(20)->get();
        return view('siswa.tryout.ranking', compact('tryout', 'ranking', 'myHasil'));
    }
}
