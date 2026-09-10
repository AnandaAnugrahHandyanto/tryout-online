<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaran extends Model
{
    use HasFactory;
    protected $table = 'mata_pelajaran';
    protected $fillable = ['nama', 'kode'];
    public function soal() { return $this->hasMany(Soal::class, 'mapel_id'); }
    public function tryout() { return $this->hasMany(Tryout::class, 'mapel_id'); }
}
