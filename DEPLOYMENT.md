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

Atur `APP_URL` ke URL HTTPS final, misalnya `https://budget.domain-anda.com`. Jangan commit `.env.production`.

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

## 3. Pasang HTTPS

Letakkan reverse proxy HTTPS (Caddy, Nginx, atau Traefik) di depan port `APP_PORT` dari service `web`. Proxy harus meneruskan header `Host` dan `X-Forwarded-Proto`. Bila memakai firewall, buka hanya port 80/443 milik reverse proxy; MariaDB tetap internal Docker.

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
