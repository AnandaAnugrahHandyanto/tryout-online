# User Manual — AkademikPro

## 1. Cara Masuk

1. Buka `/` → klik **Masuk** (navbar) atau `/login`.
2. Isi **Email**, **Password** (`password`), pilih **Role** (Admin/Guru/Siswa/Orang Tua) → **Masuk**.
3. Otomatis redirect ke dashboard sesuai role. Jika role salah → error `Role tidak sesuai`.

> Akun demo tercantum di README & di card Login.

## 2. Admin — `/admin/dashboard`

**Menu:** Dashboard · Kelas · Mapel · Guru · Siswa · Ortu

- **Kelas** (`/admin/kelas`): List + search. Create: `nama` + `tingkat` wajib. Edit/Delete. Delete diblokir jika masih dipakai siswa.
- **Mapel** (`/admin/mapel`): `nama` + `kode` unik.
- **Guru** (`/admin/guru`): `name` + `email` unik + `password` min 8 + `nip` unik. Edit password opsional.
- **Siswa** (`/admin/siswa`): `name` + `email` unik + `password` + `nis` unik + `kelas_id` wajib + `orang_tua_id` opsional (link ke ortu).
- **Orang Tua** (`/admin/ortu`): `name` + `email` unik + `password` + `pekerjaan` + `no_hp`.

Tips: Gunakan search di tiap list. Validasi duplicate langsung balik ke form dengan error.

## 3. Guru — `/guru/dashboard`

**Menu:** Dashboard · Bank Soal · Tryout · Analitik · Notifikasi (bell header)

- **Bank Soal** (`/guru/soal`): Filter `tingkat` (mudah/sedang/sulit) + search. Field: `mapel_id`, `pertanyaan`, `pilihan_a/b/c/d`, `jawaban_benar` (a/b/c/d), `tingkat_kesulitan`, `bobot`. Hanya soal milik guru login yang tampil.
- **Tryout** (`/guru/tryout`):
  - Create: `nama` + `mapel_id` + `status` (draft/aktif/selesai) + `durasi_menit` 5–300 + `tanggal_mulai`/`tanggal_selesai` (selesai harus after mulai) + pilih soal manual (centang) **atau** centang `Acak otomatis` + isi `jumlah_soal`.
  - Jika `Pilih soal` kosong dan `random` off → error `Pilih soal atau centang random.`
  - Edit: bisa ganti meta + pilih ulang soal (detach+attach).
  - Jika `status=aktif` → otomatis kirim notifikasi `jadwal` ke semua siswa.
  - Show (`/guru/tryout/{id}`): detail soal + hasil siswa (avg/max/min) + ranking submit.
- **Analitik** (`/guru/analitik`): Chart rata per kelas (Bar) + distribusi 0–50/50–70/70–85/85–100 (Doughnut) + tabel Perlu Evaluasi (nilai <70). Data dari `hasil_tryout` whereNotNull `waktu_submit`.
- **Forbid:** Guru tidak bisa buka `/admin/*` → 403.

## 4. Siswa — `/siswa/dashboard`

**Menu:** Dashboard · Tryout · Hasil/Ranking · Notifikasi

- **Dashboard:** Card tryout aktif terbaru + nilai terakhir + ranking terakhir + jumlah tryout selesai.
- **Daftar Tryout** (`/siswa/tryout`): List tryout `aktif` (tanggal_mulai <= now <= tanggal_selesai). Draft tidak muncul. Status per row:
  - Belum mulai → tombol **MULAI TRYOUT** (POST `/siswa/tryout/{id}/start`).
  - Sedang dikerjakan → **Lanjutkan** → `/siswa/tryout/{id}/exam`.
  - Selesai → **Lihat Hasil** + **Ranking**.
- **Exam** (`/siswa/tryout/{id}/exam`): 
  - Timer server `Sisa Waktu` (hitung dari `started_at`, `durasi_menit*60 - elapsed`).
  - Navigasi nomor soal, jawaban radio a/b/c/d, **Ragu** toggle, **Simpan** autosave (POST `/{tryout}/save` + `detail_id`+`jawaban`).
  - Jika timeout → auto `doSubmit` → redirect hasil.
  - PWA header: `manifest.json` + `serviceWorker` tetap ada di exam (standalone support).
  - **Catatan:** `save` & `ragu` butuh route `POST /siswa/tryout/{tryout}/save|ragu` + CSRF. Salah route → 404/419.
