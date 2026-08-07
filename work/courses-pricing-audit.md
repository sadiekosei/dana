# Dana's courses — offer & pricing audit

Audited 2026-08-07 from public sources: danaskaggs.com (WP REST + rendered
pages) and danaskaggs.thinkific.com. No analytics or sales data available —
conversion claims are inferred from funnel structure, not measured.

Rate card confirmed by Sadie: **base rate $200/hr.** She currently charges
$250 for a 90-minute session (a 17% discount on her own rate) and $200 for
60 minutes. Boundary Boosters are 15-minute Zoom calls = $50 of call time.

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
discounted (Booster = 15 min = $50):

| Package | À la carte at her own rates | Package price | Markup |
|---|---|---|---|
| Essentials | $695 + 4×$250 + $50 = **$1,745** | $2,000 | **+15%** |
| Evolve | $695 + 6×$250 + $100 = **$2,295** | $3,750 | **+63%** |
| Unity | $695 + 8×$250 + $150 = **$2,845** | $4,995 | **+76%** |

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

- Publish the one allowed course on Thinkific ($99).
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
| 1 | Self-Paced Course | $695 | **$99** | — |
| 2 | Course + 4 calls (Essentials) | $2,000 | **$1,100** | 3 × $385 |
| 3 | Course + 6 calls (Evolve) | $3,750 | **$1,700** | 3 × $595 |
| 4 | Course + 8 calls, couples (Unity) | $4,995 | **$2,200** | 3 × $770 |

This assumes the **90-minute session moves to $300** (see the rate fork
below). Publish the session rates on the coaching page either way.

### Price endings: $99, not $97

The 7-ending is a convention of internet-marketing launch culture, not a
research finding. There is no good evidence that 7 outperforms 9; the
reason 7s are everywhere is that one subculture copied another.

9-endings do have real evidence behind them (the classic catalogue
experiments found $39 outselling $34), though the effects are modest and
concentrated in low-consideration consumer purchases. For a $99 impulse
buy that is exactly the right context — and the part that actually matters
is staying **under $100**, which both do.

The tiebreaker is signalling. To a corporate keynote audience — HR
leaders, professionals — a 7-ending reads as *funnel*, which cuts against
the credibility of a licensed psychotherapist. $99 gets the sub-$100
threshold without the tell.

**For the packages, use round numbers.** Charm pricing on high-ticket 1:1
services works against you: $1,997 for a clinician's time looks like a
funnel, $2,200 looks like a professional fee. There is also evidence that
rounded prices feel "right" for emotionally-driven purchases, which
boundaries coaching after a keynote very much is. Round is both the
premium signal and the better fit.

### The rate fork — her 90-minute session is underpriced

If Dana's rate is **$200/hr**, then a 90-minute session should be $300.
She charges **$250** — a 17% discount on her longest sessions, which are
also her most common (the intro session is 90 minutes). That single number
decides what the packages can cost, because a buyer compares the package
against booking sessions on Calendly at whatever she charges there.

A Boundary Booster is a **scheduled 15-minute Zoom call** with a pre-call
form (per the course FAQ), used within 60 days. That is delivery time, not
a bonus: $50 at $200/hr. Earlier drafts carried it at $100, double-counted.

Total call time per package: **6.25 / 9.5 / 12.75 hours**.

**Path A — raise the 90-minute session to $300** (recommended)

| Tier | À la carte | Package | Saving | Her $/hr | After credit |
|---|---|---|---|---|---|
| Essentials | $1,349 | **$1,100** | 18% | **$176** | $160 |
| Evolve | $1,999 | **$1,700** | 15% | **$179** | $169 |
| Unity | $2,649 | **$2,200** | 17% | **$173** | $165 |

**Path B — keep the 90-minute session at $250**

| Tier | À la carte | Package | Saving | Her $/hr | After credit |
|---|---|---|---|---|---|
| Essentials | $1,149 | **$997** | 13% | $160 | $144 |
| Evolve | $1,699 | **$1,497** | 12% | $158 | $147 |
| Unity | $2,249 | **$1,997** | 11% | $157 | $149 |

Path A earns roughly **$20/hr more on every package** and allows the round
premium numbers. Path B caps the packages near $1,000/$1,500/$2,000 — go
any higher and booking four sessions on Calendly beats the package, which
is the exact arbitrage that makes the current pricing unsellable.

Both paths give the buyer a real 11–18% saving. The difference is entirely
whether Dana stops discounting her own longest session.

**The sales line is "you pay for the call time, the course is included"** —
not "the course *and the Boosters* are included." Boosters are 45 minutes
of delivery on Unity; the copy should not imply they are free. They earn
their place as a retention and upsell touchpoint — the FAQ already routes
a Booster needing more depth into a paid session — but they cost calendar.

Note the pre-call form: a 15-minute Booster likely consumes closer to 30
minutes once review and notes are counted, so the true rate sits a little
below these tables.

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

**The $99 is credited in full toward any package, within 90 days.**

- Removes the "I already bought the course" objection entirely.
- An ascending buyer pays $1,001 / $1,601 / $2,101.
- The 90-day expiry creates a real deadline without manufactured scarcity.
- Costs almost nothing: it converts a $99 buyer into a $1,100+ buyer.

Automate a reminder at day 7, 30 and 60 showing the credit and its expiry.
This is the whole strategy — the $99 is the qualifying step, not the sale.

### Why $99 and not $197

$99 is the line below which a professional buys without a decision
process. From stage that matters more than margin: the goal is to convert
a room into a buyer list, then ascend it. $99 × a keynote audience beats
$695 × nobody, and every buyer is a warm prospect for a $997+ package
carrying $99 of credit.

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

1. **Reprice the self-paced course to $99** and show the price on its
   sales page (it currently shows none).
2. **Create three Stripe Payment Links** at $1,100 / $1,700 / $2,200, plus
   the 3-pay variants. No Thinkific upgrade required.
3. **Replace the waitlist popup** on `/boundaries-course/` with those
   links, and delete the stale "Freedom of No" pricing block.
4. **Hide the unpublished tier pages** on Thinkific, or at minimum get the
   old $2,000/$3,750/$4,995 prices off `/collections`.
5. **Build the keynote page**: one URL, one button, QR code for the closing
   slide, plus the $67 room-rate code.
6. Set up the $99 credit: track buyers, automate the day-7/30/60 reminder.
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
