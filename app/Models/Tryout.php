<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tryout extends Model
{
    use HasFactory;
    protected $table = 'tryout';
    protected $fillable = ['nama','mapel_id','guru_id','jumlah_soal','durasi_menit','tanggal_mulai','tanggal_selesai','status'];
    protected $casts = ['tanggal_mulai'=>'datetime','tanggal_selesai'=>'datetime'];
    public function mapel() { return $this->belongsTo(MataPelajaran::class, 'mapel_id'); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function tryoutSoal() { return $this->hasMany(TryoutSoal::class); }
    public function soal() { return $this->belongsToMany(Soal::class, 'tryout_soal')->withPivot('urutan'); }
    public function hasilTryout() { return $this->hasMany(HasilTryout::class); }
}