- **Hasil** (`/siswa/tryout/{id}/hasil`): Nilai (benar/total*100), jumlah benar/salah, waktu pengerjaan, ranking saya (#), top 10.
- **Ranking** (`/siswa/tryout/{id}/ranking`): Top 20 order `nilai desc, waktu_menit asc`.

Edge: Submit tanpa jawab → nilai 0. Save setelah submit → 403 `sudah submit`. Start draft → 403.

## 5. Orang Tua — `/orang-tua/dashboard`

**Menu:** Dashboard · Riwayat · Analisis · Ranking · Notifikasi

Semua halaman support `?siswa_id=` jika punya >1 anak (selector dropdown).

- **Dashboard** (`/orang-tua/dashboard`): KPI nilai terakhir / ranking (count nilai> saya +1 / total) / progress delta vs tryout sebelumnya / jumlah tryout. Chart 5 tryout terakhir (Chart.js). Progress per mapel (avg, color green≥85 yellow≥70 red). Card Perlu Perhatian (avg <70 atau turun >3 poin 2 terakhir).
- **Riwayat** (`/orang-tua/riwayat`): Tabel tryout × nilai × ranking × waktu. Ranking per row dihitung live.
- **Analisis** (`/orang-tua/analisis`): Per mapel status Tinggi (≥80) / Sedang (≥65) / Perlu Perhatian + rekomendasi (Pertahankan / Latihan 30m/hari / Remedial + bimbingan).
- **Ranking** (`/orang-tua/ranking`): `?tryout_id=` filter. Tabel Top 20 + highlight anak + badge medal. List tryout filter dari `hasilTryout` anak.

Jika anak belum pernah submit → KPI/chart kosong, tetap 200.

## 6. Notifikasi — `/notifikasi` (semua role, auth only)

- **Tipe:** `jadwal` (tryout baru ke siswa), `nilai` (selalu ke ortu saat submit), `peringatan` (drop >10% vs sebelumnya), `pencapaian` (naik >0 atau pertama ≥80).
- Filter: `?tipe=semua|nilai|jadwal|pencapaian|peringatan`.
- **Mark read:** `POST /notifikasi/{id}/read` (satu) + `POST /notifikasi/read-all` (semua). Badge bell header `sudah_dibaca=false` count.
- Bell ada di header + bottom nav (mobile 390px). Unauth → 302 ke `/login`.

## 7. PWA & Mobile

- Install: Chrome desktop → ikon Install di address bar. Android → Add to Home Screen. Manifest `AkademikPro` standalone, theme `#1E3A8A`, icons 192/512/maskable.
- Offline: SW cache `akademikpro-v1` stale-while-revalidate, network-first untuk halaman role. Lighthouse PWA ≥90 saat HTTPS.
- Mobile 390px: tabel `overflow-x-auto` scroll horizontal, bottom nav `lg:hidden` 3 item, sidebar drawer hamburger (admin/guru/siswa/orang tua) overlay 280px.

## 8. Troubleshooting

| Gejala | Sebab | Solusi |
|--------|-------|--------|
| `419 Page Expired` saat save/ragu | CSRF token expired / salah route (`/siswa/tryout/save` tanpa `{tryout}`) | Reload exam, pastikan route `POST /siswa/tryout/{id}/save` dengan `_token` |
| `403 Tryout tidak aktif` | Tryout status draft/selesai atau tanggal di luar window | Guru ubah ke `aktif` + tanggal_mulai ≤ now ≤ tanggal_selesai |
| `Role tidak sesuai` | Email benar tapi pilih role salah | Login ulang dengan role sesuai `users.role` |
| `Pilih soal atau centang random` | Guru create tryout tanpa soal | Centang soal manual atau aktifkan `Acak otomatis` |
| Kelas tidak bisa dihapus | Masih dipakai siswa | Pindahkan/hapus siswa dulu |
| Chart kosong di ortu | Anak belum submit tryout | Siswa kerjakan tryout dulu |
