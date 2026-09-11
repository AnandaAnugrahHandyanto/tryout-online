# API / Route Docs — AkademikPro

> Base: `http://127.0.0.1:8001` · Auth: session `web` guard + `role` middleware · Format: Blade (redirect + flash), bukan JSON API · CSRF `_token` wajib untuk POST/PUT/DELETE · 84 routes

## 1. Auth & Umum

| Method | Path | Name | Guard | Ket |
|--------|------|------|-------|-----|
| GET | `/` | — | guest | Welcome landing `welcome.blade.php` |
| GET | `/login` | `login` | guest | Split navy login |
| POST | `/login` | `login` | guest | `email`, `password`, `role` → redirect per role |
| POST | `/logout` | `logout` | auth | |
| GET | `/dashboard` | `dashboard` | auth | Redirect ke `admin/guru/siswa/orang-tua.dashboard` sesuai `users.role` |
| GET | `/profile` | `profile.edit` | auth | Breeze |
| PATCH | `/profile` | `profile.update` | auth | |
| DELETE | `/profile` | `profile.destroy` | auth | |
| GET/POST | `/forgot-password`, `/reset-password/*`, `/confirm-password`, `/verify-email/*` | Breeze | — | Default Breeze |
| GET | `/register` | `register` | — | `404` (registrasi dimatikan Fase 1) |
| POST | `/register` | — | — | `404` |

**Login Request/Response**

```http
POST /login
Content-Type: application/x-www-form-urlencoded

_token=xxx&email=siswa@tryout.test&password=password&role=siswa
```

- Sukses: `302` → `/siswa/dashboard` (atau `/admin/dashboard` / `/guru/dashboard` / `/orang-tua/dashboard` sesuai role).
- Gagal role mismatch: `302` balik `/login` + error `Role tidak sesuai`.
- Gagal password: `302` balik `/login` + error `These credentials do not match…`.
- Tanpa CSRF: `419`.

## 2. PWA

| Method | Path | Ket |
|--------|------|-----|
| GET | `/manifest.json` | `name: AkademikPro`, `display: standalone`, `theme_color: #1E3A8A` |
| GET | `/sw.js` | `CACHE akademipro-v1`, network-first |
| GET | `/icons/icon-192x192.png` | 192 |
| GET | `/icons/icon-512x512.png` | 512 |
| GET | `/icons/icon-512x512-maskable.png` | maskable |
| GET | `/icons/icon-180x180.png` | apple-touch 180 |
| GET | `/build/assets/app-*.css` | Vite build |
| GET | `/build/assets/app-*.js` | Vite build |

## 3. Admin — `role:admin`, prefix `/admin`

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/admin/dashboard` | `admin.dashboard` | KPI Guru/Siswa/Kelas/Tryout aktif |
| GET | `/admin/kelas` | `admin.kelas.index` | `?search=` |
| GET | `/admin/kelas/create` | `admin.kelas.create` | form `nama`, `tingkat` |
| POST | `/admin/kelas` | `admin.kelas.store` | `nama` required, `tingkat` required |
| GET | `/admin/kelas/{kelas}/edit` | `admin.kelas.edit` | |
| PUT | `/admin/kelas/{kelas}` | `admin.kelas.update` | |
| DELETE | `/admin/kelas/{kelas}` | `admin.kelas.destroy` | guard jika `siswa()->exists()` → tolak |
| GET/POST | `/admin/mapel` | `admin.mapel.*` | `nama` required, `kode` required unique |
| GET/POST | `/admin/guru` | `admin.guru.*` | `name`, `email` unique, `password` min8, `nip` unique nullable |
| GET/POST | `/admin/siswa` | `admin.siswa.*` | `name`, `email` unique, `password` min8, `nis` unique, `kelas_id` exists, `orang_tua_id` nullable exists |
| GET/POST | `/admin/ortu` | `admin.ortu.*` | `name`, `email` unique, `password` min8, `pekerjaan` nullable, `no_hp` nullable |

**Example — Create Kelas**

```http
POST /admin/kelas
_token=xxx&nama=10C&tingkat=10

→ 302 Location: /admin/kelas  (flash: Kelas berhasil dibuat)
→ GET /admin/kelas  body contains "10C"

DELETE /admin/kelas/{id}  (kelas dipakai siswa)
→ 302 balik index + error "Kelas masih dipakai siswa"  (guard, tidak terhapus)
```

**Example — Create Siswa**

```http
POST /admin/siswa
_token=xxx&name=Budi&email=budi@tryout.test&password=password&nis=20250099&kelas_id=1&orang_tua_id=1

