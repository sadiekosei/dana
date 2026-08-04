# Bulk drafting instructions (one agent, N episodes)

You are drafting SEO show notes for episodes of Dana Skaggs' "Phoenix
and Flame" podcast. Work in /home/user/dana. Process EVERY episode id
you were given, one at a time, fully, before moving to the next.

## Read once, before the first episode

1. `.claude/skills/dana-podcast/references/shownotes-playbook.md` (the
   format spec + mandatory SEO checklist)
2. `.claude/skills/dana-podcast/references/dana-voice.md` (the voice;
   non-negotiable)
3. `work/pilot/ep3962-draft.md` — the approved exemplar. Match its
   section order and formatting EXACTLY (it already encodes every rule:
   date line, YouTube embed placement, follow links, quiz CTA, closed
   em-dash policy, new-tab externals).
4. `work/bulk/catalog.txt` — all 176 episodes (id|date|slug|title), for
   picking related episodes.

## Per episode

Input: `work/bulk/ep<ID>-input.json` — current WP title/content/excerpt,
slug, url, first_aired (pre-formatted), transcript_file (repo-relative),
youtube_id (null = omit the "Watch this episode" section).

Read the FULL transcript. Then write `work/bulk/ep<ID>-draft.md` with
the exemplar's exact structure:

# Episode <ID>: <current title>
## Proposed title
## Proposed excerpt (meta description)
## Proposed WordPress content   (one ```html block)
## Proposed Libsyn description
## Notes for reviewer

The html block, in order: player embed from content_raw preserved
VERBATIM (the [iframe ...] shortcode or the libsyn Gutenberg block
comment — whichever the episode has; if it has neither, start with the
first-aired line and say so in reviewer notes) → `<p><em>First aired
<first_aired></em></p>` → 2-3 paragraph episode summary (150-250 words,
Dana's first-person voice, opens with a story/image FROM THE EPISODE)
→ Watch this episode (H2 + responsive 16:9 iframe,
youtube.com/embed/<youtube_id>) → In this episode (4-6 benefit
bullets) → Timestamps → guest bio + links (guest episodes only) →
1-3 verbatim pull quotes → Resources mentioned → **Related episodes**
(H2 + 2-3 `<a href="https://danaskaggs.com/podcast/<slug>/">` links
picked from catalog.txt by topic closeness, each with one line on why
it pairs; internal links = same tab) → Subscribe & connect (copy the
exemplar's follow-links paragraph verbatim, then link-tree line, then
quiz line) → full transcript in `<details>` (lightly cleaned: keep
speaker labels + timestamps, fix typos, drop filler; never paraphrase
or shorten).

## Hard rules (each one has already burned us)

- FACTUAL ABOVE ALL: every claim about a person must trace to exact
  transcript wording. Don't upgrade "sitting" to "locked", don't add
  details like pay or feelings that weren't said, keep the speaker's
  own dialect words. When in doubt, quote.
- URLs: only ones present in the transcript or current content, plus
  the canonical set in the exemplar (follow links, quiz, link-tree).
  NEVER invent or guess a URL.
- Em dashes: avoid; when unavoidable set closed (word—word). Quote
  attributions use `—Name`.
- External links: `target="_blank" rel="noopener"`. Internal
  danaskaggs.com links: no target.
- Slug/URL: never propose changing it.
- Titles: topic/benefit first, ≤60 chars where possible, guest name
  included when there is a guest.
- Excerpt: 150-160 chars, keyword + reason to click.

Return only: a list of `ep<ID>: done` lines (or `ep<ID>: PROBLEM -
<one line>` if something blocked you).
