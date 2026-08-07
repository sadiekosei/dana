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

**There is no entry point.** A top-5% podcast with 176 episodes has a
cheapest paid product of $695, and nothing between free and $695.

## Blocker 3 — the buy buttons sell the wrong product

On **every** Thinkific course page, the two most prominent CTAs ("Join The
Course" in the hero, "Enroll Now" further down) are hardcoded to the same
link:

```
/cart/add_product/2232432?price_id=3001780
```

That is the **self-paced course** product. Only the small pricing-table
button carries the correct per-tier link:

| Page | Price shown | Hero CTA goes to | Correct link |
|---|---|---|---|
| `/courses/boundaries` (self-paced) | *none shown* | product 2232432 | — (correct) |
| `/courses/boundaries-essentials` | $2,000.00 | product 2232432 | `/enroll/3353324` |
| `/courses/boundaries-evolve` | $3,750.00 | product 2232432 | `/enroll/3360281` |
| `/courses/copy-of-…-evolve` (Unity) | $4,995.00 | product 2232432 | `/enroll/3360285` |

So a visitor on the $3,750 page who clicks the big obvious button gets the
$695 self-paced course in their cart. Fix this before repricing — a price
change on a page whose main button sells something else changes nothing.

## Recommended ladder — four tiers, no new products

Prices dropped across the board, built only from what already exists, and
priced so every package is a genuine discount on booking à la carte.

| # | Product | Now | **New** | Payment plan |
|---|---|---|---|---|
| 1 | Self-Paced Course | $695 | **$197** | — |
| 2 | Course + 4 calls (Essentials) | $2,000 | **$1,097** | 3 × $385 |
| 3 | Course + 6 calls (Evolve) | $3,750 | **$1,597** | 3 × $560 |
| 4 | Course + 8 calls, couples (Unity) | $4,995 | **$2,097** | 3 × $735 |

Also: publish the $250 / $200 single-session rates on the coaching page.

Math at her real rates ($250 per 90-min call, Booster valued ~$100):

| Tier | À la carte | Package | Saving |
|---|---|---|---|
| Essentials | $197 + $1,000 + $100 = $1,297 | **$1,097** | 15% |
| Evolve | $197 + $1,500 + $200 = $1,897 | **$1,597** | 16% |
| Unity | $197 + $2,000 + $300 = $2,497 | **$2,097** | 16% |

Every tier is now cheaper than assembling the same thing from Calendly,
which is what makes a package worth buying. Effective rate lands
$175–$183/hr across all three — consistent with her own rate card.

### Why $197 for tier 1

With no mini-course, the self-paced course *is* the entry product, so it
has to be the thing a podcast listener buys without deliberating. 1.5 hours
of video cannot do that at $695; at $197 it can. It also anchors the ladder
correctly: $197 buys information, $1,097+ buys Dana's time. Consider $147
for a first-30-days launch window, then settle at $197.

### Simplifying the confusion

- **Lead with call counts, not tier names.** "Course + 6 Calls" is legible
  on sight; "Evolve" is not. Keep the names as subtitles if she likes them.
- **One product name everywhere.** The Thinkific site is still titled
  *The "Freedom of No" Formula* while the WP site says *The Boundary
  Blueprint*, and the coaching page advertises the old name too.
- **Fix the slug** `copy-of-the-freedom-of-no-formula-evolve` → `boundaries-unity`.
- **One pricing table** on `/boundaries-course/`, not the current two.
- **Show the price** on the self-paced sales page — it currently shows none.

## Sequence

Fix the path before touching the numbers, or the new prices sell as well as
the old ones.

1. **Fix the Thinkific hero CTAs** so each tier's buttons sell that tier.
2. Replace the waitlist popup on `/boundaries-course/` with real Thinkific
   checkout links. Delete the stale "Freedom of No" pricing block. The
   traffic and the leads already exist; this is where they hit a wall.
3. Reprice per the table and turn on the 3-pay plans.
4. Put a course CTA in the podcast page + episode content — the $197
   course. Capture already works there; the pitch doesn't exist.
5. Add an offer section to the homepage, or accept that `/` is
   speaker-only and drive course traffic from the podcast instead.
6. Show prices on the Thinkific sales page and the coaching page. Fix the
   `http://` nav link and the dead `learn.danaskaggs.com` link.
7. Rename the Thinkific site and the `copy-of-...` slug.

Worth checking where the quiz results actually go — whether the Typeform
responses feed an email list and a follow-up sequence, or just sit in
Typeform. That determines whether the captured leads are reachable at all.

## Open questions for Dana

- Any historical sales at $695? Changes whether this is a repricing or a
  first launch.
- Is the keynote business the priority, with courses secondary? That
  changes how hard the homepage should push courses.
- What exactly is a Boundary Booster (length)? Valued at ~$100 above; if
  it is a full 60-min session the package discounts get slightly deeper.
- Do the Typeform responses feed an email list and a follow-up sequence,
  or sit in Typeform? Determines whether captured leads are reachable.

Decisions taken by Sadie (2026-08-07): drop prices; no mini-course; no
group cohort; keep the ladder to the four existing products.
