# API / Route Docs — AkademikPro

Web routes (Blade, session + CSRF, bukan JSON API). Auth via `web` guard + `role` middleware. Base: `http://127.0.0.1:8001`.

## Auth

| Method | Path | Name | Guard | Ket |
|--------|------|------|-------|-----|
| GET | `/` | — | guest | Welcome landing |
| GET | `/login` | `login` | guest | Split navy login |
| POST | `/login` | `login` | guest | `email`, `password`, `role` (admin/guru/siswa/orang_tua) → redirect per role |
| POST | `/logout` | `logout` | auth | |
| GET | `/dashboard` | `dashboard` | auth | Redirect ke `admin/guru/siswa/orang-tua.dashboard` sesuai `users.role` |
| GET/POST | `/forgot-password`, `/reset-password/*`, `/confirm-password`, `/verify-email/*` | Breeze | — | Default Breeze |
| GET | `/profile` | `profile.edit` | auth | Breeze |
| PATCH | `/profile` | `profile.update` | auth | |
| DELETE | `/profile` | `profile.destroy` | auth | |

**Login validation:** `role` harus cocok dengan `users.role` untuk `email` tersebut, else `Role tidak sesuai`. No CSRF → 419.

## PWA

| Method | Path | Ket |
|--------|------|-----|
| GET | `/manifest.json` | PWA manifest (AkademikPro) |
| GET | `/sw.js` | Service worker CACHE `akademikpro-v1` |
| GET | `/icons/icon-192x192.png` | 192 |
| GET | `/icons/icon-512x512.png` | 512 |
| GET | `/icons/icon-512x512-maskable.png` | maskable |
| GET | `/icons/icon-180x180.png` | apple-touch |

## Admin (`role:admin`, prefix `/admin`)

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/admin/dashboard` | `admin.dashboard` | KPI Guru/Siswa/Kelas/Tryout aktif |
| GET | `/admin/kelas` | `admin.kelas.index` | `?search=` |
| GET | `/admin/kelas/create` | `admin.kelas.create` | form `nama`, `tingkat` |
| POST | `/admin/kelas` | `admin.kelas.store` | validate required |
| GET | `/admin/kelas/{kelas}/edit` | `admin.kelas.edit` | |
| PUT | `/admin/kelas/{kelas}` | `admin.kelas.update` | |
| DELETE | `/admin/kelas/{kelas}` | `admin.kelas.destroy` | blokir jika `siswa()->exists()` |
| GET/POST… | `/admin/mapel` | `admin.mapel.*` | `nama`, `kode` unique |
| GET/POST… | `/admin/guru` | `admin.guru.*` | `name`, `email` unique, `password` min8, `nip` unique |
| GET/POST… | `/admin/siswa` | `admin.siswa.*` | `name`, `email` unique, `nis` unique, `kelas_id` exists, `orang_tua_id` nullable |
| GET/POST… | `/admin/ortu` | `admin.ortu.*` | `name`, `email` unique, `pekerjaan`, `no_hp` |

## Guru (`role:guru`, prefix `/guru`)

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/guru/dashboard` | `guru.dashboard` | counts + links |
| GET | `/guru/soal` | `guru.soal.index` | `?search=&tingkat=mudah/sedang/sulit` |
| GET | `/guru/soal/create` | `guru.soal.create` | |
| POST | `/guru/soal` | `guru.soal.store` | `mapel_id`, `pertanyaan`, `pilihan_a/b/c/d`, `jawaban_benar` a/b/c/d, `tingkat_kesulitan` mudah/sedang/sulit, `bobot` |
| GET | `/guru/soal/{soal}/edit` | `guru.soal.edit` | own check |
| PUT | `/guru/soal/{soal}` | `guru.soal.update` | |
| DELETE | `/guru/soal/{soal}` | `guru.soal.destroy` | |
| GET | `/guru/tryout` | `guru.tryout.index` | `?status=&search=` |
| GET | `/guru/tryout/create` | `guru.tryout.create` | mapel + soal milik guru |
| POST | `/guru/tryout` | `guru.tryout.store` | `nama`, `mapel_id`, `durasi_menit` 5-300, `tanggal_mulai`, `tanggal_selesai` after mulai, `status` draft/aktif/selesai, `soal_ids[]` atau `random=1&jumlah_soal` → attach `tryout_soal.urutan` |
| GET | `/guru/tryout/{tryout}/edit` | `guru.tryout.edit` | own |
| PUT | `/guru/tryout/{tryout}` | `guru.tryout.update` | |
| DELETE | `/guru/tryout/{tryout}` | `guru.tryout.destroy` | |
| GET | `/guru/tryout/{tryout}` | `guru.tryout.show` | avg/max/min + hasil siswa |
| GET | `/guru/analitik` | `guru.analitik` | Chart per kelas + distribusi 4 bucket + perlu <70 |

