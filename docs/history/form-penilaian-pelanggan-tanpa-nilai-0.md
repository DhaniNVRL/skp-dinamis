# Form Penilaian Pelanggan Tanpa Nilai 0

Tanggal: 23 September 2026

## Permintaan

Menambahkan dua tipe Form Penilaian Pelanggan yang berperilaku sama seperti tipe lama, tetapi tidak menyediakan dan tidak menerima nilai 0 pada admin maupun role user:

- ID 15: skala 1–5 tanpa nilai 0.
- ID 16: skala 1–7 tanpa nilai 0.

## Cakupan implementasi

- Master `form_types`, seeder, dan migration produksi.
- Pemetaan pembuatan/edit pertanyaan admin memakai template tipe 2 dan 3.
- Hide and Show serta preview admin tetap bersifat per Sub Unit.
- Pengisian user/surveyor memakai renderer Penilaian Pelanggan yang sama dengan konfigurasi tanpa nilai 0.
- Validasi server menolak nilai 0 untuk skala Kepentingan, Kinerja, dan indikator tunggal pada tipe 15/16.
- Raw Data Export memperlakukan tipe 15/16 sama seperti Penilaian Pelanggan.
- Dropdown Tambah Form dan Edit Form menampilkan tipe 15/16 setelah migration dijalankan.
- Kartu pengelolaan form admin memakai renderer Penilaian Pelanggan 1-5/1-7 sehingga form dapat ditambah dan diedit tanpa pesan tipe tidak ditemukan.
- Tambah/Edit Pertanyaan memiliki template khusus tanpa nilai 0 untuk tipe 15/16.

## Cara menerapkan

Jalankan `php artisan migrate`, lalu bersihkan cache aplikasi dengan `php artisan optimize:clear`. Untuk database baru melalui `migrate:fresh --seed`, `FormTypeSeeder` juga sudah memuat kedua tipe.
