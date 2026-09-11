# User Manual — Admin | AkademikPro

> Role: `admin` · Login: `admin@tryout.test` / `password` · Base: `http://127.0.0.1:8001`

## 1. Cara Login

1. Buka `/` → klik **Masuk** atau langsung `/login`.
2. Isi **Email** `admin@tryout.test`, **Password** `password`, pilih **Role = Admin** → **Masuk**.
3. Redirect ke `/admin/dashboard`. Jika pilih role salah → error _Role tidak sesuai_.
4. Logout via dropdown avatar kanan atas → **Log Out** → redirect `/login`.

> Screenshot: `docs/screenshots/admin-login.png` — form login dengan dropdown Role.  
> Screenshot: `docs/screenshots/admin-dashboard.png` — KPI Guru/Siswa/Kelas/Tryout Aktif.

## 2. Dashboard Admin — `/admin/dashboard`

- KPI cards: Jumlah Guru, Siswa, Kelas, Tryout Aktif.
- Navigasi sidebar: Dashboard · Kelas · Mapel · Guru · Siswa · Ortu.
- Mobile: hamburger → drawer 280px; bottom nav hidden untuk admin (sidebar only).

## 3. Kelola Kelas — `/admin/kelas`

| Aksi | Langkah |
|------|---------|
| **Lihat** | `GET /admin/kelas` — tabel `nama`, `tingkat`, aksi Edit/Hapus. Search `?search=9A`. |
| **Create** | Klik **Tambah Kelas** → `/admin/kelas/create` → isi `Nama` (wajib) + `Tingkat` (wajib, ex: `9`, `X`, `XII`) → **Simpan** → `302` redirect index + flash sukses. |
| **Edit** | Klik **Edit** → `/admin/kelas/{id}/edit` → ubah field → **Update**. |
| **Delete** | Klik **Hapus** → konfirmasi → `DELETE /admin/kelas/{id}`. Jika masih dipakai siswa → ditolak: _Kelas masih dipakai siswa_ (guard `siswa()->exists()`). |

Validasi: kosong → `422` kembali ke form dengan error. Duplicate `nama+tingkat` tidak diblok unique — boleh sama (opsional upgrade: unique constraint).

> Screenshot: `docs/screenshots/admin-kelas-index.png`  
> Screenshot: `docs/screenshots/admin-kelas-form.png`

## 4. Kelola Mata Pelajaran — `/admin/mapel`

- Field: `nama` (wajib) + `kode` (wajib, unique, ex: `MTK`, `IPA`).
- Flow sama: Index → Create → Edit → Delete.
- Delete cascade ke `soal` & `tryout` yang pakai mapel tersebut (FK `cascadeOnDelete`) — hati-hati.

> Screenshot: `docs/screenshots/admin-mapel-index.png`

## 5. Kelola Guru — `/admin/guru`

- Field: `name` (wajib), `email` (unique, wajib), `password` (min 8, wajib saat create, opsional saat edit), `nip` (unique, nullable).
- Create: `POST /admin/guru` → buat `users` (`role=guru`) + `guru` row.
- Edit: `PUT /admin/guru/{id}` — password kosong = tidak update.
- Delete: hapus `users` cascade ke `guru` + soal/tryout milik guru.

> Screenshot: `docs/screenshots/admin-guru-index.png`  
> Screenshot: `docs/screenshots/admin-guru-form.png`

## 6. Kelola Siswa — `/admin/siswa`

- Field: `name`, `email` (unique), `password` (min 8), `nis` (unique, wajib), `kelas_id` (exists `kelas.id`, wajib), `orang_tua_id` (nullable, exists `orang_tua.id` — link ke ortu).
- Create/Edit/Delete flow sama.
- Search `?search=Kayla` filter by name/nis.

> Screenshot: `docs/screenshots/admin-siswa-index.png`

## 7. Kelola Orang Tua — `/admin/ortu`

- Field: `name`, `email` (unique), `password`, `pekerjaan` (nullable), `no_hp` (nullable).
- Siswa yang `orang_tua_id`-nya menunjuk ke ortu ini akan muncul di dashboard ortu tersebut.

> Screenshot: `docs/screenshots/admin-ortu-index.png`

## 8. Laporan & Navigasi Lain

- **Notifikasi:** bell header `GET /notifikasi` — Admin juga bisa lihat notifikasi (umumnya kosong; notifikasi `jadwal` hanya ke siswa, `nilai` ke ortu).
- **Profile:** `GET /profile` — update name/email/password, delete account (Breeze).
- **Akses ditolak:** Admin tidak bisa buka `/guru/*`, `/siswa/*`, `/orang-tua/*` → `403`.

## 9. Troubleshooting Admin

| Gejala | Sebab | Solusi |
|--------|-------|--------|
| `Role tidak sesuai` | Pilih role salah saat login | Login ulang pilih **Admin** |
| `Kelas masih dipakai` | FK guard | Pindahkan/hapus siswa di kelas tsb dulu |
| `email sudah dipakai` | Unique violation | Ganti email |
| `419 Page Expired` | CSRF expired | Reload form |

## 10. Checklist Harian Admin

- [ ] Cek KPI dashboard
- [ ] Approve kelas/mapel baru
- [ ] Cek guru/siswa duplikat NIS/NIP
