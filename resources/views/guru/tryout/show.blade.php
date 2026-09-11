@php
$menus=[['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>true]];
$roleLabel='Guru';$title=$tryout->nama;$header='Hasil Tryout';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-6">
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#0F172A]">{{ $tryout->nama }}</h2>
        <p class="text-sm text-[#64748B]">{{ $tryout->mapel->nama ?? '-' }} • {{ $tryout->jumlah_soal }} soal • {{ $tryout->durasi_menit }} menit • <span class="capitalize">{{ $tryout->status }}</span></p>
        <p class="text-xs text-[#64748B] mt-1">{{ $tryout->tanggal_mulai?->format('d M Y H:i') }} — {{ $tryout->tanggal_selesai?->format('d M Y H:i') }}</p>
        <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-center"><p class="text-xs text-[#64748B] tracking-widest">RATA-RATA</p><p class="text-xl font-bold text-[#0F172A] mt-1">{{ $avg !== null ? number_format($avg,1) : '-' }}</p></div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center"><p class="text-xs text-green-700 tracking-widest">TERTINGGI</p><p class="text-xl font-bold text-green-700 mt-1">{{ $max ?? '-' }}</p></div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center"><p class="text-xs text-red-700 tracking-widest">TERENDAH</p><p class="text-xl font-bold text-red-700 mt-1">{{ $min ?? '-' }}</p></div>
        </div>
    </div>

    <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b flex justify-between items-center"><h3 class="font-semibold text-[#0F172A]">Hasil Siswa ({{ $hasil->count() }})</h3><span class="text-xs text-[#64748B]">{{ $tryout->jumlah_soal }} soal</span></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-[#F8FAFC] text-[#64748B] text-xs uppercase tracking-widest"><tr><th class="px-5 py-3 text-left">Siswa</th><th class="px-5 py-3 text-left">Nilai</th><th class="px-5 py-3 text-left">Benar</th><th class="px-5 py-3 text-left">Salah</th><th class="px-5 py-3 text-left">Waktu</th><th class="px-5 py-3 text-left">Submit</th></tr></thead>
            <tbody class="divide-y">
            @forelse($hasil as $h)
                <tr class="hover:bg-[#F8FAFC]">
                    <td class="px-5 py-3 font-medium text-[#0F172A]">{{ $h->siswa->user->name ?? '-' }} <span class="text-xs text-[#64748B]">({{ $h->siswa->nis ?? '' }})</span></td>
                    <td class="px-5 py-3"><span class="font-bold {{ $h->nilai >= 75 ? 'text-green-600' : ($h->nilai >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $h->nilai }}</span></td>
                    <td class="px-5 py-3 text-green-600 font-semibold">{{ $h->jumlah_benar }}</td>
                    <td class="px-5 py-3 text-red-600 font-semibold">{{ $h->jumlah_salah }}</td>
                    <td class="px-5 py-3 text-[#64748B]">{{ $h->waktu_pengerjaan_menit ?? '-' }} mnt</td>
                    <td class="px-5 py-3 text-xs text-[#64748B]">{{ $h->waktu_submit?->format('d/m H:i') ?? $h->created_at->format('d/m H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-[#64748B]">Belum ada siswa mengerjakan</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>

    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold text-[#0F172A] mb-3">Daftar Soal ({{ $tryout->soal->count() }})</h3>
        <ol class="space-y-3 list-decimal list-inside text-sm">
            @foreach($tryout->soal as $i=>$s)
                <li class="text-[#0F172A]">{{ \Illuminate\Support\Str::limit($s->pertanyaan,100) }} <span class="text-xs text-[#64748B]">({{ $s->tingkat_kesulitan }} • {{ strtoupper($s->jawaban_benar) }})</span></li>
            @endforeach
        </ol>
    </div>

    <div><a href="{{ route('guru.tryout.index') }}" class="text-sm text-[#64748B] hover:text-[#1E3A8A]">← Kembali ke daftar tryout</a></div>
</div>
</x-layouts.role>
