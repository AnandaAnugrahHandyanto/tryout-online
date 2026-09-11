@php
$menus=[
 ['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>false],
 ['label'=>'Riwayat Tryout','url'=>route('orang-tua.riwayat'),'active'=>true],
 ['label'=>'Analisis','url'=>route('orang-tua.analisis'),'active'=>false],
 ['label'=>'Ranking','url'=>route('orang-tua.ranking'),'active'=>false],
];
$roleLabel='Orang Tua'; $title='Riwayat'; $header='Riwayat Tryout';
$hasAnak=isset($siswa) && $siswa;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
@if($ortu->siswa->count()>1)
<form method="GET" class="flex gap-2 items-center"><label class="text-sm text-[#64748B]">Anak:</label><select name="siswa_id" onchange="this.form.submit()" class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm bg-white">@foreach($ortu->siswa as $a)<option value="{{$a->id}}" @selected($siswa && $siswa->id==$a->id)>{{$a->user->name}}</option>@endforeach</select></form>
@endif
@if(!$hasAnak)
<div class="bg-white border border-[#E2E8F0] rounded-2xl p-8 text-center text-[#64748B]">Belum ada anak terhubung.</div>
@else
<div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
<div class="overflow-x-auto"><table class="w-full text-sm">
<thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left">Tryout</th><th class="px-5 py-3 text-left">Mapel</th><th class="px-5 py-3 text-left">Nilai</th><th class="px-5 py-3 text-left">Ranking</th><th class="px-5 py-3 text-left">Waktu</th><th class="px-5 py-3 text-left">Tanggal</th></tr></thead>
<tbody class="divide-y">
@forelse($riwayat as $h)
<tr class="hover:bg-[#F8FAFC]"><td class="px-5 py-3 font-medium text-[#0F172A]">{{$h->tryout->nama}}</td><td class="px-5 py-3 text-[#64748B]">{{$h->tryout->mapel->nama ?? '-'}}</td><td class="px-5 py-3"><span class="font-bold {{ $h->nilai>=75?'text-green-600':($h->nilai>=50?'text-yellow-600':'text-red-600')}}">{{$h->nilai}}</span></td><td class="px-5 py-3 text-[#64748B]">#{{$h->rank}} / {{$h->rankTotal}}</td><td class="px-5 py-3 text-[#64748B]">{{$h->waktu_pengerjaan_menit}} mnt</td><td class="px-5 py-3 text-xs text-[#64748B]">{{$h->waktu_submit?->format('d M Y H:i')}}</td></tr>
@empty
<tr><td colspan="6" class="px-5 py-8 text-center text-[#64748B]">Belum ada riwayat tryout.</td></tr>
@endforelse
</tbody>
</table></div>
</div>
@endif
</div>
</x-layouts.role>
