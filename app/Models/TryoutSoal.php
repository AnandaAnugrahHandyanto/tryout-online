<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TryoutSoal extends Model
{
    use HasFactory;
    protected $table = 'tryout_soal';
    protected $fillable = ['tryout_id','soal_id','urutan'];
    public function tryout() { return $this->belongsTo(Tryout::class); }
    public function soal() { return $this->belongsTo(Soal::class); }
}
