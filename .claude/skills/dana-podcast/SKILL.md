---
name: dana-podcast
description: >
  How to access and edit Dana's podcast episodes (177 episodes) on her
  WordPress site and Libsyn, and how to rewrite show notes for SEO/traffic
  using the Riverside transcripts. Use this skill WHENEVER the task touches
  the podcast in any way: editing show notes or episode pages, updating
  Libsyn descriptions, working with transcripts, auditing episode SEO, or
  answering questions about how the podcast publishing pipeline works.
---

# Dana's podcast — WordPress/Libsyn show-notes guide

Dana's podcast has 177 episodes with weak show notes; the mission is to
rewrite them for search traffic, on both the WordPress site and Libsyn.
This skill was ported from the Melinda repo's `melinda-site` skill and
generalized: Dana's site has no custom deploy plugin, so everything goes
through the core WordPress REST API (`wp/v2`) via `tools/wp.sh`.

The user is Sadie (Kosei Designs). Dana is her client. Dana (or her team)
may edit episodes live in WordPress — so, as with Melinda's site, never
blindly overwrite a live post from a stale fetch: fetch, merge your
change, then update.

## Credentials — first thing to sort out (currently MISSING)

Nothing is stored in the repo, and as of 2026-08-04 Sadie has not yet
provided any of these. Ask for them once at session start, save to the
session scratchpad, and export as env vars. Never commit them.

