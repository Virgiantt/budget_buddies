# Deploy ke VPS dengan Docker

Konfigurasi ini menjalankan Laravel dengan PHP-FPM, Nginx, MariaDB, queue worker, dan Laravel scheduler. Hanya Nginx yang membuka port ke host; MariaDB tidak dapat diakses langsung dari internet.

## 1. Siapkan VPS

Instal Docker Engine dan Docker Compose Plugin, lalu clone repository ke VPS. Buat file konfigurasi produksi dari contoh:

```sh
cp .env.production.example .env.production
```

Isi nilai rahasia yang kuat. Buat `APP_KEY` dengan perintah berikut (jalankan di mesin yang memiliki PHP/Laravel), lalu salin seluruh hasilnya ke `APP_KEY`:

```sh
php artisan key:generate --show
```

Atur `APP_URL` ke `http://IP-VPS`, misalnya `http://103.10.20.30`. Jangan commit `.env.production`.

## 2. Build dan jalankan

```sh
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production run --rm app php artisan migrate --force
docker compose --env-file .env.production exec app php artisan optimize
```

Cek status dan log bila perlu:

```sh
docker compose --env-file .env.production ps
docker compose --env-file .env.production logs -f web app queue scheduler
```

## 3. Akses via IP VPS

Aplikasi diakses lewat `http://IP-VPS`. Buka port 80 di firewall VPS. Service `web` yang mempublikasikan port 80; MariaDB tetap internal Docker dan tidak dipublikasikan.

Koneksi ini belum terenkripsi (HTTP). Login dan data dikirim tanpa TLS, sehingga sebaiknya hanya dipakai untuk uji coba atau sampai domain/HTTPS disiapkan.

## Update rilis

```sh
git pull
docker compose --env-file .env.production up -d --build
docker compose --env-file .env.production run --rm app php artisan migrate --force
docker compose --env-file .env.production exec app php artisan optimize
```

Sebelum pembaruan besar, buat backup database:

```sh
docker compose --env-file .env.production exec -T db mariadb-dump -uroot -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" > budget-buddies-backup.sql
```

`db-data` dan `app-storage` adalah Docker volumes persisten. Jangan menjalankan `docker compose down -v` di server produksi kecuali memang ingin menghapus database dan berkas storage.
