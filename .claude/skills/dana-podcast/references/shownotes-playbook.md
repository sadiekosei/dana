# Show-notes playbook — the rewrite template

Goal: turn each episode page into something search engines can rank and
listeners want to click. Bad show notes = a two-line summary and a
player. Good show notes = a mini-article targeting the question the
episode answers.

## Titles

- Lead with the topic/benefit, not the episode number: `How to X (with
  Guest Name) — Ep. 42`, never `Episode 42: Guest Name`.
- Front-load the primary keyword (the phrase someone would type into
  Google to find this episode's answer).
- ≤ 60 characters where possible so it doesn't truncate in SERPs.
- Keep WP title and Libsyn title identical — inconsistency confuses
  listeners moving between the site and a podcast app.

## Page structure (WordPress `content`)

1. **Player embed** — keep the existing player/shortcode first, untouched.
2. **Episode summary** (2–3 paragraphs, 150–250 words total): a real
   mini-article opening, not a teaser line. Paragraph 1: the problem the
   episode solves, primary keyword in the first sentence. Paragraph 2:
   who the guest is and why they're worth listening to (or for solo
   episodes, Dana's angle), plus what the conversation covers. Optional
   paragraph 3: what the listener walks away with. Work in 2–3 natural
   keyword variants across the paragraphs — visible editorial prose is
   what search engines weight most, since the transcript sits collapsed
   and bullets don't read as article text.
   **Voice is non-negotiable**: written in Dana's first-person sassy
   voice per `dana-voice.md` (read it first, every time). Open with a
   story or image FROM THE EPISODE, not "In this episode, X joins
   Dana..." The pilot drafts in `work/pilot/` are the exemplars.
3. **"In this episode"** — 4–6 bullet takeaways, each a benefit not a
   topic ("Why X fails and what to do instead", not "We discuss X").
4. **Timestamps** — `(MM:SS) Topic` list, built from the transcript.
   Google can surface these as key moments.
5. **Guest bio + links** (guest episodes): 2–3 sentences, link to their
   site/social. Guest names are search queries — include full name and
   title/company in text, not just in an image.
6. **Quotes** — 1–2 pull quotes from the transcript (quotable = shareable
   = long-tail keyword coverage).
7. **Resources mentioned** — every book/tool/article named in the
   episode, linked.
8. **Related episodes** — 2–3 internal links to other episodes on the
   topic. With 177 episodes this internal mesh is the cheapest ranking
   win available; add links in BOTH directions.
9. **CTA** — subscribe links (Apple/Spotify), review ask, newsletter.
10. **Transcript** — full transcript at the bottom, ideally in a
    collapsible block (`<details><summary>Read the full transcript
    </summary>…</details>`). This is the single biggest SEO lever: it
    puts every phrase spoken in the episode into the index. Lightly clean
    it (speaker labels, remove filler), don't paraphrase it.

## Metadata

- **Excerpt / meta description**: 150–160 chars, keyword + reason to
  click. Never let it default to the first line of the player embed.
- **Tags/categories**: consistent topic taxonomy across all 177 episodes
  (8–15 categories total, not one per episode).
- **Slug**: leave unchanged unless Sadie approves redirects (see golden
  rule 3 in SKILL.md).

## Libsyn description

Libsyn feeds the RSS shown in Apple/Spotify. Mirror the WP page down
through "Resources mentioned", then a link to the full show notes page
on Dana's site ("Full show notes & transcript: <url>"). Podcast apps
strip most HTML — Libsyn descriptions should use simple paragraphs,
`<p>`/`<a>`/`<ul>` only. No transcript in the RSS description (length
limits; some apps truncate at ~4000 chars).

## Keyword picking, per episode

From the transcript, identify: (a) the core question the episode
answers, (b) 2–3 phrases a searcher would use, (c) guest name variants.
Primary keyword goes in title + first sentence + one H2. Don't stuff —
one natural use in each location beats five forced ones.

## Quality bar for the pilot batch

An episode rewrite is done when: title leads with topic; hook para
exists; ≥4 takeaway bullets; timestamps present (if transcript exists);
excerpt is a real meta description; ≥2 internal links added; player
still renders; Libsyn matches. Log all of it in the ledger.
