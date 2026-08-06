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

## Perbaikan utama

- Menyamakan route create Activity dengan `admin.storeactivity`.
- Memperbaiki foreign key Activity/Group menjadi `activity_id`.
- Melengkapi method controller yang sudah terdaftar pada route Data User dan Activity.
- Membuat tampilan Data User aman terhadap profile/user/role yang tidak lengkap.
- Menyamakan format bulk delete Data User dengan input `ids[]` dari JavaScript.
- Menambahkan partial empty state yang hilang.
- Memperbaiki pencocokan role agar tidak sensitif terhadap kapitalisasi.
- Membersihkan dependency Node khusus Windows dan menyertakan hasil build Vite production.
