@php
$menus = [
            ['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>request()->routeIs('orang-tua.dashboard')],
            ['label'=>'Monitoring Anak','url'=>'#','active'=>false],
            ['label'=>'Nilai','url'=>'#','active'=>false],
            ['label'=>'Progress','url'=>'#','active'=>false],
            ['label'=>'Riwayat Tryout','url'=>'#','active'=>false],
            ['label'=>'Ranking','url'=>'#','active'=>false],
            ['label'=>'Notifikasi','url'=>'#','active'=>false],
            ['label'=>'Profile','url'=>'#','active'=>false],
];
$roleLabel = 'Orang Tua';
$title = 'Orang Tua Dashboard';
$header = 'Orang Tua - Dashboard';
@endphp

<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" :title="$title" :header="$header">

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">NILAI TERAKHIR</p><p class="text-2xl font-bold text-[#0F172A] mt-2">87.5</p><span class="inline-flex mt-2 text-[11px] bg-green-100 text-green-700 px-2 py-1 rounded-full">naik 2.3</span></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">RANKING</p><p class="text-2xl font-bold text-[#0F172A] mt-2">#12</p><p class="text-xs text-[#64748B] mt-1">dari 120</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">PROGRESS</p><p class="text-2xl font-bold text-[#0F172A] mt-2">+12%</p><p class="text-xs text-[#64748B] mt-1">30 hari</p></div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm"><p class="text-[11px] tracking-widest text-[#64748B]">TRYOUT</p><p class="text-2xl font-bold text-[#0F172A] mt-2">8x</p><p class="text-xs text-[#64748B] mt-1">dikerjakan</p></div>
        </div>
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-[#0F172A]">Monitoring - {{ Auth::user()->name }} (Orang Tua)</h3>
            <p class="text-sm text-[#64748B] mt-1">Pantau perkembangan nilai dan progress anak Anda secara real-time.</p>
        </div>
        <div class="bg-amber-50 border border-[#F59E0B] rounded-2xl p-5">
            <p class="text-sm font-semibold text-[#0F172A]">Perlu Perhatian</p>
            <p class="text-sm text-[#64748B] mt-1">Nilai B. Inggris turun 4 poin - sarankan latihan tambahan.</p>
        </div>
    </div>

</x-layouts.role>
