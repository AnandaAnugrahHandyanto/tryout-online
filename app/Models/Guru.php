<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $fillable = ['user_id', 'nip'];
    public function user() { return $this->belongsTo(User::class); }
    public function soal() { return $this->hasMany(Soal::class); }
    public function tryout() { return $this->hasMany(Tryout::class); }
}
