@php
$menus=[['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>true]];
$roleLabel='Guru';$title='Tryout';$header='Tryout';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-4">
    <div class="flex flex-col lg:flex-row justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input name="search" value="{{ request('search') }}" placeholder="Cari tryout..." class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm w-56 focus:ring-2 focus:ring-[#1E3A8A] outline-none">
            <select name="status" class="border border-[#E2E8F0] rounded-xl px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="draft" @selected(request('status')=='draft')>Draft</option>
                <option value="aktif" @selected(request('status')=='aktif')>Aktif</option>
                <option value="selesai" @selected(request('status')=='selesai')>Selesai</option>
            </select>
            <button class="bg-[#1E3A8A] text-white px-4 py-2 rounded-xl text-sm">Filter</button>
        </form>
        <a href="{{ route('guru.tryout.create') }}" class="bg-[#1E3A8A] text-white px-5 py-2.5 rounded-xl text-sm font-semibold text-center">+ Buat Tryout</a>
    </div>
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>@endif
    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left">Nama</th><th class="px-5 py-3 text-left">Mapel</th><th class="px-5 py-3 text-left">Soal</th><th class="px-5 py-3 text-left">Durasi</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-left">Hasil</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y">
            @forelse($tryout as $t)
                @php $badge=['draft'=>'bg-gray-100 text-gray-700','aktif'=>'bg-green-100 text-green-700','selesai'=>'bg-blue-100 text-blue-700'][$t->status]??'bg-gray-100'; @endphp
                <tr class="hover:bg-[#F8FAFC]">
                    <td class="px-5 py-3 font-medium text-[#0F172A]"><a href="{{ route('guru.tryout.show',$t) }}" class="hover:text-[#1E3A8A] hover:underline">{{ $t->nama }}</a></td>
                    <td class="px-5 py-3 text-[#64748B]">{{ $t->mapel->nama ?? '-' }}</td>
                    <td class="px-5 py-3 text-[#0F172A] font-semibold">{{ $t->jumlah_soal }}</td>
                    <td class="px-5 py-3 text-[#64748B]">{{ $t->durasi_menit }} mnt</td>
                    <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($t->status) }}</span></td>
                    <td class="px-5 py-3 text-[#64748B]">{{ $t->hasilTryout->count() }} siswa</td>
                    <td class="px-5 py-3 text-right flex justify-end gap-2">
                        <a href="{{ route('guru.tryout.show',$t) }}" class="text-[#1E3A8A] text-xs font-semibold hover:underline">Lihat</a>
                        <a href="{{ route('guru.tryout.edit',$t) }}" class="text-[#3B82F6] text-xs font-semibold hover:underline">Edit</a>
                        <form method="POST" action="{{ route('guru.tryout.destroy',$t) }}" onsubmit="return confirm('Hapus tryout?')">@csrf @method('DELETE')<button class="text-red-600 text-xs font-semibold hover:underline">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-[#64748B]">Belum ada tryout</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="p-4 border-t">{{ $tryout->links() }}</div>
    </div>
</div>
</x-layouts.role>
