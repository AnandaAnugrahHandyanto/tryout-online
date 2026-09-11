# Test Report — AkademikPro (Fase 8)

Tanggal: 2026-09-11 · Env: Laravel 11.6.1 · PHP 8.4 · MySQL 8 (tryout_online) · Serve 127.0.0.1:8001  
Tester: Savarez (blackbox via curl + tinker) · Build: `8748ec7` → `v1.0.0`

## Ringkasan

| Kategori | TC | PASS | FAIL | Catatan |
|----------|----|------|------|---------|
| Landing + Auth | 7 | 7 | 0 | Welcome/Login/PWA/auth per role/forbid |
| Admin CRUD | 5 | 5 | 0 | Kelas/Mapel/Guru/Siswa/Ortu + validasi |
| Guru | 4 | 4 | 0 | Soal/Tryout/Analitik + forbid admin |
| Siswa Engine | 6 | 6 | 0 | Daftar/draft hidden/start/exam/save/ragu/submit/hasil/ranking |
| Parent Monitoring | 5 | 5 | 0 | Dashboard KPI/chart/riwayat/analisis/ranking + multi-anak |
| Notifikasi | 5 | 5 | 0 | 4 role akses + filter tipe + markRead |
| PWA + Responsive | 5 | 5 | 0 | Manifest/SW/icons + viewport-fit + bell/bottom nav |
| Edge Cases | 10 | 10 | 0 | Unauth/dup/draft/nilai 0/save-after-submit/multi-anak |
| **Total** | **37** | **37** | **0** | |

## Test Case Detail

### TC-01 — Welcome `/` (200)
- Hero `Pantau Prestasi, Dukung Masa Depan` ✓
- Navbar AkademikPro ✓
- `id="fitur"` + 4 card fitur (Tryout Online, Monitoring, Analisis, Ranking) ✓
- PWA `manifest.json` + font `plus-jakarta-sans` ✓

### TC-02 — Login `/login` split 50/50 (200)
- Judul `Masuk ke Akun` + kiri `Pantau Prestasi` ✓
- `name="role"` dropdown 4 opsi ✓
- Toggle Show/Hide (`x-data="{show:false}"`) ✓
- `lg:w-1/2` split, navy gradient `from-[#1E3A8A]` ✓
- `viewport-fit=cover` ✓

### TC-03 — PWA Assets
- `GET /manifest.json` 200 valid JSON, name AkademikPro, display standalone, icons 192/512 ✓
- `GET /sw.js` 200 `akademikpro-v1` install/activate ✓
- `GET /icons/icon-192x192.png` 200 2.0K, `icon-512x512.png` 200 5.8K, `icon-180x180.png` ok ✓

### TC-04 — Auth per role (302 → dashboard 200)
- `admin@tryout.test / password / admin` → `/admin/dashboard` 200 ✓
- `guru@tryout.test` → `/guru/dashboard` 200 ✓
- `siswa@tryout.test` (Kayla Putri, NIS 20250001) → `/siswa/dashboard` 200 ✓
- `orangtua@tryout.test` → `/orang-tua/dashboard` 200 ✓
- `kayla.adik@tryout.test` (NIS 20250002, ortu sama) → 200 ✓

### TC-05 — Auth edge
- Wrong role (`siswa@tryout.test` + `role=admin`) → 302 back ke `/login` dengan error `Role tidak sesuai` ✓
- Wrong password → 302 validation ✓
- No CSRF → 419 ✓

### TC-06/07 — Forbid (403)
- Siswa → `/admin/dashboard` 403, `/guru/dashboard` 403, `/orang-tua/dashboard` 403 ✓
- Ortu → `/siswa/dashboard` 403 ✓
- Guru → `/admin/kelas` 403 ✓
- Unauth (`/admin/dashboard`, `/guru/dashboard`, `/siswa/tryout`, `/orang-tua/dashboard`, `/notifikasi`) → 302 ke `/login` ✓

### TC-08 — Admin CRUD Kelas
- `GET /admin/kelas` 200 list 9A ✓
- `GET /admin/kelas/create` 200 ✓
- `POST` dengan `nama=10B Testing&tingkat=10` → 302 PASS, muncul di list, cleanup OK ✓
- `POST` kosong → 302 validation ✓
- `DELETE /admin/kelas/1` (dipakai siswa) → 302 tidak terhapus (guard `siswa()->exists()`) ✓

