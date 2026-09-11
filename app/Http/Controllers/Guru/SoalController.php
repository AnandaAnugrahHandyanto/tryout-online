<?php
namespace App\Http\Controllers\Guru;
use App\Http\Controllers\Controller;
use App\Models\Soal; use App\Models\MataPelajaran; use App\Models\Guru;
use Illuminate\Http\Request;
class SoalController extends Controller {
    private function guruId(){ $u=auth()->user(); return optional($u->guru)->id ?? Guru::where('user_id',$u->id)->value('id'); }
    public function index(Request $r){
        $gid=$this->guruId();
        $q=Soal::with('mapel')->where('guru_id',$gid);
        if($s=$r->search) $q->where('pertanyaan','like',"%$s%");
        if($r->mapel_id) $q->where('mapel_id',$r->mapel_id);
        if($r->tingkat) $q->where('tingkat_kesulitan',$r->tingkat);
        $soal=$q->latest()->paginate(10)->withQueryString();
        $mapel=MataPelajaran::all();
        return view('guru.soal.index', compact('soal','mapel'));
    }
    public function create(){ return view('guru.soal.form', ['soal'=>new Soal(),'mapel'=>MataPelajaran::all()]); }
    public function store(Request $r){
        $d=$r->validate([
            'mapel_id'=>'required|exists:mata_pelajaran,id',
            'pertanyaan'=>'required|string',
            'pilihan_a'=>'required|string|max:500','pilihan_b'=>'required|string|max:500','pilihan_c'=>'required|string|max:500','pilihan_d'=>'required|string|max:500',
            'jawaban_benar'=>'required|in:a,b,c,d','tingkat_kesulitan'=>'required|in:mudah,sedang,sulit']);
        $d['guru_id']=$this->guruId();
        Soal::create($d);
        return redirect()->route('guru.soal.index')->with('success','Soal ditambahkan.');
    }
    public function edit(Soal $soal){ $this->authorizeOwner($soal); return view('guru.soal.form', ['soal'=>$soal,'mapel'=>MataPelajaran::all()]); }
    public function update(Request $r, Soal $soal){
        $this->authorizeOwner($soal);
        $d=$r->validate([
            'mapel_id'=>'required|exists:mata_pelajaran,id','pertanyaan'=>'required|string',
            'pilihan_a'=>'required|string|max:500','pilihan_b'=>'required|string|max:500','pilihan_c'=>'required|string|max:500','pilihan_d'=>'required|string|max:500',
            'jawaban_benar'=>'required|in:a,b,c,d','tingkat_kesulitan'=>'required|in:mudah,sedang,sulit']);
        $soal->update($d);
        return redirect()->route('guru.soal.index')->with('success','Soal diupdate.');
    }
    public function destroy(Soal $soal){
        $this->authorizeOwner($soal);
        if($soal->tryoutSoal()->exists()) return back()->with('error','Soal masih dipakai tryout.');
        $soal->delete();
        return redirect()->route('guru.soal.index')->with('success','Soal dihapus.');
    }
    private function authorizeOwner($soal){ if($soal->guru_id !== $this->guruId()) abort(403); }
}
