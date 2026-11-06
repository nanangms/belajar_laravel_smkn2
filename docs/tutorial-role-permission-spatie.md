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
| User | `user.create`, `user.list`, `user.edit`, `user.delete`, `user.assign-role` |
| Berita | `berita.create`, `berita.list`, `berita.edit`, `berita.delete` |
| Kategori | `kategori.create`, `kategori.list`, `kategori.edit`, `kategori.delete` |
| Role | `role.create`, `role.list`, `role.edit`, `role.delete` |
| Permission | `permission.create`, `permission.list`, `permission.edit`, `permission.delete` |

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

        $permissionNames[] = 'user.assign-role';

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
admin secara otomatis kepada akun tertentu. Untuk instalasi awal, tetapkan
role Admin sekali kepada akun administrator yang sudah diverifikasi. Setelah
itu, role user lain dikelola melalui CRUD User:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->assignRole('Admin');
```

Ganti email contoh dengan akun yang benar. Jangan simpan kredensial atau
menetapkan role Admin secara otomatis di seeder.

## 6. Berikan role melalui CRUD User

Role diberikan saat membuat atau mengedit user, bukan dengan menjalankan
perintah Tinker untuk setiap akun. Contoh berikut mengikuti resource
`users` dan view `admin.users` yang sudah ada di proyek.

### 6.1 Muat daftar role pada controller

Di `app/Http/Controllers/UserController.php`, tambahkan import:

```php
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
```

Ubah `create` dan `edit` agar mengirim daftar role untuk pilihan form:

```php
public function create()
{
    $roles = auth()->user()->can('user.assign-role')
        ? Role::where('guard_name', 'web')->orderBy('name')->get()
        : collect();

    return view('admin.users.create', compact('roles'));
}

public function edit(string $id)
{
    $user = User::findOrFail($id);
    $roles = auth()->user()->can('user.assign-role')
        ? Role::where('guard_name', 'web')->orderBy('name')->get()
        : collect();
    $selectedRoles = $user->getRoleNames()->all();

    return view('admin.users.edit', compact('user', 'roles', 'selectedRoles'));
}
```

Pada method `store`, validasi field `roles`, buat user, lalu sinkronkan
role yang dipilih:

```php
$validated = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'unique:users,email'],
    'password' => ['required', 'string', 'min:8'],
]);

if (auth()->user()->can('user.assign-role')) {
    $validated = $request->validate([
        'roles' => ['nullable', 'array'],
        'roles.*' => [
            'string',
            Rule::exists('roles', 'name')->where('guard_name', 'web'),
        ],
    ]) + $validated;
}

$user = User::create([
    'name' => $validated['name'],
    'email' => $validated['email'],
    'password' => $validated['password'],
]);

if (auth()->user()->can('user.assign-role')) {
    $user->syncRoles($validated['roles'] ?? []);
}
```

Ganti bagian validasi dan penyimpanan method `update` dengan pola berikut.
Rule `ignore` menjaga email user yang sedang diedit tetap valid:

```php
$validated = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($user->id),
    ],
    'password' => ['nullable', 'string', 'min:8'],
]);

if (auth()->user()->can('user.assign-role')) {
    $validated = $request->validate([
        'roles' => ['nullable', 'array'],
        'roles.*' => [
            'string',
            Rule::exists('roles', 'name')->where('guard_name', 'web'),
        ],
    ]) + $validated;
}

$user->name = $validated['name'];
$user->email = $validated['email'];

if (! empty($validated['password'])) {
    $user->password = $validated['password'];
}

$user->save();
if (auth()->user()->can('user.assign-role')) {
    $user->syncRoles($validated['roles'] ?? []);
}
```

Model `User` telah mengatur cast password ke `hashed`, sehingga password
plain text dari request akan di-hash saat disimpan. Jika model belum memakai
cast tersebut, hash password secara eksplisit sebelum menyimpannya.
`syncRoles([])` menghapus semua role user ketika tidak ada role yang dipilih;
hal ini disengaja agar pilihan pada form edit mencerminkan data terbaru.

### 6.2 Tambahkan pilihan role pada form

Tambahkan field berikut pada form tambah dan edit
(`resources/views/admin/users/create.blade.php` dan
`resources/views/admin/users/edit.blade.php`). Pada form edit, variabel
`$selectedRoles` berasal dari controller di atas.

```blade
<div class="form-group">
    <label>Role</label>
    @foreach ($roles as $role)
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                name="roles[]"
                id="role-{{ $role->id }}"
                value="{{ $role->name }}"
                @checked(in_array(
                    $role->name,
                    old('roles', $selectedRoles ?? [])
                ))
            >
            <label class="form-check-label" for="role-{{ $role->id }}">
                {{ $role->name }}
            </label>
        </div>
    @endforeach
