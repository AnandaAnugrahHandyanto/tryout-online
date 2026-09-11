@php
$menus=[['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false],['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>false],['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>true],['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false]];
$roleLabel='Admin';$title='Guru';$header='Guru';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <form method="GET" class="flex gap-2"><input name="search" value="{{ request('search') }}" placeholder="Cari nama/email/NIP..." class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-[#1E3A8A] outline-none"><button class="bg-[#1E3A8A] text-white px-4 py-2 rounded-xl text-sm">Cari</button></form>
        <a href="{{ route('admin.guru.create') }}" class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold text-center">+ Tambah Guru</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left">Nama</th><th class="px-5 py-3 text-left">Email</th><th class="px-5 py-3 text-left">NIP</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y">@forelse($guru as $g)<tr class="hover:bg-[#F8FAFC]"><td class="px-5 py-3 font-medium text-[#0F172A]">{{ $g->user->name }}</td><td class="px-5 py-3 text-[#64748B]">{{ $g->user->email }}</td><td class="px-5 py-3 text-[#64748B]">{{ $g->nip ?? '-' }}</td><td class="px-5 py-3 text-right flex justify-end gap-2"><a href="{{ route('admin.guru.edit',$g) }}" class="text-[#3B82F6] text-xs font-semibold hover:underline">Edit</a><form method="POST" action="{{ route('admin.guru.destroy',$g) }}" onsubmit="return confirm('Hapus guru & akun?')">@csrf @method('DELETE')<button class="text-red-600 text-xs font-semibold hover:underline">Hapus</button></form></td></tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-[#64748B]">Belum ada data</td></tr>@endforelse</tbody>
        </table></div>
        <div class="p-4 border-t">{{ $guru->links() }}</div>
    </div>
</div>
</x-layouts.role>
