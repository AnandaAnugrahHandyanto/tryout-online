@php
$menus=[
  ['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],
  ['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],
  ['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>false],
  ['label'=>'Analitik','url'=>route('guru.analitik'),'active'=>true],
];
$roleLabel='Guru';$title='Analitik Kelas';$header='Guru — Analitik';
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">
<div class="space-y-6">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5">
      <h3 class="font-semibold text-[#0F172A] text-sm">Rata-rata Nilai per Kelas</h3>
      <canvas id="chartPerKelas" height="220" class="mt-4"></canvas>
    </div>
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5">
      <h3 class="font-semibold text-[#0F172A] text-sm">Distribusi Nilai</h3>
      <canvas id="chartDist" height="220" class="mt-4"></canvas>
    </div>
  </div>
  <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-[#0F172A] text-sm">Siswa Perlu Evaluasi <span class="font-normal text-[#64748B]">(nilai &lt; KKM 70)</span></h3>
      <span class="text-xs bg-[#FEF2F2] text-[#EF4444] px-2 py-1 rounded-full font-semibold">{{ $perlu->count() }} siswa</span>
    </div>
    @if($perlu->isEmpty())
      <p class="text-sm text-[#64748B]">Semua siswa di atas KKM. Mantap.</p>
    @else
    <div class="overflow-auto">
      <table class="w-full text-sm">
        <thead class="text-[11px] tracking-widest text-[#64748B]"><tr><th class="text-left py-2">Siswa</th><th class="text-left py-2">Kelas</th><th class="text-center py-2">Rata-rata</th><th class="text-center py-2">Terakhir</th></tr></thead>
        <tbody>
        @foreach($perlu as $row)
          <tr class="border-t border-[#F1F5F9]">
            <td class="py-2 font-medium text-[#0F172A]">{{ $row['siswa']->user->name }}</td>
            <td class="py-2 text-[#64748B]">{{ $row['kelas'] }}</td>
            <td class="py-2 text-center"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $row['avg']<50?'bg-[#FEF2F2] text-[#EF4444]':'bg-[#FEF3C7] text-[#B45309]' }}">{{ $row['avg'] }}</span></td>
            <td class="py-2 text-center text-[#64748B]">{{ $row['last'] }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
  <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5">
    <h3 class="font-semibold text-[#0F172A] text-sm mb-3">Rata-rata per Kelas (tabel)</h3>
    <div class="overflow-auto">
      <table class="w-full text-sm">
        <thead class="text-[11px] tracking-widest text-[#64748B]"><tr><th class="text-left py-2">Kelas</th><th class="text-center py-2">Jumlah Siswa</th><th class="text-center py-2">Rata-rata</th></tr></thead>
        <tbody>
        @foreach($perKelas as $k)
          <tr class="border-t border-[#F1F5F9]">
            <td class="py-2 font-medium text-[#0F172A]">{{ $k['kelas'] }}</td>
            <td class="py-2 text-center text-[#64748B]">{{ $k['jumlah'] }}</td>
            <td class="py-2 text-center font-bold text-[#1E3A8A]">{{ $k['avg'] }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const ctx1=document.getElementById('chartPerKelas');
if(ctx1) new Chart(ctx1,{type:'bar',data:{labels:@json($chartKelasLabels),datasets:[{label:'Rata-rata',data:@json($chartKelasValues),backgroundColor:'#3B82F6',borderRadius:8}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,max:100}}}});
const ctx2=document.getElementById('chartDist');
if(ctx2) new Chart(ctx2,{type:'bar',data:{labels:@json($distLabels),datasets:[{label:'Jumlah',data:@json($distValues),backgroundColor:['#EF4444','#F59E0B','#3B82F6','#22C55E'],borderRadius:8}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
</script>
@endpush
</x-layouts.role>
