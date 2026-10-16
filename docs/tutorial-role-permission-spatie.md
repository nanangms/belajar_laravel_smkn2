# Tutorial Role & Permission dengan Spatie di Laravel

Panduan ini menjelaskan cara menambahkan role dan permission menggunakan
`spatie/laravel-permission` pada proyek ini. Contohnya disesuaikan dengan
Laravel 12, autentikasi Breeze, dan resource `users`, `berita`, serta
`kategori`.

> Panduan ini adalah instruksi saja. Kode aplikasi belum diubah. Sebelum
> menjalankan migrasi pada database berisi data penting, buat backup terlebih
> dahulu dan uji langkah-langkahnya di lingkungan pengembangan.

## 1. Memahami role dan permission

- **Permission** menyatakan tindakan tertentu pada modul, misalnya
  `user.create`, `user.list`, `user.edit`, atau `user.delete`.
- **Role** mengelompokkan sejumlah permission, misalnya role `Admin` mendapat
  semua permission dan role `Editor` hanya mendapat permission untuk modul
  berita dan kategori.
- User diberi role, lalu memperoleh permission dari role tersebut. Permission
  juga dapat diberikan langsung kepada user bila memang dibutuhkan.

Gunakan format `<modul>.<aksi>` secara konsisten. Contoh permission pada
panduan ini:

| Modul | Permission |
|---|---|
| User | `user.create`, `user.list`, `user.edit`, `user.delete` |
| Berita | `berita.create`, `berita.list`, `berita.edit`, `berita.delete` |
| Kategori | `kategori.create`, `kategori.list`, `kategori.edit`, `kategori.delete` |

Permission mengatur akses tiap tindakan, sedangkan role memudahkan pengelolaan
sekumpulan permission. Periksa permission pada route atau controller; jangan
hanya mengandalkan nama role.

## 2. Pasang paket

Dari root proyek, jalankan:

```bash
composer require spatie/laravel-permission
```

Publikasikan konfigurasi dan migration paket:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Perintah ini menambahkan konfigurasi `config/permission.php` dan migration
untuk tabel role, permission, dan relasinya. Setelah meninjau konfigurasi serta
migration yang dipublikasikan, jalankan:

```bash
php artisan migrate
```

Jangan menghapus atau mengedit tabel pengguna yang sudah ada. Paket
menyimpan relasi role/permission di tabel terpisah.

## 3. Tambahkan trait pada model User

Di `app/Models/User.php`, tambahkan trait `HasRoles` milik Spatie ke model:

```php
use Spatie\Permission\Traits\HasRoles;
```

Tambahkan trait tersebut pada deklarasi trait dalam class `User`:

```php
use HasFactory, Notifiable, HasRoles;
```

Model `User` harus tetap mewarisi `Illuminate\Foundation\Auth\User` seperti
sebelumnya. Trait ini menyediakan method seperti `assignRole`,
`hasRole`, `givePermissionTo`, dan `can`.

## 4. Daftarkan middleware Spatie untuk Laravel 12

Pada Laravel 12, alias middleware aplikasi didaftarkan di
`bootstrap/app.php`. Di callback `withMiddleware`, daftarkan alias yang akan
digunakan pada route:

```php
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

// Di dalam withMiddleware(function (Middleware $middleware): void { ... })
$middleware->alias([
    'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
]);
```

Pertahankan middleware lain yang mungkin sudah ditambahkan ke callback
tersebut. Alias memungkinkan route menggunakan `role:...` dan
`permission:...`.

## 5. Buat role dan permission dengan seeder

Buat seeder:

```bash
php artisan make:seeder RolePermissionSeeder
```

Isi `database/seeders/RolePermissionSeeder.php` dengan definisi role dan
permission berikut:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = ['user', 'berita', 'kategori'];
        $actions = ['create', 'list', 'edit', 'delete'];
        $permissionNames = [];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionNames[] = "{$module}.{$action}";
            }
        }

        foreach ($permissionNames as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::findOrCreate('Admin', 'web');
        $editor = Role::findOrCreate('Editor', 'web');
        $user = Role::findOrCreate('User', 'web');

        $admin->syncPermissions($permissionNames);
        $editor->syncPermissions([
            'berita.create',
            'berita.list',
            'berita.edit',
            'berita.delete',
            'kategori.create',
            'kategori.list',
            'kategori.edit',
            'kategori.delete',
        ]);
        $user->syncPermissions([]);
    }
}
```

`web` guard digunakan karena aplikasi memakai autentikasi session Laravel.
Role `User` dalam contoh ini tidak menerima permission administratif.

Panggil seeder dari `database/seeders/DatabaseSeeder.php`, di dalam method
`run()`:

```php
$this->call(RolePermissionSeeder::class);
```

Setelah memastikan seeder tidak menghapus atau mengganti data yang ada,
jalankan:

```bash
php artisan db:seed
```

Seeder di atas hanya membuat role dan permission. Ia tidak menetapkan role
admin secara otomatis kepada akun tertentu.

## 6. Berikan role kepada user yang sudah ada

Tetapkan role kepada akun melalui Tinker setelah memastikan email akun yang
benar. Ganti alamat contoh dengan email user yang memang akan diberi akses:

```bash
php artisan tinker
```

Kemudian di prompt Tinker:

```php
$user = \App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->assignRole('Admin');
```

Untuk menetapkan Editor atau User:

```php
$user->assignRole('Editor');
```

Jangan menetapkan role Admin kepada akun yang tidak berwenang. Untuk
lingkungan production, proses pembuatan dan pemberian akses admin sebaiknya
dilakukan secara terkontrol, bukan melalui kredensial yang ditulis langsung
di seeder atau repository.

## 7. Lindungi route berdasarkan permission per aksi

Route aplikasi saat ini sudah berada di dalam grup `auth`. Tambahkan
middleware sesuai aksi pada resource di `routes/web.php`. Laravel 12
mendukung `middlewareFor` pada resource registration sehingga setiap aksi
dapat memakai permission tersendiri:

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
        ->name('dashboard');

    Route::resource('users', UserController::class)
        ->middlewareFor(['index', 'show'], 'permission:user.list')
        ->middlewareFor(['create', 'store'], 'permission:user.create')
        ->middlewareFor(['edit', 'update'], 'permission:user.edit')
        ->middlewareFor('destroy', 'permission:user.delete');

    Route::resource('berita', BeritaController::class)
        ->middlewareFor(['index', 'show'], 'permission:berita.list')
        ->middlewareFor(['create', 'store'], 'permission:berita.create')
        ->middlewareFor(['edit', 'update'], 'permission:berita.edit')
        ->middlewareFor('destroy', 'permission:berita.delete');

    Route::resource('kategori', KategoriController::class)
        ->middlewareFor(['index', 'show'], 'permission:kategori.list')
        ->middlewareFor(['create', 'store'], 'permission:kategori.create')
        ->middlewareFor(['edit', 'update'], 'permission:kategori.edit')
        ->middlewareFor('destroy', 'permission:kategori.delete');

    // Route profile tetap dapat mengikuti aturan auth yang sudah ada.
});
```

