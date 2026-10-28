# Deploy Budget Buddies ke VPS

Stack: Nginx (`web`) + PHP-FPM (`app`) + SQLite + queue worker + scheduler.

Hanya service `web` yang membuka port ke host, dan hanya di loopback:

```
127.0.0.1:44000 -> container:80
```

Artinya aplikasi **tidak** bisa diakses langsung dari internet. Untuk membukanya
pakai SSH tunnel (untuk testing) atau reverse proxy di host (untuk produksi).

## 1. Deploy pertama: manual, sekali saja

Ya. Deploy pertama **harus manual** karena dua hal yang tidak bisa dilakukan CI:
instal Docker dan membuat `.env` berisi `APP_KEY`. Setelah itu semua update
otomatis lewat GitHub Actions.

### a. Instal Docker

```sh
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
```

Logout lalu login lagi (agar grup `docker` aktif), cek dengan `docker compose version`.

### b. Clone repo ke /opt

```sh
sudo mkdir -p /opt/budget-buddies
sudo chown $USER:$USER /opt/budget-buddies
git clone https://github.com/Virgiantt/budget_buddies.git /opt/budget-buddies
cd /opt/budget-buddies
```

Path `/opt/budget-buddies` ini juga yang dipakai workflow CI. Kalau diubah,
ubah juga `cd` di `.github/workflows/ci.yml`.

### c. Buat .env

```sh
cp .env.production.example .env
chmod 600 .env
nano .env
```

Isi minimal: `APP_URL`. Biarkan `DB_CONNECTION=sqlite` dan `DB_DATABASE`
apa adanya. `APP_KEY` dikosongkan dulu, diisi di langkah (d).

### d. Generate APP_KEY

```sh
docker compose build
docker compose run --rm --no-deps app php artisan key:generate --show
```

Salin hasil `base64:...` ke `APP_KEY=` di `.env`.

`APP_KEY` hanya dibuat **sekali**. Jangan diganti setelah ada data - semua
session dan data terenkripsi akan rusak.

### e. Jalankan

```sh
docker compose run --rm app php artisan migrate --force
docker compose up -d --wait
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan view:cache
docker compose ps
```

Pastikan `web` berstatus `healthy`, sisanya `running`.
File `storage/database/database.sqlite` otomatis dibuat saat migrasi pertama.

### f. Firewall

Port 44000 **tidak** perlu dibuka - sudah terikat ke 127.0.0.1.

```sh
sudo ufw allow OpenSSH
sudo ufw enable
```

Kalau nanti pakai reverse proxy: `sudo ufw allow 80,443/tcp`.

## 2. Letak file .env

Satu file saja, di **root project, sebelah `compose.yml`**:

```
/opt/budget-buddies/.env
```

Docker Compose membacanya otomatis (untuk interpolasi di compose.yml) dan
menyuntikkannya ke container lewat `env_file: .env`. Tidak perlu flag
`--env-file`, tidak perlu `.env.production`.

File ini di-gitignore, tidak ikut image, dan **tidak pernah ditimpa CI** karena
CI hanya melakukan `git reset --hard` pada file yang dilacak Git.

## 3. CI/CD (GitHub Actions)

Workflow: `.github/workflows/ci.yml` - jalan saat push ke `main` atau manual
(`workflow_dispatch`). Urutannya: job `test` dulu, kalau lulus baru `deploy`.

Tambahkan 4 secret di **Settings > Secrets and variables > Actions**:

| Secret | Isi |
|---|---|
| `VPS_HOST` | IP atau domain VPS |
| `VPS_USER` | user SSH (harus bisa menjalankan `docker` tanpa sudo) |
| `VPS_PASSWORD` | password SSH user tersebut |
| `VPS_PORT` | opsional, default 22 |

Login pakai password butuh `PasswordAuthentication yes` di sshd VPS. Kalau
server mematikan password auth, aktifkan sementara:

