<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
class MataPelajaranController extends Controller {
    public function index(Request $r){
        $q=MataPelajaran::query();
        if($s=$r->search) $q->where('nama','like',"%$s%")->orWhere('kode','like',"%$s%");
        $mapel=$q->latest()->paginate(10)->withQueryString();
        return view('admin.mapel.index', compact('mapel'));
    }
    public function create(){ return view('admin.mapel.form', ['mapel'=>new MataPelajaran()]); }
    public function store(Request $r){
        $d=$r->validate(['nama'=>'required|string|max:100','kode'=>'required|string|max:20|unique:mata_pelajaran,kode']);
        MataPelajaran::create($d);
        return redirect()->route('admin.mapel.index')->with('success','Mata pelajaran ditambahkan.');
    }
    public function edit(MataPelajaran $mapel){ return view('admin.mapel.form', compact('mapel')); }
    public function update(Request $r, MataPelajaran $mapel){
        $d=$r->validate(['nama'=>'required|string|max:100','kode'=>'required|string|max:20|unique:mata_pelajaran,kode,'.$mapel->id]);
        $mapel->update($d);
        return redirect()->route('admin.mapel.index')->with('success','Mata pelajaran diupdate.');
    }
    public function destroy(MataPelajaran $mapel){
        if($mapel->soal()->exists()||$mapel->tryout()->exists()) return back()->with('error','Mapel masih dipakai soal/tryout.');
        $mapel->delete();
        return redirect()->route('admin.mapel.index')->with('success','Mapel dihapus.');
    }
}
