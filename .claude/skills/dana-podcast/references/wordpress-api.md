# WordPress REST API notes (core wp/v2)

All calls go through `tools/wp.sh` (auth + anti-bot retries). Base:
`$WP_BASE/wp-json/wp/v2`. Application Passwords use HTTP Basic auth —
already handled by the script.

## Finding the episode post type

`GET /wp-json/wp/v2/types` lists every registered post type with its
`rest_base`. Podcast plugins register their own CPTs:

- Seriously Simple Podcasting → post type `podcast`
- PowerPress → episodes are usually plain `posts` in a category
- Custom themes → anything (`episode`, `episodes`, …)

The `rest_base` value is what goes in the URL path (`/wp/v2/<rest_base>`).
If a CPT exists but doesn't appear in `/types`, it was registered with
`show_in_rest => false` — flag that to Sadie; it needs a one-line theme
change or a different access path.

## Listing episodes

```
GET /wp/v2/<rest_base>?per_page=100&page=N&status=any&context=edit
```

- `context=edit` returns raw (unrendered) title/content and full meta —
  requires auth with edit rights.
- The `X-WP-Total` and `X-WP-TotalPages` response headers give the counts
  (pass `-i` through `wp.sh api` to see headers). 177 episodes = 2 pages
  at 100/page.

## Reading one episode

```
GET /wp/v2/<rest_base>/<id>?context=edit
```

Fields that matter for show notes: `title.raw`, `content.raw`,
`excerpt.raw`, `slug`, `status`, `meta` (SEO plugin fields live here if
the plugin exposes them to REST).

## Updating

```
POST /wp/v2/<rest_base>/<id>
Content-Type: application/json

{"title": "...", "content": "...", "excerpt": "..."}
```

- Send ONLY the fields being changed — omitted fields are untouched.
- WordPress returns the updated object; verify `title.raw` etc. match
  what was sent.
- Content is block HTML. Preserve any existing player embed / shortcode
  (e.g. `[podcast_episode]`, PowerPress player, Libsyn embed iframe) at
  the top of `content` when rewriting — losing the player is the classic
  bulk-edit disaster.

## SEO plugin meta

- **Yoast**: exposes `yoast_head_json` read-only; writing
  `_yoast_wpseo_title` / `_yoast_wpseo_metadesc` via `meta` requires them
  registered in REST (often not by default — check a `context=edit`
  response). If not writable, note it in the ledger and set meta via
  excerpt + title only, or ask Sadie about installing a snippet.
- **RankMath**: `rank_math_title`, `rank_math_description` in `meta`
  (usually REST-writable).
- No SEO plugin: the theme's `<title>`/description comes from post title
  and excerpt — make the excerpt a deliberate 150–160 char meta
  description.

## Categories & tags

`GET /wp/v2/categories`, `/wp/v2/tags` (also `?post=<id>`). Assign via
`{"categories": [id,...], "tags": [id,...]}` on the post. Create a tag:
`POST /wp/v2/tags {"name": "..."}`.

## Errors & anti-bot

- HTML instead of JSON → challenge page; wp.sh retries 8×, but wrap batch
  loops in your own parse-check + retry as well.
- `401 rest_cannot_edit` → app password lacks rights or user role too low.
- `rest_no_route` on a CPT → wrong `rest_base` or CPT not in REST.
