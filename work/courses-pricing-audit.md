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

## The platform constraint (context that reframes the above)

Dana is on the **Thinkific free plan**, which allows one published course.
The three coaching tiers are therefore unpublished, which is *why* the WP
pricing table falls back to a waitlist. That was a deliberate workaround,
not an oversight.

Verified: all three premium `/enroll/` links redirect back to
`/courses/boundaries`. Nothing but the self-paced course can be bought.

| Page | Price shown publicly | Result of clicking through |
|---|---|---|
| `/courses/boundaries` (self-paced) | *none shown* | Real order page |
| `/courses/boundaries-essentials` | **$2,000.00** | Bounces to self-paced |
| `/courses/boundaries-evolve` | **$3,750.00** | Bounces to self-paced |
| `/courses/copy-of-…-evolve` (Unity) | **$4,995.00** | Bounces to self-paced |

**The unpublished tiers are still publicly visible with their old prices**
— listed on `/collections` and reachable at their own URLs. A prospect
sees $3,750, clicks, and gets dumped on a different product. That is worse
than a waitlist. Hide them, or take the pricing conversation onto
danaskaggs.com entirely.

### The free plan does not actually block the packages

The coaching tiers are *1:1 time sold with course access*. They do not need
to be Thinkific products at all:

- Publish the one allowed course on Thinkific ($97).
- Sell the three packages with **Stripe Payment Links** — free to create,
  standard processing fees, no subscription.
- On purchase, manually enrol the buyer in the free-tier course and send
  the Calendly link.

At three to eight package sales a month, manual fulfilment is minutes of
work. This turns the waitlist into a real buy button today, at zero
platform cost, and removes the plan limit from the critical path. Upgrade
Thinkific later, when monthly course revenue covers the plan several times
over — not before.

## Recommended ladder — one entry product, three packages

Built for ascension: the entry product exists to create buyers, not
revenue. Dana is speaking to live audiences and pitching the course from
stage, so tier 1 has to be something a stranger buys on a phone, in the
room, without deliberating.

| # | Product | Now | **New** | Payment plan |
|---|---|---|---|---|
| 1 | Self-Paced Course | $695 | **$97** | — |
| 2 | Course + 4 calls (Essentials) | $2,000 | **$997** | 3 × $350 |
| 3 | Course + 6 calls (Evolve) | $3,750 | **$1,497** | 3 × $525 |
| 4 | Course + 8 calls, couples (Unity) | $4,995 | **$1,997** | 3 × $700 |

Also: publish the $250 / $200 single-session rates on the coaching page.

### Counting Boundary Boosters as the call time they are

A Boundary Booster is a **scheduled 15-minute Zoom call** with a pre-call
form (per the course FAQ), and must be used within 60 days of enrolment.
That is delivery time, not a bonus, and it belongs in the maths. At her
60-minute rate ($200/hr) a 15-minute call is worth **$50** — earlier
drafts of this audit carried them at $100, which double-counted them.

Total call time and what each package actually earns per hour:

| Tier | Calls | Boosters | Total call time | À la carte | Price | Disc. | **$/hr** | $/hr after credit |
|---|---|---|---|---|---|---|---|---|
| Essentials | 4 × 90m | 1 | **6.25 hrs** | $1,050 | $997 | 5% | **$160** | $144 |
| Evolve | 6 × 90m | 2 | **9.5 hrs** | $1,600 | $1,497 | 6% | **$158** | $147 |
| Unity | 8 × 90m | 3 | **12.75 hrs** | $2,150 | $1,997 | 7% | **$157** | $149 |

Against her own rates ($167/hr for 90-min, $200/hr for 60-min), the
packages earn **$157–160/hr** — a 5–7% prepaid-package discount, which is
normal and defensible. The $97 ascension credit takes it to $144–149/hr,
a 12–14% total discount. That is the top of the reasonable band but not
outside it.

**The honest sales line is therefore "you pay for the call time, the
course is included"** — not "the course *and the Boosters* are included."
Boosters are 45 minutes of delivery on Unity; they are not free, and the
copy should not imply they are. They earn their place as a retention and
upsell touchpoint — the FAQ already routes a Booster that needs more depth
into a paid full session — but they cost real calendar.

Note also the pre-call form: a 15-minute Booster consumes closer to 30
minutes of Dana's time once form review and notes are counted, so the
true rate is a little below the table.

### If she wants to protect the rate instead

Price each package at exactly its call value, keeping the course free:

| Tier | Price | $/hr | After credit |
|---|---|---|---|
| Essentials | **$1,047** | $168 | $152 |
| Evolve | **$1,597** | $168 | $158 |
| Unity | **$2,147** | $169 | $161 |

Less memorable numbers, ~5% more revenue per package, same story. Given
the strategy is volume-and-ascension off the stage, the rounder
$997/$1,497/$1,997 is still the better bet — but this is the version that
holds her rate exactly.

### Capacity — what this can actually produce

At roughly 10 client hours a week (~43/month), and counting Booster time:

| Tier | Appointments | Hours | Clients/mo | Revenue/mo |
|---|---|---|---|---|
| Essentials | 5 | 6.25 | 6.9 | ~$6,900 |
| Evolve | 8 | 9.50 | 4.5 | ~$6,800 |
| Unity | 11 | 12.75 | 3.4 | ~$6,700 |

The tiers are near-identical on revenue per hour, so which one sells is a
buyer-preference question, not a margin question — Dana can push whichever
converts best. A full 1:1 calendar tops out near **$6,800/month** plus
course and keynote income. Unity is the thinnest tier and the heaviest to
deliver (11 appointments, two people); it should be capped or sold
sparingly while she is travelling to speak.

### The ascension mechanism

**The $97 is credited in full toward any package, within 90 days.**

- Removes the "I already bought the course" objection entirely.
- An ascending buyer pays $900 / $1,400 / $1,900.
- The 90-day expiry creates a real deadline without manufactured scarcity.
- Costs almost nothing: it converts a $97 buyer into a $997+ buyer.

Automate a reminder at day 7, 30 and 60 showing the credit and its expiry.
This is the whole strategy — the $97 is the qualifying step, not the sale.

### Why $97 and not $197

$97 is the line below which a professional buys without a decision
process. From stage that matters more than margin: the goal is to convert
a room into a buyer list, then ascend it. $97 × a keynote audience beats
$695 × nobody, and every buyer is a warm prospect for a $997+ package
carrying $97 of credit.

For live events, offer a **$67 room rate** with a code that expires at the
end of the event. It gives Dana a reason-to-act-now from the stage, which
is the single largest lever on in-room conversion.

### Making the keynote channel work

- One short URL and a QR code on the closing slide, going to a
  **single-purpose page with one button**. Do not send a keynote audience
  to danaskaggs.com — the homepage sells keynotes and the course page is a
  maze.
- Non-buyers still get captured by the existing quiz, which already works.
- One talk of ~100 people converting at even 5% covers a Thinkific
  upgrade several times over — that is the way out of the free tier.

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

1. **Reprice the self-paced course to $97** and show the price on its
   sales page (it currently shows none).
2. **Create three Stripe Payment Links** at $997 / $1,497 / $1,997, plus
   the 3-pay variants. No Thinkific upgrade required.
3. **Replace the waitlist popup** on `/boundaries-course/` with those
   links, and delete the stale "Freedom of No" pricing block.
4. **Hide the unpublished tier pages** on Thinkific, or at minimum get the
   old $2,000/$3,750/$4,995 prices off `/collections`.
5. **Build the keynote page**: one URL, one button, QR code for the closing
   slide, plus the $67 room-rate code.
6. Set up the $97 credit: track buyers, automate the day-7/30/60 reminder.
7. Put a course CTA in the podcast page + episode content. Capture already
   works there; the pitch doesn't exist.
8. Add an offer section to the homepage, or accept that `/` is
   speaker-only and drive course traffic from the podcast and stage.
9. Publish the $250/$200 session rates. Fix the `http://` nav link and the
   dead `learn.danaskaggs.com` link.
10. Rename the Thinkific site and the `copy-of-...` slug.

Worth checking where the quiz results actually go — whether the Typeform
responses feed an email list and a follow-up sequence, or just sit in
Typeform. That determines whether the captured leads are reachable at all.

## Open questions for Dana

- Any historical sales at $695? Changes whether this is a repricing or a
  first launch.
- Is the keynote business the priority, with courses secondary? That
  changes how hard the homepage should push courses.
- Boosters are 15-min Zoom calls per the FAQ, valued at $50. Confirm the
  pre-call form overhead — if each Booster really costs 30 min of Dana's
  time, Unity's effective rate drops another ~$6/hr.
- Do the Typeform responses feed an email list and a follow-up sequence,
  or sit in Typeform? Determines whether captured leads are reachable.

Decisions taken by Sadie (2026-08-07): drop prices; no mini-course; no
group cohort; keep the ladder to the four existing products.
