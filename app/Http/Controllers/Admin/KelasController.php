<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
class KelasController extends Controller {
    public function index(Request $r){
        $q = Kelas::query();
        if($s=$r->search) $q->where('nama','like',"%$s%")->orWhere('tingkat','like',"%$s%");
        $kelas = $q->latest()->paginate(10)->withQueryString();
        return view('admin.kelas.index', compact('kelas'));
    }
    public function create(){ return view('admin.kelas.form', ['kelas'=>new Kelas()]); }
    public function store(Request $r){
        $d=$r->validate(['nama'=>'required|string|max:100','tingkat'=>'required|string|max:50']);
        Kelas::create($d);
        return redirect()->route('admin.kelas.index')->with('success','Kelas berhasil ditambahkan.');
    }
    public function edit(Kelas $kela){ return view('admin.kelas.form', ['kelas'=>$kela]); }
    public function update(Request $r, Kelas $kela){
        $d=$r->validate(['nama'=>'required|string|max:100','tingkat'=>'required|string|max:50']);
        $kela->update($d);
        return redirect()->route('admin.kelas.index')->with('success','Kelas berhasil diupdate.');
    }
    public function destroy(Kelas $kela){
        if($kela->siswa()->exists()) return back()->with('error','Kelas masih dipakai siswa.');
        $kela->delete();
        return redirect()->route('admin.kelas.index')->with('success','Kelas dihapus.');
    }
}