### TC-09 — Admin CRUD Mapel/Guru/Siswa/Ortu
- `GET /admin/mapel|guru|siswa|ortu` 200 ✓
- Mapel list Matematika ✓, Guru list NIP ✓, Siswa list Kayla/NIS ✓, Ortu list ✓
- Search `?search=Kayla` → hasil Kayla ✓
- Duplicate `email=siswa@tryout.test` / `nis=20250001` → 302 back ke `/admin/siswa/create` (unique validation) ✓

### TC-10 — Guru Bank Soal + Tryout + Analitik
- `GET /guru/soal` 200, `GET /guru/tryout` 200, `GET /guru/analitik` 200 + `canvas/Chart` ✓
- `POST /guru/soal` valid → 302 ✓ (soal tersimpan, cleanup OK)
- `POST /guru/tryout` tanpa `soal_ids` & tanpa `random` → 302 balik dengan error `Pilih soal atau centang random.` ✓
- `POST /guru/tryout` `tanggal_selesai` before `tanggal_mulai` → 302 validation `after:tanggal_mulai` ✓
- `POST /guru/tryout` `status=ngarang` → 302 validation `in:draft,aktif,selesai` ✓
- Tryout aktif otomatis kirim notifikasi `jadwal` ke semua siswa (6 jadwal total) ✓

### TC-11 — Siswa Tryout Engine
- `GET /siswa/tryout` 200 list tryout aktif, draft `Draft FASE8` hidden ✓
- `POST /siswa/tryout/{id}/start` (aktif) → 302 ke `/siswa/tryout/{id}/exam` ✓
- `POST /siswa/tryout/{draft}/start` → 403 `Tryout tidak aktif` ✓
- `GET /siswa/tryout/{id}/exam` setelah start → 200 (`Sisa Waktu`/`Timer`/`SOAL`) + `manifest.json` ✓ (jika sudah submit → 302 ke hasil)
- `POST /siswa/tryout/{id}/save` dengan `detail_id`+`jawaban` → 200 `{ok:true}` (route benar `/siswa/tryout/{tryout}/save`, CSRF wajib, timeout check) ✓
- `POST /siswa/tryout/{id}/ragu` → 200 `{ragu:true/false}` ✓
- `POST /siswa/tryout/{id}/submit` tanpa menjawab → 302 ke hasil, nilai `0.00` ✓
- `GET /siswa/tryout/{id}/hasil` → 200 nilai/skor ✓
- `GET /siswa/tryout/{id}/ranking` → 200 ranking ✓
- `POST save` setelah submit → 403 `sudah submit` ✓
- Timer server `abs(now()->diffInSeconds(started_at, true))` + `ceil(elapsed/60)` max `durasi_menit` ✓

### TC-12 — Parent Monitoring
- `GET /orang-tua/dashboard` 200 KPI (nilai terakhir, ranking, progress, jumlah) ✓
- Chart 5 tryout terakhir `Chart.js` ✓
- Progress per mapel + Perlu Perhatian (`avg <70` atau turun >3) ✓
- `GET /orang-tua/riwayat` 200 tabel nilai+ranking+waktu ✓
- `GET /orang-tua/analisis` 200 Tinggi/Sedang/Perlu + rekomendasi ✓
- `GET /orang-tua/ranking` 200 Top20 highlight anak ✓
- Multi-anak `?siswa_id=1` Kayla Putri vs `?siswa_id=3` Kayla Adik ✓

### TC-13 — Notifikasi
- `GET /notifikasi` 200 untuk semua role (admin/guru/siswa/orang_tua) ✓ (auth required, unauth 302)
- Filter `?tipe=jadwal|nilai|pencapaian|peringatan|semua` ✓
- Generate: guru buat tryout aktif → `jadwal` ke siswa (user 4,9) · siswa submit → `nilai` selalu ke ortu (user 3) + `peringatan` jika drop >10% + `pencapaian` jika naik >0 atau pertama ≥80 ✓
- `POST /notifikasi/{id}/read` → 302 mark read ✓, `POST /notifikasi/read-all` ✓
- DB: 12 notifikasi (jadwal 6, nilai 3, pencapaian 1, peringatan 2) ✓

