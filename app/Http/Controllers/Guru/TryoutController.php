<?php
namespace App\Http\Controllers\Guru;
use App\Http\Controllers\Controller;
use App\Models\Tryout; use App\Models\Soal; use App\Models\MataPelajaran; use App\Models\Guru; use App\Models\HasilTryout; use App\Models\Notifikasi; use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request; use Illuminate\Support\Facades\DB;
class TryoutController extends Controller {
    private function guruId(){ $u=auth()->user(); return optional($u->guru)->id ?? Guru::where('user_id',$u->id)->value('id'); }
    public function index(Request $r){
        $gid=$this->guruId();
        $q=Tryout::with(['mapel','hasilTryout'])->where('guru_id',$gid);
        if($r->status) $q->where('status',$r->status);
        if($s=$r->search) $q->where('nama','like',"%$s%");
        $tryout=$q->latest()->paginate(10)->withQueryString();
        return view('guru.tryout.index', compact('tryout'));
    }
    public function create(){
        $mapel=MataPelajaran::all();
        $soal=Soal::where('guru_id',$this->guruId())->with('mapel')->get();
        return view('guru.tryout.form', ['tryout'=>new Tryout(),'mapel'=>$mapel,'soal'=>$soal]);
    }
    public function store(Request $r){
        $d=$r->validate([
            'nama'=>'required|string|max:150','mapel_id'=>'required|exists:mata_pelajaran,id',
            'durasi_menit'=>'required|integer|min:5|max:300','tanggal_mulai'=>'required|date','tanggal_selesai'=>'required|date|after:tanggal_mulai',
            'status'=>'required|in:draft,aktif,selesai','soal_ids'=>'nullable|array','soal_ids.*'=>'exists:soal,id','random'=>'nullable|boolean']);
        $gid=$this->guruId();
        $soalIds=$d['soal_ids'] ?? [];
        if(empty($soalIds) && $r->boolean('random')){
            $need=$r->input('jumlah_soal',5);
            $soalIds=Soal::where('guru_id',$gid)->where('mapel_id',$d['mapel_id'])->inRandomOrder()->limit($need)->pluck('id')->toArray();
        }
        if(empty($soalIds)) return back()->withInput()->with('error','Pilih soal atau centang random.');
        $tryout=DB::transaction(function() use($d,$soalIds,$gid){
            $t=Tryout::create(['nama'=>$d['nama'],'mapel_id'=>$d['mapel_id'],'guru_id'=>$gid,'jumlah_soal'=>count($soalIds),'durasi_menit'=>$d['durasi_menit'],'tanggal_mulai'=>$d['tanggal_mulai'],'tanggal_selesai'=>$d['tanggal_selesai'],'status'=>$d['status']]);
            foreach($soalIds as $i=>$sid) $t->soal()->attach($sid,['urutan'=>$i+1]);
            return $t;
        });
        // notifikasi ke siswa kalau aktif: jadwal tryout baru
        if($tryout->status==='aktif'){
            try{
                $t2=$tryout->load('mapel');
                $siswaUserIds=\App\Models\Siswa::pluck('user_id');
                foreach($siswaUserIds as $uid){
                    \App\Models\Notifikasi::create(['user_id'=>$uid,'tipe'=>'jadwal','judul'=>'Tryout baru: '.$t2->nama,'pesan'=>'Tryout '.$t2->nama.' ('.($t2->mapel->nama??'-').') tersedia. Durasi '.$t2->durasi_menit.' menit. Kerjakan sebelum '.Carbon::parse($t2->tanggal_selesai)->format('d M H:i').'.']);
                }
            }catch(\Throwable $e){ \Log::warning('notif siswa tryout baru gagal: '.$e->getMessage()); }
        }
        return redirect()->route('guru.tryout.index')->with('success','Tryout dibuat ('.count($soalIds).' soal).');
    }
    public function edit(Tryout $tryout){ $this->own($tryout); return view('guru.tryout.form', ['tryout'=>$tryout->load('soal'),'mapel'=>MataPelajaran::all(),'soal'=>Soal::where('guru_id',$this->guruId())->with('mapel')->get()]); }
    public function update(Request $r, Tryout $tryout){
        $this->own($tryout);
        $d=$r->validate([
            'nama'=>'required|string|max:150','mapel_id'=>'required|exists:mata_pelajaran,id',
            'durasi_menit'=>'required|integer|min:5|max:300','tanggal_mulai'=>'required|date','tanggal_selesai'=>'required|date|after:tanggal_mulai',
            'status'=>'required|in:draft,aktif,selesai','soal_ids'=>'nullable|array','soal_ids.*'=>'exists:soal,id']);
        DB::transaction(function() use($d,$tryout){
            $tryout->update(['nama'=>$d['nama'],'mapel_id'=>$d['mapel_id'],'durasi_menit'=>$d['durasi_menit'],'tanggal_mulai'=>$d['tanggal_mulai'],'tanggal_selesai'=>$d['tanggal_selesai'],'status'=>$d['status'],'jumlah_soal'=>isset($d['soal_ids'])?count($d['soal_ids']):$tryout->jumlah_soal]);
            if(isset($d['soal_ids'])){ $tryout->soal()->detach(); foreach($d['soal_ids'] as $i=>$sid) $tryout->soal()->attach($sid,['urutan'=>$i+1]); }
        });
        return redirect()->route('guru.tryout.index')->with('success','Tryout diupdate.');
    }
    public function destroy(Tryout $tryout){ $this->own($tryout); $tryout->delete(); return redirect()->route('guru.tryout.index')->with('success','Tryout dihapus.'); }
    public function show(Tryout $tryout){
        $this->own($tryout);
        $tryout->load(['mapel','soal','hasilTryout.siswa.user']);
        $hasil=$tryout->hasilTryout()->with('siswa.user')->latest()->get();
        $avg=$hasil->avg('nilai'); $max=$hasil->max('nilai'); $min=$hasil->min('nilai');
        return view('guru.tryout.show', compact('tryout','hasil','avg','max','min'));
    }
    private function own($t){ if($t->guru_id !== $this->guruId()) abort(403); }
}