1. **WordPress**: `WP_BASE=https://danaskaggs.com`, `WP_USER=sadie`
   (the app password is NAMED "Kosei" but belongs to user `sadie`,
   id 2 — do not auth as "kosei", that user doesn't exist), and
   `WP_APP_PASS` (Sadie pastes it per session). Verify with
   `bash tools/wp.sh me`.

   **Server-side dependency (do not remove):** Hostinger's CDN strips
   the `Authorization` header, and WordPress behind that CDN can't see
   it. A Code Snippets snippet on the site ("Restore Authorization
   header", added 2026-08-04) fixes HTTPS detection
   (`X-Forwarded-Proto` → `$_SERVER['HTTPS']`), force-enables
   application passwords, and copies the `X-Kosei-Auth` request header
   into `PHP_AUTH_USER/PW`. `tools/wp.sh` sends credentials in BOTH
   `Authorization` and `X-Kosei-Auth`. If auth ever breaks with a
   generic `rest_not_logged_in`, check that snippet is still active —
   it also emits `X-Kosei-*` diagnostic response headers on REST calls
   (snippet v3; the diagnostics block can be trimmed once stable).
2. **Libsyn**: Libsyn has an API (api.libsyn.com, OAuth) but the practical
   path depends on what access Sadie can share — API credentials, or
   login for manual/scripted dashboard edits. Sort this out with her
   before promising automated Libsyn updates.
3. **Riverside transcripts**: there is no Riverside API access from this
   environment. Ask Sadie to bulk-export the transcripts from Riverside
   (each recording's page offers a transcript download, TXT or SRT) and
   commit them to `transcripts/` in this repo (see `transcripts/README.md`
   for the naming convention), or share them via Google Drive.

## Using the API helper

Always call the API through `tools/wp.sh`, never bare curl — it sends a
browser User-Agent and same-origin Referer and retries through anti-bot
challenges (some hosts, e.g. SiteGround, captcha bare `/wp-json` calls).
Still verify each response parses: a challenge page is HTML, so check the
body starts with `{` or `[` before trusting it.

```bash
export WP_BASE=https://…  WP_USER=…  WP_APP_PASS=…
bash tools/wp.sh me                 # auth sanity check
bash tools/wp.sh types              # find the episode post type
bash tools/wp.sh posts episodes 1   # list page 1 of that type
bash tools/wp.sh get episodes 123
bash tools/wp.sh update episodes 123 body.json
```

Full endpoint notes: `references/wordpress-api.md`.

## The golden rules (inherited from the Melinda project, hard-earned)

1. **Never overwrite live posts wholesale.** Fetch the live post
   immediately before every update, change only the fields you mean to
   change (`title`, `content`, `excerpt`, `slug`, SEO meta), and post back.
   Assume a human edited something since your last fetch.
2. **Discover, don't assume.** Episodes may be plain `posts`, a custom
   post type (`podcast`, `episodes` — PowerPress and Seriously Simple
   Podcasting each register their own), or pages. Run `wp.sh types` and
   inspect one real episode before writing any batch code.
3. **Slugs are load-bearing.** Changing a slug changes the URL and breaks
   inbound links unless the site has redirects. Default: improve titles
   and content but LEAVE SLUGS ALONE unless Sadie explicitly approves a
   slug change plus a redirect plan.
4. **Batch work needs a ledger.** With 177 episodes, track progress in a
   committed file (`work/episode-status.json` or a markdown checklist):
   episode ID, old title, new title, WP updated?, Libsyn updated?,
   transcript available? Commit after every working session so a fresh
   session can resume without re-auditing.
5. **Pilot before bulk.** Rewrite 3–5 episodes first, get Sadie's sign-off
   on the format, then run the remaining episodes to that template.
6. **Verify after writing.** Re-fetch each updated post and confirm the
   change landed; spot-check rendered pages in a browser or via curl.

## The show-notes rewrite workflow

1. **Audit**: pull all episodes (`wp.sh posts <type> <page>`, paginate),
   dump ID/title/slug/excerpt/word-count to the ledger. Identify the SEO
   plugin (Yoast/RankMath/AIOSEO) from the post meta or `wp.sh types`
   output so meta titles/descriptions can be set via REST too.
2. **Match transcripts**: pair each episode with its Riverside transcript
   in `transcripts/` (by episode number/date/guest name).
3. **Rewrite** per `references/shownotes-playbook.md` (titles, structure,
   keywords, timestamps, links, transcript embedding).
4. **Update WordPress** via `wp.sh update`, one episode at a time, ledger
   after each.
5. **Update Libsyn** to mirror the new title/description (path per the
   credentials section above). Libsyn feeds the RSS that podcast apps
   show, so titles/descriptions must stay consistent with WP.
6. **Report**: summarize before/after for a sample + full ledger link.

## Working agreements (standing, from Sadie)

- Branch: work on the designated `claude/...` branch only; push after each
  completed change.
- Never commit passwords, tokens, or model identifiers to the repo.
- When her feedback and your assumption conflict, her intent wins.
- Verify before reporting done.

## Site facts (audited 2026-08-04)

- Episodes are the CPT **`podcast`** (rest_base `podcast`, via CPT UI):
  **176 published**, 2020-01-01 → present, all status publish.
- Show notes are minimal: median 62 words; 174/176 under 200 words.
  Typical structure: Libsyn `[iframe]` player shortcode, one intro
  paragraph, sometimes a bare guest URL, then a "Check out this
  episode!" Libsyn link. PRESERVE the player shortcode when rewriting.
- **170/176 posts embed their Libsyn episode ID** in the player iframe
  (`embed/episode/id/<ID>`) — that's the WP↔Libsyn join key; it's in
  `work/episode-status.json`.
- Titles pre-~2023 are "Episode N: Topic" (weak SEO); recent ones are
  topic-first. Podcast tagline: "The Queen of Boundaries".
- Yoast SEO active, but posts carry no custom meta via REST (`meta`
  only exposes `footnotes`); Yoast fields not REST-writable — meta
  description work goes through `excerpt` unless that changes.
- Stack: Hostinger + LiteSpeed + CDN, WP 7.0.2, Elementor, CPT UI,
  Pretty Links, WPForms, UpdraftPlus. Admin user is
  `danabskaggs@gmail.com` (Dana); Sadie is user `sadie` (id 2).
- **`work/episode-status.json`** is the ledger: one row per episode
  (id, date, slug, title, libsyn_episode_id, word_count, flags
  transcript_matched / wp_rewritten / libsyn_updated). Update it as
  episodes are processed; commit after every working session.

## Session state at 2026-08-04

- Connection FULLY WORKING end-to-end (auth saga documented above:
  CDN header stripping + HTTPS detection + username was `sadie` not
  `kosei`). The "Dana" cloud environment allowlists danaskaggs.com.
- Audit complete; ledger committed. No episodes rewritten yet.
- Next: Sadie provides Riverside transcripts into `transcripts/` →
  pilot-rewrite 3–5 episodes for her sign-off → bulk run.
- Still pending: Libsyn access decision (credentials vs paste-ready
  files; the ledger's libsyn_episode_id column is ready either way).
