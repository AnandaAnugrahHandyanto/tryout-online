@php
$roleLabel='Siswa'; $title='Tryout'; $header='SOAL '.str_pad($currentIdx,2,'0',STR_PAD_LEFT).' / '.str_pad($details->count(),2,'0',STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#1E3A8A">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="AkademikPro">
<link rel="manifest" href="/manifest.json">
<link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
<link rel="apple-touch-icon" href="/icons/icon-180x180.png">
<title>{{ $tryout->nama }} - Soal {{ $currentIdx }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8FAFC] min-h-screen">
<!-- Sticky top -->
<div class="sticky top-0 z-10 bg-white border-b border-[#E2E8F0] px-4 py-3 flex justify-between items-center">
  <div>
    <p class="text-xs tracking-widest text-[#64748B]">SOAL {{ str_pad($currentIdx,2,'0',STR_PAD_LEFT) }} / {{ str_pad($details->count(),2,'0',STR_PAD_LEFT) }}</p>
    <p class="text-xs text-[#64748B] truncate max-w-[200px]">{{ $tryout->nama }}</p>
  </div>
  <div class="flex items-center gap-3">
    <span id="timer" class="bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-xl text-sm font-mono font-bold">--:--</span>
    <form method="POST" action="{{ route('siswa.tryout.submit',$tryout) }}" onsubmit="return confirm('Yakin submit?')">@csrf<button class="bg-[#1E3A8A] text-white px-4 py-1.5 rounded-xl text-xs font-semibold">Selesai</button></form>
  </div>
</div>

<div class="max-w-3xl mx-auto p-4 space-y-4">
  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded-xl text-sm">{{ session('success') }}</div>@endif
  <!-- Progress bar -->
  <div class="h-1 bg-[#E2E8F0] rounded-full overflow-hidden"><div class="h-full bg-[#1E3A8A]" style="width: {{ round($currentIdx/$details->count()*100) }}%"></div></div>

  <!-- Question -->
  <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm">
    <h3 class="font-semibold text-[#0F172A] text-[15px] leading-relaxed">{{ $current->soal->pertanyaan }}</h3>
    <div class="mt-4 space-y-2" id="options">
      @foreach(['a','b','c','d'] as $opt)
        @php $txt = $current->soal->{'pilihan_'.$opt}; $checked = $current->jawaban_siswa === $opt; @endphp
        <label class="flex gap-3 p-3 rounded-xl border cursor-pointer hover:bg-[#F8FAFC] {{ $checked ? 'border-[#1E3A8A] bg-[#DBEAFE]/40' : 'border-[#E2E8F0] bg-white' }}">
          <input type="radio" name="jawaban" value="{{ $opt }}" {{ $checked ? 'checked' : '' }} class="mt-1">
          <span class="w-6 h-6 rounded-full bg-[#1E3A8A] text-white text-xs flex items-center justify-center shrink-0 flex-none grid place-items-center">{{ strtoupper($opt) }}</span>
          <span class="text-sm text-[#0F172A]">{{ $txt }}</span>
        </label>
      @endforeach
    </div>
    <p id="saveStatus" class="text-xs text-[#64748B] mt-3"></p>
  </div>

  <!-- Actions -->
  <div class="flex gap-2">
    @if($currentIdx > 1)
      <a href="{{ route('siswa.tryout.exam', [$tryout,'q'=>$currentIdx-1]) }}" class="flex-1 bg-white border border-[#E2E8F0] rounded-xl py-2.5 text-sm font-semibold text-center">Sebelumnya</a>
    @else
      <span class="flex-1 bg-gray-100 text-gray-400 rounded-xl py-2.5 text-sm text-center">Sebelumnya</span>
    @endif
    <button id="btnRagu" class="px-5 py-2.5 rounded-xl text-sm font-semibold border {{ $current->ragu ? 'bg-yellow-400 border-yellow-400 text-white' : 'bg-white border-[#EAB308] text-[#EAB308]' }}">{{ $current->ragu ? 'Batal Ragu' : 'Ragu-ragu' }}</button>
    @if($currentIdx < $details->count())
      <a id="btnNext" href="{{ route('siswa.tryout.exam', [$tryout,'q'=>$currentIdx+1]) }}" class="flex-1 bg-[#1E3A8A] text-white rounded-xl py-2.5 text-sm font-semibold text-center">Berikutnya</a>
    @else
      <button id="btnSubmit" class="flex-1 bg-green-600 text-white rounded-xl py-2.5 text-sm font-semibold">Submit</button>
    @endif
  </div>

  <!-- Grid navigasi -->
  <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 shadow-sm">
    <p class="text-xs font-semibold text-[#0F172A] mb-3">Navigasi Soal</p>
    <div class="grid grid-cols-5 gap-2">
      @foreach($details as $idx=>$d)
        @php
          $n=$idx+1;
          $isCurrent = $n===$currentIdx;
          $answered = $d->jawaban_siswa !== null;
          $ragu = $d->ragu;
          $cls = $isCurrent ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : ($ragu ? 'bg-yellow-100 border-yellow-300 text-yellow-700' : ($answered ? 'bg-green-50 border-green-300 text-green-700' : 'bg-white border-[#E2E8F0] text-[#64748B]'));
        @endphp
        <a href="{{ route('siswa.tryout.exam', [$tryout,'q'=>$n]) }}" class="h-10 rounded-xl border text-sm font-semibold grid place-items-center {{ $cls }}">{{ $n }}</a>
      @endforeach
    </div>
    <div class="flex gap-3 mt-3 text-[11px] text-[#64748B]">
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-50 border border-green-300"></span> Sudah</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-300"></span> Ragu</span>
      <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-white border"></span> Belum</span>
    </div>
  </div>
  <div class="text-center pb-6"><a href="{{ route('siswa.tryout.index') }}" class="text-xs text-[#64748B] hover:text-[#1E3A8A]">← Daftar tryout</a></div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const tryoutId = {{ $tryout->id }};
const detailId = {{ $current->id }};
let remaining = {{ $remaining }};
const timerEl = document.getElementById('timer');
const saveStatus = document.getElementById('saveStatus');
function fmt(s){ const m=Math.floor(s/60), sc=s%60; return String(m).padStart(2,'0')+':'+String(sc).padStart(2,'0'); }
function tick(){
  timerEl.textContent = fmt(remaining);
  if(remaining<=0){
    timerEl.textContent='00:00';
    // autosubmit via server
    fetch(`{{ route('siswa.tryout.submit',$tryout) }}`,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}})
      .then(r=>r.json().catch(()=>({}))).then(j=>{
        if(j.redirect) location.href=j.redirect;
        else location.href="{{ route('siswa.tryout.hasil',$tryout) }}";
      }).catch(()=>location.href="{{ route('siswa.tryout.hasil',$tryout) }}");
    return;
  }
  remaining--; setTimeout(tick,1000);
}
tick();

