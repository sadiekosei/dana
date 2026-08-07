# Dana's courses — offer & pricing audit

Audited 2026-08-07 from public sources: danaskaggs.com (WP REST + rendered
pages) and danaskaggs.thinkific.com. No analytics or sales data available —
conversion claims are inferred from funnel structure, not measured.

Rate card confirmed by Sadie: **90-min coaching call = $250, 60-min = $200.**

## What exists today

Checkout lives entirely on **Thinkific** (`danaskaggs.thinkific.com`).
danaskaggs.com has no store, no cart, no Stripe — the WP site is a brochure.

| Product | Price | What it is | Buyable? |
|---|---|---|---|
| Work Boundaries Quiz (`/quiz/`) | Free | Typeform `01KBV3EYERQH0W81MPFCQS2PD5` | Yes — working |
| Healthier Boundaries Assessment | Free | Typeform `yJT91y3Z` | Yes — working |
| Site-wide "Quick check-in" popup | Free | Typeform `01KCQ6232B10186HGVFA2EGEB2` | Yes — on every page |
| Boundary Blueprint — Self-Paced | **$695** | 14 modules / 22 lessons / **1.5 hrs video** | Thinkific only |
| Boundary Blueprint — Essentials | **$2,000** | Course + 4 × 90-min 1:1 + 1 Booster | Thinkific only |
| Boundary Blueprint — Evolve | **$3,750** | Course + 6 × 90-min 1:1 + 2 Boosters | Thinkific only |
| Boundary Blueprint — Unity (couples) | **$4,995** | Course + 8 × 90-min 1:1 + 3 Boosters | Thinkific only |
| 1:1 coaching | **$250 / 90-min, $200 / 60-min** | Calendly | Yes, but price unpublished |

## The free tier works — lead capture is not the problem

An earlier draft of this audit claimed both quizzes were broken. That was
wrong, and the error was mine: I grepped the served HTML for
`embed.typeform.com` and got zero hits. LiteSpeed Cache rewrites the
loader into a local optimized bundle, so the literal string never appears:

```html
<div data-tf-live="01KBV3EYERQH0W81MPFCQS2PD5"></div>
<script data-optimized="1"
  src="https://danaskaggs.com/wp-content/litespeed/js/624ca60f…js"></script>
```

That bundle does contain the Typeform loader (verified by fetching it).
Both quiz pages render and are confirmed working in-browser.

Better still, the **"Quick check-in" Typeform popup
(`01KCQ6232B10186HGVFA2EGEB2`) is injected site-wide** — verified present
on `/`, `/podcast/`, individual episode pages, `/boundaries-course/` and
`/coaching/`. So every one of the 176 episode pages does have an email
capture on it.

This sharpens the diagnosis rather than softening it: **leads are being
captured and then have nothing to buy.** The break is entirely downstream,
at the offer.

## The blocker — you cannot buy anything on danaskaggs.com

Every CTA in the $2,000/$3,750/$4,995 pricing table on `/boundaries-course/`
opens the same Elementor waitlist popup (`popup id 1636`). All three tiers
are live and instantly purchasable on Thinkific. Anyone landing on the site
is gated behind a waitlist for products that are already for sale.

Also:

- **The homepage sells keynotes, not courses.** `/` is a speaker-booking
  page — "Book Dana for Your Event" ×3, zero course mentions or prices.
- **The podcast sells nothing.** `/podcast/` and the episode pages (176
  published, top-5% show) carry no course link at all — the only offer
  link on an episode page is a sidebar "Learn More → /coaching". The
  site-wide popup does capture email there, but nothing in the episode
  content points to a product.
- **The self-paced course is a submenu item on another domain**, linked as
  `http://danaskaggs.thinkific.com`, with no presence in any page body.
- **Prices are hidden where it counts.** The Thinkific self-paced sales page
  shows no price. The coaching page shows no price.
- **`learn.danaskaggs.com` is a dead link** on the course page — the
  subdomain does not resolve (verified via DNS; danaskaggs.com and the
  Thinkific host resolve fine from the same resolver).
- **No payment plans** on any tier, including the $4,995 one.
- **Stale naming.** `/boundaries-course/` carries two pricing tables — the
  new Boundary Blueprint block plus an old "Freedom of No Formula" block.
  Thinkific's site title is still `The "Freedom of No" Formula`, and one
  product slug is `copy-of-the-freedom-of-no-formula-evolve`.

## The pricing problem — the packages cost more than their parts

With the real rate card, the bundles are **marked up over à la carte**, not
discounted. Valuing a Booster at ~$100 (short check-in):

| Package | À la carte at her own rates | Package price | Markup |
|---|---|---|---|
| Essentials | $695 + 4×$250 + $100 = **$1,795** | $2,000 | **+11%** |
| Evolve | $695 + 6×$250 + $200 = **$2,395** | $3,750 | **+57%** |
| Unity | $695 + 8×$250 + $300 = **$2,995** | $4,995 | **+67%** |

