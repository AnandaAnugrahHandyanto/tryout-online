@php
$menus=[['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false],['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>false],['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>false],['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false]];
$menus[1]['active']=true;
$roleLabel='Admin';$title='Kelas';$header='Kelas';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <form method="GET" class="flex gap-2"><input name="search" value="{{ request('search') }}" placeholder="Cari kelas/tingkat..." class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-[#1E3A8A] outline-none"><button class="bg-[#1E3A8A] text-white px-4 py-2 rounded-xl text-sm">Cari</button></form>
        <a href="{{ route('admin.kelas.create') }}" class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold text-center">+ Tambah Kelas</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left">Nama</th><th class="px-5 py-3 text-left">Tingkat</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-[#E2E8F0]">@forelse($kelas as $k)<tr class="hover:bg-[#F8FAFC]"><td class="px-5 py-3 font-medium text-[#0F172A]">{{ $k->nama }}</td><td class="px-5 py-3 text-[#64748B]">{{ $k->tingkat }}</td><td class="px-5 py-3 text-right flex justify-end gap-2"><a href="{{ route('admin.kelas.edit',$k) }}" class="text-[#3B82F6] text-xs font-semibold hover:underline">Edit</a><form method="POST" action="{{ route('admin.kelas.destroy',$k) }}" onsubmit="return confirm('Hapus kelas?')">@csrf @method('DELETE')<button class="text-red-600 text-xs font-semibold hover:underline">Hapus</button></form></td></tr>@empty<tr><td colspan="3" class="px-5 py-8 text-center text-[#64748B]">Belum ada data</td></tr>@endforelse</tbody>
        </table></div>
        <div class="p-4 border-t">{{ $kelas->links() }}</div>
    </div>
</div>
</x-layouts.role>
