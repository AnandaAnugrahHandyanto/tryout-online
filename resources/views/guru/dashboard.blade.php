@php
$menus = [
            ['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>request()->routeIs('guru.dashboard')],
            ['label'=>'Bank Soal','url'=>'#','active'=>false],
            ['label'=>'Tryout','url'=>'#','active'=>false],
            ['label'=>'Nilai Siswa','url'=>'#','active'=>false],
            ['label'=>'Analisis','url'=>'#','active'=>false],
            ['label'=>'Kelas Saya','url'=>'#','active'=>false],
];
$roleLabel = 'Guru';
$title = 'Guru Dashboard';
$header = 'Guru - Dashboard';
@endphp

<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">BANK SOAL</p><p class="text-2xl font-bold text-[#0F172A] mt-2">42</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT DIBUAT</p><p class="text-2xl font-bold text-[#0F172A] mt-2">5</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">SISWA DINILAI</p><p class="text-2xl font-bold text-[#0F172A] mt-2">86</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Halo, {{ Auth::user()->name }} (Guru)</h3>
            <p class="text-sm text-[#64748B] mt-1">Buat soal dan kelola tryout untuk siswa Anda.</p>
            <a href="#" class="inline-flex mt-4 bg-[#1E3A8A] text-white text-sm px-4 py-2 rounded-xl">+ Buat Soal</a>
        </div>
    </div>

</x-layouts.role>