```sh
sudo sed -i 's/^PasswordAuthentication no/PasswordAuthentication yes/' /etc/ssh/sshd_config
sudo systemctl restart ssh
```

Yang dilakukan job deploy di server:

```sh
git reset --hard FETCH_HEAD      # samakan kode dengan origin/main (.env aman)
docker compose build --pull
docker compose run --rm app php artisan migrate --force
docker compose up -d --wait
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan queue:restart
```

Catatan: `route:cache` sengaja tidak dipakai karena `routes/web.php` masih
memakai closure (route `/`, `/login`, `/register`), dan `php artisan optimize`
akan gagal pada kondisi itu. Setelah closure diganti controller, tambahkan
`docker compose exec -T app php artisan route:cache` di workflow.

## 4. Cara mengakses aplikasi

**SSH tunnel (paling cepat, tanpa buka port):**

```sh
ssh -L 44000:127.0.0.1:44000 user@IP-VPS
```

Lalu buka `http://localhost:44000`. Set `APP_URL=http://localhost:44000`.

**Reverse proxy di host (untuk domain + HTTPS):**

Contoh Nginx di host, `/etc/nginx/sites-available/budget-buddies`:

```nginx
server {
    listen 80;
    server_name app.example.com;

    location / {
        proxy_pass http://127.0.0.1:44000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Set `APP_URL=https://app.example.com` dan `SESSION_SECURE_COOKIE=true`.
Karena Laravel belum mengonfigurasi trusted proxy, tambahkan di
`bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

## 5. Backup dan restore

```sh
# backup - hentikan penulis sebentar supaya file konsisten
docker compose stop app queue scheduler
docker compose cp app:/var/www/html/storage/database/database.sqlite ./backup-$(date +%F).sqlite
docker compose start app queue scheduler

# restore
docker compose stop app queue scheduler
docker compose run --rm --no-deps app sh -c 'rm -f storage/database/database.sqlite-wal storage/database/database.sqlite-shm'
docker compose cp ./backup-2026-01-01.sqlite app:/var/www/html/storage/database/database.sqlite
docker compose start app queue scheduler
```

### Di mana data sebenarnya disimpan

| Data | Lokasi | Hilang kalau? |
|---|---|---|
| Database SQLite | volume `app-storage`, file `storage/database/database.sqlite` | hanya `docker compose down -v` / `docker volume prune` |
| Upload & cache | volume `app-storage` | idem |
| `.env` | `/opt/budget-buddies/.env` | tidak pernah, di luar Git dan image |

`docker compose build`, `docker compose up -d`, dan `git reset --hard` di CI
**tidak** menyentuh volume. Yang menghapus data hanya perintah yang eksplisit
menyebut `-v` atau `docker volume rm`.

Jangan pernah jalankan `docker compose down -v` di server.

## 6. Troubleshooting

**`web` unhealthy / 502**

```sh
docker compose logs --tail=50 app web
docker compose exec app php artisan about
```

**`Database file at path [...] does not exist`**

File SQLite belum ada di volume. Entrypoint sekarang membuatnya otomatis
(`docker/php/entrypoint.sh`), jadi kalau error ini masih muncul berarti image
masih lama - jalankan `docker compose build` lalu `docker compose up -d`.

**`no such table: jobs` / `no such table: cache` di log `queue`**

Migrasi belum dijalankan. Container `queue` akan restart sendiri dan normal
setelah `docker compose run --rm app php artisan migrate --force` selesai.
Urutan yang benar: migrasi dulu, baru `up -d`.

**`No application encryption key has been specified`**

`APP_KEY` di `.env` masih kosong - ulangi langkah 1d lalu
`docker compose up -d --force-recreate`.

**Perubahan `.env` tidak terbaca**

```sh
docker compose up -d --force-recreate
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan view:cache
```

**Deploy CI gagal di `git reset`**

Ada perubahan lokal di `/opt/budget-buddies`. Cek `git status`, pindahkan
perubahan yang mau disimpan, lalu jalankan deploy ulang.
