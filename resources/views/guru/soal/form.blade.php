@php
$menus=[['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>true],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>false]];
$isEdit=$soal->exists;
$roleLabel='Guru';$title=$isEdit?'Edit Soal':'Tambah Soal';$header=$title;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="max-w-2xl">
    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $isEdit? route('guru.soal.update',$soal): route('guru.soal.store') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div><label class="text-sm font-medium">Mata Pelajaran</label><select name="mapel_id" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="">-- Pilih Mapel --</option>@foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mapel_id',$soal->mapel_id)==$m->id)>{{ $m->nama }} ({{ $m->kode }})</option>@endforeach</select></div>
        <div><label class="text-sm font-medium">Pertanyaan</label><textarea name="pertanyaan" required rows="3" class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1E3A8A] outline-none">{{ old('pertanyaan',$soal->pertanyaan) }}</textarea></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Pilihan A</label><input name="pilihan_a" value="{{ old('pilihan_a',$soal->pilihan_a) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Pilihan B</label><input name="pilihan_b" value="{{ old('pilihan_b',$soal->pilihan_b) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Pilihan C</label><input name="pilihan_c" value="{{ old('pilihan_c',$soal->pilihan_c) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Pilihan D</label><input name="pilihan_d" value="{{ old('pilihan_d',$soal->pilihan_d) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Jawaban Benar</label><select name="jawaban_benar" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="a" @selected(old('jawaban_benar',$soal->jawaban_benar)=='a')>A</option><option value="b" @selected(old('jawaban_benar',$soal->jawaban_benar)=='b')>B</option><option value="c" @selected(old('jawaban_benar',$soal->jawaban_benar)=='c')>C</option><option value="d" @selected(old('jawaban_benar',$soal->jawaban_benar)=='d')>D</option></select></div>
            <div><label class="text-sm font-medium">Tingkat Kesulitan</label><select name="tingkat_kesulitan" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="mudah" @selected(old('tingkat_kesulitan',$soal->tingkat_kesulitan)=='mudah')>Mudah</option><option value="sedang" @selected(old('tingkat_kesulitan',$soal->tingkat_kesulitan)=='sedang' || !$soal->exists)>Sedang</option><option value="sulit" @selected(old('tingkat_kesulitan',$soal->tingkat_kesulitan)=='sulit')>Sulit</option></select></div>
        </div>
        <div class="flex gap-3 pt-2"><button class="bg-[#1E3A8A] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">{{ $isEdit?'Update':'Simpan' }}</button><a href="{{ route('guru.soal.index') }}" class="px-6 py-2.5 rounded-xl text-sm border">Batal</a></div>
    </form>
</div>
</x-layouts.role>
