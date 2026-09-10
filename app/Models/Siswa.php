<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $fillable = ['user_id', 'nis', 'kelas_id', 'orang_tua_id'];
    public function user() { return $this->belongsTo(User::class); }
    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function orangTua() { return $this->belongsTo(OrangTua::class); }
    public function hasilTryout() { return $this->hasMany(HasilTryout::class); }
}
