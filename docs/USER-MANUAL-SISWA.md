# User Manual — Siswa | AkademikPro

> Role: `siswa` · Login: `siswa@tryout.test` / `password` (Kayla Putri, NIS 20250001) · Alternatif: `kayla.adik@tryout.test` · Base: `http://127.0.0.1:8001`

## 1. Cara Login

1. `/login` → Email `siswa@tryout.test` + `password` + **Role = Siswa** → **Masuk** → `/siswa/dashboard`.
2. Salah role → _Role tidak sesuai_.

> Screenshot: `docs/screenshots/siswa-login.png`  
> Screenshot: `docs/screenshots/siswa-dashboard.png`

## 2. Dashboard Siswa — `GET /siswa/dashboard`

- Card: tryout aktif terdekat, nilai terakhir, ranking terakhir, jumlah tryout selesai.
- List tryout aktif terbaru (max 5) dengan badge status.
- Sidebar: Dashboard · Tryout · Hasil/Ranking · Notifikasi.

## 3. Daftar Tryout — `GET /siswa/tryout`

Menampilkan **hanya** tryout dengan `status=aktif` **dan** `tanggal_mulai <= now <= tanggal_selesai`. Draft/selesai tidak muncul.

| Status Row | Tombol | Aksi |
|------------|--------|------|
| Belum mulai | **MULAI TRYOUT** (navy) | `POST /siswa/tryout/{id}/start` → buat `hasil_tryout` + `started_at=now` |
| Sedang dikerjakan | **Lanjutkan** (outline) | `GET /siswa/tryout/{id}/exam` |
| Selesai | **Lihat Hasil** + **Ranking** | `GET /siswa/tryout/{id}/hasil` / `ranking` |

> Screenshot: `docs/screenshots/siswa-tryout-index.png` — list dengan 3 state.

Validasi start:

- Tryout `draft` → `403 Tryout tidak aktif`.
- Sudah pernah submit (`waktu_submit` not null) → redirect ke `/siswa/tryout/{id}/hasil` (cegah kerjakan 2x).

## 4. Mengerjakan Tryout — `GET /siswa/tryout/{id}/exam`

> Screenshot: `docs/screenshots/siswa-exam.png` — timer + navigasi nomor + ragu.

### 4.1 Timer

- Header sticky: **Sisa Waktu** `MM:SS` — hitung server: `durasi_menit*60 - (now - started_at)` (Carbon `diffInSeconds` absolute).
- Jika `0` → auto `doSubmit()` → redirect hasil. Jangan rely timer JS saja — server yang tentukan timeout saat `submit`.

### 4.2 Navigasi Soal

- Grid nomor `1..N` — klik untuk pindah. Soal aktif highlight navy.
- Warna nomor: putih (belum jawab), hijau (sudah jawab), kuning (ragu).
- Prev/Next button.

### 4.3 Menjawab & Ragu

- Pilih radio `a/b/c/d` → auto trigger **Simpan** (atau klik tombol Simpan).
- **Ragu** toggle → `POST /siswa/tryout/{tryout}/ragu` body `detail_id`, `ragu=1/0` → tombol berubah kuning + nomor kuning.
- **Simpan** → `POST /siswa/tryout/{tryout}/save` body `detail_id`, `jawaban=a/b/c/d` → response `{"ok":true}`. Disimpan ke `detail_jawaban.jawaban_siswa`.

Autosave:

- JS `keepalive` tiap 30 detik + saat `beforeunload` + saat ganti soal.
- Refresh halaman → jawaban tetap (di-load dari DB).
- CSRF `_token` wajib; salah route (`/siswa/tryout/save` tanpa `{tryout}`) → `404`/`419`.

### 4.4 Submit

- Tombol **Selesai & Kirim** → konfirmasi → `POST /siswa/tryout/{tryout}/submit`.
- Server hitung: `benar = count jawaban_siswa == jawaban_benar`, `nilai = benar/total*100` (decimal 5,2), `waktu_menit = ceil(abs(now - started_at)/60)` min 1, `waktu_submit=now`.
- Redirect `302` → `/siswa/tryout/{id}/hasil`.
- Setelah submit: `save`/`ragu` → `403 sudah submit`.
- Submit tanpa jawab → `nilai 0`.

### 4.5 Edge Case

- Session expired saat exam → `302` ke `/login`.
- XSS di `pertanyaan` → di-escape Blade `{{ }}` → `&lt;script&gt;`.
- SQL injection di search → binding Eloquent aman.

## 5. Lihat Hasil — `GET /siswa/tryout/{id}/hasil`

- Skor besar `Nilai: 80.00`, ringkasan `Benar/Salah`, `Waktu: Xm`, `Ranking: #2`.
- Tabel per soal: `pertanyaan`, `jawaban_siswa` vs `jawaban_benar`, badge benar/salah.
- Top 10 ranking mini.
- Belum submit → redirect ke exam atau 403.

> Screenshot: `docs/screenshots/siswa-hasil.png`

## 6. Ranking — `GET /siswa/tryout/{id}/ranking`

- Tabel Top 20 `ORDER BY nilai DESC, waktu_pengerjaan_menit ASC`.
- Baris siswa login di-highlight navy + badge **Saya**.
- Filter tidak ada — per tryout.

> Screenshot: `docs/screenshots/siswa-ranking.png`

## 7. Notifikasi — `GET /notifikasi`

- Tipe untuk siswa: `jadwal` (tryout baru).
- Bell header menampilkan count `sudah_dibaca=false`. Klik bell → list.
- Filter `?tipe=jadwal` + Mark Read.

## 8. PWA & Mobile

- Install: Chrome → Install app. Standalone `AkademikPro`.
- Exam tetap ada `manifest.json` + `sw.js` walau standalone.
- Mobile 390px: bottom nav 3 item (Dashboard, Tryout, Notifikasi + badge).

## 9. Troubleshooting Siswa

| Gejala | Sebab | Solusi |
|--------|-------|--------|
| `403 Tryout tidak aktif` | Draft / luar window tanggal | Tunggu guru aktifkan & cek tanggal |
| `419 Page Expired` saat Save | CSRF/salah route | Reload exam, pastikan route `POST /siswa/tryout/{id}/save` |
| Jawaban hilang setelah refresh | Belum klik Simpan | Pastikan Simpan / autosave `{"ok":true}` di Network tab |
| Tidak bisa start 2x | Sudah submit | Lihat Hasil saja; 1 tryout 1 submit |
| Timer 00:00 tapi belum submit | Auto-submit pending | Tunggu redirect atau klik Selesai manual |
