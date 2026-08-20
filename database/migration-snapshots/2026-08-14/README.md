# Migration Create Ringkas — 2026-08-14

Folder ini berisi snapshot skema akhir dalam bentuk migration `create_*` saja.
Perubahan dari migration `add`, `repair`, `drop`, dan `expand` telah digabungkan
langsung ke file create tabel terkait agar daftar migration tidak menumpuk.

Folder ini tidak dibaca otomatis oleh Laravel dan tidak memengaruhi survei yang
sedang berjalan.

## Penggunaan untuk database lokal/baru

Folder ini ditujukan untuk database kosong yang memang boleh dihapus seluruhnya.
Jangan jalankan perintah berikut pada database produksi atau database survei.

```bash
php artisan config:clear
php artisan migrate:fresh --path=database/migration-snapshots/2026-08-14 --seed
```

Selalu periksa nilai `DB_DATABASE` di `.env` sebelum menjalankan
`migrate:fresh`. Perintah tersebut menghapus seluruh tabel dan data pada database
yang sedang aktif.

## Production setelah survei selesai

Untuk production yang sudah memiliki data, tetap gunakan migration utama tanpa
`fresh`:

```bash
php artisan config:clear
php artisan migrate:status
php artisan migrate --force
```

Jangan menyalin snapshot ringkas ini ke `database/migrations` pada installation
yang sudah memiliki riwayat migration. Snapshot ini khusus pembangunan database
baru dari nol.