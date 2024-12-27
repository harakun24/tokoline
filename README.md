### TokoLine

# Web Toko Online Sederhana

## cara installasi

### 1. pastikan program berikut sudah terinstall dan siap dijalankan

-   php (versi 8.4)

-   postgreSQL

-   node.js (untuk front-end)

-   composer

### 2. aktifkan beberapa ekstensi pada file `php.ini` dengan cara menghapus `;` di depan extensi

file php.ini berada satu folder dengan file php.exe

-   extension=intl
-   extension=pdo_pgsql
-   extension=pgsql
-   extension=zip
-   extension=fileinfo
-   extension=gd

### 3. ubah ukuran file maksimal yang dapat dikirim jika perlu (upload sampul barang)

upload_max_filesize = 10M

### 4. install dependensi laravel

masuk pada folder tokoline kemudian ketik perintah di terminal:

`commposer install`

### 5. salin file .env

tambahkan baris SU untuk membuat akun superadmin

```env
SU_USER=user
SU_PASSWORD=user
```

### 6. install dependensi javascript

`npm install`

### 7. jalankan database `postgreSQL`

### 8. migrasi database

`php artisan migrate`

### 9 persiapkan asset

untuk mode dev (jangan ditutup):

`npm run dev`

### 10 jalankan server laravel

`php artisan serve`

### 11 masuk menu admin

masuk ke url `/kelola_barang`
