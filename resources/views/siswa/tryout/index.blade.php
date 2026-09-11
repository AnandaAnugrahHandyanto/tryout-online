@php
$menus=[['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>false],['label'=>'Tryout','url'=>route('siswa.tryout.index'),'active'=>true],['label'=>'Ranking','url'=>'#','active'=>false]];
$roleLabel='Siswa';$title='Daftar Tryout';$header='Tryout';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if($tryouts->isEmpty())
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-8 text-center shadow-sm"><p class="text-[#64748B]">Belum ada tryout aktif</p></div>
    @endif
    <div class="grid gap-4">
    @foreach($tryouts as $t)
        @php $done = $t->hasil && $t->hasil->waktu_submit; $inProgress = $t->hasil && !$t->hasil->waktu_submit; @endphp
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-[#0F172A]">{{ $t->nama }}</h3>
                <p class="text-sm text-[#64748B]">{{ $t->mapel->nama ?? '-' }} • {{ $t->jumlah_soal }} soal • {{ $t->durasi_menit }} menit</p>
                <p class="text-xs text-[#64748B] mt-1">{{ $t->tanggal_mulai?->format('d M H:i') }} — {{ $t->tanggal_selesai?->format('d M H:i') }} • <span class="capitalize px-2 py-0.5 rounded-full text-xs font-semibold {{ $t->status=='aktif'?'bg-green-100 text-green-700':'bg-gray-100' }}">{{ $t->status }}</span></p>
                @if($done)<p class="text-sm font-semibold text-green-600 mt-1">Nilai: {{ $t->hasil->nilai }} ({{ $t->hasil->jumlah_benar }} benar)</p>@endif
                @if($inProgress)<p class="text-sm text-yellow-600 mt-1">Sedang dikerjakan — lanjutkan</p>@endif
            </div>
            <div class="flex flex-wrap gap-2">
                @if($done)
                    <a href="{{ route('siswa.tryout.hasil',$t) }}" class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Lihat Hasil</a>
                    <a href="{{ route('siswa.tryout.ranking',$t) }}" class="bg-white border border-[#E2E8F0] px-5 py-2.5 rounded-xl text-sm font-semibold">Ranking</a>
                @elseif($inProgress)
                    <a href="{{ route('siswa.tryout.exam',$t) }}" class="bg-yellow-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Lanjutkan</a>
                @else
                    <form method="POST" action="{{ route('siswa.tryout.start',$t) }}">@csrf<button class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold">MULAI TRYOUT</button></form>
                @endif
            </div>
        </div>
    @endforeach
    </div>
</div>
</x-layouts.role>
