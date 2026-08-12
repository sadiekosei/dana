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
- **FAQ edited again once `/course/` existed** (5 of 12 items):
  - "Can I do the course without the coaching calls?" now links to
    `danaskaggs.com/course/`, not the Thinkific subdomain, and states the
    $99 upgrade credit.
  - "What's the difference between the packages?" now **names them** the
    way the pricing table does — Essentials (4 calls), Evolve (6),
    Unity (8, couples) — instead of only counting calls.
  - "Is this appropriate for couples?" and "Do you offer payment plans?"
    likewise name the tiers.
  - "lifetime access" → "access that never expires", matching `/course/`.

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

## I. Plugin reverted mid-session — how to recover

The plugin dropped back to **v1.9.1**, taking every endpoint added this
session with it. The likely cause: the `claude/dana-podcast-shownotes-wt0v42`
branch carries v1.9.1, so a session working there that ran
`plugin-self-update` would overwrite this one's build.

Recovering it needed a step no API call could do:

1. Re-push the current plugin via `/plugin-self-update` — this **succeeds**
   and `wp/v2/plugins` then reports the new version, because WordPress reads
   the header off disk.
2. But the *running* code stays old: PHP serves stale compiled bytecode. The
   new routes 404 no matter how long you wait. The plugin's own
   `opcache_reset()` on load cannot help, because the cached copy is what
   executes.
3. `wp/v2/plugins` can't cycle it either — that route returns
   `rest_plugin_not_found` for this plugin's path form.
4. **Fix: deactivate and reactivate the plugin in WP Admin → Plugins.**
   That forces a fresh compile and all routes return immediately.

Diagnostic that identifies it quickly: `GET /wp-json/kosei-dana/v1` lists
the registered routes. If yours are missing while `wp/v2/plugins` reports
your version, it is bytecode, not the file.

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

**Rounded imagery, site-wide (Sadie, 2026-08-07).** All content images get
`border-radius: 20px` via the kit's Custom CSS, so it applies everywhere
without per-widget settings. Deliberately excluded: logos (header, footer,
`.custom-logo`), SVGs, and testimonial avatars — those stay square or
circular. Kit 6 had no `custom_css` before this; the payload is backed up
at `work/backups/kit-6-custom-css-2026-08-07.json`. Verified loading on
`/`, `/boundaries-course/`, `/about/` and `/podcast/`.

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

## G. `/course/` (page 4589) — Elementor build notes

Three mistakes worth not repeating, all found by Sadie on screenshots:

1. **Never copy a donor widget's settings blind.** This cost three separate
   bugs from one donor:
   - `title_color`/`text_color` of `#FFFFFF` (the donor sat on a dark band)
     — every heading rendered white on white
   - a `__dynamic__` popup tag that silently overrode the checkout href
   - **`hide_tablet: "hidden-tablet"` and `hide_mobile: "hidden-mobile"`** —
     both checkout buttons were invisible on phones and tablets, on a page
     built for a QR-code audience. Nothing in the data looks wrong; the
     buttons simply do not render below desktop.

   Copy only the keys you actually want (typography, padding, colour) and
   set layout/visibility explicitly. Whitelist, never blacklist.
2. **Full-bleed backgrounds come from the PAGE TEMPLATE, not the section.**
   OceanWP wraps a default-template page in `.content-area` inside
   `.container`, which constrains every section no matter what
   `layout`/`content_width` you set on it — section-level `full_width` only
   un-boxes the *content*, it cannot escape the theme wrapper.

   The fix is `template: "elementor_header_footer"` on the page (settable
   via core REST). Every other Elementor page on this site uses it; you can
   confirm from the body classes — a correct page carries
   `content-full-width content-max-width elementor-template-full-width` and
   has **no** `.content-area` wrapper.

   With that template in place, leave sections **boxed**: the background
   bleeds to the viewport edge while content stays constrained to 1140px,
   which is what Sadie asked for. No percentage column-padding hack needed.
3. **Binding `typography_typography` to a global discards every font size
   you set.** The global's own size wins. The site sets
   `typography_typography: 'custom'` with an explicit family/size/weight and
   binds only the *colour* to a global. Sizes in use: 48–91px desktop
   headings, 28–32px tablet/mobile, 16px body, EB Garamond 500 for headings
   and Helvetica 400 for body, 1.3em heading line-height.

Also: the YouTube intro (`i2JWm2caWCE`) follows the podcast playbook —
`referrerpolicy` as the FIRST iframe attribute and `margin-bottom:32px` on
the wrapper.

**Design decisions taken with Sadie:**

- **Access wording.** Access is not time-limited, so the page says
  *"unlimited access"* / *"your access never expires"*, never "lifetime
  access".
- **The `epicursive` script accent is used sparingly** — three headings on
  the page (the hero title, "Meet *Dana*", and the closing CTA), not every
  heading. Using it everywhere flattens it into noise.
