@php
$counts = [
  'guru' => \App\Models\Guru::count(),
  'siswa' => \App\Models\Siswa::count(),
  'kelas' => \App\Models\Kelas::count(),
  'tryout' => \App\Models\Tryout::where('status','aktif')->count(),
];
$menus = [
            ['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>request()->routeIs('admin.dashboard')],
            ['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],
            ['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>false],
            ['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>false],
            ['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],
            ['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false],
            ['label'=>'Tryout','url'=>'#','active'=>false],
];
$roleLabel = 'Admin';
$title = 'Admin Dashboard';
$header = 'Admin - Dashboard';
@endphp

<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TOTAL GURU</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['guru'] }}</p><p class="text-xs text-[#22C55E] mt-1">Aktif</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TOTAL SISWA</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['siswa'] }}</p><p class="text-xs text-[#22C55E] mt-1">Terdaftar</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">KELAS</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['kelas'] }}</p><p class="text-xs text-[#64748B] mt-1">Aktif</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT AKTIF</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['tryout'] }}</p><p class="text-xs text-[#3B82F6] mt-1">Berjalan</p></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <a href="{{ route('admin.kelas.index') }}" class="bg-[#1E3A8A] text-white rounded-2xl p-4 text-center font-semibold text-sm hover:bg-[#1a347a]">Kelas</a>
            <a href="{{ route('admin.mapel.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-4 text-center font-semibold text-sm text-[#0F172A] hover:bg-[#F8FAFC]">Mata Pelajaran</a>
            <a href="{{ route('admin.guru.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-4 text-center font-semibold text-sm text-[#0F172A] hover:bg-[#F8FAFC]">Guru</a>
            <a href="{{ route('admin.siswa.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-4 text-center font-semibold text-sm text-[#0F172A] hover:bg-[#F8FAFC]">Siswa</a>
            <a href="{{ route('admin.ortu.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-4 text-center font-semibold text-sm text-[#0F172A] hover:bg-[#F8FAFC]">Orang Tua</a>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Selamat datang, {{ Auth::user()->name }} (Admin)</h3>
            <p class="text-sm text-[#64748B] mt-1">Kelola data master, guru, siswa, dan orang tua dari sini.</p>
        </div>
    </div>

</x-layouts.role>