</div>
```

Tampilkan field role tersebut hanya jika
`auth()->user()->can('user.assign-role')`; contohnya bungkus dengan
`@can('user.assign-role') ... @endcan`. Jangan hanya mengandalkan
penyembunyian field: contoh controller di atas juga hanya memvalidasi dan
menyimpan role untuk user yang memiliki permission ini. Admin mendapat
`user.assign-role` melalui seeder; berikan permission ini hanya kepada
petugas yang boleh mengubah akses akun. Form edit tetap mengosongkan password
jika tidak ingin menggantinya. Untuk menampilkan role pada daftar user, muat
relasinya dengan `User::with('roles')->get()` di `index`, lalu tampilkan
`$user->getRoleNames()->join(', ')` pada kolom Role.

Jangan memberikan role `Admin` otomatis pada semua user baru. Batasi CRUD
User dan perubahan role hanya untuk petugas yang memang berwenang.

## 7. Buat CRUD Role dan Permission

CRUD ini mengelola tabel milik Spatie: Role menyimpan kumpulan permission,
sedangkan Permission menentukan akses per aksi. Pastikan langkah instalasi,
migrasi, trait `HasRoles`, dan seeder pada bagian sebelumnya sudah selesai.

Tambahkan `role` dan `permission` ke daftar `$modules` pada seeder bagian 5
agar permission pengelolaan dapat dibuat:

```php
$modules = ['user', 'berita', 'kategori', 'role', 'permission'];
```

Permission `role.*` dan `permission.*` dalam contoh route di bawah diberikan
hanya kepada role `Admin`. Jalankan seeder kembali setelah mengubah daftar
tersebut:

```bash
php artisan db:seed
```

### 7.1 Buat controller dan route

Buat controller resource:

```bash
php artisan make:controller RoleController --resource
php artisan make:controller PermissionController --resource
```

Di `routes/web.php`, import kedua controller, kemudian daftarkan resource di
dalam grup `auth`. Gunakan permission berbeda untuk setiap aksi:

```php
Route::resource('roles', RoleController::class)->except(['show'])
    ->middlewareFor('index', 'permission:role.list')
    ->middlewareFor(['create', 'store'], 'permission:role.create')
    ->middlewareFor(['edit', 'update'], 'permission:role.edit')
    ->middlewareFor('destroy', 'permission:role.delete');

Route::resource('permissions', PermissionController::class)->except(['show'])
    ->middlewareFor('index', 'permission:permission.list')
    ->middlewareFor(['create', 'store'], 'permission:permission.create')
    ->middlewareFor(['edit', 'update'], 'permission:permission.edit')
    ->middlewareFor('destroy', 'permission:permission.delete');
```

### 7.2 Implementasikan RoleController

Isi `app/Http/Controllers/RoleController.php`. Method `create` dan `edit`
mengirim daftar permission untuk form. `syncPermissions` membuat pilihan
permission pada form edit menjadi data role terbaru.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();
        $selectedPermissions = $role->permissions->pluck('name')->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'Admin') {
            return back()->with('error', 'Role Admin tidak boleh diubah.');
        }

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'Admin') {
            return back()->with('error', 'Role Admin tidak boleh dihapus.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
```

Form tambah/edit role memerlukan input `name` dan checkbox permission berikut.
Gunakan action `roles.store` untuk tambah, atau `roles.update` dengan
`@method('PUT')` untuk edit; sertakan `@csrf` pada keduanya.

```blade
<label for="name">Nama role</label>
<input id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required>

@foreach ($permissions as $permission)
    <label>
        <input
            type="checkbox"
            name="permissions[]"
            value="{{ $permission->name }}"
            @checked(in_array(
                $permission->name,
                old('permissions', $selectedPermissions ?? [])
            ))
        >
        {{ $permission->name }}
    </label>
@endforeach
```

Buat `resources/views/admin/roles/index.blade.php` berisi tabel nama role,
permission (`$role->permissions->pluck('name')->join(', ')`), tautan edit ke
`route('roles.edit', $role)`, tombol hapus ke `route('roles.destroy', $role)`
dengan form `POST`, `@csrf`, dan `@method('DELETE')`, serta tautan tambah ke
`route('roles.create')`.

### 7.3 Implementasikan PermissionController

Isi `app/Http/Controllers/PermissionController.php` dengan operasi berikut.
Nama permission harus unik dalam guard `web`; guard diset eksplisit agar
konsisten dengan model User dan Role:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($permission->id),
            ],
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        abort_unless($permission->guard_name === 'web', 404);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission berhasil dihapus.');
    }
}
```

Buat form tambah/edit dengan input `name`. Action form tambah adalah
`route('permissions.store')`; action edit adalah
`route('permissions.update', $permission)` dan memakai `@method('PUT')`.
Keduanya wajib menyertakan `@csrf`. Buat
`resources/views/admin/permissions/index.blade.php` berupa tabel nama
permission, tautan tambah/edit, dan form hapus dengan `@method('DELETE')`.

Tambahkan tautan menu ke `roles.index` dan `permissions.index` di sidebar
AdminLTE bila diperlukan. Jangan tampilkan tautan kepada user tanpa izin:

```blade
@can('role.list')
    <a href="{{ route('roles.index') }}">Role</a>
@endcan

@can('permission.list')
    <a href="{{ route('permissions.index') }}">Permission</a>
@endcan
```

Menghapus role akan mencabut role tersebut dari user yang memilikinya.
Menghapus permission dapat mencabut akses dari semua role yang
memakainya. Tinjau dampak sebelum menghapus; role `Admin` dilindungi dari
perubahan nama, permission, dan penghapusan pada contoh controller.

## 8. Lindungi route berdasarkan permission per aksi

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

    // Tambahkan route roles dan permissions dari bagian 7 di sini.
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

## 9. Sembunyikan kontrol di Blade

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

## 10. Mengelola role dan permission

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

## 11. Verifikasi

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
6. User dapat diberi beberapa role dari form User; mengosongkan semua pilihan
   pada edit user menghapus role user tersebut.
7. Role dapat dibuat, diubah permission-nya, dan dihapus kecuali role Admin.
   Permission dapat dibuat, diubah, dan dihapus; setelah perubahan, pastikan
   akses semua role yang terdampak sudah benar.

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
