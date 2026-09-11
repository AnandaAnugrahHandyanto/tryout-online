<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1E3A8A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="AkademikPro">
    <meta name="description" content="Masuk ke AkademikPro — Dashboard monitoring tryout untuk orang tua, siswa, dan guru.">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-180x180.png">
    <title>Masuk — AkademikPro</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:600,700,800|inter:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none}</style>
</head>
<body class="font-sans antialiased bg-[#F8FAFC] text-[#0F172A]">
<div class="min-h-screen flex flex-col lg:flex-row">
    {{-- KIRI 50% — navy gradient --}}
    <div class="relative lg:w-1/2 bg-gradient-to-br from-[#1E3A8A] to-[#3B82F6] text-white overflow-hidden flex flex-col">
        {{-- dotted pattern --}}
        <div class="absolute inset-0 opacity-[0.12]" style="background-image: radial-gradient(circle at 1.5px 1.5px, white 1.5px, transparent 0); background-size: 22px 22px;"></div>
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-16 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>

        <div class="relative p-6 sm:p-8 lg:p-10 flex flex-col flex-1">
            {{-- logo --}}
            <a href="/" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-white grid place-items-center text-[#1E3A8A] font-bold text-sm">AP</span>
                <span class="font-heading font-bold text-[15px] tracking-tight">AkademikPro</span>
            </a>

            {{-- tengah — di mobile jadi header tipis, di desktop centered --}}
            <div class="flex-1 flex flex-col justify-center py-8 lg:py-0 lg:max-w-[520px] mt-4 lg:mt-0">
                <h1 class="font-heading font-extrabold text-[26px] sm:text-[30px] lg:text-[36px] leading-[1.15] tracking-tight">Pantau Prestasi,<br>Dukung Masa Depan</h1>
                <p class="text-white/85 text-[14px] lg:text-[15px] leading-relaxed mt-4 max-w-[480px]">Dashboard monitoring tryout untuk orang tua — lihat nilai, progress, dan ranking anak real-time. Terhubung dengan guru dan siswa dalam satu ekosistem.</p>

                {{-- mini stats di desktop --}}
                <div class="hidden lg:flex items-center gap-3 mt-8">
                    <div class="bg-white/10 backdrop-blur border border-white/15 rounded-2xl px-4 py-3 flex-1">
                        <p class="text-[11px] tracking-widest opacity-70">NILAI TERAKHIR</p>
                        <p class="text-xl font-bold mt-1">88 <span class="text-xs font-medium text-white/80">↑ +2.4%</span></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/15 rounded-2xl px-4 py-3 flex-1">
                        <p class="text-[11px] tracking-widest opacity-70">RANKING</p>
                        <p class="text-xl font-bold mt-1">#3 <span class="text-xs font-normal opacity-80">/ 32</span></p>
                    </div>
                </div>
            </div>

            <p class="relative hidden lg:block text-white/60 text-xs mt-auto">© {{ date('Y') }} AkademikPro • Sistem Akademik Tryout Online</p>
        </div>
    </div>

    {{-- KANAN 50% — form card --}}
    <div class="flex-1 lg:w-1/2 bg-[#F8FAFC] flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-[420px] bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="lg:hidden flex items-center gap-2 mb-6">
                <span class="w-8 h-8 rounded-xl bg-[#1E3A8A] grid place-items-center text-white font-bold text-xs">AP</span>
                <span class="font-heading font-bold text-sm">AkademikPro</span>
            </div>

            <h2 class="font-heading font-bold text-xl text-[#0F172A]">Masuk ke Akun</h2>
            <p class="text-sm text-[#64748B] mt-1.5">Masuk sebagai orang tua, siswa, atau guru</p>

            <x-auth-session-status class="mt-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4" x-data="{show:false}">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-[#0F172A]">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="nama@email.com"
                        class="mt-1.5 block w-full rounded-xl border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#1E3A8A] focus:ring-[#1E3A8A]">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Password + toggle --}}
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-[#0F172A]">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#3B82F6] hover:text-[#1E3A8A]">Lupa password?</a>
                        @endif
                    </div>
                    <div class="relative mt-1.5">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="block w-full rounded-xl border-[#E2E8F0] bg-white px-4 py-2.5 pr-11 text-sm text-[#0F172A] placeholder-[#94A3B8] focus:border-[#1E3A8A] focus:ring-[#1E3A8A]">
                        <button type="button" @click="show=!show" class="absolute inset-y-0 right-0 px-3 grid place-items-center text-[#64748B] hover:text-[#0F172A]" tabindex="-1" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                            <span x-show="!show" class="text-xs border border-[#E2E8F0] rounded-lg px-2 py-1 bg-white">Show</span>
                            <span x-show="show" x-cloak class="text-xs border border-[#E2E8F0] rounded-lg px-2 py-1 bg-white">Hide</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-[#0F172A]">Masuk sebagai</label>
                    <select id="role" name="role" required
                        class="mt-1.5 block w-full rounded-xl border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-[#0F172A] focus:border-[#1E3A8A] focus:ring-[#1E3A8A]">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                        <option value="admin" {{ old('role')=='admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ old('role')=='guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ old('role')=='siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="orang_tua" {{ old('role')=='orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#E2E8F0] text-[#1E3A8A] focus:ring-[#1E3A8A]">
                    <span class="text-sm text-[#64748B]">Ingat saya</span>
                </label>

                <button type="submit" class="w-full bg-[#1E3A8A] hover:bg-[#1e2e6b] text-white font-semibold text-sm py-2.5 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:ring-offset-2">
                    Masuk
                </button>

                <div class="flex items-center justify-center gap-1 text-sm pt-1">
                    <span class="text-[#64748B]">Butuh bantuan?</span>
                    <a href="/" class="font-medium text-[#1E3A8A] hover:underline">Hubungi Admin</a>
                </div>
            </form>

            {{-- demo accounts hint --}}
            <div class="mt-6 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3">
                <p class="text-[11px] tracking-widest font-semibold text-[#64748B]">AKUN DEMO • password: password</p>
                <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                    <span class="bg-white border border-[#E2E8F0] rounded-lg px-2.5 py-1.5">Admin: admin@tryout.test</span>
                    <span class="bg-white border border-[#E2E8F0] rounded-lg px-2.5 py-1.5">Guru: guru@tryout.test</span>
                    <span class="bg-white border border-[#E2E8F0] rounded-lg px-2.5 py-1.5">Siswa: siswa@tryout.test</span>
                    <span class="bg-white border border-[#E2E8F0] rounded-lg px-2.5 py-1.5">Ortu: orangtua@tryout.test</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>if('serviceWorker' in navigator){window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));}</script>
</body>
</html>
