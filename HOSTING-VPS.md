# Panduan Hosting Budget Buddies di VPS (Step by Step)

Panduan ini mengasumsikan VPS berbasis Ubuntu 22.04 atau 24.04, akses awal memakai user dengan hak sudo, dan aplikasi diakses lewat IP VPS (`http://IP-VPS`) tanpa domain.

Detail arsitektur dan perintah update ada di `DEPLOYMENT.md`.

## Langkah 1: Siapkan VPS dan masuk via SSH

1. Catat IP publik VPS, misalnya `103.10.20.30`.
2. Dari komputer lokal, masuk ke server:
   ```sh
   ssh user@103.10.20.30
   ```

## Langkah 2: Update sistem

```sh
sudo apt update && sudo apt upgrade -y
```

## Langkah 3: Atur firewall

Izinkan SSH lebih dulu agar tidak terkunci, lalu buka HTTP:

```sh
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw enable
sudo ufw status
```

Port 3306 (MariaDB) sengaja tidak dibuka. Database hanya bisa diakses dari dalam jaringan Docker.

## Langkah 4: Instal Docker dan Docker Compose

```sh
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```

Keluar lalu masuk lagi ke SSH agar grup `docker` aktif, kemudian cek:

```sh
docker --version
docker compose version
```

## Langkah 5: Clone repository

```sh
git clone <URL-REPOSITORY> budget-buddies
cd budget-buddies
```

Ganti `<URL-REPOSITORY>` dengan alamat repo Git Anda.

## Langkah 6: Buat file konfigurasi produksi

```sh
cp .env.production.example .env.production
nano .env.production
```

Ubah nilai berikut:

| Variabel | Isi |
|---|---|
| `APP_URL` | `http://IP-VPS`, misalnya `http://103.10.20.30` |
| `DB_PASSWORD` | Password kuat untuk user database |
| `DB_ROOT_PASSWORD` | Password kuat untuk root database |
| `APP_KEY` | Diisi di langkah 7 |

Simpan dengan `Ctrl+O`, Enter, lalu `Ctrl+X`.

## Langkah 7: Buat APP_KEY

Cara termudah adalah lewat container. Image harus sudah di-build, jadi jalankan perintah ini setelah langkah 8 (build pertama), lalu kembali ke langkah 8 untuk menjalankan ulang:

```sh
docker compose --env-file .env.production run --rm app php artisan key:generate --show
```

Jika ingin menjalankannya di mesin lokal yang sudah punya PHP dan Laravel, gunakan `php artisan key:generate --show` di sana.

Salin hasil yang diawali `base64:` ke `APP_KEY` di `.env.production`.

## Langkah 8: Build dan jalankan container

```sh
docker compose --env-file .env.production up -d --build
```

Tunggu hingga build selesai. Cek status:

```sh
docker compose --env-file .env.production ps
```

Pastikan `db` berstatus `healthy` dan `app`, `web`, `queue`, `scheduler` berstatus `running`.

## Langkah 9: Jalankan migrasi database

```sh
docker compose --env-file .env.production run --rm app php artisan migrate --force
```

## Langkah 10: Optimasi Laravel

```sh
docker compose --env-file .env.production exec app php artisan optimize
```

## Langkah 11: Buka aplikasi

Buka `http://IP-VPS` di browser. Jika halaman belum muncul, cek log:

```sh
docker compose --env-file .env.production logs -f web app
```

## Troubleshooting

**Halaman 502 atau error PHP**
- Pastikan service `app` berjalan: `docker compose --env-file .env.production ps`.
- Lihat log: `docker compose --env-file .env.production logs app`.

**Error `No application encryption key has been specified`**
- `APP_KEY` di `.env.production` kosong. Isi lalu jalankan ulang:
  ```sh
  docker compose --env-file .env.production up -d
  ```

**Tidak bisa diakses dari browser**
- Cek firewall: `sudo ufw status` harus menampilkan `80/tcp ALLOW`.
- Cek apakah port 80 dipakai service lain: `sudo ss -tlnp | grep :80`.

**Ingin memastikan `.env.production` tidak ikut ter-commit**
- File ini sudah dikecualikan dari image oleh `.dockerignore`. Pastikan juga tidak ada di Git: `git status` tidak menampilkannya sebagai file baru.

## Update aplikasi

```sh
cd budget-buddies
git pull
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production run --rm app php artisan migrate --force
docker compose --env-file .env.production exec app php artisan optimize
```

## Backup database

```sh
docker compose --env-file .env.production exec -T db mariadb-dump -uroot -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > backup-$(date +%F).sql
```

Jangan jalankan `docker compose down -v` di server produksi. Perintah itu menghapus volume database dan storage.
