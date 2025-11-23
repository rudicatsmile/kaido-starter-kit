file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\03-user-profile-breezy.md

# 03 — User Profile via Filament Breezy

## Objective

-   Provide user profile management inside Filament using `jeffgreco13/filament-breezy`.

## Dependencies

-   Filament v4
-   `jeffgreco13/filament-breezy`
-   Optional: Laravel email verification, 2FA

## Implementation Steps

1. Install Breezy
    - `composer require jeffgreco13/filament-breezy`
    - Register plugin in Panel provider: `->plugin(BreezyCorePlugin::make()...)`
2. Configure profile
    - Enable profile page: update `name`, `email`.
    - Enable password change form.
    - Optionally enable 2FA (TOTP).
3. Validation and events
    - Validate email uniqueness.
    - Fire events on email change; handle verification status.
4. Navigation
    - Add “Profile” to the user menu in panel header.
5. Avatar and media
    - Integrate avatar upload via Spatie Media Library if desired.

## Data Model Changes

-   Ensure `users` has `name`, `email`, `password`; fields for 2FA if enabled.

## Configuration / Env

-   Mailer for verification; QR generation if 2FA.

## Security

-   Require current password for sensitive changes.
-   Rate-limit 2FA setup/disable.

## Testing

-   Profile update and password change tests.
-   Email uniqueness validation tests.
-   2FA enable/disable tests.

## Rollout

-   Deploy plugin configuration.
-   Migrate additional fields if needed.

## Acceptance Criteria

-   Users can edit profile and change password within Filament.
-   Validations and persistence work correctly.
-   Optional 2FA functions end-to-end.

## Risks

-   Email re-verification flows; define desired behavior.
