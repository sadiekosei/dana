# Pricing rollout — exact change set

Agreed ladder: **$99 course / $1,100 / $1,700 / $2,200**, packages sold via
Stripe Payment Links, 3-pay available. Rate basis $200/hr; 90-min package
call = $300; Booster (15 min) = $50; $250 one-time intro call folded in as
call #1 of every package.

Status: **section C applied and verified live on 2026-08-07.** D and E
still open; B (Stripe) is with Dana/Sadie.

---

## A. Thinkific — needs Dana or Sadie (no API access from here)

1. ~~Self-paced course **$695 → $99**~~ — **done**, verified $99 on
   `/collections` (product `2232432`, price_id `3001780`).
2. ~~Unlist the three tiers from `/collections`~~ — **done**, only the
   self-paced course is listed now.
3. **Still open — the three tier pages remain publicly reachable and still
   show the old prices.** Delisting removed them from `/collections` but
   the URLs return 200 with the prices intact:
   - `/courses/boundaries-essentials` → **$2,000.00**
   - `/courses/boundaries-evolve` → **$3,750.00**
   - `/courses/copy-of-the-freedom-of-no-formula-evolve` → **$4,995.00**

   They carry **no `noindex`**, and Thinkific's `robots.txt` does not
   block `/courses/`, so they are crawlable. A targeted search surfaces
   nothing from the subdomain, so they appear unindexed — low urgency,
   but not zero.

   **Thinkific will not let you edit pricing on an unpublished course**,
   and the free plan allows only one published course, so the price on
   these pages cannot be corrected in place.

   **Don't fight it — delete them.** Under the new model these three
   courses have no job: the packages sell through Stripe Payment Links
   and the buyer is manually enrolled in the single `boundaries` course.
   The tier courses are redundant by design, and deleting them makes the
   URLs 404, which is the outcome we actually want.

   Check enrolments first in the Thinkific admin (very likely zero given
   revenue has been zero). If any exist, or she wants to keep the content,
   the fallback is renaming each slug to something unguessable — a
   settings field rather than a pricing field, so it may not be gated the
   same way.

   Not worth doing: the publish-swap dance. Publishing a tier to edit its
   price means unpublishing the live $99 course first, three times over,
   to fix a number on a page nobody should reach.
4. **Still open — no price on the sales page.** `/courses/boundaries`
   displays no price anywhere; a visitor only sees $99 after clicking
   through to the order page. Put $99 on the page.
5. Site title now reads *The Boundary Blueprint* — **done**.
6. Fix slug `copy-of-the-freedom-of-no-formula-evolve` → `boundaries-unity`
   (or moot if the page is removed under item 3).

## B. Stripe — needs Dana or Sadie

Payment Links (free to create, no Thinkific upgrade required):

| Product | Pay in full | 3-pay |
|---|---|---|
| Course + 4 calls (Essentials) | $1,100 | 3 × $385 |
| Course + 6 calls (Evolve) | $1,700 | 3 × $595 |
| Course + 8 calls, couples (Unity) | $2,200 | 3 × $770 |

On purchase: manually enrol in the Thinkific course, send the Calendly link.

## C. WordPress `/boundaries-course/` (page 1356) — DONE 2026-08-07

Applied via plugin v1.10.x `page-elementor` endpoints and verified on the
live page. Backups: `work/backups/boundaries-course-elementor-backup-2026-08-07.json`
plus a server-side copy in postmeta (`_kosei_elementor_backup_20260807_185801`).

- Prices now $1100 / $1700 / $2200 in the compare table (`d5943ef`).
  **Note:** the EAEL price field is numeric-only — "1,100" renders as
  "$10", so there is no thousands separator. Left as integers.
- New feature row "$250 Intro Deep-Dive Session Included" across all three
  packages.
- Legacy "Freedom of No" pricing tables (`c9dfa1b`, `d31a61a`, `2b550e0`)
  removed from column `96ff517`.
- Curriculum accordion fixed: Modules 11 & 12 now listed under "Property
  Lines" instead of repeating 9 & 10.
- FAQ expanded 7 → 25 items; dead `learn.danaskaggs.com` link replaced
  with `https://danaskaggs.thinkific.com/`.
