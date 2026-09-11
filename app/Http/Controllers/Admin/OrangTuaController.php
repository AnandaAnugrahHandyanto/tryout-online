<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\OrangTua; use App\Models\User;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rule;
class OrangTuaController extends Controller {
    public function index(Request $r){
        $q=OrangTua::with(['user','siswa']);
        if($s=$r->search) $q->whereHas('user',fn($x)=>$x->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        $ortu=$q->latest()->paginate(10)->withQueryString();
        return view('admin.ortu.index', compact('ortu'));
    }
    public function create(){ return view('admin.ortu.form'); }
    public function store(Request $r){
        $d=$r->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','password'=>'required|min:8','pekerjaan'=>'nullable|string|max:100','no_hp'=>'nullable|string|max:20']);
        DB::transaction(function() use($d){
            $u=User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>Hash::make($d['password']),'role'=>'orang_tua','email_verified_at'=>now()]);
            OrangTua::create(['user_id'=>$u->id,'pekerjaan'=>$d['pekerjaan']??null,'no_hp'=>$d['no_hp']??null]);
        });
        return redirect()->route('admin.ortu.index')->with('success','Orang tua ditambahkan.');
    }
    public function edit(OrangTua $ortu){ $ortu->load('user'); return view('admin.ortu.form', compact('ortu')); }
    public function update(Request $r, OrangTua $ortu){
        $ortu->load('user');
        $d=$r->validate(['name'=>'required|string|max:100','email'=>['required','email',Rule::unique('users','email')->ignore($ortu->user_id)],'password'=>'nullable|min:8','pekerjaan'=>'nullable|string|max:100','no_hp'=>'nullable|string|max:20']);
        DB::transaction(function() use($d,$ortu){
            $ud=['name'=>$d['name'],'email'=>$d['email']];
            if(!empty($d['password'])) $ud['password']=Hash::make($d['password']);
            $ortu->user->update($ud);
            $ortu->update(['pekerjaan'=>$d['pekerjaan']??null,'no_hp'=>$d['no_hp']??null]);
        });
        return redirect()->route('admin.ortu.index')->with('success','Orang tua diupdate.');
    }
    public function destroy(OrangTua $ortu){
        if($ortu->siswa()->exists()) return back()->with('error','Orang tua masih terhubung ke siswa.');
        $ortu->user()->delete();
        return redirect()->route('admin.ortu.index')->with('success','Orang tua dihapus.');
    }
}
