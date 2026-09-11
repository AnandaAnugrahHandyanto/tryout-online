# Changelog — AkademikPro

Semua perubahan penting per versi.

## v1.0.0 - 2026-09-11

> Release v1.0.0 — Sistem Akademik Tryout Online + Parent Monitoring Dashboard

### Added
- **Authentication 4 role** — `admin` / `guru` / `siswa` / `orang_tua`; login dengan `email + password + role`; `RoleMiddleware` 403; `RegisteredUserController` 404; `LoginRequest` validasi role; redirect per role (`/admin/dashboard`, `/guru/dashboard`, `/siswa/dashboard`, `/orang-tua/dashboard`); unit test 25 passed.
- **Master data CRUD (Admin)** — Kelas, Mata Pelajaran (`kode` unique), Guru (`nip` unique), Siswa (`nis` unique, `kelas_id`, `orang_tua_id` nullable), Orang Tua (`pekerjaan`, `no_hp`); resource controllers + search + guard `kelas` dipakai siswa.
- **Bank soal + Tryout Management (Guru)** — Soal 4 pilihan `a/b/c/d` + `tingkat_kesulitan`; filter `tingkat` + search; ownership scope; Tryout `draft/aktif/selesai` + `durasi_menit` 5–300 + validasi tanggal + pilih soal manual / `random` + broadcast `jadwal` ke siswa saat `aktif` (fix 8B: `PUT draft→aktif` juga broadcast idempotent).
- **Tryout Engine (Siswa)** — daftar `aktif` window tanggal; `start` → `hasil_tryout` + `started_at`; `exam` timer server `durasi*60 - elapsed` (Carbon `diffInSeconds` absolute); `save` JSON `{"ok":true}` + `ragu` toggle; navigasi 1..N; autosave keepalive 30s + beforeunload; `submit` scoring `nilai=benar/total*100`, `waktu=ceil(abs(now-started_at)/60)` min 1; cegah kerjakan 2x (redirect hasil); auto-submit timeout.
- **Hasil & Ranking (Siswa)** — `hasil` (nilai, benar/salah, waktu, ranking), `ranking` Top 20 `nilai DESC, waktu ASC` highlight self.
- **Parent Monitoring Dashboard** — KPI nilai terakhir / ranking / progress delta / jumlah; Chart.js 5 terakhir; progress per mapel (avg, warna); card Perlu Perhatian (`avg<70` atau drop >10%); `riwayat` + `analisis` (Tinggi ≥80 / Sedang ≥65 / Perlu) + rekomendasi; `ranking` Top 20; selector multi-anak `?siswa_id=` (Kayla 20250001 + Adik 20250002).
- **Notifikasi otomatis** — `jadwal` ke siswa (tryout aktif), `nilai` selalu ke ortu saat submit, `peringatan` drop >10%, `pencapaian` naik/>0 atau pertama ≥80; `/notifikasi` filter `?tipe=` + mark read; badge bell header + bottom nav.
- **Analitik Guru** — rata per kelas (Bar) + distribusi 4 bucket (Doughnut) + tabel Perlu Evaluasi (<70).
- **PWA support** — `manifest.json` (AkademikPro, standalone, theme `#1E3A8A`), `sw.js` CACHE `akademikpro-v1` network-first, icons 192/512/maskable/180, installable; responsive 390px (`layouts/role` sidebar 240 + drawer 280 + bottom nav).
- **Design System** — Figma RtAPoCo6CYjIXV5T6p4dmJ; tokens Navy `#1E3A8A`/Blue `#3B82F6`/Soft `#DBEAFE`/Surface `#F8FAFC`/Border `#E2E8F0`/Text `#0F172A`/Muted `#64748B`; Plus Jakarta Sans + Inter; radius 8/12/16; welcome 183 + login split 50/50 (commit `8748ec7`).
- **Dokumentasi** — `README.md` + `docs/USER-MANUAL-*.md` (4 role) + `DATABASE-SCHEMA.md` (15 tabel + ERD Mermaid) + `API-DOCS.md` (84 routes) + `docs/testing/blackbox-test-fase-8.md` (56 TC PASS, Bug #1 fixed 8B) + `TEST_REPORT.md`.

### Fixed
- `PUT /guru/tryout/{id}` draft→aktif tidak broadcast — fix 8B `TryoutController@update` deteksi `!$wasActive && status==='aktif'` (commit `5b9db92`).
- `detail_jawaban.jawaban_siswa` NOT NULL + `hasil_tryout` tanpa `started_at` — migrasi `add_ragu` (nullable + `ragu` + `started_at`).
- `waktu_pengerjaan_menit` out of range (negatif/0) — `abs` + `ceil/60` min 1.
- `UserFactory` tanpa `role` — default `siswa`; `AuthenticationTest` + `RegistrationTest` 404; `phpunit.xml` MySQL `tryout_online_testing` (commit `f5fcd8d`).
- DB kosong setelah `migrate:fresh` pada 8D — restore `backup/tryout_online_v1.0.sql` + seed Adik 20250002 + tryout 2 (commit 8D).

### Infra
- Laravel 11.6.1 + PHP 8.4 + MySQL 8 (MariaDB 11.8) + Breeze 2.4 + Tailwind 3 + Vite 5 + Chart.js
- Backup: `backup/tryout_online_v1.0.sql` (31K, 14 INSERT, 20 CREATE TABLE)
- Tag: `v1.0.0` annotated — `Release v1.0.0 - Sistem Akademik Tryout Online`
- Build: `public/build` 49.89K css + 107K js

[1.0.0]: https://github.com/AnandaAnugrahHandyanto/tryout-online/releases/tag/v1.0.0
