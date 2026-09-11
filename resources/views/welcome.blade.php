<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1E3A8A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="AkademikPro">
    <meta name="description" content="AkademikPro — Sistem Tryout Online dengan monitoring orang tua real-time. Pantau nilai, progress, dan ranking anak.">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-180x180.png">
    <title>AkademikPro — Pantau Prestasi, Dukung Masa Depan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:600,700,800|inter:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8FAFC] text-[#0F172A]">
    {{-- NAVBAR --}}
    <header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="/" class="flex items-center gap-3 shrink-0">
                <span class="w-9 h-9 rounded-xl bg-[#1E3A8A] grid place-items-center text-white font-bold text-sm">AP</span>
                <span class="font-heading font-bold text-[15px] tracking-tight text-[#0F172A]">AkademikPro</span>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm">
                <a href="#fitur" class="text-[#64748B] hover:text-[#0F172A] font-medium">Fitur</a>
                <a href="#untuk-siapa" class="text-[#64748B] hover:text-[#0F172A] font-medium">Untuk Siapa</a>
                <a href="#tentang" class="text-[#64748B] hover:text-[#0F172A] font-medium">Tentang</a>
            </nav>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center bg-[#1E3A8A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#1e2e6b]">Dashboard</a>
                    <a href="{{ route('dashboard') }}" class="sm:hidden inline-flex items-center bg-[#1E3A8A] text-white text-sm font-semibold px-4 py-2 rounded-xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center bg-[#1E3A8A] text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-[#1e2e6b] transition">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 bg-[#DBEAFE] text-[#1E3A8A] text-xs font-semibold px-3 py-1.5 rounded-full">🎓 Sistem Tryout Online + Monitoring Orang Tua</span>
                <h1 class="font-heading font-extrabold text-3xl sm:text-4xl lg:text-[44px] leading-[1.1] tracking-tight text-[#0F172A] mt-5">Pantau Prestasi,<br><span class="text-[#1E3A8A]">Dukung Masa Depan</span></h1>
                <p class="text-[#64748B] text-[15px] leading-relaxed mt-4 max-w-xl">Sistem tryout online terintegrasi dengan dashboard monitoring untuk orang tua. Lihat nilai, progress belajar, dan ranking anak secara real-time — kapan pun, di mana pun.</p>
                <div class="flex flex-wrap gap-3 mt-7">
                    <a href="#fitur" class="inline-flex items-center justify-center bg-[#1E3A8A] text-white text-sm font-semibold px-6 py-3 rounded-xl hover:bg-[#1e2e6b] transition">Mulai Sekarang →</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] text-[#0F172A] text-sm font-semibold px-6 py-3 rounded-xl hover:bg-[#F8FAFC] transition">Masuk</a>
                </div>
                <div class="flex items-center gap-6 mt-8 text-xs text-[#64748B]">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#22C55E]"></span> 4 Role terintegrasi</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#3B82F6]"></span> PWA & Mobile Ready</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span> Realtime Notifikasi</span>
                </div>
            </div>
            {{-- Ilustrasi kanan --}}
            <div class="relative">
                <div class="bg-white border border-[#E2E8F0] rounded-[20px] p-6 shadow-sm overflow-hidden">
                    {{-- Mock dashboard preview --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-[#1E3A8A] grid place-items-center text-white text-xs font-bold">AP</span>
                            <div><p class="text-xs font-semibold text-[#0F172A]">Dashboard Orang Tua</p><p class="text-[11px] text-[#64748B]">Kayla Putri • Kelas 9A</p></div>
                        </div>
                        <span class="text-[11px] font-semibold bg-[#22C55E]/10 text-[#22C55E] px-2.5 py-1 rounded-full">● Online</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 mt-5">
                        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3"><p class="text-[11px] text-[#64748B]">Nilai Terakhir</p><p class="text-lg font-bold text-[#1E3A8A] mt-1">88</p><p class="text-[11px] text-[#22C55E]">↑ +2.4%</p></div>
                        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3"><p class="text-[11px] text-[#64748B]">Ranking</p><p class="text-lg font-bold text-[#0F172A] mt-1">#3</p><p class="text-[11px] text-[#64748B]">dari 32 siswa</p></div>
                        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3"><p class="text-[11px] text-[#64748B]">Progress</p><p class="text-lg font-bold text-[#0F172A] mt-1">78%</p><div class="h-1.5 bg-[#E2E8F0] rounded-full mt-2 overflow-hidden"><div class="h-full bg-[#1E3A8A] rounded-full" style="width:78%"></div></div></div>
                    </div>
                    <div class="mt-5 bg-[#1E3A8A] rounded-xl p-4 text-white">
                        <p class="text-xs opacity-80">Notifikasi terbaru</p>
                        <p class="text-sm font-semibold mt-1">🔔 Nilai tryout anak Anda: 88</p>
                        <p class="text-xs opacity-80 mt-1">Kayla menyelesaikan Tryout Matematika 5 dengan nilai 88 — naik 2 poin!</p>
                    </div>
                </div>
                {{-- decor --}}
                <div class="hidden lg:block absolute -z-10 -top-6 -right-6 w-32 h-32 bg-[#DBEAFE] rounded-full blur-2xl opacity-60"></div>
                <div class="hidden lg:block absolute -z-10 -bottom-6 -left-6 w-40 h-40 bg-[#1E3A8A]/10 rounded-full blur-2xl"></div>
            </div>
        </div>
    </section>

    {{-- FITUR --}}
    <section id="fitur" class="bg-white border-y border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="max-w-2xl">
                <h2 class="font-heading font-bold text-2xl lg:text-3xl text-[#0F172A]">Fitur Utama</h2>
                <p class="text-[#64748B] text-sm mt-3">Dirancang untuk guru, siswa, dan orang tua — semua dalam satu sistem yang terhubung.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-8">
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-5">
                    <div class="w-10 h-10 rounded-xl bg-[#1E3A8A] grid place-items-center text-white text-lg">📝</div>
                    <h3 class="font-semibold text-[#0F172A] mt-4">Tryout Online</h3>
                    <p class="text-sm text-[#64748B] mt-2 leading-relaxed">Kerjakan tryout dengan timer, autosave, dan navigasi soal yang nyaman di HP maupun laptop.</p>
                </div>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-5">
                    <div class="w-10 h-10 rounded-xl bg-[#3B82F6] grid place-items-center text-white text-lg">👨‍👩‍👧</div>
                    <h3 class="font-semibold text-[#0F172A] mt-4">Monitoring Orang Tua</h3>
                    <p class="text-sm text-[#64748B] mt-2 leading-relaxed">Pantau nilai, grafik perkembangan, dan progress per mata pelajaran anak secara real-time.</p>
                </div>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-5">
                    <div class="w-10 h-10 rounded-xl bg-[#22C55E] grid place-items-center text-white text-lg">📊</div>
                    <h3 class="font-semibold text-[#0F172A] mt-4">Analisis Nilai</h3>
                    <p class="text-sm text-[#64748B] mt-2 leading-relaxed">Analisis otomatis: tinggi, sedang, perlu perhatian — plus rekomendasi belajar personal.</p>
                </div>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-5">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B] grid place-items-center text-white text-lg">🏆</div>
                    <h3 class="font-semibold text-[#0F172A] mt-4">Ranking & Notifikasi</h3>
                    <p class="text-sm text-[#64748B] mt-2 leading-relaxed">Ranking per tryout dan notifikasi otomatis saat nilai turun, naik, atau tryout baru tersedia.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- UNTUK SIAPA --}}
    <section id="untuk-siapa" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="font-heading font-bold text-2xl lg:text-3xl text-[#0F172A]">Untuk Siapa?</h2>
            <p class="text-[#64748B] text-sm mt-3">Empat peran, satu ekosistem belajar yang saling terhubung.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-8">
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-[#1E3A8A] text-white grid place-items-center mx-auto text-xl">🛡️</div>
                <h3 class="font-semibold text-[#0F172A] mt-4">Admin</h3>
                <p class="text-sm text-[#64748B] mt-1">Kelola kelas, mapel, guru, siswa & orang tua.</p>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-[#DBEAFE] text-[#1E3A8A] grid place-items-center mx-auto text-xl">👩‍🏫</div>
                <h3 class="font-semibold text-[#0F172A] mt-4">Guru</h3>
                <p class="text-sm text-[#64748B] mt-1">Buat bank soal, tryout, dan lihat analitik kelas.</p>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-[#22C55E]/10 text-[#22C55E] grid place-items-center mx-auto text-xl">🎓</div>
                <h3 class="font-semibold text-[#0F172A] mt-4">Siswa</h3>
                <p class="text-sm text-[#64748B] mt-1">Kerjakan tryout, lihat hasil & ranking.</p>
            </div>
            <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-[#F59E0B]/10 text-[#F59E0B] grid place-items-center mx-auto text-xl">👨‍👩‍👧‍👦</div>
                <h3 class="font-semibold text-[#0F172A] mt-4">Orang Tua</h3>
                <p class="text-sm text-[#64748B] mt-1">Monitoring nilai & progress anak realtime.</p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-[#1E3A8A] rounded-[20px] p-8 lg:p-10 text-white overflow-hidden relative">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
            <div class="relative flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <h3 class="font-heading font-bold text-xl lg:text-2xl">Siap memantau progress belajar?</h3>
                    <p class="text-white/80 text-sm mt-2 max-w-xl">Masuk sebagai siswa, orang tua, atau guru. Admin telah menyiapkan akun untuk Anda.</p>
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('login') }}" class="bg-white text-[#1E3A8A] font-semibold text-sm px-6 py-3 rounded-xl hover:bg-[#F8FAFC]">Masuk Sekarang</a>
                    <a href="#fitur" class="bg-white/10 border border-white/20 text-white font-semibold text-sm px-6 py-3 rounded-xl hover:bg-white/15">Lihat Fitur</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-[#E2E8F0] bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
            <p class="text-[#64748B]">© {{ date('Y') }} AkademikPro — Sistem Akademik Tryout Online.</p>
            <div class="flex items-center gap-4 text-[#64748B]">
                <a href="#fitur" class="hover:text-[#0F172A]">Fitur</a>
                <a href="{{ route('login') }}" class="hover:text-[#0F172A]">Masuk</a>
                <span class="hidden sm:inline text-[#E2E8F0]">|</span>
                <span class="text-xs">PWA Ready • Mobile 390px</span>
            </div>
        </div>
    </footer>

    <script>if('serviceWorker' in navigator){window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));}</script>
</body>
</html>
