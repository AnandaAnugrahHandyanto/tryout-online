# Database Schema — AkademikPro

> DB: MySQL 8 / MariaDB 11.8 · `tryout_online` + `tryout_online_testing` · 15 migrations · Engine InnoDB · Charset utf8mb4

## 1. Daftar Tabel

### 1.1 `users` — akun semua role

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `name` | VARCHAR(255) | |
| `email` | VARCHAR(255) UNIQUE | |
| `email_verified_at` | TIMESTAMP NULL | Breeze |
| `password` | VARCHAR(255) | `hashed` cast |
| `role` | ENUM('admin','guru','siswa','orang_tua') DEFAULT 'siswa' | — |
| `remember_token` | VARCHAR(100) NULL | |
| `created_at`, `updated_at` | TIMESTAMP | |

Index: `email` unique.

### 1.2 `password_reset_tokens`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `email` | VARCHAR(255) PK | |
| `token` | VARCHAR(255) | |
| `created_at` | TIMESTAMP NULL | |

### 1.3 `sessions` (database driver)

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | VARCHAR(255) PK | |
| `user_id` | BIGINT UNSIGNED NULL INDEX FK → `users.id` | |
| `ip_address` | VARCHAR(45) NULL | |
| `user_agent` | TEXT NULL | |
| `payload` | LONGTEXT | |
| `last_activity` | INT | |

### 1.4 `kelas`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `nama` | VARCHAR(255) | ex: `9A` |
| `tingkat` | VARCHAR(255) | ex: `9`, `XII` |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.5 `mata_pelajaran`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `nama` | VARCHAR(255) | ex: `Matematika` |
| `kode` | VARCHAR(255) UNIQUE | ex: `MTK` |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.6 `guru`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `user_id` | BIGINT UNSIGNED FK → `users.id` CASCADE | |
| `nip` | VARCHAR(255) UNIQUE NULL | |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.7 `orang_tua`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `user_id` | BIGINT UNSIGNED FK → `users.id` CASCADE | |
| `pekerjaan` | VARCHAR(255) NULL | |
| `no_hp` | VARCHAR(255) NULL | |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.8 `siswa`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `user_id` | BIGINT UNSIGNED FK → `users.id` CASCADE | |
| `nis` | VARCHAR(255) UNIQUE | |
| `kelas_id` | BIGINT UNSIGNED FK → `kelas.id` CASCADE | |
| `orang_tua_id` | BIGINT UNSIGNED NULL FK → `orang_tua.id` NULL ON DELETE | link ke ortu |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.9 `soal`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `mapel_id` | BIGINT UNSIGNED FK → `mata_pelajaran.id` CASCADE | |
| `guru_id` | BIGINT UNSIGNED FK → `guru.id` CASCADE | ownership |
| `pertanyaan` | TEXT | |
| `pilihan_a` | TEXT | |
| `pilihan_b` | TEXT | |
| `pilihan_c` | TEXT | |
| `pilihan_d` | TEXT | |
| `jawaban_benar` | ENUM('a','b','c','d') | |
| `tingkat_kesulitan` | ENUM('mudah','sedang','sulit') DEFAULT 'sedang' | |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.10 `tryout`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `nama` | VARCHAR(255) | |
| `mapel_id` | BIGINT UNSIGNED FK → `mata_pelajaran.id` CASCADE | |
| `guru_id` | BIGINT UNSIGNED FK → `guru.id` CASCADE | |
| `jumlah_soal` | INT UNSIGNED DEFAULT 0 | denormalized count |
| `durasi_menit` | INT UNSIGNED | 5–300 |
| `tanggal_mulai` | DATETIME | |
| `tanggal_selesai` | DATETIME | must `after:tanggal_mulai` |
| `status` | ENUM('draft','aktif','selesai') DEFAULT 'draft' | |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.11 `tryout_soal` — pivot tryout ↔ soal

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `tryout_id` | BIGINT UNSIGNED FK → `tryout.id` CASCADE | |
| `soal_id` | BIGINT UNSIGNED FK → `soal.id` CASCADE | |
| `urutan` | INT UNSIGNED DEFAULT 0 | |
| `created_at`, `updated_at` | TIMESTAMP | |
| UNIQUE | (`tryout_id`, `soal_id`) | |