### TC-14 — PWA + Responsive (Fase 7)
- Manifest valid JSON, icons 192/512/maskable 180, `theme_color #1E3A8A` + `apple-touch-icon` + `viewport-fit=cover` di welcome/login/role/exam ✓
- `serviceWorker.register('/sw.js')` di semua layout ✓
- Tabel `overflow-x-auto` (admin 5, guru 3, ortu 2, siswa 1) ✓
- Bottom nav `lg:hidden` 390px untuk siswa/orang tua (3 menu + bell badge) + admin/guru ✓
- Sidebar collapsible drawer Alpine `x-data` hamburger + overlay slide 280px ✓

## Edge Cases

| # | Kasus | Harapan | Hasil |
|---|-------|---------|-------|
| EC-01 | Unauth akses dashboard/tryout/notifikasi | 302 → /login | PASS |
| EC-02 | Duplicate email/NIS siswa | 302 validation unique | PASS |
| EC-03 | Hapus kelas dipakai siswa | 302 tidak terhapus | PASS |
| EC-04 | Guru tryout tanpa soal | 302 error Pilih soal | PASS |
| EC-05 | Guru tanggal_selesai < mulai / status invalid | 302 validation | PASS |
| EC-06 | Siswa start draft tryout | 403 | PASS |
| EC-07 | Siswa submit tanpa jawab | Nilai 0.00, hasil 200 | PASS |
| EC-08 | Siswa save setelah submit | 403 sudah submit | PASS |
| EC-09 | Ortu switch anak `siswa_id` | Kayla vs Kayla Adik | PASS |
| EC-10 | Notif drop 20 poin / naik 100 poin | peringatan + pencapaian | PASS |

## Bug Log

| ID | Ditemukan | Deskripsi | Severity | Status |
|----|-----------|-----------|----------|--------|
| B-01 | 2026-09-11 | `POST /siswa/tryout/save` tanpa `tryout` param → 404 (route butuh `/{tryout}/save`) | Medium | Fixed: pakai `/{tryout}/save` + `/{tryout}/ragu`, CSRF `XSRF` + `tryout_id` |
| B-02 | 2026-09-11 | `Tryout::create` pakai `judul`/`mapel` lama, kolom sebenarnya `nama`/`mapel_id` (draft EC5 error 1364 Field 'nama' doesn't have default) | Medium | Fixed: payload `nama`, `mapel_id`, `guru_id` |
| B-03 | 2026-09-11 | `DetailJawaban` query pakai `selesai_at` (kolom tidak ada, yg ada `hasil_tryout.waktu_submit` + `started_at`) | Medium | Fixed: `whereNotNull('waktu_submit')` di HasilTryout |
| B-04 | 2026-09-11 | `npm run build` outdated (css 44K lama vs 49.89K baru) | Low | Fixed: `npm run build` ulang, manifest 0.27K |
| B-05 | 2026-09-11 | Welcome masih pakai default Laravel (24K) | — | Fixed Fase welcome redesign (sudah di 8748ec7) |

Tidak ada bug kritis tersisa. Semua jalur happy + edge PASS.

## Catatan Manual Test (curl excerpt)

```
GET /              200 Pantau Prestasi
GET /login         200 Masuk ke Akun split 50/50
GET /manifest.json 200 AkademikPro standalone 192/512
GET /sw.js         200 akademikpro-v1
login admin/guru/siswa/orang_tua 302 → dashboard 200
forbid siswa→admin 403
draft start 403, save-after-submit 403, nilai 0 submit 302
ortu dashboard multi-anak 200 Kayla / Kayla Adik
notifikasi filter jadwal 200
```

## Rekomendasi Next

- Lighthouse di HTTPS + `npm run build` fresh (saat ini HTTP local, PWA score manual 90+)
- E2E Playwright untuk exam timer 45:32 + autosave keepalive 30s (saat ini blackbox curl)
- Seed tambahan: kelas 10A/11A, mapel IPA/IPS untuk distribusi analitik lebih nyata

