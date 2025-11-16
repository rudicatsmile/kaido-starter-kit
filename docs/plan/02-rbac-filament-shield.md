file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\02-rbac-filament-shield.md

# 02 — Role-Based Access Control with Filament Shield

## Objective

-   Implement roles and permissions using `bezhansalleh/filament-shield` to secure Filament resources and pages.

## Dependencies

-   Filament v4
-   `bezhansalleh/filament-shield`
-   Spatie Permission (bundled by Shield)

## Implementation Steps

1. Install and configure Shield
    - `composer require bezhansalleh/filament-shield`
    - `php artisan shield:install`
    - Publish config/migrations if prompted; `php artisan migrate`
2. Define roles
    - Seed roles: `super_admin`, `admin`, `user`.
    - Grant `super_admin` all permissions.
3. Sync permissions
    - `php artisan shield:generate` to scan resources/pages/widgets and create permissions.
    - Enable auto-discovery in `config/filament-shield.php` if desired.
4. Apply permissions
    - Ensure each `Resource` respects `view`, `create`, `update`, `delete` via Shield.
    - Restrict panel navigation based on permissions.
5. User management
    - Add role assignment UI to `UserResource` using Shield relation managers.
6. Middleware/guards
    - Confirm Filament panel uses correct `auth` guard.
    - Add `can` checks to sensitive pages/actions.

## Data Model Changes

-   `roles`, `permissions`, and pivot tables created by Shield/Spatie.

## Configuration / Env

-   Configure generation strategy and `super_admin` name in `config/filament-shield.php`.

## Security

-   Deny-by-default; explicit grants only.
-   No action should bypass permission checks.

## Testing

-   Role assignment tests.
-   Permission gate tests for CRUD across resources.
-   Navigation visibility by role.

## Rollout

-   Generate permissions on deploy/update.
-   Seed baseline roles and assign to admins.

## Acceptance Criteria

-   Roles/permissions exist and are enforced.
-   Restricted users cannot access unauthorized areas/actions.
-   Super admin sees all.

## Risks

-   Permission drift; run generation regularly.
-   Misnamed roles; standardize via seeds.
