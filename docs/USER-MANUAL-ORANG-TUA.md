# User Manual — Orang Tua | AkademikPro

> Role: `orang_tua` · Login: `orangtua@tryout.test` / `password` (Orang Tua Kayla — 2 anak) · Base: `http://127.0.0.1:8001`

## 1. Cara Login

1. `/login` → Email `orangtua@tryout.test` + `password` + **Role = Orang Tua** → **Masuk** → `/orang-tua/dashboard`.
2. Jika punya 2 anak, selector anak muncul di kanan atas dashboard.

> Screenshot: `docs/screenshots/ortu-login.png`  
> Screenshot: `docs/screenshots/ortu-dashboard.png`

## 2. Dashboard — `GET /orang-tua/dashboard` (`?siswa_id=` jika multi-anak)

> Screenshot: `docs/screenshots/ortu-dashboard-kpi.png` — 4 KPI + chart.

### 2.1 KPI Cards (4)

| KPI | Sumber | Rumus |
|-----|--------|-------|
| **Nilai Terakhir** | `hasil_tryout` terbaru (order `waktu_submit DESC`) | `nilai` |
| **Ranking** | vs semua siswa di tryout tsb | `COUNT(nilai > saya) + 1 / total` |
| **Progress** | delta vs tryout sebelumnya | `nilai_now - nilai_prev` (↑ hijau / ↓ merah) |
| **Jumlah Tryout** | total submit anak | `COUNT(hasil_tryout where waktu_submit not null)` |

Jika anak belum submit → KPI `0` / `-`.

### 2.2 Grafik Perkembangan Nilai — Chart.js Line

- 5 tryout terakhir (order `waktu_submit ASC` untuk chart).
- X: `nama tryout`, Y: `nilai 0–100`.
- Hover tooltip tampil nilai.

> Screenshot: `docs/screenshots/ortu-chart.png`

### 2.3 Progress per Mapel — Bar Horizontal

- Per `mata_pelajaran`: `AVG(nilai)` dari tryout mapel tersebut.
- Warna: hijau `≥85`, kuning `≥70`, merah `<70`.
- Progress bar `width = avg%`.

> Screenshot: `docs/screenshots/ortu-progress-mapel.png`

### 2.4 Card Perlu Perhatian

Muncul jika **salah satu**:

- `AVG per mapel < 70`, atau
- Drop `>10` poin di 2 tryout terakhir (atau `>3` poin untuk warna kuning di chart — threshold ringkas).

Isi card: `Mapel — Nilai — Rekomendasi`.

### 2.5 Selector Anak

- Dropdown `Pilih Anak` — `?siswa_id=1` (Kayla Putri) / `?siswa_id=2` (Kayla Adik).
- Semua KPI/chart/filter ikut `siswa_id` tersebut.
- Tanpa param → default anak pertama (`siswa[0]`).

## 3. Riwayat — `GET /orang-tua/riwayat` (`?siswa_id=`)

Tabel: `Tryout | Mapel | Nilai | Ranking | Waktu Pengerjaan | Tanggal Submit`.

- Ranking per row dihitung live: `COUNT(nilai > row.nilai) + 1`.
- Urut `waktu_submit DESC`.

> Screenshot: `docs/screenshots/ortu-riwayat.png`

## 4. Analisis — `GET /orang-tua/analisis` (`?siswa_id=`)

Per mapel:

| Status | Syarat | Rekomendasi |
|--------|--------|-------------|
| **Tinggi** | `avg ≥ 80` | Pertahankan, lanjut latihan soal sulit |
| **Sedang** | `avg ≥ 65` | Latihan 30 menit/hari |
| **Perlu Perhatian** | `avg < 65` | Remedial + bimbingan guru |

Card per mapel menampilkan badge status + rekomendasi.

> Screenshot: `docs/screenshots/ortu-analisis.png`

## 5. Ranking — `GET /orang-tua/ranking` (`?siswa_id=&tryout_id=`)

- Filter dropdown tryout: list dari `hasilTryout` anak (hanya tryout yang pernah dikerjakan anak).
- Tabel Top 20 `ORDER BY nilai DESC, waktu_pengerjaan_menit ASC`.
- Baris anak di-highlight navy + badge medal 🥇🥈🥉 untuk top 3.
- Tanpa `tryout_id` → tryout terbaru anak.

> Screenshot: `docs/screenshots/ortu-ranking.png`

## 6. Notifikasi — `GET /notifikasi` (`?tipe=`)

Untuk orang tua:

| Tipe | Trigger |
|------|---------|
| `nilai` | Setiap siswa submit (selalu) |
| `peringatan` | Drop `>10%` vs tryout sebelumnya |
| `pencapaian` | Naik `>0` atau pertama kali `≥80` |

- Filter: `?tipe=semua|nilai|pencapaian|peringatan|jadwal`.
- **Mark Read:** `POST /notifikasi/{id}/read` (satu) / `POST /notifikasi/read-all` (semua). Hanya owner bisa (403 jika bukan miliknya).
- Bell header + bottom nav menampilkan count `sudah_dibaca=false`.

> Screenshot: `docs/screenshots/ortu-notifikasi.png` — list + filter + mark read.

## 7. PWA & Mobile

- Manifest `AkademikPro` standalone, theme `#1E3A8A`.
- Mobile 390px: KPI jadi 2 kolom, chart scroll, bottom nav 3 item.

## 8. Troubleshooting Orang Tua

| Gejala | Sebab | Solusi |
|--------|-------|--------|
| Chart/KPI kosong | Anak belum submit | Siswa kerjakan tryout dulu |
| Ranking `-` | Belum ada hasil | Tunggu submit |
| Selector anak tidak muncul | Hanya 1 anak ter-link | Admin → Siswa → set `orang_tua_id` |
| Notifikasi tidak masuk | Tryout masih `draft` | Guru harus `aktif` + siswa submit |
| Salah anak tampil | `?siswa_id` salah | Pilih dari dropdown, jangan manual edit ID yang bukan anak Anda (403 jika cross-ortu) |
