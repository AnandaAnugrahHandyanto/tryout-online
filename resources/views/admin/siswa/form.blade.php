@php
$menus=[['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false],['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>false],['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>false],['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false]];
$menus[4]['active']=true;
$isEdit=isset($siswa) && $siswa->exists;
$roleLabel='Admin';$title=$isEdit?'Edit Siswa':'Tambah Siswa';$header=$title;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="max-w-2xl">
    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $isEdit? route('admin.siswa.update',$siswa): route('admin.siswa.store') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Nama</label><input name="name" value="{{ old('name', $isEdit? $siswa->user->name:'') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email', $isEdit? $siswa->user->email:'') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        </div>
        <div><label class="text-sm font-medium">Password {{ $isEdit?' (kosongkan jika tidak ganti)':'' }}</label><input type="password" name="password" {{ $isEdit?'':'required' }} class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">NIS</label><input name="nis" value="{{ old('nis', $isEdit? $siswa->nis:'') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Kelas</label><select name="kelas_id" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="">-- Pilih Kelas --</option>@foreach($kelas as $k)<option value="{{ $k->id }}" @selected(old('kelas_id', $isEdit? $siswa->kelas_id:'')==$k->id)>{{ $k->nama }} ({{ $k->tingkat }})</option>@endforeach</select></div>
        </div>
        <div><label class="text-sm font-medium">Orang Tua (opsional)</label><select name="orang_tua_id" class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="">-- Tanpa Orang Tua --</option>@foreach($ortu as $o)<option value="{{ $o->id }}" @selected(old('orang_tua_id', $isEdit? $siswa->orang_tua_id:'')==$o->id)>{{ $o->user->name }}</option>@endforeach</select></div>
        <div class="flex gap-3 pt-2"><button class="bg-[#1E3A8A] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">{{ $isEdit?'Update':'Simpan' }}</button><a href="{{ route('admin.siswa.index') }}" class="px-6 py-2.5 rounded-xl text-sm border">Batal</a></div>
    </form>
</div>
</x-layouts.role>
