# Hide and Show Form per Unit

Tanggal: 23 September 2026

## Perilaku

- Admin mengatur visibilitas setiap form pada tab **Hide and Show** milik Unit.
- Default form adalah tampil apabila belum memiliki konfigurasi.
- Form yang disembunyikan tidak menghapus konfigurasi Hide/Show pertanyaannya.
- Preview admin tidak menampilkan form tersembunyi.
- User dan Surveyor otomatis melewati form tersembunyi pada navigasi maju, navigasi mundur, akses URL langsung, validasi kelengkapan, dan penyelesaian survei.
- Endpoint penyimpanan juga menolak pemrosesan form tersembunyi dan meneruskan pengguna ke form berikutnya.

## Database

Migration `2026_09_23_010000_create_unit_form_visibilities_table.php` membuat tabel `unit_form_visibilities` dengan kombinasi unik `unit_id` dan `form_id`.

## Penerapan server

Jalankan `php artisan migrate`, `php artisan optimize:clear`, dan build aset frontend apabila deployment tidak membangun Vite secara otomatis.
