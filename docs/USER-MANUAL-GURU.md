# User Manual — Guru | AkademikPro

> Role: `guru` · Login: `guru@tryout.test` / `password` · Base: `http://127.0.0.1:8001`

## 1. Cara Login

1. `/login` → Email `guru@tryout.test` + `password` + **Role = Guru** → **Masuk** → `/guru/dashboard`.
2. Salah role → _Role tidak sesuai_. Salah password → error validasi.

> Screenshot: `docs/screenshots/guru-login.png`  
> Screenshot: `docs/screenshots/guru-dashboard.png`

## 2. Dashboard Guru — `/guru/dashboard`

- Statistik: jumlah soal milik guru, jumlah tryout, tryout aktif.
- Shortcut: **Bank Soal**, **Tryout**, **Analitik**.
- Sidebar: Dashboard · Bank Soal · Tryout · Analitik · Notifikasi (bell).

## 3. Bank Soal — `/guru/soal`

### 3.1 Lihat Daftar

`GET /guru/soal` — tabel soal milik guru login saja (scope `where guru_id = auth guru`).  
Filter: `?tingkat=mudah|sedang|sulit` + `?search=keyword` (cari di `pertanyaan`).  
Pagination default Laravel.

> Screenshot: `docs/screenshots/guru-soal-index.png` — filter tingkat + search.

### 3.2 Create Soal — `/guru/soal/create`

Field wajib:

| Field | Tipe | Ket |
|-------|------|-----|
| `mapel_id` | select | exists `mata_pelajaran.id` |
| `pertanyaan` | textarea | wajib |
| `pilihan_a` | text | wajib |
| `pilihan_b` | text | wajib |
| `pilihan_c` | text | wajib |
| `pilihan_d` | text | wajib |
| `jawaban_benar` | select `a/b/c/d` | wajib |
| `tingkat_kesulitan` | select `mudah/sedang/sulit` | default `sedang` |

Submit `POST /guru/soal` → `302` ke index + flash. Validasi kosong → `422` balik form.

> Screenshot: `docs/screenshots/guru-soal-form.png`

### 3.3 Edit / Delete

- **Edit** `GET /guru/soal/{id}/edit` → `PUT /guru/soal/{id}` — hanya owner bisa (403 jika milik guru lain).
- **Delete** `DELETE /guru/soal/{id}` — cascade hapus dari `tryout_soal` + `detail_jawaban` terkait.

### 3.4 Tips

- Buat soal per mapel agar mudah filter saat bikin tryout.
- Gunakan `tingkat` untuk sebar kesulitan saat random.

## 4. Tryout Management — `/guru/tryout`

### 4.1 Lihat Daftar — `GET /guru/tryout`

Tabel: `nama`, `mapel`, `durasi`, `tanggal_mulai–selesai`, `status` badge (`draft` abu / `aktif` hijau / `selesai` merah), aksi Lihat/Edit/Hapus.

> Screenshot: `docs/screenshots/guru-tryout-index.png`

### 4.2 Create Tryout — `GET /guru/tryout/create`

Field:

| Field | Validasi |
|-------|----------|
| `nama` | required, max 150 |
| `mapel_id` | required, exists |
| `status` | `draft` / `aktif` / `selesai` |
| `durasi_menit` | required, integer 5–300 |
| `tanggal_mulai` | required, datetime |
| `tanggal_selesai` | required, `after:tanggal_mulai` |
| **Pilih soal** | checkbox `soal_ids[]` (manual) |
| **Acak otomatis** | checkbox `random` + `jumlah_soal` (integer) |

Aturan:

- Jika `soal_ids` kosong **dan** `random` off → error _Pilih soal atau centang random._
- Jika `random` on → ambil `jumlah_soal` random dari `soal` milik guru + mapel tersebut.
- Jika `status=aktif` saat create → broadcast notifikasi `jadwal` ke **semua siswa** (judul: `Tryout Baru: {nama}`).
- `PUT draft→aktif` (edit) juga broadcast (fix 8B, idempotent by judul).

Submit `POST /guru/tryout` → `302`.

> Screenshot: `docs/screenshots/guru-tryout-form.png` — form + checkbox soal + random.

### 4.3 Edit Tryout — `GET /guru/tryout/{id}/edit`

- Ubah meta + pilih ulang soal (detach+attach pivot `tryout_soal`).
- Ganti `draft→aktif` → trigger broadcast `jadwal` (dedup by judul).

### 4.4 Lihat Hasil Tryout — `GET /guru/tryout/{id}`

- Detail tryout + daftar soal.
- Tabel hasil siswa: `nama`, `nilai`, `benar/salah`, `waktu_menit`, `waktu_submit` — sorted `nilai DESC`.
- Summary: `avg`, `max`, `min` (dari `hasil_tryout` whereNotNull `waktu_submit`).

> Screenshot: `docs/screenshots/guru-tryout-show.png`

### 4.5 Delete

`DELETE /guru/tryout/{id}` — cascade ke `tryout_soal` + `hasil_tryout`.

## 5. Analitik — `GET /guru/analitik`

- **Rata per Kelas** — Bar Chart (Chart.js): `AVG(nilai)` group by `kelas.nama`.
- **Distribusi Nilai** — Doughnut: bucket `0–50`, `50–70`, `70–85`, `85–100`.
- **Tabel Perlu Evaluasi** — siswa dengan `nilai < 70` (per tryout).
- Data hanya dari `hasil_tryout` yang sudah submit (`waktu_submit` not null).

> Screenshot: `docs/screenshots/guru-analitik.png`

Filter opsional: `?mapel_id=` / `?tryout_id=` (jika ada di UI).

## 6. Notifikasi — `GET /notifikasi`

- Guru menerima tipe `jadwal` jika ada tryout baru (umumnya untuk siswa; guru lihat via bell).
- Filter `?tipe=` + **Mark Read** `POST /notifikasi/{id}/read` / `POST /notifikasi/read-all`.

## 7. Troubleshooting Guru

| Gejala | Sebab | Solusi |
|--------|-------|--------|
| `Pilih soal atau centang random` | Submit tanpa soal | Centang soal atau aktifkan Random + isi jumlah |
| `tanggal_selesai must be after` | Tanggal terbalik | Set selesai > mulai |
| Soal tidak muncul di list | Bukan owner | Soal hanya milik guru pembuat |
| `403` buka `/admin/*` | Role guard | Login sebagai Admin |
| Chart kosong | Belum ada submit | Siswa harus submit tryout dulu |
