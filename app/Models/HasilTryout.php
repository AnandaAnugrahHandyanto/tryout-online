<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class HasilTryout extends Model
{
    use HasFactory;
    protected $table = 'hasil_tryout';
    protected $fillable = ['tryout_id','siswa_id','nilai','jumlah_benar','jumlah_salah','waktu_pengerjaan_menit','waktu_submit','started_at'];
    protected $casts = ['nilai'=>'decimal:2','waktu_submit'=>'datetime','started_at'=>'datetime'];
    public function tryout() { return $this->belongsTo(Tryout::class); }
    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function detailJawaban() { return $this->hasMany(DetailJawaban::class); }
}
