@php
$menus=[
 ['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>false],
 ['label'=>'Riwayat','url'=>route('orang-tua.riwayat'),'active'=>false],
 ['label'=>'Analisis','url'=>route('orang-tua.analisis'),'active'=>false],
 ['label'=>'Ranking','url'=>route('orang-tua.ranking'),'active'=>true],
];
$roleLabel='Orang Tua'; $title='Ranking'; $header='Ranking - '.($siswa ? $siswa->user->name : 'Anak');
$hasAnak=isset($siswa) && $siswa;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4 max-w-3xl mx-auto">
@if($ortu->siswa->count()>1)
<form method="GET" class="flex gap-2 items-center"><label class="text-sm text-[#64748B]">Anak:</label><select name="siswa_id" onchange="this.form.submit()" class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm bg-white">@foreach($ortu->siswa as $a)<option value="{{$a->id}}" @selected($siswa && $siswa->id==$a->id)>{{$a->user->name}}</option>@endforeach</select></form>
@endif
@if(!$hasAnak)
<div class="bg-white border rounded-2xl p-8 text-center text-[#64748B]">Belum ada anak.</div>
@else
<form method="GET" class="flex gap-2">
<input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
<select name="tryout_id" onchange="this.form.submit()" class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm flex-1 bg-white">
<option value="">-- Pilih Tryout --</option>
@foreach($tryoutList as $t)<option value="{{$t->id}}" @selected((int)$tryoutId===(int)$t->id)>{{$t->nama}} - {{$t->mapel->nama ?? '-'}}</option>@endforeach
</select>
</form>

<div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
<div class="overflow-x-auto"><table class="w-full text-sm">
<thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-4 py-3">#</th><th class="px-4 py-3 text-left">Siswa</th><th class="px-4 py-3">Nilai</th><th class="px-4 py-3">Benar</th><th class="px-4 py-3">Waktu</th></tr></thead>
<tbody class="divide-y">
@forelse($ranking as $i=>$r)
@php $isMe = $myHasil && (int)$r->siswa_id === (int)$siswa->id; @endphp
<tr class="{{ $isMe?'bg-[#1E3A8A] text-white':'hover:bg-[#F8FAFC]' }}"><td class="px-4 py-3 font-bold text-center">{{$i+1}} @if($i==0) 🥇 @elseif($i==1) 🥈 @elseif($i==2) 🥉 @endif</td><td class="px-4 py-3 font-medium">{{$r->siswa->user->name ?? '-'}} @if($isMe)<span class="text-xs {{ $isMe?'opacity-80':'' }}">(Anak Anda)</span>@endif</td><td class="px-4 py-3 text-center font-bold">{{(int)$r->nilai}}</td><td class="px-4 py-3 text-center">{{$r->jumlah_benar}}/{{$r->jumlah_benar+$r->jumlah_salah}}</td><td class="px-4 py-3 text-center text-xs">{{$r->waktu_pengerjaan_menit}} mnt</td></tr>
@empty
<tr><td colspan="5" class="px-4 py-8 text-center text-[#64748B]">Tidak ada data ranking. Anak belum mengerjakan tryout ini.</td></tr>
@endforelse
</tbody>
</table></div>
</div>
@if($myHasil)
<div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-center text-sm"><span class="font-semibold text-green-800">Anak Anda: {{ (int)$myHasil->nilai }} • Ranking di tryout ini terlihat di atas (highlight biru)</span></div>
@endif
@endif
</div>
</x-layouts.role>
