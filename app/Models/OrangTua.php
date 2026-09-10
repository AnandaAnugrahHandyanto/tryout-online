<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrangTua extends Model
{
    use HasFactory;
    protected $table = 'orang_tua';
    protected $fillable = ['user_id', 'pekerjaan', 'no_hp'];
    public function user() { return $this->belongsTo(User::class); }
    public function siswa() { return $this->hasMany(Siswa::class); }
}
