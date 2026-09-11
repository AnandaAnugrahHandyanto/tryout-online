@php
$menus = [
  ['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>request()->routeIs('orang-tua.dashboard')],
  ['label'=>'Riwayat Tryout','url'=>route('orang-tua.riwayat'),'active'=>false],
  ['label'=>'Analisis Akademik','url'=>route('orang-tua.analisis'),'active'=>false],
  ['label'=>'Ranking','url'=>route('orang-tua.ranking'),'active'=>false],
];
$roleLabel='Orang Tua'; $title='Dashboard'; $header='Dashboard';
$hasAnak = isset($siswa) && $siswa;
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-6">
  {{-- Child selector --}}
  @if($ortu->siswa->count() > 1)
    <form method="GET" class="flex gap-2 items-center">
      <label class="text-sm font-medium text-[#64748B]">Anak:</label>
      <select name="siswa_id" onchange="this.form.submit()" class="border border-[#E2E8F0] rounded-xl px-4 py-2 text-sm bg-white">
        @foreach($ortu->siswa as $a)
          <option value="{{ $a->id }}" @selected($siswa && $siswa->id==$a->id)>{{ $a->user->name }}</option>
        @endforeach
      </select>
    </form>
  @elseif($hasAnak)
    <div class="bg-white border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm inline-flex gap-2">
      <span class="text-[#64748B]">Anak:</span> <span class="font-semibold text-[#0F172A]">{{ $siswa->user->name }}</span>
      <span class="text-[#64748B]">• {{ $siswa->kelas->nama ?? '-' }} • {{ $siswa->nis }}</span>
    </div>
  @endif

  @if(!$hasAnak)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
      <p class="font-semibold text-[#0F172A]">Belum ada data anak</p>
      <p class="text-sm text-[#64748B] mt-1">Hubungi admin untuk menautkan akun anak.</p>
    </div>
  @else
    {{-- KPI 4 cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] tracking-widest text-[#64748B]">NILAI TERAKHIR</p>
        <p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $nilaiTerakhir !== null ? $nilaiTerakhir : '-' }}</p>
        @if($delta !== null)
          <span class="inline-flex mt-2 text-[11px] px-2 py-1 rounded-full font-semibold {{ $delta>=0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $delta>=0?'+':'' }}{{ $delta }}</span>
        @else
          <span class="text-xs text-[#64748B] mt-2 inline-block">-</span>
        @endif
      </div>
      <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] tracking-widest text-[#64748B]">RANKING</p>
        <p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $ranking ? '#'.$ranking : '-' }}</p>
        <p class="text-xs text-[#64748B] mt-1">{{ $rankingTotal ? 'dari '.$rankingTotal : '-'}}</p>
      </div>
      <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] tracking-widest text-[#64748B]">PROGRESS</p>
        <p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $progressPct ?? '-' }}</p>
        <p class="text-xs text-[#64748B] mt-1">vs tryout sebelumnya</p>
      </div>
      <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
        <p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT</p>
        <p class="text-2xl font-bold text-[#0F172A] mt-2">{{ $jumlahTryout }}x</p>
        <p class="text-xs text-[#64748B] mt-1">dikerjakan</p>
      </div>
    </div>

    {{-- Line chart + Perlu Perhatian row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold text-[#0F172A] text-sm">Grafik Perkembangan Nilai</h3>
        <p class="text-xs text-[#64748B] mb-4">5 tryout terakhir</p>
        <div class="h-[220px]">
          <canvas id="chartNilai"></canvas>
        </div>
        @if(empty($chartValues) || count($chartValues) < 2)
          <p class="text-xs text-[#64748B] mt-2">Butuh minimal 2 tryout untuk melihat trend.</p>
        @endif
      </div>
      <div class="space-y-4">
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
          <h4 class="font-semibold text-[#0F172A] text-sm">Progress per Mata Pelajaran</h4>
          <div class="mt-3 space-y-3">
            @forelse($perMapel as $pm)
              @php $w = min(100, max(5,$pm['avg'])); $bar = $pm['color']=='green' ? 'bg-[#22C55E]' : ($pm['color']=='yellow' ? 'bg-[#EAB308]' : 'bg-[#EF4444]'); @endphp
              <div>
                <div class="flex justify-between text-xs"><span class="font-medium text-[#0F172A]">{{ $pm['nama'] }}</span><span class="text-[#64748B]">{{ $pm['avg'] }} • {{ $pm['count'] }}x</span></div>
                <div class="h-2 bg-[#F1F5F9] rounded-full mt-1 overflow-hidden"><div class="h-full {{ $bar }} rounded-full" style="width: {{ $w }}%"></div></div>
              </div>
            @empty
              <p class="text-sm text-[#64748B]">Belum ada data</p>
            @endforelse
          </div>
        </div>
        <div class="{{ empty($perlu) ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-[#F59E0B]' }} rounded-2xl p-5">
          <p class="text-sm font-semibold text-[#0F172A]">{{ empty($perlu) ? 'Semua Baik' : 'Perlu Perhatian' }}</p>
          @if(empty($perlu))
            <p class="text-sm text-[#64748B] mt-1">Tidak ada mapel yang menurun. Pertahankan!</p>
          @else
            <ul class="text-sm text-[#64748B] mt-2 list-disc list-inside space-y-1">
              @foreach($perlu as $p)<li>{{ $p }}</li>@endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>
  @endif
</div>

@if($hasAnak && !empty($chartValues))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = @json($chartLabels);
const values = @json($chartValues);
const ctx = document.getElementById('chartNilai');
if(ctx){
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Nilai',
        data: values,
        borderColor: '#1E3A8A',
        backgroundColor: 'rgba(30,58,138,0.08)',
        tension: 0.35,
        fill: true,
        pointBackgroundColor: '#1E3A8A',
        pointRadius: 5,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { min: 0, max: 100, grid: { color: '#F1F5F9' } }, x: { grid: { display: false } } },
      plugins: { legend: { display: false } }
    }
  });
}
</script>
@endif
</x-layouts.role>
