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

1. **WordPress**: `WP_BASE` (Dana's site URL), `WP_USER`, and
   `WP_APP_PASS` (an Application Password: WP admin → Users → Profile →
   Application Passwords). Verify with `bash tools/wp.sh me`.
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

## Session state at 2026-08-04

- Repo just initialized; skill ported from Melinda repo (branch
  `claude/staged-site-copy-review-zragwt`, commit f26583f).
- BLOCKED on: WordPress URL + application password, Libsyn access,
  Riverside transcript exports. None of the 177 episodes touched yet.
