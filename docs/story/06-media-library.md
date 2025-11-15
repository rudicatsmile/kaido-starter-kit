file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\06-media-library.md

# 06 — Spatie Media Library Integration

## Objective

-   Use Spatie Media Library to manage user-uploaded files and images, integrated with Filament forms and resources.

## Dependencies

-   `spatie/laravel-medialibrary`
-   Filament v3 with Spatie Media Library form components

## Implementation Steps

1. Install and configure
    - `composer require spatie/laravel-medialibrary:^10.0`
    - `php artisan vendor:publish --tag="media-library"`
    - Ensure `filesystems.php` `public` disk is configured; `php artisan storage:link`
2. Model setup
    - Add `InteractsWithMedia` to models needing uploads (e.g., `User`).
    - Define media collections (e.g., `avatar`, `attachments`).
3. Filament forms
    - Use `SpatieMediaLibraryFileUpload` for file/image fields.
    - Configure multiple uploads, validation (size, mime), and conversions.
4. Image conversions and responsive images
    - Define conversions (e.g., thumbnails) in `registerMediaConversions`.
    - Enable responsive images if needed.
5. Display
    - Show media in tables with image columns and download actions.
6. Cleanup and quotas
    - Implement pruning jobs for orphaned media.
    - Optional per-user quotas.

## Data Model Changes

-   `media` table created by the package.

## Configuration / Env

-   Storage disk credentials if using S3-like storage.
-   Queue worker for conversions if asynchronous.

## Security

-   Validate file types and sizes.
-   Use signed URLs or gated downloads for private content.

## Testing

-   Feature tests uploading files via Filament forms.
-   Verify conversions and URL access.

## Rollout

-   Run migrations and `storage:link`.
-   Test uploads with realistic file types.

## Acceptance Criteria

-   Users can upload and manage files/images through Filament.
-   Media stored and displayed correctly with conversions.
-   Access control enforced for private media.

## Risks

-   Large files; configure timeouts/chunking if needed.
-   Disk misconfiguration; verify URLs and permissions.
