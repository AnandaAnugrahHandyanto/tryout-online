@php
$menus = [
            ['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>request()->routeIs('admin.dashboard')],
            ['label'=>'Kelola Guru','url'=>'#','active'=>false],
            ['label'=>'Kelola Siswa','url'=>'#','active'=>false],
            ['label'=>'Kelola Orang Tua','url'=>'#','active'=>false],
            ['label'=>'Kelas','url'=>'#','active'=>false],
            ['label'=>'Mata Pelajaran','url'=>'#','active'=>false],
            ['label'=>'Tryout','url'=>'#','active'=>false],
            ['label'=>'Pengaturan','url'=>'#','active'=>false],
];
$roleLabel = 'Admin';
$title = 'Admin Dashboard';
$header = 'Admin - Dashboard';
@endphp

<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TOTAL GURU</p><p class="text-2xl font-bold text-[#0F172A] mt-2">12</p><p class="text-xs text-[#22C55E] mt-1">Aktif</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TOTAL SISWA</p><p class="text-2xl font-bold text-[#0F172A] mt-2">120</p><p class="text-xs text-[#22C55E] mt-1">Terdaftar</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">KELAS</p><p class="text-2xl font-bold text-[#0F172A] mt-2">6</p><p class="text-xs text-[#64748B] mt-1">Aktif</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT AKTIF</p><p class="text-2xl font-bold text-[#0F172A] mt-2">3</p><p class="text-xs text-[#3B82F6] mt-1">Berjalan</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Selamat datang, {{ Auth::user()->name }} (Admin)</h3>
            <p class="text-sm text-[#64748B] mt-1">Kelola data master, guru, siswa, dan orang tua dari sini.</p>
        </div>
    </div>

</x-layouts.role>
