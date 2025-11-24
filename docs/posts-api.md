# Posts API

## Endpoints

- `GET /api/posts`
  - Query params: `q` (search in title, content), `status` (draft|published|archived), `sort=published_at`, `direction=asc|desc`, `per_page`
- `GET /api/posts/{id}`
- `POST /api/posts`
  - Body: `title`, `content`, `author`, `published_at?`, `status?`, `slug?`
- `PUT /api/posts/{id}`
  - Body: same as POST (partial update allowed)
- `DELETE /api/posts/{id}`

## Auth

- All routes require `auth` middleware.

## Validation

- `title`: required, string, max:255
- `content`: required
- `author`: required, string, max:255
- `published_at`: date, optional
- `status`: in `draft|published|archived`
- `slug`: unique

## Sorting

- Default by `id desc`. When `sort=published_at`, orders by `published_at` then `id`.

## Model

- `App\Models\Post` with auto `slug` generation from `title` and unique constraint.

