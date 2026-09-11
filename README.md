# AkademikPro — Sistem Akademik Tryout Online + Parent Monitoring

Sistem tryout online untuk SMP/SMA dengan monitoring orang tua real-time. Siswa mengerjakan tryout berbasis timer server; orang tua memantau nilai, ranking, progress, dan notifikasi otomatis. Dibangun untuk laporan KP — 4 role terpisah dengan middleware `role`.

> Figma: https://www.figma.com/design/RtAPoCo6CYjIXV5T6p4dmJ/Sistem-Akademik-Tryout-Online---Parent-Monitoring-Dashboard  
> Live local: http://127.0.0.1:8001

## Fitur Utama

- **Autentikasi role-aware** — login dengan `email + password + role`; redirect per role (`/admin/dashboard`, `/guru/dashboard`, `/siswa/dashboard`, `/orang-tua/dashboard`); `403` jika akses lintas role; register `404`.
- **Master Data (Admin)** — CRUD Kelas, Mata Pelajaran, Guru, Siswa, Orang Tua; validasi unique, guard hapus kelas yang masih dipakai siswa.
- **Bank Soal (Guru)** — CRUD soal 4 pilihan (`a/b/c/d`), `tingkat_kesulitan` mudah/sedang/sulit, filter & search, ownership per guru.
- **Tryout Management (Guru)** — create tryout dengan pilih soal manual atau acak (`random`), `draft/aktif/selesai`, `durasi_menit` 5–300, validasi tanggal, broadcast notifikasi `jadwal` ke semua siswa saat `aktif` (juga saat `PUT draft→aktif`).
- **Tryout Engine (Siswa)** — daftar tryout aktif (window tanggal), `start` → `exam` dengan timer server (`started_at`), `save` autosave (keepalive 30s), `ragu` toggle, navigasi 1..N, `submit` scoring `nilai = benar/total*100`, cegah kerjakan 2x, auto-submit saat timeout.
- **Hasil & Ranking (Siswa)** — halaman hasil (skor, benar/salah, waktu, ranking), ranking Top 20 `nilai DESC, waktu ASC`, highlight siswa login.
- **Parent Dashboard (Orang Tua)** — KPI nilai terakhir / ranking / progress delta / jumlah tryout; Chart.js 5 tryout terakhir; progress per mapel; card Perlu Perhatian (`avg<70` atau drop >10%); riwayat, analisis (Tinggi ≥80 / Sedang ≥65 / Perlu), ranking Top 20; selector multi-anak `?siswa_id=`.
- **Notifikasi** — `jadwal` (ke siswa), `nilai`/`peringatan` (drop >10%)/`pencapaian` (naik/pertama ≥80) ke orang tua; halaman `/notifikasi` filter `?tipe=` + mark read; badge bell header & bottom nav.
- **Analitik Guru** — rata nilai per kelas (Bar) + distribusi 0–50/50–70/70–85/85–100 (Doughnut) + tabel Perlu Evaluasi (<70).
- **PWA + Responsive** — `manifest.json` + `sw.js` (`akademikpro-v1`), icons 192/512/maskable/180, installable; layout `layouts/role` sidebar 240px + drawer 280px + bottom nav 390px.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 11.6.1, PHP 8.4, Blade |
| Auth | Laravel Breeze 2.4 (Blade + Vite) |
| DB | MySQL 8 / MariaDB 11.8 (`tryout_online`), `tryout_online_testing` untuk phpunit |
| Frontend | Tailwind CSS 3, Vite 5, Alpine.js, Chart.js 4 |
| PWA | `public/manifest.json` (standalone, theme `#1E3A8A`), `public/sw.js` network-first |
| Design | Figma RtAPoCo6CYjIXV5T6p4dmJ — Navy `#1E3A8A` / Blue `#3B82F6` / Surface `#F8FAFC` — Plus Jakarta Sans + Inter |
| Tooling | Composer, npm, PHPUnit 11 |

## Cara Install di Lokal

```bash
git clone <repo-url> tryout-online
cd tryout-online
cp .env.example .env
php artisan key:generate

# MySQL — edit .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tryout_online
# DB_USERNAME=root
# DB_PASSWORD=...

mysql -u root -p -e "CREATE DATABASE tryout_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE DATABASE tryout_online_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
npm install
npm run build   # atau npm run dev untuk HMR

php artisan serve --host=127.0.0.1 --port=8001
# buka http://127.0.0.1:8001
```

> VPS note: PHP 8.4 tanpa `pdo_sqlite` tetap jalan (MySQL only). Wrapper `~/bin/php` hanya untuk migrate SQLite lokal jika diperlukan.

## Cara Running

```bash
php artisan serve --host=127.0.0.1 --port=8001
npm run dev          # watch Tailwind/Vite
php artisan test     # 25 passed (auth + profile)
php artisan route:list
```

## Kredensial Default (seed)

Password semua: `password`

| Role | Email | Nama | Catatan |
|------|-------|------|---------|
| Admin | `admin@tryout.test` | Admin | — |
| Guru | `guru@tryout.test` | Kayla Guru | NIP 198001012000011001 |
| Siswa | `siswa@tryout.test` | Kayla Putri | NIS 20250001 · Kelas 9A |
| Siswa 2 | `kayla.adik@tryout.test` | Kayla Adik | NIS 20250002 · Kelas 9A |
| Orang Tua | `orangtua@tryout.test` | Orang Tua Kayla | 2 anak (selector) |

Login wajib pilih **Role** yang sesuai `users.role` — salah role → error `Role tidak sesuai`.

## Struktur Folder Penting

```
app/Http/Controllers/
  Admin/        KelasController, MataPelajaranController, GuruController, SiswaController, OrangTuaController
  Guru/         SoalController, TryoutController, AnalitikController
  Siswa/        TryoutController (index/start/exam/save/ragu/submit/hasil/ranking)
  OrangTua/     ParentController (dashboard/riwayat/analisis/ranking)
  NotifikasiController, ProfileController, Auth/*
app/Models/     User, Kelas, MataPelajaran, Guru, Siswa, OrangTua, Soal, Tryout, TryoutSoal, HasilTryout, DetailJawaban, Notifikasi
database/migrations/  15 migrasi (users, kelas, guru, mapel, orang_tua, siswa, soal, tryout, tryout_soal, hasil_tryout, detail_jawaban, notifikasi, add_ragu)
resources/views/
  welcome.blade.php              # landing AkademikPro
  auth/login.blade.php           # split 50/50 navy gradient
  layouts/role.blade.php         # sidebar + drawer + bell + bottom nav + PWA meta
  admin/*  guru/*  siswa/*  orang-tua/*  notifikasi/*
public/         manifest.json  sw.js  icons/*  build/*
docs/
  USER-MANUAL-ADMIN.md  USER-MANUAL-GURU.md  USER-MANUAL-SISWA.md  USER-MANUAL-ORANG-TUA.md
  DATABASE-SCHEMA.md  API-DOCS.md  TEST_REPORT.md  USER_MANUAL.md
  testing/blackbox-test-fase-8.md  (56 TC KP)
```

## Dokumentasi

- **User Manual per role:** `docs/USER-MANUAL-*.md`
- **Database Schema + ERD Mermaid:** `docs/DATABASE-SCHEMA.md`
- **API / Route Docs:** `docs/API-DOCS.md`
- **Blackbox KP (56 TC):** `docs/testing/blackbox-test-fase-8.md` — 56 PASS, 1 bug fixed 8B
- **Figma:** https://www.figma.com/design/RtAPoCo6CYjIXV5T6p4dmJ/

## Lisensi

MIT — template Laravel 11.
