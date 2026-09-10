<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::create(['nama' => '9A', 'tingkat' => '9']);
        $mapel = MataPelajaran::create(['nama' => 'Matematika', 'kode' => 'MTK']);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@tryout.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $guruUser = User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru@tryout.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'email_verified_at' => now(),
        ]);
        $guru = Guru::create(['user_id' => $guruUser->id, 'nip' => '198001012000011001']);

        $ortuUser = User::create([
            'name' => 'Orang Tua Kayla',
            'email' => 'orangtua@tryout.test',
            'password' => Hash::make('password'),
            'role' => 'orang_tua',
            'email_verified_at' => now(),
        ]);
        $ortu = OrangTua::create([
            'user_id' => $ortuUser->id,
            'pekerjaan' => 'Wiraswasta',
            'no_hp' => '081234567890',
        ]);

        $siswaUser = User::create([
            'name' => 'Kayla Putri',
            'email' => 'siswa@tryout.test',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);
        Siswa::create([
            'user_id' => $siswaUser->id,
            'nis' => '20250001',
            'kelas_id' => $kelas->id,
            'orang_tua_id' => $ortu->id,
        ]);
    }
}
