@php
$menus = [
            ['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>request()->routeIs('siswa.dashboard')],
            ['label'=>'Tryout','url'=>'#','active'=>false],
            ['label'=>'Hasil Saya','url'=>'#','active'=>false],
            ['label'=>'Ranking','url'=>'#','active'=>false],
            ['label'=>'Profil','url'=>'#','active'=>false],
];
$roleLabel = 'Siswa';
$title = 'Siswa Dashboard';
$header = 'Siswa - Dashboard';
@endphp

<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">

    <div class="space-y-6">
        <div class="bg-gradient-to-br from-[#1E3A8A] to-[#3B82F6] rounded-2xl p-6 text-white">
            <p class="text-sm opacity-80">Tryout Aktif</p>
            <h3 class="text-lg font-bold mt-1">Tryout UTBK #5 - Paket Lengkap</h3>
            <p class="text-sm opacity-80 mt-1">60 soal - 90 menit - Batas: 30 Sep 2026</p>
            <a href="#" class="inline-flex mt-4 bg-white text-[#1E3A8A] text-sm font-semibold px-5 py-2.5 rounded-xl">MULAI TRYOUT</a>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-xs text-[#64748B]">Nilai Terakhir</p><p class="text-xl font-bold text-[#0F172A] mt-1">87.5</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-xs text-[#64748B]">Ranking</p><p class="text-xl font-bold text-[#0F172A] mt-1">#12</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Halo, {{ Auth::user()->name }} (Siswa)</h3>
            <p class="text-sm text-[#64748B] mt-1">Kerjakan tryout dan lihat hasilmu di sini.</p>
        </div>
    </div>

</x-layouts.role>
