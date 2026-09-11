@php
$menus=[['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>true],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>false]];
$roleLabel='Guru';$title='Bank Soal';$header='Bank Soal';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
    <div class="flex flex-col lg:flex-row justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan..." class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm w-56 focus:ring-2 focus:ring-[#1E3A8A] outline-none">
            <select name="mapel_id" class="border border-[#E2E8F0] rounded-xl px-3 py-2 text-sm"><option value="">Semua Mapel</option>@foreach($mapel as $m)<option value="{{ $m->id }}" @selected(request('mapel_id')==$m->id)>{{ $m->nama }}</option>@endforeach</select>
            <select name="tingkat" class="border border-[#E2E8F0] rounded-xl px-3 py-2 text-sm"><option value="">Semua Tingkat</option><option value="mudah" @selected(request('tingkat')=='mudah')>Mudah</option><option value="sedang" @selected(request('tingkat')=='sedang')>Sedang</option><option value="sulit" @selected(request('tingkat')=='sulit')>Sulit</option></select>
            <button class="bg-[#1E3A8A] text-white px-4 py-2 rounded-xl text-sm">Filter</button>
        </form>
        <a href="{{ route('guru.soal.create') }}" class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold text-center">+ Tambah Soal</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left w-[45%]">Pertanyaan</th><th class="px-5 py-3 text-left">Mapel</th><th class="px-5 py-3 text-left">Kesulitan</th><th class="px-5 py-3 text-left">Kunci</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y">@forelse($soal as $s)<tr class="hover:bg-[#F8FAFC]"><td class="px-5 py-3 text-[#0F172A]">{{ \Illuminate\Support\Str::limit($s->pertanyaan,80) }}</td><td class="px-5 py-3"><span class="bg-[#DBEAFE] text-[#1E3A8A] px-2.5 py-1 rounded-full text-xs font-semibold">{{ $s->mapel->nama ?? '-' }}</span></td><td class="px-5 py-3 text-[#64748B] text-xs capitalize">{{ $s->tingkat_kesulitan }}</td><td class="px-5 py-3"><span class="bg-[#F8FAFC] border px-2 py-1 rounded text-xs font-bold uppercase">{{ $s->jawaban_benar }}</span></td><td class="px-5 py-3 text-right flex justify-end gap-2"><a href="{{ route('guru.soal.edit',$s) }}" class="text-[#3B82F6] text-xs font-semibold hover:underline">Edit</a><form method="POST" action="{{ route('guru.soal.destroy',$s) }}" onsubmit="return confirm('Hapus soal?')">@csrf @method('DELETE')<button class="text-red-600 text-xs font-semibold hover:underline">Hapus</button></form></td></tr>@empty<tr><td colspan="5" class="px-5 py-8 text-center text-[#64748B]">Belum ada soal</td></tr>@endforelse</tbody>
        </table></div>
        <div class="p-4 border-t">{{ $soal->links() }}</div>
    </div>
</div>
</x-layouts.role>
