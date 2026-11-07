@php
$u = auth()->user();
$unread = \App\Models\Notifikasi::where('user_id',$u->id)->where('sudah_dibaca',false)->count();
$menus = match($u->role){
  'admin' => [['label'=>'Dashboard','url'=>route('admin.dashboard'),'active'=>false]],
  'guru' => [['label'=>'Dashboard','url'=>route('guru.dashboard'),'active'=>false],['label'=>'Bank Soal','url'=>route('guru.soal.index'),'active'=>false],['label'=>'Tryout','url'=>route('guru.tryout.index'),'active'=>false],['label'=>'Analitik','url'=>route('guru.analitik'),'active'=>false]],
  'siswa' => [['label'=>'Dashboard','url'=>route('siswa.dashboard'),'active'=>false],['label'=>'Tryout','url'=>route('siswa.tryout.index'),'active'=>false]],
  'orang_tua' => [['label'=>'Dashboard','url'=>route('orang-tua.dashboard'),'active'=>false],['label'=>'Riwayat','url'=>route('orang-tua.riwayat'),'active'=>false],['label'=>'Analisis','url'=>route('orang-tua.analisis'),'active'=>false],['label'=>'Ranking','url'=>route('orang-tua.ranking'),'active'=>false]],
  default => [],
};
$menus[] = ['label'=>'Notifikasi','url'=>route('notifikasi.index'),'active'=>true];
$roleLabel = match($u->role){'admin'=>'Admin','guru'=>'Guru','siswa'=>'Siswa','orang_tua'=>'Orang Tua',default=>$u->role};
@endphp
<x-layouts.role :menus="$menus" :roleLabel="$roleLabel" title="Notifikasi" header="Notifikasi">
<div class="space-y-4">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex gap-2">
      @php $cur = request('tipe','semua'); $tabs=['semua'=>'Semua','nilai'=>'Nilai','jadwal'=>'Jadwal','pencapaian'=>'Pencapaian','peringatan'=>'Peringatan']; @endphp
      @foreach($tabs as $k=>$label)
        <a href="{{ route('notifikasi.index',['tipe'=>$k]) }}" class="text-xs px-3 py-1.5 rounded-full border {{ $cur===$k ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-white text-[#64748B] border-[#E2E8F0]' }}">{{ $label }} <span class="opacity-70">({{ $counts[$k] ?? 0 }})</span></a>
      @endforeach
    </div>
    @if($unread>0)
    <form method="POST" action="{{ route('notifikasi.readAll') }}">@csrf<button class="text-xs bg-white border border-[#E2E8F0] px-3 py-1.5 rounded-full text-[#0F172A]">Tandai semua dibaca ({{ $unread }})</button></form>
    @endif
  </div>
  @if(session('success'))<div class="bg-[#F0FDF4] border border-[#BBF7D0] text-[#166534] text-sm px-4 py-2 rounded-xl">{{ session('success') }}</div>@endif
  <div class="bg-white border border-[#E2E8F0] rounded-2xl divide-y divide-[#F1F5F9]">
    @if ($notif)
      <div class="p-4 flex gap-3 {{ $notif->sudah_dibaca ? 'opacity-60' : 'bg-[#F8FAFC]' }}">
        <div class="shrink-0 mt-0.5">
          @php $dot = match($notif->tipe){'nilai'=>'bg-[#3B82F6]','jadwal'=>'bg-[#22C55E]','pencapaian'=>'bg-[#EAB308]','peringatan'=>'bg-[#EF4444]',default=>'bg-[#64748B]'}; @endphp
          <span class="inline-block w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-[#0F172A]">{{ $notif->judul }} @if(!$notif->sudah_dibaca)<span class="ml-2 text-[10px] tracking-widest bg-[#1E3A8A] text-white px-1.5 py-0.5 rounded">BARU</span>@endif <span class="ml-2 text-[10px] tracking-widest bg-[#F1F5F9] text-[#64748B] px-1.5 py-0.5 rounded uppercase">{{ $notif->tipe }}</span></p>
          <p class="text-sm text-[#475569] mt-1">{{ $notif->pesan }}</p>
          <p class="text-xs text-[#94A3B8] mt-1">{{ $notif->created_at->diffForHumans() }} · {{ $notif->created_at->format('d M Y H:i') }}</p>
        </div>
        @if(!$notif->sudah_dibaca)
        <form method="POST" action="{{ route('notifikasi.read',$notif) }}" class="shrink-0">@csrf<button class="text-xs text-[#1E3A8A] border border-[#DBEAFE] bg-[#EFF6FF] px-3 py-1 rounded-full">Tandai dibaca</button></form>
        @endif
      </div>
    @else
      <div class="p-8 text-center text-sm text-[#64748B]">Belum ada notifikasi untuk ditampilkan.</div>
    @endif
  </div>
  @if(session('success'))
  <div class="bg-[#F0FDF4] border border-[#BBF7D0] text-[#166534] text-sm px-4 py-2 rounded-xl mt-4">{{ session('success') }}</div>
  @endif
</div>
</x-layouts.role>
