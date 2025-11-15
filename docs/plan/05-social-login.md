file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\05-social-login.md

# 05 — Social Login via Filament Socialite

## Objective

-   Enable social login providers (Google, GitHub, etc.) using a Filament Socialite plugin.

## Dependencies

-   `laravel/socialite`
-   Filament Socialite plugin (e.g., `pxlrbt/filament-socialite`)
-   Filament v3

## Implementation Steps

1. Install packages
    - `composer require laravel/socialite`
    - `composer require pxlrbt/filament-socialite`
2. Configure providers
    - In `config/services.php`, add `google`, `github`, etc. with `client_id`, `client_secret`, `redirect`.
    - Add env vars: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT`, etc.
3. Register plugin
    - Register Socialite plugin in Panel provider and enable providers.
    - Configure button labels and scopes if needed.
4. User linking/creation
    - On first social login, create `User` with provider email/ID.
    - Store `provider`, `provider_id` on users or in `social_accounts` table.
    - Link to existing users by matching email where appropriate.
5. Email verification
    - Decide whether social accounts are auto-verified; otherwise enforce verification middleware.
6. UX
    - Add social buttons to Filament login page.
    - Provide “unlink” option in profile if linking is supported.

## Data Model Changes

-   Add `provider`, `provider_id` to `users` or create `social_accounts` with `user_id`, `provider`, `provider_id`, optional `token`.

## Configuration / Env

-   Provider secrets in `.env`; correct `APP_URL` and redirect URIs.

## Security

-   Validate provider responses.
-   Store minimal token data; avoid long-lived tokens.

## Testing

-   Mock Socialite responses to test login/link flows.
-   Handle provider errors and existing account linking.

## Rollout

-   Configure providers’ dashboards with correct redirect URLs.
-   Test in staging using provider test apps.

## Acceptance Criteria

-   Users can log in with at least one provider.
-   New accounts created or linked correctly.
-   Redirects to Filament panel after auth.

## Risks

-   Provider misconfiguration; ensure correct redirect URLs.
-   Account linking collisions; define deterministic behavior.
