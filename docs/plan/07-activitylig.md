# Rencana Implementasi: User Activity Monitoring untuk Super Admin (Filament Activity Log)

## 1. Analisis Kebutuhan Teknis

-   Plugin dan kompatibilitas:
    -   Gunakan `pxlrbt/filament-activity-log` versi 2.x (kompatibel Filament v4, PHP ≥8.1).
    -   Wajib memasang dan mengonfigurasi `spatie/laravel-activitylog` karena plugin hanya menampilkan data.
-   Instalasi dan konfigurasi:
    -   Tambahkan paket `pxlrbt/filament-activity-log` via Composer.
    -   Pastikan tema kustom Filament menyertakan CSS plugin: `@import '../../../../vendor/pxlrbt/filament-activity-log/resources/css/styles.css';` lalu recompile.
    -   Publikasi dan konfigurasi `spatie/laravel-activitylog` (`config/activitylog.php`) sesuai kebutuhan (log names, pruning, formatting).
-   Skema basis data yang diperlukan:
    -   Tabel `activity_log` dari Spatie (publish migration dan jalankan `php artisan migrate`).
    -   Index yang direkomendasikan: `subject_type`, `subject_id`, `causer_id`, `log_name`, `created_at` untuk query cepat.
-   Perizinan dan struktur peran:
    -   Akses halaman aktivitas dibatasi untuk `role:super_admin`.
    -   Jika menggunakan Filament Shield, tambahkan permission spesifik (mis. `view activity log`) dan map ke super admin.
    -   Pastikan kebijakan (policy) mencegah akses oleh non-super-admin ke halaman aktivitas.

## 2. Langkah Implementasi

-   Instalasi plugin:
    -   `composer require pxlrbt/filament-activity-log`.
    -   Pastikan `spatie/laravel-activitylog` terpasang (`composer require spatie/laravel-activitylog`) dan konfigurasinya aktif.
    -   Publish config dan migration Spatie: `php artisan vendor:publish --provider="Spatie\\Activitylog\\ActivitylogServiceProvider"` lalu `php artisan migrate`.
-   Integrasi backend dengan autentikasi:
    -   Aktifkan trait `LogsActivity` pada model yang ingin dipantau (mis. `User`, `Post`, dll.).
    -   Gunakan logging event sesuai kebutuhan (create/update/delete/login/impersonation) melalui model events atau manual logging (`activity()->by($user)->on($subject)->log($description)`).
    -   Pastikan middleware `auth` dan pembatasan `role:super_admin` diterapkan untuk halaman aktivitas.
-   Antarmuka frontend (Filament):
    -   Buat halaman `ListActivities` untuk resource yang relevan mengikuti pola plugin:
        -   Contoh: `App\Filament\Resources\UserResource\Pages\ListUserActivities extends pxlrbt\FilamentActivityLog\Pages\ListActivities`.
        -   Registrasikan di `getPages()` resource: tambahkan route `/{record}/activities`.
        -   Tambahkan action dari tabel/halaman untuk membuka log: action `activities` mengarah ke URL `YourResource::getUrl('activities', ['record' => $record])`.
    -   Opsi global overview (opsional): buat Page khusus menampilkan `activity_log` lintas subject dengan filter causer/subject/event/date.
-   Kustomisasi retensi dan penyaringan:
    -   Konfigurasi `activitylog` untuk periodik pruning: gunakan `Prunable` atau jadwalkan job/artisan command untuk menghapus log lebih lama dari N hari.
    -   Tambahkan filter di UI: rentang tanggal, `log_name`, `event` (created/updated/deleted), `subject_type`, `causer_id`.

## 3. Kebutuhan Pengujian

-   Unit tests:
    -   Verifikasi logging saat operasi CRUD pada model (mis. `Post`) menghasilkan entri `activity_log` sesuai.
    -   Uji uniqueness dan format data (log_name, properties, subject/causer linking).
-   Integration tests:
    -   Pastikan hanya `super_admin` dapat mengakses halaman aktivitas Filament.
    -   Uji navigasi dari resource ke halaman `activities` (link action berfungsi, data tampil).
    -   Validasi filter UI mengubah hasil query sesuai parameter.
-   Performance tests:
    -   Uji query pada `activity_log` dengan data besar; verifikasi waktu respons.
    -   Evaluasi indeks pada kolom kunci (`subject_type`, `subject_id`, `causer_id`, `created_at`, `log_name`).

## 4. Dokumentasi

-   Panduan admin:
    -   Cara membuka halaman aktivitas dari resource atau menu.
    -   Penjelasan filter: tanggal, causer, subject, event, log name.
    -   Cara mengekspor (opsional) atau menelusuri detail.
-   Dokumentasi teknis perawatan:
    -   Lokasi konfigurasi `config/activitylog.php` dan opsi penting.
    -   Jadwal pruning dan prosedur menyesuaikan retensi.
    -   Cara menambah model baru ke pemantauan (mengaktifkan `LogsActivity`, menambah halaman activities).
-   Dokumentasi API (jika diekspos):
    -   Endpoint internal untuk mengambil log (opsional), format respons, autentikasi, dan parameter query.

## 5. Strategi Deploy

-   Validasi staging:
    -   Jalankan migrasi, isi data sample, verifikasi halaman aktivitas tampil dan filter bekerja.
    -   Uji peran/permission: hanya super admin yang melihat menu/halaman.
-   Rollout produksi:
    -   Backup basis data sebelum deploy.
    -   Jalankan migrasi `activity_log` di maintenance window.
    -   Aktifkan worker queue jika ada event logging asinkron yang membutuhkan proses terpisah.
-   Prosedur backup dan rollback:
    -   Backup harian tabel `activity_log` (opsional, tergantung retensi).
    -   Rollback migrasi jika diperlukan dan nonaktifkan halaman aktivitas sementara.

## 6. Kriteria Keberhasilan

-   Super admin dapat melihat log aktivitas pengguna secara komprehensif (per model dan agregat), menelusuri detail, dan memanfaatkan filter.
-   Performa sistem stabil pada beban yang diharapkan; query `activity_log` responsif dengan indeks memadai.
-   Persyaratan keamanan terpenuhi: akses dibatasi peran, data sensitif dilindungi, audit trail akurat dan tidak dapat dimanipulasi.
