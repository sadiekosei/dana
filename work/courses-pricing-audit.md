# Dana's courses — offer & pricing audit

Audited 2026-08-07 from public sources: danaskaggs.com (WP REST + rendered
pages) and danaskaggs.thinkific.com. No analytics or sales data available —
conversion claims below are inferred from funnel structure, not measured.

## What exists today

Checkout lives entirely on **Thinkific** (`danaskaggs.thinkific.com`).
danaskaggs.com has no store, no cart, no Stripe — the WP site is a brochure.

| Product | Price | What you get | Buyable? |
|---|---|---|---|
| Work Boundaries Quiz | Free | Interact quiz, captures email | Yes |
| Healthier Boundaries Assessment | Free | Lead magnet | Yes |
| Boundary Blueprint — Self-Paced | **$695** | 14 modules / 22 lessons / **1.5 hrs video** | Thinkific only |
| Boundary Blueprint — Essentials | **$2,000** | Course + 4 × 90-min 1:1 + 1 Booster | Thinkific only |
| Boundary Blueprint — Evolve | **$3,750** | Course + 6 × 90-min 1:1 + 2 Boosters | Thinkific only |
| Boundary Blueprint — Unity (couples) | **$4,995** | Course + 8 × 90-min 1:1 + 3 Boosters | Thinkific only |
| 1:1 coaching sessions | **not published** | 90-min intro, then 60-min | Calendly only |

## Why it earns nothing — structure first, price second

These are funnel breaks, not pricing problems. Repricing on top of them
still yields zero.

1. **You cannot buy anything on danaskaggs.com.** Every CTA in the
   $2,000/$3,750/$4,995 pricing table on `/boundaries-course/` opens the
   same Elementor waitlist popup (`popup id 1636`). Meanwhile all three
   tiers are live and instantly purchasable on Thinkific. Anyone who lands
   on the site is gated behind a waitlist for a product that is actually
   for sale.
2. **The homepage sells keynotes, not courses.** `/` is now a
   speaker-booking page — "Book Dana for Your Event" ×3. It contains zero
   mentions of a course, a price, or a buy link.
3. **The podcast — the largest asset — sells nothing.** `/podcast/` and the
   episode pages (176 published, top-5% show) carry no course link and no
   email capture. The only offer link on an episode page is a sidebar
   "Learn More → /coaching".
4. **The self-paced course is a submenu item on another domain**, linked as
   `http://danaskaggs.thinkific.com` (insecure scheme, redirects), with no
   presence in any page body.
5. **Prices are hidden where it counts.** The Thinkific self-paced sales
   page shows no price at all. The coaching page shows no price at all.
6. **`learn.danaskaggs.com` is a dead link** on the course page — the
   subdomain does not resolve (verified via DNS; `danaskaggs.com` and the
   Thinkific host resolve fine from the same resolver).
7. **No payment plans exist** on any tier, including the $4,995 one.
8. **Stale naming.** The course page still carries an old block selling
   "The Freedom of No Formula" alongside the new Boundary Blueprint block —
   two pricing tables on one page. Thinkific's site title is still
   `The "Freedom of No" Formula`, and one product slug is
   `copy-of-the-freedom-of-no-formula-evolve`.

## The pricing problem itself

**The $695 self-paced tier is dominated and cannot sell.** A buyer sees
$695 for 1.5 hours of video with no support, next to $2,000 for that same
video *plus six hours of 1:1 time with a licensed psychotherapist*. The
$2,000 is obviously the better deal, so the $695 tier converts nobody — and
it is simultaneously too expensive to be an impulse buy. It fails in both
directions.

**There is no entry point.** A top-5% podcast with 176 episodes has a
cheapest paid product of $695. Nothing exists between free and $695.

**There is no middle.** $695 → $2,000 is the gap where most solo-expert
businesses make their money (course + live group).

**1:1 is underpriced relative to the course.** Backing the course out of
Essentials implies roughly $217/hr for a 15-year licensed psychotherapist —
below her likely therapy rate.

## Recommended ladder

| Tier | Now | Recommended | Plan | Notes |
|---|---|---|---|---|
| Quiz / assessment | Free | Free | — | Make it the CTA on all 176 episodes |
| **NEW — "Say No Without Guilt" mini-course** | — | **$47** | — | 3 strongest modules + the scripts. Impulse buy, sold at the end of every episode. This is the volume engine. |
| Self-Paced Boundary Blueprint | $695 | **$297** | 2 × $165 | Add workbook + scripts + 30-day guarantee. $247 for a launch window. |
| **NEW — Blueprint + Live Group** | — | **$697** | 3 × $265 | 6-week cohort, 2–3 cohorts/yr. Highest margin; fills the missing middle. |
| Essentials (4 × 1:1) | $2,000 | **$2,400** | 3 × $850 | Raise once the course drops — the ladder finally makes sense |
| Evolve (6 × 1:1) | $3,750 | **$3,750** | 4 × $1,050 | Keep. Mark "Most Popular" |
| Unity (couples, 8 × 1:1) | $4,995 | **$4,995** | 5 × $1,120 | Keep. Sell as "for two", not as call-count arithmetic |
| Single 1:1 sessions | unpublished | **publish**, ~$275 intro / $195 follow-up | — | Currently invisible; Calendly is the only path |

Payment-plan totals carry the standard ~10–12% premium over pay-in-full.

### Why cut the course but raise 1:1

The cut and the raise are one move. At $695 vs $2,000 the course is the
worse deal and the 1:1 is the bargain. At $297 vs $2,400 each tier is
priced for what it actually is: $297 buys information, $2,400 buys Dana's
time. That also makes the new $697 group tier the obvious step up from
self-paced rather than a squeeze between two bad options.

## Sequence

Fix the path before touching the numbers, or the new prices sell as well as
the old ones.

1. Replace the waitlist popup on `/boundaries-course/` with real Thinkific
   checkout links. Delete the stale "Freedom of No" pricing block.
2. Put one course CTA on the podcast page + all 176 episode pages (the
   quiz for cold traffic, the $47 mini-course for warm).
3. Add an offer section to the homepage, or accept that `/` is a
   speaker-only page and drive course traffic from the podcast instead.
4. Show prices on the Thinkific sales page and the coaching page. Fix the
   `http://` nav link and the dead `learn.danaskaggs.com` link.
5. Then reprice per the table, and turn on payment plans.
6. Rename the Thinkific site and the `copy-of-...` slug.

## Open questions for Dana

- Current 1:1 therapy/coaching rate — sets the floor for the 1:1 tiers.
- Any historical sales at $695? Changes whether this is a repricing or a
  first launch.
- Appetite for running live cohorts (the $697 tier depends on it).
- Is the keynote business the priority, with courses as a secondary
  funnel? That changes how hard the homepage should push courses.