→ 302 /admin/siswa
```

## 4. Guru — `role:guru`, prefix `/guru`

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/guru/dashboard` | `guru.dashboard` | counts + links |
| GET | `/guru/soal` | `guru.soal.index` | `?search=` `?tingkat=mudah/sedang/sulit` (scope owner) |
| GET | `/guru/soal/create` | `guru.soal.create` | |
| POST | `/guru/soal` | `guru.soal.store` | `mapel_id` exists, `pertanyaan`, `pilihan_a/b/c/d`, `jawaban_benar` a/b/c/d, `tingkat_kesulitan` mudah/sedang/sulit |
| GET | `/guru/soal/{soal}/edit` | `guru.soal.edit` | 403 jika bukan owner |
| PUT | `/guru/soal/{soal}` | `guru.soal.update` | |
| DELETE | `/guru/soal/{soal}` | `guru.soal.destroy` | |
| GET | `/guru/tryout` | `guru.tryout.index` | |
| GET | `/guru/tryout/create` | `guru.tryout.create` | |
| POST | `/guru/tryout` | `guru.tryout.store` | `nama` max150, `mapel_id` exists, `status` draft/aktif/selesai, `durasi_menit` 5–300, `tanggal_mulai` datetime, `tanggal_selesai` after:mulai, `soal_ids[]` atau `random`+`jumlah_soal` |
| GET | `/guru/tryout/{tryout}` | `guru.tryout.show` | detail + hasil siswa avg/max/min |
| GET | `/guru/tryout/{tryout}/edit` | `guru.tryout.edit` | |
| PUT | `/guru/tryout/{tryout}` | `guru.tryout.update` | jika `draft→aktif` broadcast `jadwal` ke semua siswa (idempotent) |
| DELETE | `/guru/tryout/{tryout}` | `guru.tryout.destroy` | |
| GET | `/guru/analitik` | `guru.analitik` | Bar rata/kelas + Doughnut distribusi + tabel <70 |

**Example — Create Soal**

```http
POST /guru/soal
_token=xxx&mapel_id=1&pertanyaan=2%2B2%3F&pilihan_a=3&pilihan_b=4&pilihan_c=5&pilihan_d=6&jawaban_benar=b&tingkat_kesulitan=sedang

→ 302 /guru/soal
→ GET /guru/soal?tingkat=sedang  body contains pertanyaan
```

**Example — Create Tryout (random)**

```http
POST /guru/tryout
_token=xxx&nama=UTS+MTK&mapel_id=1&status=aktif&durasi_menit=60
  &tanggal_mulai=2026-09-11+00%3A00%3A00&tanggal_selesai=2026-09-18+00%3A00%3A00
  &random=1&jumlah_soal=5

→ 302 /guru/tryout  + notifikasi `jadwal` ke semua siswa (judul: Tryout Baru: UTS MTK)
→ GET /siswa/tryout  (sebagai siswa) contains "UTS MTK"

POST /guru/tryout  (tanpa soal & tanpa random)
→ 302 balik form + error "Pilih soal atau centang random."
```

**Example — Analitik**

```http
GET /guru/analitik
→ 200  body contains canvas Bar + Doughnut + "Perlu Evaluasi"
```

## 5. Siswa — `role:siswa`, prefix `/siswa`

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/siswa/dashboard` | `siswa.dashboard` | |
| GET | `/siswa/tryout` | `siswa.tryout.index` | hanya `aktif` dalam window tanggal |
| POST | `/siswa/tryout/{tryout}/start` | `siswa.tryout.start` | buat `hasil_tryout` + `started_at=now`; 403 jika draft/luar window/sudah submit |
| GET | `/siswa/tryout/{tryout}/exam` | `siswa.tryout.exam` | timer `durasi*60 - (now-started_at)` |
| POST | `/siswa/tryout/{tryout}/save` | `siswa.tryout.save` | `detail_id`, `jawaban` a/b/c/d → `{"ok":true}` |
| POST | `/siswa/tryout/{tryout}/ragu` | `siswa.tryout.ragu` | `detail_id`, `ragu` 0/1 |
| POST | `/siswa/tryout/{tryout}/submit` | `siswa.tryout.submit` | hitung `nilai=benar/total*100`, `waktu=ceil(abs(now-started_at)/60)` min1 → `302` ke hasil |
| GET | `/siswa/tryout/{tryout}/hasil` | `siswa.tryout.hasil` | nilai, benar/salah, waktu, ranking, top 10 |
| GET | `/siswa/tryout/{tryout}/ranking` | `siswa.tryout.ranking` | Top 20 `nilai DESC, waktu ASC`, highlight self |

**Example — Start → Save → Submit**

```http
POST /siswa/tryout/1/start
_token=xxx
→ 302 Location: /siswa/tryout/1/exam
→ GET /siswa/tryout/1/exam  body contains "Sisa Waktu" + soal 1..N

