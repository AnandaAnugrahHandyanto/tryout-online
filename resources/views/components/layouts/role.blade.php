<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Tryout Online') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|plus-jakarta-sans:600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8FAFC]">
<div class="flex min-h-screen">
    {{-- Sidebar desktop --}}
    <aside class="w-[240px] shrink-0 bg-[#1E3A8A] flex flex-col text-white hidden lg:flex">
        <div class="flex items-center gap-3 px-5 py-5">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <span class="text-[#1E3A8A] font-bold text-xs">AP</span>
            </div>
            <span class="font-bold text-sm tracking-wide">AkademikPro</span>
        </div>
        <div class="px-3 flex-1">
            <p class="px-3 py-2 text-[10px] tracking-widest opacity-50 uppercase">{{ $roleLabel ?? '' }}</p>
            <nav class="space-y-1">
                @foreach($menus as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] transition {{ $item['active'] ? 'bg-white text-[#1E3A8A] font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span class="w-2 h-2 rounded-full {{ $item['active'] ? 'bg-[#1E3A8A]' : 'bg-white/50' }}"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
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

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-4 lg:px-8 shrink-0 sticky top-0 z-10">
            <div>
                <h1 class="text-sm font-semibold text-[#0F172A]">{{ $header ?? $title ?? 'Dashboard' }}</h1>
                <p class="text-xs text-[#64748B]">{{ Auth::user()->name }} • {{ ucfirst(str_replace('_',' ', Auth::user()->role)) }}</p>
            </div>
            <div class="flex items-center gap-3">
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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-[#64748B] hover:text-[#1E3A8A] border border-[#E2E8F0] rounded-lg px-3 py-1.5 bg-white">Logout</button>
                </form>
            </div>
        </header>

        {{-- Bottom nav mobile --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t flex justify-around py-2 z-20">
            @foreach(array_slice($menus, 0, 4) as $item)
                <a href="{{ $item['url'] }}" class="flex flex-col items-center gap-1 {{ $item['active'] ? 'text-[#1E3A8A]' : 'text-gray-400' }}">
                    <span class="w-5 h-5 rounded-full {{ $item['active'] ? 'bg-[#1E3A8A]' : 'bg-gray-300' }}"></span>
                    <span class="text-[10px]">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        <main class="flex-1 p-4 lg:p-8 pb-20 lg:pb-8">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
