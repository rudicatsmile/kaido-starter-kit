# RBAC API & Admin Resources

## Admin Resources (Filament)
- Roles: `/admin/roles`
- Permissions: `/admin/permissions`

### Roles UI
- Create role dengan nama unik.
- Assign/unassign permissions melalui multi-select.
- Edit role, validasi nama unik.
- Hapus role dengan konfirmasi; diblokir jika role dipakai oleh user.

### Permissions UI
- Create permission dengan nama unik dan guard `web`.
- Edit dan hapus permission.

### Audit Log
- Disimpan di tabel `audit_logs`.
- Fields: `subject_type`, `subject_id`, `action`, `changes`, `user_id`, `timestamps`.
- Perubahan dari operasi admin dan API dicatat.

## API Endpoints
Semua endpoint berada di prefix `api/` dan memerlukan autentikasi serta role `super_admin`.

### Roles
- `GET /api/roles` — list roles beserta permissions (paginate)
- `POST /api/roles` — buat role
  - Body: `{ "name": "string", "permissions": ["permission_name", ...] }`
- `GET /api/roles/{id}` — detail role beserta permissions
- `PUT /api/roles/{id}` — update role
  - Body: `{ "name": "string?", "permissions": ["permission_name", ...]? }`
- `DELETE /api/roles/{id}` — hapus role; gagal bila role sedang digunakan

### Permissions
- `GET /api/permissions` — list permissions (paginate)
- `POST /api/permissions` — buat permission
  - Body: `{ "name": "string", "guard_name": "web" }`
- `GET /api/permissions/{id}` — detail permission
- `PUT /api/permissions/{id}` — update permission
- `DELETE /api/permissions/{id}` — hapus permission

## Validasi
- Role.name unik.
- Permission.name unik, `guard_name` valid.
- Hapus role diblokir jika digunakan oleh user.

## Caching
- Menggunakan cache bawaan Spatie Permission.
- Cache di-reset otomatis setelah create/update/delete role/permission.

## Keamanan
- Grup route API menggunakan middleware: `auth`, `role:super_admin`.
- UI admin menggunakan Filament + Shield; izin akses dikontrol via role/permission.

