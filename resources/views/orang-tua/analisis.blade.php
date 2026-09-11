@php
$menus=[
 ['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>false],
 ['label'=>'Riwayat','url'=>route('orang-tua.riwayat'),'active'=>false],
 ['label'=>'Analisis Akademik','url'=>route('orang-tua.analisis'),'active'=>true],
 ['label'=>'Ranking','url'=>route('orang-tua.ranking'),'active'=>false],
];
$roleLabel='Orang Tua'; $title='Analisis'; $header='Analisis Akademik';
$hasAnak=isset($siswa) && $siswa;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-6">
@if($ortu->siswa->count()>1)
<form method="GET" class="flex gap-2 items-center"><label class="text-sm text-[#64748B]">Anak:</label><select name="siswa_id" onchange="this.form.submit()" class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm bg-white">@foreach($ortu->siswa as $a)<option value="{{$a->id}}" @selected($siswa && $siswa->id==$a->id)>{{$a->user->name}}</option>@endforeach</select></form>
@endif
@if(!$hasAnak)
<div class="bg-white border rounded-2xl p-8 text-center text-[#64748B]">Belum ada anak.</div>
@else
<div class="grid gap-4 md:grid-cols-3">
@forelse($analisis as $a)
@php $c=$a['color']=='green'?'border-green-200 bg-green-50':($a['color']=='yellow'?'border-yellow-200 bg-yellow-50':'border-red-200 bg-red-50'); $badge=$a['status']=='Tinggi'?'bg-green-600':($a['status']=='Sedang'?'bg-yellow-500':'bg-red-500'); @endphp
<div class="bg-white border {{$c}} rounded-2xl p-5 shadow-sm">
<p class="text-sm font-bold text-[#0F172A]">{{$a['nama']}}</p>
<p class="text-2xl font-bold mt-1 {{ $a['color']=='green'?'text-green-600':($a['color']=='yellow'?'text-yellow-600':'text-red-600')}}">{{$a['avg']}}</p>
<span class="inline-block mt-2 text-xs font-semibold text-white px-2 py-1 rounded-full {{$badge}}">{{$a['status']}}</span>
<p class="text-xs text-[#64748B] mt-3">{{$a['count']}} tryout</p>
<p class="text-sm text-[#0F172A] mt-3 font-medium">Rekomendasi:</p><p class="text-sm text-[#64748B]">{{$a['rekom']}}</p>
</div>
@empty
<div class="col-span-3 bg-white border border-[#E2E8F0] rounded-2xl p-8 text-center text-[#64748B]">Belum ada data. Anak belum mengerjakan tryout.</div>
@endforelse
</div>
@if(isset($perlu) && !empty($perlu))
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-5"><p class="font-semibold text-[#0F172A] text-sm">Perlu Perhatian</p><ul class="list-disc list-inside text-sm text-[#64748B] mt-2">@foreach($perlu as $p)<li>{{$p}}</li>@endforeach</ul></div>
@endif
@endif
</div>
</x-layouts.role>