A buyer can book the same calls on Calendly at $250 and buy the course
separately for less money. The Calendly link sits on the coaching page,
one click away. A package has to be cheaper than its parts or it has no
reason to exist.

**The $695 self-paced tier is also dominated.** $695 buys 1.5 hours of
video with no support; $2,000 buys that plus six hours with a licensed
psychotherapist. The $2,000 is obviously better, so the $695 converts
nobody — and it is far too expensive to be an impulse buy. It fails in
both directions.

**There is no entry point and no middle.** A top-5% podcast with 176
episodes has a cheapest paid product of $695, nothing between free and
$695, and nothing between $695 and $2,000 — which is where solo-expert
businesses usually make their money.

## Recommended ladder

| Tier | Now | Recommended | Payment plan |
|---|---|---|---|
| Quiz + site-wide popup | Free | Free — keep, it works | — |
| **NEW — "Say No Without Guilt" mini-course** | — | **$47** | — |
| Self-Paced Blueprint | $695 | **$297** | 2 × $165 |
| **NEW — Blueprint + Live Group** (6-wk cohort) | — | **$697** | 3 × $265 |
| Essentials (4 × 90-min + 1 Booster) | $2,000 | **$1,497** | 3 × $530 |
| Evolve (6 × 90-min + 2 Boosters) | $3,750 | **$1,997** | 4 × $530 |
| Unity (couples, 8 × 90-min + 3 Boosters) | $4,995 | **$2,997** | 5 × $650 |
| Single 1:1 | $250 / $200, unpublished | **publish on the coaching page** | — |

Rebuilt so each package is a genuine ~10–15% discount on her own rates,
with the course at $297:

- Essentials: $297 + $1,000 + $100 = $1,397 à la carte → **$1,497**, where
  the small premium is the 8 weeks of between-session access (name it).
- Evolve: $297 + $1,500 + $200 = $1,997 à la carte → **$1,997**, sold on
  structure rather than discount.
- Unity: two people, $297 + $2,000 + $300 = $2,597 → **$2,997** for a
  couple, i.e. ~$1,500 each.

### The fork on the premium tiers

Cutting Evolve from $3,750 to $1,997 is a large drop. The alternative is to
**raise the session rate** instead: boundaries coaching is not insurance-
billed therapy, and $250/90-min ($167/hr) is under market for a 15-year
licensed clinician with a top-5% show and a keynote practice. At
$400/90-min the current package prices become defensible almost as they
stand.

Pick one — they cannot coexist. Advertising $250 calls on Calendly while
selling those same calls inside a $3,750 package is the arbitrage that
makes the packages unsellable today. Given the goal is revenue from zero,
align down first and raise rates once volume exists.

Side note: at $250/90-min ($167/hr) vs $200/60-min ($200/hr), the longer
session is her cheapest hour. Consider $275–$300 for the 90-min to level it.

### Why cut the course but keep 1:1 strong

At $695 vs $2,000 the course is the worse deal. At $297 the ladder reads
correctly: $297 buys information, $1,497+ buys Dana's time. That also makes
the new $697 group tier the obvious step up from self-paced rather than a
squeeze between two bad options — and at ~10 people per cohort it is the
highest-margin product on the list.

## Sequence

Fix the path before touching the numbers, or the new prices sell as well as
the old ones.

1. Replace the waitlist popup on `/boundaries-course/` with real Thinkific
   checkout links. Delete the stale "Freedom of No" pricing block. This is
   the single highest-value change: the traffic and the leads already
   exist, and this is where they hit a wall.
2. Put a product CTA in the podcast page + episode content — the $47
   mini-course. Capture already works there; the pitch doesn't exist.
3. Add an offer section to the homepage, or accept that `/` is
   speaker-only and drive course traffic from the podcast instead.
4. Show prices on the Thinkific sales page and the coaching page. Fix the
   `http://` nav link and the dead `learn.danaskaggs.com` link.
5. Then reprice per the table and turn on payment plans.
6. Rename the Thinkific site and the `copy-of-...` slug.

Worth checking where the quiz results actually go — whether the Typeform
responses feed an email list and a follow-up sequence, or just sit in
Typeform. That determines whether the captured leads are reachable at all.

## Open questions for Dana

- Any historical sales at $695? Changes whether this is a repricing or a
  first launch.
- Which side of the fork — align packages down, or raise the session rate?
- Appetite for running live cohorts (the $697 tier depends on it).
- Is the keynote business the priority, with courses secondary? That
  changes how hard the homepage should push courses.
- What exactly is a Boundary Booster (length)? Priced at ~$100 above.
