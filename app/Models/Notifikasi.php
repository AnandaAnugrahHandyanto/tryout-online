<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notifikasi extends Model
{
    use HasFactory;
    protected $table = 'notifikasi';
    protected $fillable = ['user_id','tipe','judul','pesan','sudah_dibaca'];
    protected $casts = ['sudah_dibaca'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function scopeUnread($q) { return $q->where('sudah_dibaca', false); }
}
