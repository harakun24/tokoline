### TokoLine

# Web Toko Online Sederhana

## Hal yang perlu diinstall

untuk menjalankan web toko sederhana ini diperlukan beberapa aplikasi berikut:

-   PHP (v8.4)
-   composer
-   PostgreSQL (database)
-   Node.js

### konfigurasi ekstensi php

masuk ke file `php.ini` dan aktifkan beberapa ekstensi berikut dengan cara menghapus tanda `;` di depan extension

extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
extension=zip

ubah ukuran upload_max_filesize supaya foto bukti transfer dapat diupload dengan lancar (jika file sedikit lebih besar)
contoh: upload_max_filesize = 10M

# menjalankan web

isi konfigurasi file pada `.env`

tambahkan baris berikut untuk login sebagai super user (mengelola user Admin, CS1 dan CS2)

SU_USER=sesuaikan
SU_PASSWORD=sesuaikan

sesuaikan kolom database untuk koneksi ke database

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tokoline
DB_USERNAME=root
DB_PASSWORD=root

## installasi depedency

install keperluan Laravel

`composer install`

install keperluan javascript

`npm install`

## konfigurasi database

hidupkan postgreSQL terlebih dahulu lalu jalankan

`php artisan migrate`

## build front-end

build kebutuhan front-end dengan perintah berikut

`npm run build` atau jika dalam proses development(jangan dimatikan) `npm run dev`

### otomasi tugas

Buat cronjob pada linux:

`* * * * * php /lokasi_project/artisan schedule:run >> /dev/null 2>&1`

atau jalankan file node berikut (jangan ditutup)
`node autoBatal.js`

## Jalankan server

`php artisan serve`

### Mengelola user

masuk manual ke `/kelola_barang`

masuk menggunakan akun yang disetting pada `.env`

tambahkan admin,cs1 dan cs2 dari panel super admin

masuk menggunakan akun yang telah dibuat

#### Admin

-   mengelola barang dan kategori
-   bulk input

#### CS1

-   validasi transaksi
-   otomatis terbatalkan jika 1x24 jam tidak tervalidasi

#### CS2

-   pengemasan barang
-   pengiriman barang
-   memantau sampai barang diterima

## pengunjung

untuk pengunjung dapat melihat katalog barang. namun untuk memasukkan keranjang perlu masuk terlebih dulu.

Jika belum memiliki akun maka silahkan membuat akun terlebih dulu.
