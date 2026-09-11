@php
$menus = [
            ['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>request()->routeIs('siswa.dashboard')],
            ['label'=>'Tryout','url'=>route('siswa.tryout.index'),'active'=>false],
            ['label'=>'Hasil Saya','url'=>route('siswa.tryout.index'),'active'=>false],
];
$roleLabel='Siswa';$title='Siswa Dashboard';$header='Siswa - Dashboard';
$siswaId = optional(auth()->user()->siswa)->id ?? \App\Models\Siswa::where('user_id', auth()->id())->value('id');
$tryoutAktif = \App\Models\Tryout::with('mapel')->where('status','aktif')->latest()->first();
$lastHasil = $siswaId ? \App\Models\HasilTryout::where('siswa_id',$siswaId)->whereNotNull('waktu_submit')->latest('waktu_submit')->first() : null;
$rank = null;
if($lastHasil){
  $rank = \App\Models\HasilTryout::where('tryout_id',$lastHasil->tryout_id)->whereNotNull('waktu_submit')->where('nilai','>',$lastHasil->nilai)->count()+1;
}
$hasilCount = $siswaId ? \App\Models\HasilTryout::where('siswa_id',$siswaId)->whereNotNull('waktu_submit')->count() : 0;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
    <div class="space-y-6">
        @if($tryoutAktif)
        <div class="bg-gradient-to-br from-[#1E3A8A] to-[#3B82F6] rounded-2xl p-6 text-white">
            <p class="text-sm opacity-80">Tryout Aktif</p>
            <h3 class="text-lg font-bold mt-1">{{ $tryoutAktif->nama }}</h3>
            <p class="text-sm opacity-80 mt-1">{{ $tryoutAktif->jumlah_soal }} soal - {{ $tryoutAktif->durasi_menit }} menit - {{ $tryoutAktif->mapel->nama ?? '-' }}</p>
            <a href="{{ route('siswa.tryout.index') }}" class="inline-flex mt-4 bg-white text-[#1E3A8A] text-sm font-semibold px-5 py-2.5 rounded-xl">Lihat Tryout</a>
        </div>
        @endif
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-xs text-[#64748B]">Nilai Terakhir</p><p class="text-xl font-bold text-[#0F172A] mt-1">{{ $lastHasil->nilai ?? '-' }}</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-xs text-[#64748B]">Ranking Terakhir</p><p class="text-xl font-bold text-[#0F172A] mt-1">{{ $rank ? '#'.$rank : '-' }}</p></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('siswa.tryout.index') }}" class="bg-[#1E3A8A] text-white rounded-2xl p-4 text-center font-semibold text-sm">Daftar Tryout</a>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 text-center"><p class="text-xs text-[#64748B]">Selesai</p><p class="text-lg font-bold text-[#0F172A]">{{ $hasilCount }} tryout</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Halo, {{ Auth::user()->name }} (Siswa)</h3>
            <p class="text-sm text-[#64748B] mt-1">Kerjakan tryout dan lihat hasilmu di sini.</p>
        </div>
    </div>
</x-layouts.role>
