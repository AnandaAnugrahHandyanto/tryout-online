@php
$menus=[['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>false],['label'=>'Tryout','url'=>route('siswa.tryout.index'),'active'=>true],['label'=>'Ranking','url'=>route('siswa.tryout.ranking',$tryout),'active'=>false]];
$roleLabel='Siswa';$title='Hasil Tryout';$header='Hasil';
$benar=$hasil->jumlah_benar; $salah=$hasil->jumlah_salah; $total=$benar+$salah; $pct = $total ? round($benar/$total*100) : 0;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-6 max-w-3xl mx-auto">
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>@endif

    <!-- Score donut -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm text-center">
        <p class="text-xs tracking-widest text-[#64748B]">SKOR AKHIR</p>
        <div class="mx-auto mt-4 w-32 h-32 rounded-full grid place-items-center border-8 {{ $hasil->nilai >= 75 ? 'border-green-500 text-green-600' : ($hasil->nilai >= 50 ? 'border-yellow-400 text-yellow-600' : 'border-red-400 text-red-600') }}" style="border-style:solid">
            <span class="text-3xl font-black">{{ (int)$hasil->nilai }}</span>
        </div>
        <p class="text-sm text-[#64748B] mt-2">{{ $tryout->nama }} • {{ $tryout->mapel->nama ?? '-' }}</p>
        <div class="flex justify-center gap-2 mt-4 flex-wrap">
            <span class="bg-green-50 border border-green-200 text-green-700 px-3 py-1.5 rounded-full text-xs font-semibold">Benar {{ $benar }}</span>
            <span class="bg-red-50 border border-red-200 text-red-700 px-3 py-1.5 rounded-full text-xs font-semibold">Salah {{ $salah }}</span>
            <span class="bg-blue-50 border border-blue-200 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold">{{ $hasil->waktu_pengerjaan_menit }} menit</span>
            <span class="bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-1.5 rounded-full text-xs font-semibold">Ranking #{{ $myRank }}</span>
        </div>
        <div class="flex justify-center gap-2 mt-4">
            <a href="{{ route('siswa.tryout.ranking',$tryout) }}" class="bg-[#1E3A8A] text-white px-5 py-2 rounded-xl text-sm font-semibold">Lihat Ranking</a>
            <a href="{{ route('siswa.tryout.index') }}" class="bg-white border border-[#E2E8F0] px-5 py-2 rounded-xl text-sm font-semibold">Daftar Tryout</a>
        </div>
    </div>

    <!-- Detail jawaban -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b flex justify-between items-center"><h3 class="font-semibold text-[#0F172A] text-sm">Pembahasan</h3><span class="text-xs text-[#64748B]">{{ $hasil->detailJawaban->count() }} soal</span></div>
        <div class="divide-y">
        @foreach($hasil->detailJawaban as $i=>$d)
            @php $soal=$d->soal; $kunci=$soal->jawaban_benar ?? '-'; $isBenar=$d->benar_salah; @endphp
            <div class="p-4 {{ $isBenar ? 'bg-green-50/40' : 'bg-red-50/30' }}">
                <p class="text-sm font-medium text-[#0F172A]">{{ $i+1 }}. {{ $soal->pertanyaan }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-sm">
                    @foreach(['a','b','c','d'] as $opt)
                        @php $txt=$soal->{'pilihan_'.$opt}; $isKunci=strtolower($kunci)=== $opt; $isJawab=strtolower($d->jawaban_siswa ?? '')=== $opt; @endphp
                        <div class="px-3 py-2 rounded-xl border text-sm {{ $isKunci ? 'bg-green-100 border-green-300 font-semibold' : ($isJawab && !$isBenar ? 'bg-red-100 border-red-300' : 'bg-white border-[#E2E8F0]') }}">
                            <span class="uppercase font-bold">{{ $opt }}.</span> {{ $txt }}
                            @if($isKunci) <span class="text-xs text-green-700">✓ Kunci</span> @endif
                            @if($isJawab) <span class="text-xs {{ $isBenar ? 'text-green-700' : 'text-red-700' }}">← Jawabanmu</span> @endif
                        </div>
                    @endforeach
                </div>
                @if($d->jawaban_siswa === null)<p class="text-xs text-red-600 mt-1">Tidak dijawab</p>@endif
            </div>
        @endforeach
        </div>
    </div>

    <!-- Mini ranking top 5 -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
        <h3 class="font-semibold text-[#0F172A] text-sm mb-3">Top 5 Ranking</h3>
        <div class="space-y-2">
        @foreach($ranking->take(5) as $idx=>$r)
            @php $medal=['🥇','🥈','🥉'][$idx] ?? ($idx+1).'.'; $isMe = $r->siswa_id === $hasil->siswa_id; @endphp
            <div class="flex justify-between items-center px-3 py-2 rounded-xl {{ $isMe ? 'bg-[#1E3A8A] text-white' : 'bg-[#F8FAFC] border border-[#E2E8F0]' }}">
                <span class="text-sm font-semibold">{{ $medal }} {{ $r->siswa->user->name ?? '-' }}</span>
                <span class="text-sm font-bold">{{ (int)$r->nilai }}</span>
            </div>
        @endforeach
        </div>
    </div>
</div>
</x-layouts.role>
