# Panduan Hosting Laravel di Vercel

Proyek ini menggunakan Laravel 12, Vite, dan FrankenPHP melalui Dockerfile khusus Vercel (`Dockerfile.vercel`).

## Persyaratan

- Akun Vercel.
- Repository GitHub/GitLab/Bitbucket yang berisi proyek ini.
- Dockerfile dan Caddyfile di direktori utama proyek.
- Database eksternal jika aplikasi nantinya menyimpan data yang harus bertahan.
- Node.js dan Docker untuk pengujian lokal (opsional, tetapi disarankan).

## File deployment yang ditambahkan/diubah

- `Dockerfile.vercel`: membangun aset Vite, memasang dependensi Composer, dan menjalankan Laravel menggunakan FrankenPHP.
- `Caddyfile`: mengarahkan web server ke folder `public` Laravel.
- `.dockerignore`: mencegah file rahasia, vendor, dan node_modules masuk ke image Docker.
- `.vercelignore`: mengecualikan file yang tidak diperlukan dari proses upload Vercel.

Konfigurasi `vercel.json` lama dihapus karena menggunakan runtime PHP komunitas lama. Vercel saat ini mendokumentasikan deployment PHP melalui Container runtime/Docker.

## Langkah deploy

1. Pastikan folder proyek yang diunggah ke Git berisi `Dockerfile.vercel`, `Caddyfile`, `composer.json`, `composer.lock`, `package.json`, dan seluruh folder Laravel.
2. Push proyek ke repository Git.
3. Buka Vercel dan pilih **Add New → Project**, lalu impor repository tersebut.
4. Pada pengaturan proyek, pilih preset **Container** jika belum terdeteksi otomatis. Pastikan Root Directory menunjuk ke folder yang berisi `Dockerfile.vercel`.
5. Tambahkan Environment Variables berikut di pengaturan Vercel:

   | Nama | Nilai |
   |---|---|
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_KEY` | kunci aplikasi Laravel |
   | `APP_URL` | URL deployment Vercel, misalnya `https://nama-proyek.vercel.app` |
   | `LOG_CHANNEL` | `stderr` |
   | `CACHE_STORE` | `array` |
   | `SESSION_DRIVER` | `cookie` |

6. Klik **Deploy**. Setelah deployment berhasil, buka URL yang diberikan Vercel.

## Membuat APP_KEY

Di komputer yang memiliki PHP dan dependensi Composer proyek, jalankan:

```bash
php artisan key:generate --show
```

Salin hasilnya ke Environment Variable `APP_KEY` di Vercel. Jangan mengunggah file `.env` ke repository.

## Database dan file upload

- Halaman yang sekarang didefinisikan di `routes/web.php` menampilkan view Laravel. Jika aplikasi hanya menampilkan halaman-halaman tersebut, database eksternal belum tentu diperlukan.
- Jika aplikasi memakai login, formulir yang menyimpan data, atau fitur database, hubungkan ke database eksternal. Atur `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai penyedia database.
- File di dalam container tidak boleh dianggap sebagai penyimpanan permanen. Untuk upload yang harus tetap tersedia setelah container diganti, gunakan object storage seperti Vercel Blob atau layanan penyimpanan kompatibel S3.
- `SESSION_DRIVER=cookie` cocok untuk sesi sederhana. Jika aplikasi membutuhkan sesi server-side lintas instance, gunakan database atau Redis yang dikelola secara eksternal.
- `CACHE_STORE=array` tidak menyimpan cache secara permanen. Ganti dengan Redis/database eksternal jika aplikasi membutuhkan cache bersama atau persisten.

## Catatan penting

- `APP_DEBUG` harus `false` di production.
- Jangan memasukkan `.env`, password database, token, atau `APP_KEY` ke Git.
- Deployment belum dapat diuji sampai selesai di sini karena membutuhkan Docker dan akun/proyek Vercel. Jalankan deployment percobaan dan periksa **Deployment → Build Logs** serta **Runtime Logs** jika ada error.
- Container Vercel bersifat sementara; data yang ditulis ke filesystem container dapat hilang saat instance diganti.

## Referensi resmi

- Vercel: https://vercel.com/kb/guide/deploy-php-on-vercel-with-docker
- Vercel project configuration: https://vercel.com/docs/project-configuration/vercel-json
- Laravel deployment: https://laravel.com/docs/12.x/deployment
