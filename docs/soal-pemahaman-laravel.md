# Tugas Praktik CRUD Berita Laravel

## Skenario

Buat fitur pengelolaan berita sederhana. Pengguna yang sudah login harus dapat melihat daftar berita, menambahkan berita baru, melihat detail berita, mengubah berita, dan menghapus berita.

Gunakan project Laravel ini sebagai dasar pengerjaan. Fitur harus menerapkan hubungan antara **route**, **controller**, **view**, dan **model**.

## Target Fitur

Fitur CRUD harus memiliki halaman berikut:

| Halaman | URL yang disarankan | Fungsi |
| --- | --- | --- |
| Daftar berita | `/berita` | Menampilkan seluruh berita |
| Form tambah | `/berita/create` | Menampilkan form berita baru |
| Detail berita | `/berita/{berita}` | Menampilkan satu berita |
| Form edit | `/berita/{berita}/edit` | Menampilkan form perubahan berita |
| Proses simpan | `POST /berita` | Menyimpan berita baru |
| Proses ubah | `PUT/PATCH /berita/{berita}` | Menyimpan perubahan berita |
| Proses hapus | `DELETE /berita/{berita}` | Menghapus berita |

## Data Berita

Gunakan model `Berita` dan tabel `beritas` yang sudah tersedia. Field yang digunakan:

- `judul`: wajib diisi
- `slug`: wajib diisi dan harus unik
- `isi`: wajib diisi
- `gambar`: boleh kosong
- `kategori_id`: wajib memilih kategori

## Petunjuk Pengerjaan

### 1. Siapkan Model dan Database

- Periksa model `Berita` di `app/Models/Berita.php`.
- Pastikan semua field yang akan disimpan tercantum pada `$fillable`.
- Pastikan migration berita sudah dijalankan.
- Gunakan model `Kategori` untuk mengambil pilihan kategori pada form.

### 2. Buat Controller

Buat atau lengkapi `BeritaController` di `app/Http/Controllers`.

Gunakan method berikut:

- `index()` untuk mengambil semua berita.
- `create()` untuk menampilkan form tambah.
- `store()` untuk memvalidasi dan menyimpan data baru.
- `show()` untuk menampilkan detail berita.
- `edit()` untuk menampilkan form edit.
- `update()` untuk memvalidasi dan memperbarui data.
- `destroy()` untuk menghapus data.

Petunjuk tambahan:

- Gunakan route model binding untuk parameter `{berita}`.
- Gunakan validasi Laravel sebelum menyimpan atau mengubah data.
- Setelah aksi berhasil, kembalikan pengguna ke daftar berita dengan pesan sukses.
- Jika validasi gagal, tampilkan kembali form beserta pesan kesalahan.

### 3. Atur Route

Tambahkan route resource pada `routes/web.php` menggunakan `BeritaController`.

Petunjuk:

```php
Route::resource('berita', BeritaController::class);
```

Letakkan route di dalam middleware `auth` agar hanya pengguna yang login yang dapat mengelola berita.

### 4. Buat View Blade

Buat folder `resources/views/berita` dan minimal file berikut:

- `index.blade.php` untuk daftar berita.
- `create.blade.php` untuk form tambah.
- `show.blade.php` untuk detail berita.
- `edit.blade.php` untuk form edit.
- `_form.blade.php` sebagai partial form yang dapat digunakan bersama oleh halaman tambah dan edit.

Setiap view harus memiliki:

- Link atau tombol menuju halaman tambah.
- Tombol detail, edit, dan hapus pada daftar berita.
- Form dengan `@csrf`.
- Form edit dengan `@method('PUT')`.
- Konfirmasi sebelum menghapus data.
- Tampilan pesan sukses dan pesan validasi.

### 5. Hubungkan Model, Controller, dan View

Pastikan alur aplikasi berjalan seperti berikut:

1. Pengguna membuka URL berita.
2. Route memanggil method pada `BeritaController`.
3. Controller meminta atau mengubah data melalui model `Berita`.
4. Controller mengirim data ke view Blade.
5. View menampilkan hasil kepada pengguna.

