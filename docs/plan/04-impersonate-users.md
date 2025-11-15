file:d:\catatan\notes\Laravel\Vibe November 2025\kaido-starter-kit\docs\plan\04-impersonate-users.md

# 04 — User Impersonation via Filament Impersonate

## Objective

-   Allow privileged users to impersonate other users using `awcodes/filament-impersonate`.

## Dependencies

-   Filament v3
-   `awcodes/filament-impersonate`
-   RBAC to restrict who can impersonate

## Implementation Steps

1. Install plugin
    - `composer require awcodes/filament-impersonate`
    - Register plugin in Panel provider: `->plugin(ImpersonatePlugin::make())`
2. Add actions
    - Add “Impersonate” table action to `UserResource`.
    - Show “Stop Impersonating” banner/action when active.
3. Authorization
    - Gate `impersonate` via Shield permission (e.g., `users.impersonate`) or policy.
    - Prevent impersonating self or `super_admin` if desired.
4. Session handling
    - Store original user id in session; ensure logout restores original session properly.
5. Auditing
    - Log start/stop impersonation events.

## Data Model Changes

-   None required; optional audit log table.

## Configuration / Env

-   None.

## Security

-   Strict authorization checks.
-   Prevent privilege escalation via loops.

## Testing

-   Authorized user can impersonate; unauthorized cannot.
-   Session restoration after stop impersonating.

## Rollout

-   Deploy and assign permission to admin roles only.

## Acceptance Criteria

-   Admins can impersonate and revert reliably.
-   Restricted users cannot access impersonation.

## Risks

-   Misuse; provide clear UI indicators during impersonation.