POST /siswa/tryout/1/save
_token=xxx&detail_id=5&jawaban=b
→ 200 {"ok":true}   (DB: detail_jawaban.jawaban_siswa=b)

POST /siswa/tryout/1/ragu
_token=xxx&detail_id=5&ragu=1
→ 200 {"ok":true}   (ragu=1)

POST /siswa/tryout/1/submit
_token=xxx
→ 302 Location: /siswa/tryout/1/hasil
→ GET /siswa/tryout/1/hasil  body contains "Nilai" + "80.00"
→ GET /siswa/tryout/1/ranking  body contains Top 20 + highlight

POST /siswa/tryout/1/start  (lagi setelah submit)
→ 302 Location: /siswa/tryout/1/hasil  (cegah 2x)
```

## 6. Orang Tua — `role:orang_tua`, prefix `/orang-tua`

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/orang-tua/dashboard` | `orang-tua.dashboard` | `?siswa_id=` jika multi-anak; KPI + Chart 5 terakhir + progress/mapel + Perlu Perhatian |
| GET | `/orang-tua/riwayat` | `orang-tua.riwayat` | `?siswa_id=` tabel tryout × nilai × ranking |
| GET | `/orang-tua/analisis` | `orang-tua.analisis` | `?siswa_id=` per mapel Tinggi≥80/Sedang≥65/Perlu + rekomendasi |
| GET | `/orang-tua/ranking` | `orang-tua.ranking` | `?siswa_id=&tryout_id=` Top 20 |

**Example**

```http
GET /orang-tua/dashboard?siswa_id=1
→ 200  body contains KPI "Nilai Terakhir" + canvas Chart.js + "Progress per Mapel"

GET /orang-tua/riwayat?siswa_id=1
→ 200  tabel riwayat

GET /orang-tua/ranking?tryout_id=1&siswa_id=1
→ 200  Top 20 + highlight anak
```

## 7. Notifikasi — `auth` semua role

| Method | Path | Name | Ket |
|--------|------|------|-----|
| GET | `/notifikasi` | `notifikasi.index` | `?tipe=semua|jadwal|nilai|peringatan|pencapaian` |
| POST | `/notifikasi/{notifikasi}/read` | `notifikasi.read` | mark satu (owner only, 403 jika bukan milik) |
| POST | `/notifikasi/read-all` | `notifikasi.readAll` | mark semua milik user |

Tipe & trigger:

- `jadwal` → ke **siswa** saat guru create/update `tryout` jadi `aktif`.
- `nilai` → ke **orang tua** setiap submit (selalu).
- `peringatan` → ke ortu jika drop `>10%` vs sebelumnya.
- `pencapaian` → ke ortu jika naik `>0` atau pertama `≥80`.

**Example**

```http
GET /notifikasi?tipe=nilai
→ 200  list judul "Nilai Baru: ..."

POST /notifikasi/5/read
_token=xxx
→ 302 /notifikasi  (sudah_dibaca=1)

POST /notifikasi/read-all
_token=xxx
→ 302
```

## 8. Error Codes

| Status | Arti | Kapan |
|--------|------|-------|
| 200 | OK | GET halaman sukses |
| 302 | Redirect | sukses POST/PUT/DELETE, atau auth redirect ke `/login` |
| 403 | Forbidden | `role` middleware fail, atau owner check fail, atau tryout tidak aktif, atau sudah submit |
| 404 | Not Found | route tidak ada, atau `/register` (dimatikan) |
| 419 | CSRF | `_token` hilang/expired |
| 422 | Validation | field required/unique/format fail → balik form + `$errors` |

## 9. cURL Quick Test

```bash
BASE=http://127.0.0.1:8001
JAR=/tmp/jar.txt

# login siswa
TOK=$(curl -s -c $JAR -b $JAR $BASE/login | grep -oP 'name="_token" value="\K[^"]+' | head -1)
curl -s -b $JAR -c $JAR -X POST $BASE/login -d "_token=$TOK&email=siswa@tryout.test&password=password&role=siswa" -w "%{redirect_url}\n" | head

# list tryout aktif
curl -s -b $JAR $BASE/siswa/tryout | grep -o "Tryout" | head

# notifikasi
curl -s -b $JAR $BASE/notifikasi | grep -o "notifikasi" | head
```

## 10. Route List (84)

`php artisan route:list` — lihat output lengkap di terminal. Prefix: `/admin/*` (role:admin), `/guru/*` (role:guru), `/siswa/*` (role:siswa), `/orang-tua/*` (role:orang_tua), `/notifikasi` (auth).