// autosave on radio click
document.querySelectorAll('input[name="jawaban"]').forEach(el=>{
  el.addEventListener('change', ()=>{
    saveStatus.textContent='Menyimpan...';
    fetch(`{{ route('siswa.tryout.save',$tryout) }}`,{
      method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({detail_id: detailId, jawaban: el.value})
    }).then(r=>r.json()).then(j=>{
      if(j.timeout){ location.href=j.redirect; return; }
      saveStatus.textContent='Tersimpan ✓';
      setTimeout(()=>saveStatus.textContent='',1500);
    }).catch(()=>saveStatus.textContent='Gagal simpan');
  });
});

// ragu toggle
document.getElementById('btnRagu').addEventListener('click', ()=>{
  fetch(`{{ route('siswa.tryout.ragu',$tryout) }}`,{
    method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
    body: JSON.stringify({detail_id: detailId})
  }).then(r=>r.json()).then(j=>{
    location.reload();
  });
});

// submit button last soal
const btnSubmit = document.getElementById('btnSubmit');
if(btnSubmit){
  btnSubmit.addEventListener('click', ()=>{
    if(!confirm('Submit tryout?')) return;
    fetch(`{{ route('siswa.tryout.submit',$tryout) }}`,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}})
      .then(()=>location.href="{{ route('siswa.tryout.hasil',$tryout) }}");
  });
}
// keep session alive: autosave ping every 30s (server remaining check)
setInterval(()=>{
  fetch(`{{ route('siswa.tryout.save',$tryout) }}`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({detail_id:detailId})}).catch(()=>{});
},30000);
</script>
</body>
</html>
