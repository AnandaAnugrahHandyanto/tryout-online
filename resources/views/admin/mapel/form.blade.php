@php
$menus=[['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false],['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>true],['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>false],['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false]];
$isEdit=$mapel->exists;
$roleLabel='Admin';$title=$isEdit?'Edit Mapel':'Tambah Mapel';$header=$title;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="max-w-xl">
    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $isEdit? route('admin.mapel.update',$mapel): route('admin.mapel.store') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div><label class="text-sm font-medium">Nama Mata Pelajaran</label><input name="nama" value="{{ old('nama',$mapel->nama) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A8A] outline-none"></div>
        <div><label class="text-sm font-medium">Kode</label><input name="kode" value="{{ old('kode',$mapel->kode) }}" required placeholder="MTK / IPA / BIN" class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A8A] outline-none"></div>
        <div class="flex gap-3 pt-2"><button class="bg-[#1E3A8A] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">{{ $isEdit?'Update':'Simpan' }}</button><a href="{{ route('admin.mapel.index') }}" class="px-6 py-2.5 rounded-xl text-sm border border-[#E2E8F0]">Batal</a></div>
    </form>
</div>
</x-layouts.role>