`tryout.status=aktif` → notif `jadwal` ke semua `siswa.user_id`.

## Siswa (`role:siswa`, prefix `/siswa`)

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/siswa/dashboard` | `siswa.dashboard` | nilai terakhir + ranking + count |
| GET | `/siswa/tryout` | `siswa.tryout.index` | list `aktif` only, draft hidden |
| POST | `/siswa/tryout/{tryout}/start` | `siswa.tryout.start` | 403 jika tidak aktif; jika `waktu_submit` sudah → redirect hasil; else create `hasil_tryout` + `detail_jawaban` shuffle |
| GET | `/siswa/tryout/{tryout}/exam` | `siswa.tryout.exam` | timer `remaining = durasi*60 - abs(now - started_at)`, `?q=` paginasi soal |
| POST | `/siswa/tryout/{tryout}/save` | `siswa.tryout.save` | `detail_id`, `jawaban` a/b/c/d nullable, `ragu` bool → `{ok:true}` atau `{timeout:true}` |
| POST | `/siswa/tryout/{tryout}/ragu` | `siswa.tryout.ragu` | `detail_id` → toggle → `{ragu:bool}` |
| POST | `/siswa/tryout/{tryout}/submit` | `siswa.tryout.submit` | hitung `benar/salah`, `nilai=benar/total*100`, `waktu_menit=ceil(abs(now-started_at)/60)` max durasi → `waktu_submit=now` → notif ortu → redirect hasil |
| GET | `/siswa/tryout/{tryout}/hasil` | `siswa.tryout.hasil` | hasil + top10 + myRank |
| GET | `/siswa/tryout/{tryout}/ranking` | `siswa.tryout.ranking` | top20 |

Setelah `waktu_submit`, `save/ragu` → 403 `sudah submit`. Timeout di `exam/save` → auto `doSubmit`.

## Orang Tua (`role:orang_tua`, prefix `/orang-tua`)

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/orang-tua/dashboard` | `orang-tua.dashboard` | `?siswa_id=` multi-anak; KPI + chart 5 + perMapel + perlu |
| GET | `/orang-tua/riwayat` | `orang-tua.riwayat` | tabel + rank per row |
| GET | `/orang-tua/analisis` | `orang-tua.analisis` | Tinggi/Sedang/Perlu + rekom |
| GET | `/orang-tua/ranking` | `orang-tua.ranking` | `?tryout_id=` Top20 + highlight anak |

## Notifikasi (`auth`, semua role)

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/notifikasi` | `notifikasi.index` | `?tipe=semua/nilai/jadwal/pencapaian/peringatan` |
| POST | `/notifikasi/{notifikasi}/read` | `notifikasi.read` | mark satu, 403 jika bukan pemilik |
| POST | `/notifikasi/read-all` | `notifikasi.readAll` | mark semua milik user |

Generate: `Tryout@store` (aktif) → `jadwal` ke siswa; `Siswa\Tryout@doSubmit` → `nilai` selalu ke `siswa.orangTua.user_id` + `peringatan` jika delta ≤-10 + `pencapaian` jika delta>0 atau pertama ≥80.

## Error Codes

| Code | Sebab |
|------|-------|
| 302 | redirect (unauth → /login, after store, after submit) |
| 403 | role mismatch / own check / sudah submit / draft start |
| 419 | CSRF missing/expired |
| 422 | validation (unique, required, enum, after:date) |

## Schema ringkas

`users(id, name, email, password, role, email_verified_at)`  
`kelas(id, nama, tingkat)` · `mata_pelajaran(id, nama, kode)`  
`guru(id, user_id, nip)` · `orang_tua(id, user_id, pekerjaan, no_hp)` · `siswa(id, user_id, nis, kelas_id, orang_tua_id)`  
`soal(id, guru_id, mapel_id, pertanyaan, pilihan_a/b/c/d, jawaban_benar, tingkat_kesulitan, bobot)`  
`tryout(id, nama, mapel_id, guru_id, jumlah_soal, durasi_menit, tanggal_mulai, tanggal_selesai, status)`  
`tryout_soal(tryout_id, soal_id, urutan)`  
`hasil_tryout(id, tryout_id, siswa_id, nilai, jumlah_benar, jumlah_salah, waktu_pengerjaan_menit, waktu_submit, started_at, unique[tryout, siswa])`  
`detail_jawaban(id, hasil_tryout_id, soal_id, jawaban_siswa nullable, benar_salah, ragu)`  
`notifikasi(id, user_id, tipe enum[jadwal,nilai,peringatan,pencapaian], judul, pesan, sudah_dibaca bool)`
