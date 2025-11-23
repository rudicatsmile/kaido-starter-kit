file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\02-rbac-filament-shield.md

# 02 — Role-Based Access Control with Filament Shield

## Objective

-   Implement roles and permissions using `bezhansalleh/filament-shield` to secure Filament resources and pages.

## Dependencies

-   Filament v4
-   `bezhansalleh/filament-shield`
-   Spatie Permission (bundled by Shield)
-   Laravel 12

## Implementation Steps

1. Install dan konfigurasi Shield
    - `composer require bezhansalleh/filament-shield`
    - Jalankan `php artisan shield:install` (akan men-setup Spatie Permission; jika diminta, publish migrations)
    - `php artisan migrate`
    - Plugin terpasang di panel admin: lihat `app/Providers/Filament/AdminPanelProvider.php:58-60`
2. Definisikan roles
    - Seed roles baseline: `super_admin`, `admin`, `panel_user`, `user` di `database/seeders/RoleSeeder.php:14-19`
    - Grant semua permission ke `super_admin`: `database/seeders/RoleSeeder.php:28-30`
    - Opsional: assign `super_admin` ke user `test@example.com`: `database/seeders/RoleSeeder.php:32-36`
3. Sinkronisasi permissions
    - Jalankan `php artisan shield:generate` untuk memindai resources/pages/widgets dan membuat permission
    - Opsi discovery dapat diatur di `config/filament-shield.php:242-246`
4. Terapkan permissions ke Resources
    - Semua resource Filament akan dihormati oleh Shield
    - Navigasi panel otomatis tersembunyi bila user tidak memiliki permission
    - Resource yang tersedia:
        - Users: `app/Filament/Resources/Users/UserResource.php:19`
        - Roles: `app/Filament/Resources/Roles/RoleResource.php:1`
        - Permissions: `app/Filament/Resources/Permissions/PermissionResource.php:1`
5. Manajemen pengguna
    - UI penetapan role di `UserForm` melalui relasi `roles`: `app/Filament/Resources/Users/Schemas/UserForm.php:47-51`
6. Middleware/guards
    - Panel admin memakai auth default; akses diatur oleh Shield dan Spatie Permission
    - API RBAC membutuhkan user berperan `super_admin`
7. Routing API (Laravel 12)
    - Aktifkan file route API di `bootstrap/app.php:8-12` (menambahkan `api: __DIR__.'/../routes/api.php'`)
    - Endpoint aman:
        - Roles: `GET/POST/GET(id)/PUT(id)/DELETE(id)` di `routes/api.php:1` dan `app/Http/Controllers/API/RoleController.php:1`
        - Permissions: `GET/POST/GET(id)/PUT(id)/DELETE(id)` di `routes/api.php:1` dan `app/Http/Controllers/API/PermissionController.php:1`

## Data Model Changes

-   `roles`, `permissions`, dan pivot tables dibuat oleh Shield/Spatie.
-   Audit log (`audit_logs`) untuk melacak perubahan Role/Permission:
    -   Migration: `database/migrations/2025_11_23_000000_create_audit_logs_table.php:1`
    -   Model: `app/Models/AuditLog.php:1`

## Configuration / Env

-   Strategy permission generation dan nama `super_admin` di `config/filament-shield.php:65-70, 101-105`
-   Discovery untuk resources/pages/widgets: `config/filament-shield.php:242-246`
-   Panel user role: `config/filament-shield.php:83-86`

## Security

-   Deny-by-default; explicit grants only.
-   Tidak ada aksi yang boleh melewati pemeriksaan permission.
-   API routes berada dalam middleware `auth` + `role:super_admin`: `routes/api.php:1`
-   Panel menambahkan plugin Shield: `app/Providers/Filament/AdminPanelProvider.php:58-60`

## Testing

-   Role assignment
    -   Jalankan `php artisan db:seed --class=RoleSeeder`
    -   Login sebagai `test@example.com` (yang di-assign `super_admin` bila ada)
    -   Verifikasi sidebar menampilkan `Roles` dan `Permissions`
-   Permission gate CRUD
    -   Coba akses `/admin/roles` dan `/admin/permissions` dengan user tanpa role; seharusnya tersembunyi/ditolak
    -   Buat role baru, assign permissions, edit dan hapus role yang tidak terpakai
    -   Cek audit log bertambah setiap operasi
-   API
    -   Pastikan `bootstrap/app.php` mengaktifkan route API
    -   Panggil `GET /api/roles` dan `GET /api/permissions` dalam sesi login; hasil harus paginate/list
    -   `POST /api/roles` dengan body `{ "name": "editor", "permissions": ["Posts:View"] }`
    -   `DELETE /api/roles/{id}` untuk role yang terpakai harus gagal dengan pesan yang sesuai
-   Soft deletes & Users
    -   `User` memakai `SoftDeletes` untuk dukungan filter trash: `app/Models/User.php:15`

## Rollout

-   Jalankan `php artisan shield:generate` setiap deploy/update untuk sinkronisasi permissions
-   Seed roles baseline dan assign ke admin
-   Pastikan cache permissions di-reset otomatis setelah perubahan (lihat bagian Caching)

## Acceptance Criteria

-   Roles/permissions ada dan diterapkan di UI admin & API
-   User tanpa izin tidak bisa mengakses area/aksi terlarang
-   Super admin dapat mengakses semua
-   Audit log tercatat untuk create/update/delete
-   Cache permission direset saat ada perubahan RBAC

## Risks

-   Permission drift; jalankan generation secara berkala
-   Misnamed roles; standarisasi via seeds
-   Inkoherensi guard pada API; saat ini memakai session auth default. Untuk token-based API, integrasikan `laravel/sanctum` dan ubah middleware ke `auth:sanctum` (future work)

---

## Notes (Implementation References)

-   Admin Panel Provider: `app/Providers/Filament/AdminPanelProvider.php:58-60`
-   User model HasRoles & SoftDeletes: `app/Models/User.php:15`
-   Role Resource: `app/Filament/Resources/Roles/RoleResource.php:1`
-   Permission Resource: `app/Filament/Resources/Permissions/PermissionResource.php:1`
-   Role form unique, relasi permissions: `app/Filament/Resources/Roles/Schemas/RoleForm.php:21, 48-51`
-   Permission form unique: `app/Filament/Resources/Permissions/Schemas/PermissionForm.php:18`
-   Pencegahan hapus role terpakai: `app/Filament/Resources/Roles/Pages/EditRole.php:18-23`
-   Audit log hooks (admin):
    -   Roles create/update/delete: `app/Filament/Resources/Roles/Pages/CreateRole.php:12-18`, `app/Filament/Resources/Roles/Pages/EditRole.php:26-44`
    -   Permissions create/update/delete: `app/Filament/Resources/Permissions/Pages/CreatePermission.php:12-18`, `app/Filament/Resources/Permissions/Pages/EditPermission.php:19-35`
-   Audit model/migration: `app/Models/AuditLog.php:1`, `database/migrations/2025_11_23_000000_create_audit_logs_table.php:1`
-   API controllers:
    -   Roles: `app/Http/Controllers/API/RoleController.php:1`
    -   Permissions: `app/Http/Controllers/API/PermissionController.php:1`
-   API routes & bootstrap: `routes/api.php:1`, `bootstrap/app.php:8-12`
-   Dokumentasi endpoint lengkap: `docs/rbac-api.md`
