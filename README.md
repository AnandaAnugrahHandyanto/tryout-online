# AkademikPro — Sistem Akademik Tryout Online + Parent Monitoring

Sistem tryout online terintegrasi dengan dashboard monitoring orang tua real-time. 4 role: **Admin**, **Guru**, **Siswa**, **Orang Tua**. Responsive 390px, PWA installable, notifikasi otomatis.

> Figma: https://www.figma.com/design/RtAPoCo6CYjIXV5T6p4dmJ/Sistem-Akademik-Tryout-Online---Parent-Monitoring-Dashboard
> Stack: Laravel 11 + MySQL + Tailwind + Vite + Chart.js

## Fitur

| Role | Fitur |
|------|-------|
| **Admin** | CRUD Kelas, Mapel, Guru, Siswa, Orang Tua · Dashboard KPI |
| **Guru** | Bank Soal (CRUD + filter tingkat) · Tryout (manual / random acak) · Analitik per kelas & distribusi nilai · Detail hasil siswa |
| **Siswa** | Daftar tryout aktif · Exam timer server + autosave (keepalive 30s) + ragu · Submit & scoring · Hasil + ranking |
| **Orang Tua** | Dashboard KPI (nilai terakhir, ranking, progress, jumlah) · Chart 5 tryout terakhir · Progress per mapel · Perlu Perhatian · Riwayat · Analisis (Tinggi/Sedang/Perlu + rekomendasi) · Ranking Top 20 · Multi-anak selector |
| **Semua** | Notifikasi (jadwal / nilai / peringatan drop >10% / pencapaian) · Filter + mark read · PWA (manifest + SW) · Bell header + bottom nav mobile |

## Design System

- Navy `#1E3A8A` / Blue `#3B82F6` / Soft `#DBEAFE` / Surface `#F8FAFC` / Border `#E2E8F0` / Text `#0F172A` / Muted `#64748B`
- Success `#22C55E` Warning `#F59E0B` Danger `#EF4444`
- Font heading: **Plus Jakarta Sans**, body: **Inter** (bunny.net)
- Radius 8/12/16, shadow-sm

## Demo Accounts

Password semua: `password`

| Role | Email | Catatan |
|------|-------|---------|
| Admin | `admin@tryout.test` | |
| Guru | `guru@tryout.test` | NIP 198001012000011001 |
| Siswa | `siswa@tryout.test` | Kayla Putri · NIS 20250001 · Kelas 9A |
| Siswa 2 | `kayla.adik@tryout.test` | Kayla Adik · NIS 20250002 · Kelas 9A |
| Orang Tua | `orangtua@tryout.test` | Orang Tua Kayla (2 anak) |

## Quick Start

```bash
git clone <repo>
cd tryout-online
cp .env.example .env
php artisan key:generate

# MySQL (edit .env: DB_DATABASE=tryout_online DB_USERNAME=root DB_PASSWORD=***)
mysql -u root -p -e "CREATE DATABASE tryout_online;"
php artisan migrate --seed

npm install
npm run build   # atau npm run dev
php artisan serve --host=127.0.0.1 --port=8001
```

Buka http://127.0.0.1:8001 — Welcome → Login → pilih Role.

> VPS note: PHP 8.4. Jika `pdo_sqlite` tidak ada, project tetap jalan di MySQL. Wrapper `~/bin/php` hanya untuk migrate local SQLite.

## Struktur

```
app/Http/Controllers/
  Admin/   Kelas, MataPelajaran, Guru, Siswa, OrangTua
  Guru/    Soal, Tryout, Analitik
  Siswa/   Tryout (index/start/exam/save/ragu/submit/hasil/ranking)
  OrangTua/ Parent (dashboard/riwayat/analisis/ranking)
  Notifikasi, Auth/*
resources/views/
  welcome.blade.php          # landing
  auth/login.blade.php       # split 50/50 navy
  layouts/role.blade.php     # sidebar 240 + drawer + bell + bottom nav + PWA
  admin/*  guru/*  siswa/*  orang-tua/*  notifikasi/*
public/
  manifest.json  sw.js  icons/*  build/*
```

## PWA

- `public/manifest.json` — name AkademikPro, display standalone, theme `#1E3A8A`
- `public/sw.js` — CACHE `akademikpro-v1`, stale-while-revalidate + network-first
- Icons 192/512 + maskable + 180 apple
- Test: Chrome → Install app (desktop) atau Add to Home Screen (Android). Lighthouse PWA ≥ 90.

## Testing

Lihat `docs/TEST_REPORT.md` — 13 TC + 10 edge case, semua PASS (2026-09-11).

```bash
# cek route
php artisan route:list
# cek PWA
curl -s http://127.0.0.1:8001/manifest.json | python3 -m json.tool
curl -s http://127.0.0.1:8001/sw.js | head
```

## FASE

- Fase 0: Setup + schema (14 migrasi, 12 model)
- Fase 1: Auth + role middleware (403) + redirect per role
- Fase 2: Master CRUD admin
- Fase 3: Bank soal + tryout guru
- Fase 4: Tryout engine siswa (server timer abs, autosave, scoring)
- Fase 5: Parent monitoring + Chart.js
- Fase 6: Notifikasi + analitik guru
- Fase 7: PWA + responsive polish
- Fase 8: Testing + docs + tag v1.0.0

## Lisensi

MIT. Template Laravel 11.
