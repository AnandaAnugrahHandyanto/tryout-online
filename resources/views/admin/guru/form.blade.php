@php
$menus=[['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false],['label'=>'Kelas','url'=>route('admin.kelas.index'),'active'=>false],['label'=>'Mata Pelajaran','url'=>route('admin.mapel.index'),'active'=>false],['label'=>'Guru','url'=>route('admin.guru.index'),'active'=>true],['label'=>'Siswa','url'=>route('admin.siswa.index'),'active'=>false],['label'=>'Orang Tua','url'=>route('admin.ortu.index'),'active'=>false]];
$isEdit=isset($guru) && $guru->exists;
$roleLabel='Admin';$title=$isEdit?'Edit Guru':'Tambah Guru';$header=$title;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="max-w-xl">
    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $isEdit? route('admin.guru.update',$guru): route('admin.guru.store') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div><label class="text-sm font-medium">Nama</label><input name="name" value="{{ old('name', $isEdit? $guru->user->name:'') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A8A] outline-none"></div>
        <div><label class="text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email', $isEdit? $guru->user->email:'') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        <div><label class="text-sm font-medium">Password {{ $isEdit?' (kosongkan jika tidak ganti)':'' }}</label><input type="password" name="password" {{ $isEdit?'':'required' }} class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        <div><label class="text-sm font-medium">NIP (opsional)</label><input name="nip" value="{{ old('nip', $isEdit? $guru->nip:'') }}" class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        <div class="flex gap-3 pt-2"><button class="bg-[#1E3A8A] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">{{ $isEdit?'Update':'Simpan' }}</button><a href="{{ route('admin.guru.index') }}" class="px-6 py-2.5 rounded-xl text-sm border">Batal</a></div>
    </form>
</div>
</x-layouts.role>
