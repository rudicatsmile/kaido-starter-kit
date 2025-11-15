file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\01-authentication-filament.md

# 01 — Authentication (Login, Register, Forgot Password) via Filament

## Objective

-   Enable login, registration, and password reset flows using Filament pages integrated with Laravel’s auth.

## Dependencies

-   Laravel 10+ with default auth guards configured
-   Filament v3
-   Mailer configured for password reset emails

## Implementation Steps

1. Confirm Filament panel setup and auth guard
    - Ensure `User` model is the authenticatable entity used by the panel guard.
    - Verify panel uses `auth()` guard tied to `users` table.
2. Login page
    - Use Filament’s default `Login` page; configure username field `email` and `password`, enable “remember me”.
3. Registration page
    - Create a Filament page `Register`.
    - Form: `name`, `email`, `password`, `password_confirmation`, optional terms checkbox.
    - Action: create `User`, hash password, dispatch `Registered` event, optionally send email verification.
    - Redirect to Filament panel after registration.
4. Forgot/Reset password pages
    - Page `ForgotPassword`: `email`, calls `Password::sendResetLink`.
    - Page `ResetPassword`: `token`, `email`, `password`, confirmation; calls `Password::reset`.
5. Routes integration
    - Register pages with a guest-accessible area or minimal public panel guarded by `guest` for `login/register/forgot`.
6. UX details
    - Link login ↔ register ↔ forgot/reset.
    - Show success/error notifications.
7. Email verification (recommended)
    - Enable Laravel’s verification and gate panel routes with `verified` if required.

## Data Model Changes

-   Ensure `users` has `name`, `email`, `password`, `email_verified_at`.

## Configuration / Env

-   `MAIL_*` env variables; correct `APP_URL`.

## Security

-   Validate inputs; rate-limit login/forgot attempts.
-   Use `bcrypt`/Argon2 for passwords.
-   Never log credentials or tokens.

## Testing

-   Feature tests: login success/failure, registration, password reset link and completion, verification.
-   Browser tests for flow and redirects.

## Rollout

-   Deploy pages; verify mail in staging.

## Acceptance Criteria

-   Users can log in via Filament login page.
-   New users can register and are persisted.
-   Users can request a reset link and reset passwords.
-   Proper validation and notifications.

## Risks

-   Email delivery issues; validate mailer setup.
-   Guard misconfiguration causing login failures.