### 1.12 `hasil_tryout` — 1 row per siswa per tryout

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `tryout_id` | BIGINT UNSIGNED FK → `tryout.id` CASCADE | |
| `siswa_id` | BIGINT UNSIGNED FK → `siswa.id` CASCADE | |
| `nilai` | DECIMAL(5,2) DEFAULT 0 | `benar/total*100` |
| `jumlah_benar` | INT UNSIGNED DEFAULT 0 | |
| `jumlah_salah` | INT UNSIGNED DEFAULT 0 | |
| `waktu_pengerjaan_menit` | INT UNSIGNED DEFAULT 0 | `ceil(abs(now-started_at)/60)` min 1 |
| `waktu_submit` | DATETIME NULL | null = belum submit |
| `started_at` | DATETIME NULL | set saat `start` (migrasi `add_ragu`) |
| `created_at`, `updated_at` | TIMESTAMP | |
| UNIQUE | (`tryout_id`, `siswa_id`) | cegah 2x submit |

### 1.13 `detail_jawaban` — jawaban per soal per hasil

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `hasil_tryout_id` | BIGINT UNSIGNED FK → `hasil_tryout.id` CASCADE | |
| `soal_id` | BIGINT UNSIGNED FK → `soal.id` CASCADE | |
| `jawaban_siswa` | ENUM('a','b','c','d') NULL | null = belum jawab; diubah nullable via migrasi `add_ragu` |
| `benar_salah` | BOOLEAN DEFAULT false | `jawaban_siswa == soal.jawaban_benar` |
| `ragu` | BOOLEAN DEFAULT false | toggle ragu (migrasi `add_ragu`) |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.14 `notifikasi`

| Kolom | Tipe | Ket |
|-------|------|-----|
| `id` | BIGINT UNSIGNED PK AI | |
| `user_id` | BIGINT UNSIGNED FK → `users.id` CASCADE | penerima |
| `tipe` | VARCHAR(255) | `jadwal`/`nilai`/`peringatan`/`pencapaian` |
| `judul` | VARCHAR(255) | ex: `Tryout Baru: UTS MTK` |
| `pesan` | TEXT | |
| `sudah_dibaca` | BOOLEAN DEFAULT false | |
| `created_at`, `updated_at` | TIMESTAMP | |

### 1.15 Lainnya (Breeze / queue)

- `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs` — default Laravel, tidak dipakai domain logic.

---

## 2. Relasi Antar Tabel

```
users 1──0..1 guru
users 1──0..1 siswa
users 1──0..1 orang_tua
users 1──* notifikasi

kelas 1──* siswa
orang_tua 1──* siswa (nullable, nullOnDelete)

mata_pelajaran 1──* soal
mata_pelajaran 1──* tryout
guru 1──* soal
guru 1──* tryout

tryout *──* soal  via tryout_soal
tryout 1──* hasil_tryout
siswa 1──* hasil_tryout
hasil_tryout 1──* detail_jawaban
soal 1──* detail_jawaban
```

FK `cascadeOnDelete` — hapus parent hapus child (kecuali `siswa.orang_tua_id` → `nullOnDelete`).

---

## 3. Diagram ERD (Mermaid)

