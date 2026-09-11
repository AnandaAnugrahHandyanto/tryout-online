@php
$menus=[['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>true]];
$isEdit=$tryout->exists;
$roleLabel='Guru';$title=$isEdit?'Edit Tryout':'Buat Tryout';$header=$title;
$selectedIds = old('soal_ids', $isEdit ? $tryout->soal->pluck('id')->toArray() : []);
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="max-w-3xl">
    @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">{{ session('error') }}</div>@endif
    <form method="POST" action="{{ $isEdit? route('guru.tryout.update',$tryout): route('guru.tryout.store') }}" class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-sm">
        @csrf @if($isEdit) @method('PUT') @endif
        <div><label class="text-sm font-medium">Nama Tryout</label><input name="nama" value="{{ old('nama',$tryout->nama) }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-sm font-medium">Mata Pelajaran</label><select name="mapel_id" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="">-- Pilih --</option>@foreach($mapel as $m)<option value="{{ $m->id }}" @selected(old('mapel_id',$tryout->mapel_id)==$m->id)>{{ $m->nama }}</option>@endforeach</select></div>
            <div><label class="text-sm font-medium">Status</label><select name="status" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"><option value="draft" @selected(old('status',$tryout->status)=='draft')>Draft</option><option value="aktif" @selected(old('status',$tryout->status)=='aktif')>Aktif</option><option value="selesai" @selected(old('status',$tryout->status)=='selesai')>Selesai</option></select></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="text-sm font-medium">Durasi (menit)</label><input type="number" name="durasi_menit" value="{{ old('durasi_menit',$tryout->durasi_menit ?? 60) }}" required min="5" max="300" class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Tanggal Mulai</label><input type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai', $tryout->tanggal_mulai ? $tryout->tanggal_mulai->format('Y-m-d\TH:i') : '') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
            <div><label class="text-sm font-medium">Tanggal Selesai</label><input type="datetime-local" name="tanggal_selesai" value="{{ old('tanggal_selesai', $tryout->tanggal_selesai ? $tryout->tanggal_selesai->format('Y-m-d\TH:i') : '') }}" required class="mt-1 w-full border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm"></div>
        </div>
        @if(!$isEdit)
        <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" name="random" value="1" id="random" class="rounded"><label for="random" class="text-sm">Acak otomatis dari bank soal (mapel terpilih)</label>
            <input type="number" name="jumlah_soal" value="{{ old('jumlah_soal',5) }}" min="1" max="50" class="border border-[#E2E8F0] rounded-xl px-3 py-1.5 text-sm w-20 ml-2" placeholder="5">
        </div>
        @endif
        <div>
            <label class="text-sm font-medium">Pilih Soal (centang)</label>
            <p class="text-xs text-[#64748B]">Kosongkan jika pakai random. Atau pilih manual beberapa soal.</p>
            <div class="mt-2 border border-[#E2E8F0] rounded-xl max-h-72 overflow-y-auto divide-y">
                @forelse($soal as $s)
                    <label class="flex gap-3 px-4 py-3 hover:bg-[#F8FAFC] cursor-pointer">
                        <input type="checkbox" name="soal_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, $selectedIds)) class="mt-1 rounded">
                        <div class="flex-1">
                            <p class="text-sm text-[#0F172A]">{{ \Illuminate\Support\Str::limit($s->pertanyaan,90) }}</p>
                            <p class="text-xs text-[#64748B]">{{ $s->mapel->nama ?? '-' }} • {{ $s->tingkat_kesulitan }} • Kunci {{ strtoupper($s->jawaban_benar) }}</p>
                        </div>
                    </label>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-[#64748B]">Belum ada soal. <a href="{{ route('guru.soal.create') }}" class="text-[#1E3A8A] underline">Buat soal dulu</a></p>
                @endforelse
            </div>
        </div>
        <div class="flex gap-3 pt-2"><button class="bg-[#1E3A8A] text-white px-6 py-2.5 rounded-xl text-sm font-semibold">{{ $isEdit?'Update':'Simpan Tryout' }}</button><a href="{{ route('guru.tryout.index') }}" class="px-6 py-2.5 rounded-xl text-sm border">Batal</a></div>
    </form>
</div>
</x-layouts.role>