Pemetaan untuk resource controller adalah:

| Permission | Method resource | Kegunaan |
|---|---|---|
| `<modul>.list` | `index`, `show` | Melihat daftar dan detail |
| `<modul>.create` | `create`, `store` | Membuka form tambah dan menyimpan data baru |
| `<modul>.edit` | `edit`, `update` | Membuka form edit dan menyimpan perubahan |
| `<modul>.delete` | `destroy` | Menghapus data |

Contoh di atas memberi izin `user.list` untuk melihat daftar maupun detail.
Jika aplikasi perlu membedakan keduanya, tambahkan permission seperti
`user.view` dan gunakan untuk method `show`. Pastikan route profile yang sudah
ada tetap berada di grup `auth`.

Untuk memeriksa permission dari controller atau kode aplikasi, gunakan
`can`/otorisasi Laravel dengan nama permission yang sama:

```php
if (auth()->user()->can('user.create')) {
    // User berhak membuat data pengguna.
}
```

Jangan memasang satu permission pada seluruh resource bila tiap aksinya perlu
hak akses berbeda. Permission route menjadi perlindungan request di server;
role cukup digunakan untuk mengelompokkan permission.

## 8. Sembunyikan kontrol di Blade

Middleware mencegah request yang tidak berizin. Di tampilan, sembunyikan
tombol atau menu yang tidak relevan menggunakan directive `@can`:

```blade
@can('berita.create')
    <a href="{{ route('berita.create') }}">Tambah berita</a>
@endcan
```

Contoh kontrol edit dan hapus:

```blade
@can('berita.edit')
    <a href="{{ route('berita.edit', $berita) }}">Edit</a>
@endcan

@can('berita.delete')
    <form method="POST" action="{{ route('berita.destroy', $berita) }}">
        @csrf
        @method('DELETE')
        <button type="submit">Hapus</button>
    </form>
@endcan
```

Penyembunyian elemen tampilan hanya membantu UX; ini bukan pengganti
middleware atau pemeriksaan otorisasi di server.

## 9. Mengelola role dan permission

Contoh operasi yang tersedia pada model user:

```php
$user->assignRole('Editor');
$user->removeRole('Editor');
$user->syncRoles(['Editor']);

$user->givePermissionTo('berita.create');
$user->revokePermissionTo('berita.create');
$user->hasRole('Admin');
$user->can('berita.list');
```

`syncRoles` mengganti role yang sudah dimiliki user dengan daftar yang
diberikan. Gunakan dengan hati-hati agar tidak menghapus role yang masih
diperlukan.

## 10. Verifikasi

Periksa route dan middleware yang terdaftar:

```bash
php artisan route:list
```

Lalu uji setidaknya skenario berikut:

1. Guest diarahkan ke autentikasi saat membuka halaman yang membutuhkan
   `auth`.
2. User yang hanya memiliki `user.list` dapat melihat daftar pengguna, tetapi
   tidak dapat membuat, mengubah, atau menghapusnya.
3. User dengan `user.create` dapat membuka form tambah dan menyimpan pengguna,
   tetapi tidak otomatis memiliki izin edit atau delete.
4. Ulangi pemeriksaan pada modul berita dan kategori serta pastikan setiap
   role hanya memiliki permission yang ditetapkan.
5. Menu atau tombol yang dilindungi `@can` hanya muncul untuk user yang
   berwenang; akses langsung ke endpoint tanpa izin tetap ditolak.
6. Setelah perubahan role atau permission, hasil otorisasi sesuai dengan data
   terbaru.

Jika konfigurasi atau cache aplikasi berubah, bersihkan cache:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

## Catatan penting

- Jangan hanya menyembunyikan menu di Blade; lindungi endpoint di server.
- Pastikan guard role dan permission sama dengan guard user (`web` pada contoh
  ini).
- Hindari memberikan role Admin secara otomatis kepada semua user baru.
- Untuk aturan tambahan yang melibatkan kepemilikan atau isi data tertentu,
  lengkapi permission per aksi dengan Policy Laravel.
- Dokumentasi resmi: [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)
  dan [otorisasi Laravel](https://laravel.com/docs/12.x/authorization).