## Ketentuan Tampilan

- Gunakan layout utama project jika tersedia.
- Tampilkan judul, slug, kategori, isi, dan gambar berita jika ada.
- Gunakan tabel untuk halaman daftar berita.
- Gunakan form yang memiliki label untuk setiap input.
- Tampilkan keterangan jika data berita masih kosong.

## Checklist Pengujian

- [ ] Halaman daftar berita dapat dibuka.
- [ ] Form tambah dapat dibuka.
- [ ] Data baru dapat disimpan.
- [ ] Validasi berjalan ketika input kosong atau slug duplikat.
- [ ] Detail berita dapat dilihat.
- [ ] Data berita dapat diedit.
- [ ] Data berita dapat dihapus.
- [ ] Pesan sukses muncul setelah simpan, ubah, dan hapus.
- [ ] Pengguna yang belum login tidak dapat mengakses CRUD berita.

## Bonus

Tambahkan fitur berikut jika CRUD dasar sudah selesai:

- Pencarian berdasarkan judul berita.
- Pagination pada daftar berita.
- Upload gambar berita.
- Filter berita berdasarkan kategori.
- Slug dibuat otomatis dari judul.

## Alternatif Modul: CRUD Pengumuman Sekolah

Jika modul Berita dirasa kurang sesuai, gunakan studi kasus **Pengumuman Sekolah**. Alur pengerjaannya sama, tetapi data yang dikelola lebih relevan untuk website sekolah.

### Skenario

Buat fitur untuk mengelola pengumuman sekolah. Admin yang sudah login dapat menampilkan, menambahkan, melihat detail, mengubah, dan menghapus pengumuman.

### Data Pengumuman

Buat model dan tabel `Pengumuman` dengan field berikut:

- `judul`: wajib diisi.
- `slug`: wajib diisi dan harus unik.
- `isi`: wajib diisi.
- `tanggal_publish`: wajib diisi dengan tanggal yang valid.
- `status`: wajib dipilih, yaitu `draft` atau `terbit`.
- `gambar`: boleh kosong.

### Petunjuk Pengerjaan

1. Buat migration tabel `pengumumans`.
2. Buat model `Pengumuman` dan isi `$fillable` sesuai field yang digunakan.
3. Buat `PengumumanController` dengan method `index`, `create`, `store`, `show`, `edit`, `update`, dan `destroy`.
4. Tambahkan `Route::resource('pengumuman', PengumumanController::class)` di dalam middleware `auth`.
5. Buat view `index`, `create`, `show`, dan `edit` di `resources/views/pengumuman`.
6. Gunakan partial `_form.blade.php` untuk form tambah dan edit.
7. Tampilkan hanya pengumuman dengan status `terbit` pada halaman publik.
8. Tampilkan semua status pengumuman pada halaman admin.
9. Tambahkan validasi untuk judul, slug, isi, tanggal terbit, dan status.
10. Setelah proses simpan, ubah, atau hapus berhasil, arahkan kembali ke daftar pengumuman dan tampilkan pesan sukses.

### Target URL

Gunakan pola resource berikut:

| Halaman | URL |
| --- | --- |
| Daftar pengumuman | `/pengumuman` |
| Form tambah | `/pengumuman/create` |
| Detail pengumuman | `/pengumuman/{pengumuman}` |
| Form edit | `/pengumuman/{pengumuman}/edit` |

### Checklist Modul Pengumuman

- [ ] Admin dapat menambah pengumuman baru.
- [ ] Admin dapat mengubah status `draft` menjadi `terbit`.
- [ ] Pengumuman terbit tampil di halaman publik.
- [ ] Pengumuman draft tidak tampil di halaman publik.
- [ ] Admin dapat melihat detail pengumuman.
- [ ] Admin dapat mengubah dan menghapus pengumuman.
- [ ] Validasi form berjalan dengan benar.
