<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Siswa; use App\Models\User; use App\Models\Kelas; use App\Models\OrangTua;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rule;
class SiswaController extends Controller {
    public function index(Request $r){
        $q=Siswa::with(['user','kelas','orangTua.user']);
        if($s=$r->search) $q->whereHas('user',fn($x)=>$x->where('name','like',"%$s%")->orWhere('email','like',"%$s%"))->orWhere('nis','like',"%$s%");
        if($r->kelas_id) $q->where('kelas_id',$r->kelas_id);
        $siswa=$q->latest()->paginate(10)->withQueryString();
        $kelas=Kelas::all();
        return view('admin.siswa.index', compact('siswa','kelas'));
    }
    public function create(){ return view('admin.siswa.form', ['kelas'=>Kelas::all(),'ortu'=>OrangTua::with('user')->get()]); }
    public function store(Request $r){
        $d=$r->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','password'=>'required|min:8','nis'=>'required|string|max:30|unique:siswa,nis','kelas_id'=>'required|exists:kelas,id','orang_tua_id'=>'nullable|exists:orang_tua,id']);
        DB::transaction(function() use($d){
            $u=User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>Hash::make($d['password']),'role'=>'siswa','email_verified_at'=>now()]);
            Siswa::create(['user_id'=>$u->id,'nis'=>$d['nis'],'kelas_id'=>$d['kelas_id'],'orang_tua_id'=>$d['orang_tua_id']??null]);
        });
        return redirect()->route('admin.siswa.index')->with('success','Siswa ditambahkan.');
    }
    public function edit(Siswa $siswa){ $siswa->load('user'); return view('admin.siswa.form', ['siswa'=>$siswa,'kelas'=>Kelas::all(),'ortu'=>OrangTua::with('user')->get()]); }
    public function update(Request $r, Siswa $siswa){
        $siswa->load('user');
        $d=$r->validate(['name'=>'required|string|max:100','email'=>['required','email',Rule::unique('users','email')->ignore($siswa->user_id)],'password'=>'nullable|min:8','nis'=>['required','string','max:30',Rule::unique('siswa','nis')->ignore($siswa->id)],'kelas_id'=>'required|exists:kelas,id','orang_tua_id'=>'nullable|exists:orang_tua,id']);
        DB::transaction(function() use($d,$siswa){
            $ud=['name'=>$d['name'],'email'=>$d['email']];
            if(!empty($d['password'])) $ud['password']=Hash::make($d['password']);
            $siswa->user->update($ud);
            $siswa->update(['nis'=>$d['nis'],'kelas_id'=>$d['kelas_id'],'orang_tua_id'=>$d['orang_tua_id']??null]);
        });
        return redirect()->route('admin.siswa.index')->with('success','Siswa diupdate.');
    }
    public function destroy(Siswa $siswa){
        $siswa->user()->delete();
        return redirect()->route('admin.siswa.index')->with('success','Siswa dihapus.');
    }
}
