# Pricing rollout — exact change set

Agreed ladder: **$99 course / $1,100 / $1,700 / $2,200**, packages sold via
Stripe Payment Links, 3-pay available. Rate basis $200/hr; 90-min package
call = $300; Booster (15 min) = $50; $250 one-time intro call folded in as
call #1 of every package.

Status: **not yet applied.** Blocked on WordPress credentials (see bottom).

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

   These are indexable and shareable. Archive or unpublish them properly,
   or redirect them to `/courses/boundaries`.
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

## C. WordPress `/boundaries-course/` (page 1356) — I can apply this

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

## D. WordPress `/coaching/` (page 958) — I can apply this

1. **Publish the rates** — the page currently shows none:
   $200/hr, and a $250 one-time intro call.
2. **Fix the broken "Learn More" button.** It points to
   `danaskaggs.com/join`, which redirects (Pretty Links) to
   `learn.danaskaggs.com` — a subdomain that does not resolve. Point it
   at `/boundaries-course/`.
3. Update the "The Freedom of 'No' Formula 8-Week Course" section to the
   Boundary Blueprint at $99.

## E. Nav — I can apply this

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