- Waitlist popup **1636** ("Course Inquiry Form") select options updated to
  the new prices and call counts.

**Gotcha worth keeping:** Elementor caches rendered widget markup in
`_elementor_element_cache` postmeta (210KB on this page). Writing
`_elementor_data` and purging LiteSpeed is *not* enough — the front end
keeps serving the old markup. Plugin v1.10.1 adds `/page-cache-bust`,
which clears that meta plus `_elementor_css` and `_elementor_page_assets`.
Always call it after a write.

### Original plan (for reference)

1. **Compare-table prices** (`eael-mcpt-package-price`), 3 values:
   `$2000` → **`$1,100`**, `$3750` → **`$1,700`**, `$4995` → **`$2,200`**
2. **Waitlist form select** (`form_fields[field_5c5c485]`), 3 options:
   - `Essentials – $2000` → `Essentials – $1,100`
   - `Evolve – $3750` → `Evolve – $1,700`
   - `Unity (for Couples) – $4995` → `Unity (for Couples) – $2,200`
3. **Delete the legacy pricing block** — the second, stale table still
   selling "The 'Freedom of No' Formula" at the old prices.
4. **Add two feature rows** that are currently given away silently:
   - *90-minute coaching calls* (her standard ongoing session is 60 min)
   - *$250 intro call included*
5. **Swap the waitlist CTA** for the Stripe links once B exists. Until
   then the popup (Elementor popup `1636`) stays.

## D. WordPress `/coaching/` (page 958) — STILL OPEN

1. **Publish the rates** — the page currently shows none:
   $200/hr, and a $250 one-time intro call.
2. **Fix the broken "Learn More" button.** It points to
   `danaskaggs.com/join`, which redirects (Pretty Links) to
   `learn.danaskaggs.com` — a subdomain that does not resolve. Point it
   at `/boundaries-course/`.
3. Update the "The Freedom of 'No' Formula 8-Week Course" section to the
   Boundary Blueprint at $99.

## F. Brand globals (Elementor Kit 6) — 2026-08-07

The kit already matched the branding; nothing needed inventing:

| Global | Value |
|---|---|
| Primary | `#012E41` Dark Blue |
| Secondary / Accent | `#A0243F` Cranberry |
| **Text** | `#012E41` → **`#000000`** (changed) |
| Custom | Dark Blue, cranberry, Brand Orange `#F29057`, Cream `#FCEADF` |
| Typography | EB Garamond 600 headings, Helvetica 400 body |

**Rule from Sadie: body copy is black on light backgrounds, white on dark.**
Implemented by setting the Text global to `#000000` rather than per-page
hexes, so it propagates. On dark sections white stays explicit (there is no
white global).

**Gotcha this exposed:** two things were bound to the *text* global that
aren't body copy, so they would have followed navy → black:

- page 197 (home): a **section background** bound to `colors?id=text` —
  would have turned a navy band pure black
- page 1356: `eael_mcpt_icon_color` on the compare table — navy icons

Both rebound to `colors?id=primary`, which keeps `#012E41`. Audited every
`globals/colors?id=text` binding site-wide afterwards: 12 total, all body
copy on white/cream. None on a dark background.

Lesson: before changing a global, grep the page trees for
`globals/colors?id=<name>` and check what each binding actually controls —
Elementor lets any colour control bind to any global, including
backgrounds.

## E. Nav — STILL OPEN

`http://danaskaggs.thinkific.com` → `https://` (currently insecure scheme).

---

## Sequencing note

C and D drop the displayed prices while the CTA is still a waitlist. That
is a coherent intermediate state but it earns nothing on its own — the
revenue only starts when B is live and the waitlist CTA is replaced. If
the Stripe links can be made first, do B before C so the price drop and
the buy button land together.

## What I need to apply C, D and E

`WP_APP_PASS` for user `sadie` on danaskaggs.com (the app password named
"Kosei"). Everything else is already in the repo. Per the golden rules I
will fetch each page immediately before writing, change only the fields
listed above, and re-fetch to verify — with an Elementor backup committed
to `work/backups/` first, as with the podcast page redesign.