```mermaid
erDiagram
    users ||--o| guru : hasOne
    users ||--o| siswa : hasOne
    users ||--o| orang_tua : hasOne
    users ||--o{ notifikasi : hasMany

    kelas ||--o{ siswa : hasMany
    orang_tua ||--o{ siswa : hasMany

    mata_pelajaran ||--o{ soal : hasMany
    mata_pelajaran ||--o{ tryout : hasMany
    guru ||--o{ soal : hasMany
    guru ||--o{ tryout : hasMany

    tryout ||--o{ tryout_soal : hasMany
    soal ||--o{ tryout_soal : hasMany

    tryout ||--o{ hasil_tryout : hasMany
    siswa ||--o{ hasil_tryout : hasMany

    hasil_tryout ||--o{ detail_jawaban : hasMany
    soal ||--o{ detail_jawaban : hasMany

    users {
        bigint id PK
        string email UK
        string name
        enum role
        string password
    }
    kelas {
        bigint id PK
        string nama
        string tingkat
    }
    mata_pelajaran {
        bigint id PK
        string nama
        string kode UK
    }
    guru {
        bigint id PK
        bigint user_id FK
        string nip UK
    }
    orang_tua {
        bigint id PK
        bigint user_id FK
        string pekerjaan
        string no_hp
    }
    siswa {
        bigint id PK
        bigint user_id FK
        string nis UK
        bigint kelas_id FK
        bigint orang_tua_id FK_NULL
    }
    soal {
        bigint id PK
        bigint mapel_id FK
        bigint guru_id FK
        text pertanyaan
        enum jawaban_benar
        enum tingkat_kesulitan
    }
    tryout {
        bigint id PK
        string nama
        bigint mapel_id FK
        bigint guru_id FK
        int durasi_menit
        datetime tanggal_mulai
        datetime tanggal_selesai
        enum status
    }
    tryout_soal {
        bigint id PK
        bigint tryout_id FK
        bigint soal_id FK
        int urutan
    }
    hasil_tryout {
        bigint id PK
        bigint tryout_id FK
        bigint siswa_id FK
        decimal nilai
        int jumlah_benar
        int jumlah_salah
        int waktu_menit
        datetime waktu_submit
        datetime started_at
    }
    detail_jawaban {
        bigint id PK
        bigint hasil_tryout_id FK
        bigint soal_id FK
        enum jawaban_siswa_NULL
        boolean benar_salah
        boolean ragu
    }
    notifikasi {
        bigint id PK
        bigint user_id FK
        string tipe
        string judul
        text pesan
        boolean sudah_dibaca
    }
```

Render: paste ke https://mermaid.live atau GitHub markdown preview.

---

## 4. Index & Constraint Penting

- `users.email` UNIQUE — cegah duplikat akun.
- `guru.nip` UNIQUE NULL — nullable unique.
- `siswa.nis` UNIQUE — NIS unik.
- `mata_pelajaran.kode` UNIQUE — kode mapel unik.
- `tryout_soal(tryout_id, soal_id)` UNIQUE — soal tidak duplikat dalam 1 tryout.
- `hasil_tryout(tryout_id, siswa_id)` UNIQUE — 1 siswa 1 submit per tryout.
- FK cascade — hapus `kelas` hapus `siswa`, hapus `guru` hapus `soal/tryout`, hapus `tryout` hapus `tryout_soal/hasil_tryout`.

---

## 5. Seeder Default

`DatabaseSeeder` membuat:

- `admin@tryout.test` (admin)
- `guru@tryout.test` (guru, NIP 198001012000011001)
- `siswa@tryout.test` (Kayla Putri, NIS 20250001, kelas 9A, link ortu)
- `kayla.adik@tryout.test` (Kayla Adik, NIS 20250002, kelas 9A, link ortu sama)
- `orangtua@tryout.test` (orang tua, linked 2 siswa)
- `kelas` 9A, `mata_pelajaran` (MTK/IPA/IPS/B.Indonesia), `soal` 5, `tryout` 2 aktif (5 soal, durasi 60, window now±7d)

Password semua: `password`.

---

## 6. Migrasi File List

```
0001_01_01_000000_create_users_table.php
0001_01_01_000001_create_cache_table.php
0001_01_01_000002_create_jobs_table.php
2026_09_10_232903_create_kelas_table.php
2026_09_10_232904_create_mata_pelajaran_table.php
2026_09_10_232904_create_guru_table.php
2026_09_10_232904_create_orang_tua_table.php
2026_09_10_232904_create_siswa_table.php
2026_09_10_232904_create_soal_table.php
2026_09_10_232905_1_create_tryout_table.php
2026_09_10_232905_2_create_tryout_soal_table.php
2026_09_10_232905_3_create_hasil_tryout_table.php
2026_09_10_232905_4_create_detail_jawaban_table.php
2026_09_10_232905_5_create_notifikasi_table.php
2026_09_11_071201_add_ragu_to_detail_jawaban.php  # adds hasil_tryout.started_at + detail_jawaban.ragu + jawaban_siswa nullable
```
