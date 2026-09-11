<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Guru; use App\Models\User;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rule;
class GuruController extends Controller {
    public function index(Request $r){
        $q=Guru::with('user');
        if($s=$r->search) $q->whereHas('user',fn($x)=>$x->where('name','like',"%$s%")->orWhere('email','like',"%$s%"))->orWhere('nip','like',"%$s%");
        $guru=$q->latest()->paginate(10)->withQueryString();
        return view('admin.guru.index', compact('guru'));
    }
    public function create(){ return view('admin.guru.form'); }
    public function store(Request $r){
        $d=$r->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email','password'=>'required|min:8','nip'=>'nullable|string|max:30|unique:guru,nip']);
        DB::transaction(function() use($d){
            $u=User::create(['name'=>$d['name'],'email'=>$d['email'],'password'=>Hash::make($d['password']),'role'=>'guru','email_verified_at'=>now()]);
            Guru::create(['user_id'=>$u->id,'nip'=>$d['nip']??null]);
        });
        return redirect()->route('admin.guru.index')->with('success','Guru berhasil ditambahkan.');
    }
    public function edit(Guru $guru){ $guru->load('user'); return view('admin.guru.form', compact('guru')); }
    public function update(Request $r, Guru $guru){
        $guru->load('user');
        $d=$r->validate(['name'=>'required|string|max:100','email'=>['required','email',Rule::unique('users','email')->ignore($guru->user_id)],'password'=>'nullable|min:8','nip'=>['nullable','string','max:30',Rule::unique('guru','nip')->ignore($guru->id)]]);
        DB::transaction(function() use($d,$guru){
            $ud=['name'=>$d['name'],'email'=>$d['email']];
            if(!empty($d['password'])) $ud['password']=Hash::make($d['password']);
            $guru->user->update($ud);
            $guru->update(['nip'=>$d['nip']??null]);
        });
        return redirect()->route('admin.guru.index')->with('success','Guru diupdate.');
    }
    public function destroy(Guru $guru){
        $guru->user()->delete(); // cascade guru via FK
        return redirect()->route('admin.guru.index')->with('success','Guru dihapus.');
    }
}
