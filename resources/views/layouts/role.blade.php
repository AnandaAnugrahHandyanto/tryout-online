<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1E3A8A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="AkademikPro">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="AkademikPro — Sistem Akademik Tryout Online">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-180x180.png">
    <title>{{ $title ?? config('app.name', 'Tryout Online') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|plus-jakarta-sans:600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="font-sans antialiased bg-[#F8FAFC]" x-data="{drawer:false}">
<div class="flex min-h-screen">
    {{-- Sidebar desktop --}}
    <aside class="w-[240px] shrink-0 bg-[#1E3A8A] flex flex-col text-white hidden lg:flex">
        <div class="flex items-center gap-3 px-5 py-5">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <span class="text-[#1E3A8A] font-bold text-xs">AP</span>
            </div>
            <span class="font-bold text-sm tracking-wide">AkademikPro</span>
        </div>
        <div class="px-3 flex-1 overflow-y-auto">
            <p class="px-3 py-2 text-[10px] tracking-widest opacity-50 uppercase">{{ $roleLabel ?? '' }}</p>
            <nav class="space-y-1">
                @foreach($menus as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition {{ $item['active'] ? 'bg-white text-[#1E3A8A] font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span class="w-2 h-2 rounded-full {{ $item['active'] ? 'bg-[#1E3A8A]' : 'bg-white/50' }}"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
                @php $nUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('sudah_dibaca', false)->count(); $nActive = request()->routeIs('notifikasi.*'); @endphp
                <a href="{{ route('notifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition {{ $nActive ? 'bg-white text-[#1E3A8A] font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <span class="w-2 h-2 rounded-full {{ $nActive ? 'bg-[#1E3A8A]' : 'bg-white/50' }}"></span>
                    Notifikasi @if($nUnread>0)<span class="ml-auto bg-[#EF4444] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $nUnread }}</span>@endif
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] text-white/70 hover:bg-white/10 hover:text-white transition">
                    ↪ Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile drawer --}}
    <div x-show="drawer" x-transition.opacity class="fixed inset-0 bg-black/40 z-30 lg:hidden" @click="drawer=false" x-cloak></div>
    <aside x-show="drawer" x-transition:enter="transition transform duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-[280px] bg-[#1E3A8A] flex flex-col text-white z-40 lg:hidden overflow-y-auto" x-cloak>
        <div class="flex items-center justify-between px-5 py-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center"><span class="text-[#1E3A8A] font-bold text-xs">AP</span></div>
                <span class="font-bold text-sm tracking-wide">AkademikPro</span>
            </div>
            <button @click="drawer=false" class="w-8 h-8 grid place-items-center rounded-full bg-white/10 text-white">✕</button>
        </div>
        <div class="px-3 flex-1">
            <p class="px-3 py-2 text-[10px] tracking-widest opacity-50 uppercase">{{ $roleLabel ?? '' }}</p>
            <nav class="space-y-1">
                @foreach($menus as $item)
                    <a href="{{ $item['url'] }}" @click="drawer=false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition {{ $item['active'] ? 'bg-white text-[#1E3A8A] font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span class="w-2 h-2 rounded-full {{ $item['active'] ? 'bg-[#1E3A8A]' : 'bg-white/50' }}"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('notifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition {{ $nActive ? 'bg-white text-[#1E3A8A] font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <span class="w-2 h-2 rounded-full {{ $nActive ? 'bg-[#1E3A8A]' : 'bg-white/50' }}"></span>
                    Notifikasi @if($nUnread>0)<span class="ml-auto bg-[#EF4444] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $nUnread }}</span>@endif
                </a>
            </nav>
        </div>
        <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-[13px] text-white/70 hover:bg-white/10 hover:text-white transition">↪ Logout</button></form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-4 lg:px-8 shrink-0 sticky top-0 z-10 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="drawer=true" class="lg:hidden w-9 h-9 grid place-items-center bg-white border border-[#E2E8F0] rounded-xl shrink-0">☰</button>
                <div class="min-w-0">
                    <h1 class="text-sm font-semibold text-[#0F172A] truncate">{{ $header ?? $title ?? 'Dashboard' }}</h1>
                    <p class="text-xs text-[#64748B] truncate">{{ Auth::user()->name }} • {{ ucfirst(str_replace('_',' ', Auth::user()->role)) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @php $hUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('sudah_dibaca', false)->count(); @endphp
                <a href="{{ route('notifikasi.index') }}" class="relative w-9 h-9 grid place-items-center bg-white border border-[#E2E8F0] rounded-full hover:bg-[#F8FAFC] shrink-0">
                    <span class="text-sm">🔔</span>
                    @if($hUnread>0)<span class="absolute -top-1 -right-1 bg-[#EF4444] text-white text-[10px] font-bold min-w-[18px] h-[18px] grid place-items-center rounded-full px-1">{{ $hUnread > 9 ? '9+' : $hUnread }}</span>@endif
                </a>
                <div class="hidden sm:flex items-center gap-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-full px-3 py-1.5">
                    <span class="w-6 h-6 rounded-full bg-[#1E3A8A] flex items-center justify-center text-white text-[10px] font-bold">{{ substr(Auth::user()->name,0,1) }}</span>
                    <span class="text-xs font-medium text-[#0F172A]">{{ Auth::user()->name }}</span>
                </div>
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                    @if(Auth::user()->role=='admin') bg-[#1E3A8A] text-white
                    @elseif(Auth::user()->role=='guru') bg-[#DBEAFE] text-[#1E3A8A]
                    @elseif(Auth::user()->role=='siswa') bg-green-100 text-green-700
                    @else bg-amber-100 text-amber-700 @endif">
                    {{ strtoupper(str_replace('_',' ', Auth::user()->role)) }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="text-xs text-[#64748B] hover:text-[#1E3A8A] border border-[#E2E8F0] rounded-lg px-3 py-1.5 bg-white">Logout</button>
                </form>
            </div>
        </header>

        {{-- Bottom nav mobile --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t flex justify-around py-2 z-20">
            @foreach(array_slice($menus, 0, 3) as $item)
                <a href="{{ $item['url'] }}" class="flex flex-col items-center gap-1 {{ $item['active'] ? 'text-[#1E3A8A]' : 'text-gray-400' }}">
                    <span class="w-5 h-5 rounded-full {{ $item['active'] ? 'bg-[#1E3A8A]' : 'bg-gray-300' }}"></span>
                    <span class="text-[10px]">{{ $item['label'] }}</span>
                </a>
            @endforeach
            @php $bUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('sudah_dibaca', false)->count(); $bActive = request()->routeIs('notifikasi.*'); @endphp
            <a href="{{ route('notifikasi.index') }}" class="flex flex-col items-center gap-1 {{ $bActive ? 'text-[#1E3A8A]' : 'text-gray-400' }} relative">
                <span class="w-5 h-5 rounded-full {{ $bActive ? 'bg-[#1E3A8A]' : 'bg-gray-300' }}"></span>
                @if($bUnread>0)<span class="absolute -top-1 right-3 bg-[#EF4444] text-white text-[9px] font-bold min-w-[14px] h-[14px] grid place-items-center rounded-full px-1">{{ $bUnread }}</span>@endif
                <span class="text-[10px]">Notifikasi</span>
            </a>
        </div>

        <main class="flex-1 p-4 lg:p-8 pb-20 lg:pb-8">
            {{ $slot }}
        </main>
    </div>
</div>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(()=>{}));
}
</script>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
