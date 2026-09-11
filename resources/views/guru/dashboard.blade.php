@php
$counts=[
  'soal'=>\App\Models\Soal::where('guru_id', optional(auth()->user()->guru)->id ?? \App\Models\Guru::where('user_id',auth()->id())->value('id'))->count(),
  'tryout'=>\App\Models\Tryout::where('guru_id', optional(auth()->user()->guru)->id ?? \App\Models\Guru::where('user_id',auth()->id())->value('id'))->count(),
  'dinilai'=>\App\Models\HasilTryout::whereHas('tryout', fn($q)=>$q->where('guru_id', optional(auth()->user()->guru)->id ?? \App\Models\Guru::where('user_id',auth()->id())->value('id')))->count(),
];
$menus = [
            ['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>request()->routeIs('guru.dashboard')],
            ['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],
            ['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>false],
            ['label'=>'Nilai Siswa','url'=>route('guru.tryout.index'),'active'=>false],
];
$roleLabel='Guru';$title='Guru Dashboard';$header='Guru - Dashboard';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('guru.soal.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm hover:bg-[#F8FAFC]"><p class="text-[11px] tracking-widest text-[#64748B]">BANK SOAL</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['soal'] }}</p><p class="text-xs text-[#64748B] mt-1">Soal milik Anda</p></a>
            <a href="{{ route('guru.tryout.index') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm hover:bg-[#F8FAFC]"><p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT DIBUAT</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['tryout'] }}</p><p class="text-xs text-[#64748B] mt-1">Total tryout</p></a>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">SISWA DINILAI</p><p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $counts['dinilai'] }}</p><p class="text-xs text-[#64748B] mt-1">Hasil masuk</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Halo, {{ Auth::user()->name }} (Guru)</h3>
            <p class="text-sm text-[#64748B] mt-1">Buat soal dan kelola tryout untuk siswa Anda.</p>
            <div class="flex gap-3 mt-4">
                <a href="{{ route('guru.soal.create') }}" class="inline-flex bg-[#1E3A8A] text-white text-sm px-4 py-2 rounded-xl">+ Buat Soal</a>
                <a href="{{ route('guru.tryout.create') }}" class="inline-flex bg-white border border-[#E2E8F0] text-sm px-4 py-2 rounded-xl">+ Buat Tryout</a>
            </div>
        </div>
    </div>
</x-layouts.role>
