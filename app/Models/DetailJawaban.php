<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailJawaban extends Model
{
    use HasFactory;
    protected $table = 'detail_jawaban';
    protected $fillable = ['hasil_tryout_id','soal_id','jawaban_siswa','benar_salah'];
    protected $casts = ['benar_salah'=>'boolean'];
    public function hasilTryout() { return $this->belongsTo(HasilTryout::class); }
    public function soal() { return $this->belongsTo(Soal::class); }
}
