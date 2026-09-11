@php
$menus=[['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>false],['label'=>'Tryout','url'=>route('siswa.tryout.index'),'active'=>true],['label'=>'Ranking','url'=>route('siswa.tryout.ranking',$tryout),'active'=>true]];
$roleLabel='Siswa';$title='Ranking';$header='Ranking - '.$tryout->nama;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4 max-w-3xl mx-auto">
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm flex justify-between items-center">
        <div><h3 class="font-bold text-[#0F172A]">{{ $tryout->nama }}</h3><p class="text-xs text-[#64748B]">{{ $tryout->mapel->nama ?? '-' }} • {{ $tryout->jumlah_soal }} soal</p></div>
        <a href="{{ route('siswa.tryout.index') }}" class="text-xs text-[#64748B] hover:text-[#1E3A8A]">← Daftar</a>
    </div>
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-4 py-3 text-left">#</th><th class="px-4 py-3 text-left">Siswa</th><th class="px-4 py-3 text-left">Nilai</th><th class="px-4 py-3 text-left">Benar</th><th class="px-4 py-3 text-left">Waktu</th></tr></thead>
            <tbody class="divide-y">
            @forelse($ranking as $i=>$r)
                @php $isMe = $myHasil && $r->id === $myHasil->id; @endphp
                <tr class="{{ $isMe ? 'bg-[#1E3A8A] text-white' : 'hover:bg-[#F8FAFC]' }}">
                    <td class="px-4 py-3 font-bold">{{ $i+1 }} @if($i==0) 🥇 @elseif($i==1) 🥈 @elseif($i==2) 🥉 @endif</td>
                    <td class="px-4 py-3 font-medium">{{ $r->siswa->user->name ?? '-' }} @if($isMe) <span class="text-xs opacity-80">(Anda)</span> @endif</td>
                    <td class="px-4 py-3 font-bold">{{ (int)$r->nilai }}</td>
                    <td class="px-4 py-3">{{ $r->jumlah_benar }}/{{ $r->jumlah_benar+$r->jumlah_salah }}</td>
                    <td class="px-4 py-3 text-xs">{{ $r->waktu_pengerjaan_menit }} mnt</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-[#64748B]">Belum ada ranking</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    @if($myHasil && $myHasil->waktu_submit)
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center">
            <p class="text-sm font-semibold text-green-800">Nilai Anda: {{ (int)$myHasil->nilai }} • Benar {{ $myHasil->jumlah_benar }} • Salah {{ $myHasil->jumlah_salah }}</p>
            <a href="{{ route('siswa.tryout.hasil',$tryout) }}" class="inline-block mt-2 bg-[#1E3A8A] text-white px-4 py-1.5 rounded-xl text-xs font-semibold">Lihat Hasil</a>
        </div>
    @endif
</div>
</x-layouts.role>