- **Hero rhythm is set explicitly**, not left to Elementor defaults: H1
  58px at 1.05em line-height, subhead 17px/1.6em, price 46px, meta line
  15px, with `_margin` on each widget. Column side padding must be
  **equal** (24px each side) or "centred" content sits visibly off-centre.
- **The page excerpt feeds `og:description`** and is easy to forget. It has
  now twice been the last place stale copy survived after the body was
  fixed — check it whenever wording changes.
- **Sentence fragments are Title Case**, not lower case — "Self-Paced ·
  Unlimited Access · Start Today". Lower case reads informal for a
  clinician's page.

**Two ways a button silently fails to centre**, both hit on this page:

1. `_element_width: 'auto'` emits `width:auto;max-width:auto`, which
   shrink-wraps the widget to the button so it sits at the column's left
   edge — the button's own `align: center` then has nothing to centre
   inside. Don't set `_element_width` on a button you want centred.
2. Even with the correct `elementor-align-center` class in the markup,
   **LiteSpeed's unused-CSS pass strips Elementor's
   `.elementor-align-center` rule**, so the class lands with no rule behind
   it. Page-level Custom CSS survives that pass:

   ```css
   .elementor-widget-button .elementor-button-wrapper{text-align:center !important;}
   ```

   Diagnose by grepping the page CSS for the rule, not the class — the
   class being present in the HTML proves nothing.

## E. Nav — DONE 2026-08-07

Menu item **902** "Self-paced Boundaries Course" (custom link, parent 901
"Work with Me", menu 3) repointed from `http://danaskaggs.thinkific.com`
to `https://danaskaggs.com/course/`. Verified across `/`,
`/boundaries-course/`, `/podcast/` and `/coaching/`: zero remaining
links to the Thinkific root in the nav.

That also retires the insecure `http://` scheme, and means the course
funnel now runs entirely on her own domain with Thinkific reached only at
checkout.

## H2. I clobbered a live edit — what went wrong and how it was recovered

Sadie edited the hero video in Elementor (swapping my raw-HTML iframe for a
proper `video` widget with a custom overlay thumbnail, 4:3 ratio and a
styled play icon). My next write overwrote it, because I built the payload
from a **locally cached tree** (`v11.json`) instead of re-fetching. That is
exactly the golden rule in the skill: *fetch the live post immediately
before every update.* Batch-editing from a saved copy silently breaks it.

Recovery worked because `page_elementor_put` snapshots the previous
`_elementor_data` into `_kosei_elementor_backup_<UTC timestamp>` postmeta
before every save. The clobbered version was the largest snapshot (42KB vs
my 25KB — Elementor writes far more verbose settings than hand-built JSON,
which is a useful tell for "a human saved this"). Plugin v1.10.6 adds
`GET /page-backup?page_id=&key=` to read a snapshot back.

**Rule: always GET immediately before POST, even mid-sequence.**

## H3. "testimonials white" means white TEXT, not a white background

Global widget **740** rendered as an empty band on `/course/`: heading
visible, carousel invisible, one pale pagination dot. The markup and swiper
assets were identical to the working page, which ruled out JS.

The name is the trap — page 1356 uses 740 on its **navy** section
(`#012E41`). The template styles its text white, so on a white background
it is white-on-white. Moving the section to navy fixed it, and gives the
page a better rhythm: navy → cream → white → cream → **navy** → cream →
navy.

## H. Reusable Elementor globals worth knowing about

The site has a library of global widgets/templates in `elementor_library`;
reuse these instead of hand-building equivalents:

| ID | Name | Type |
|---|---|---|
| 740 | testimonials white | `testimonial-carousel`, 4 slides |
| 1180 | testimonials blue | `eael-testimonial-slider` |
| 1166 | testimonial widget | `eael-testimonial-slider` |
| 1534 | Meet Dana Skaggs | section |
| 1520 | Speaking Testimonials | section |
| 1496 | 4 Symptoms of Boundary Issues | section |
| 1636 | Course Inquiry Form | popup |

A global widget is referenced as
`{"elType":"widget","widgetType":"global","templateID":740,"settings":{}}`.

**Instance-level `settings` on a global widget are ignored.** Elementor
renders a global purely from its template, so there is no per-page
override — writing `show_arrows` onto the instance changed nothing. The
only two options are:

1. edit the template (changes every page using it), or
2. stop using the global and place a local copy of the widget.

Arrows were enabled by editing template 740 (option 1), which turned them
on for **both** `/course/` and `/boundaries-course/` — the same component,
same white arrow styling on both navy sections. Previous state is backed up
at `work/backups/global-testimonials-740-before-arrows-2026-08-07.json`;
set `show_arrows` back to `''` to revert.

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
