<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Soal extends Model
{
    use HasFactory;
    protected $table = 'soal';
    protected $fillable = ['mapel_id','guru_id','pertanyaan','pilihan_a','pilihan_b','pilihan_c','pilihan_d','jawaban_benar','tingkat_kesulitan'];
    public function mapel() { return $this->belongsTo(MataPelajaran::class, 'mapel_id'); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function tryoutSoal() { return $this->hasMany(TryoutSoal::class); }
    public function tryouts() { return $this->belongsToMany(Tryout::class, 'tryout_soal'); }
    public function detailJawaban() { return $this->hasMany(DetailJawaban::class); }
}
