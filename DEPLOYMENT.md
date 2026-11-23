# Deployment production

1. Backup project dan database production.
2. Pertahankan file `.env` production; jangan menimpanya dengan konfigurasi lokal.
3. Upload isi paket ini ke root project Laravel.
4. Pastikan folder `vendor` production tetap tersedia. Jika dependency berubah, jalankan `composer install --no-dev --optimize-autoloader`.
5. Jalankan:

   ```bash
   php artisan optimize:clear
   php artisan route:list
   ```

6. Pastikan `public/build/manifest.json` ikut terunggah.
7. Uji `/dataactivity` dan `/datauser`.
8. Jika masih terjadi HTTP 500, ambil error terbaru:

   ```bash
   grep "production.ERROR" storage/logs/laravel.log | tail -n 1
   ```

## Lonjakan hingga 1.000 pengguna

Halaman **"Resource Limit Is Reached"** berasal dari web server/hosting sebelum
Laravel menerima request. Karena itu, perubahan tampilan atau controller saja
tidak cukup bila paket hosting mencapai batas entry process, CPU, RAM, atau I/O.

Untuk deployment yang menerima lonjakan login, gunakan Redis yang dikelola
server untuk session, cache, dan queue. Isi `.env` production (sesuaikan host
dan kredensial Redis dari penyedia server):

```dotenv
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=redis
SESSION_STORE=default
SESSION_LOTTERY_NUMERATOR=0
SESSION_LOTTERY_DENOMINATOR=100
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

Kemudian jalankan setelah konfigurasi Redis aktif:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Session dan cache tidak lagi membebani MySQL pada setiap request. Jalankan
queue worker Redis melalui Supervisor/systemd jika ada pekerjaan antrean.

Untuk 1.000 login **secara bersamaan**, shared hosting umumnya tidak cukup.
Minta penyedia server menaikkan/meniadakan batas CloudLinux LVE (entry process,
CPU, RAM, dan I/O), atau pindahkan ke VPS/managed server dengan PHP-FPM,
OPcache, Redis, dan MySQL terpisah. Nginx dapat menerima koneksi secara ringan,
tetapi jumlah PHP-FPM worker dan kapasitas CPU tetap harus dihitung oleh
administrator server berdasarkan hasil load test.

## Perbaikan utama

- Menyamakan route create Activity dengan `admin.storeactivity`.
- Memperbaiki foreign key Activity/Group menjadi `activity_id`.
- Melengkapi method controller yang sudah terdaftar pada route Data User dan Activity.
- Membuat tampilan Data User aman terhadap profile/user/role yang tidak lengkap.
- Menyamakan format bulk delete Data User dengan input `ids[]` dari JavaScript.
- Menambahkan partial empty state yang hilang.
- Memperbaiki pencocokan role agar tidak sensitif terhadap kapitalisasi.
- Membersihkan dependency Node khusus Windows dan menyertakan hasil build Vite production.
