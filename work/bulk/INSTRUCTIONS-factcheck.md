# Fact-check instructions (one agent, N episodes)

You are the independent fact-checker for podcast show-notes drafts in
/home/user/dana. You did NOT write these drafts. Your only loyalty is
to the transcript. Guests share these pages; a mangled detail offends.

## Per episode id

Inputs: `work/bulk/ep<ID>-draft.md` (the draft),
`work/bulk/ep<ID>-input.json` (gives transcript_file and content_raw).

1. Read the draft's EDITORIAL sections (everything above `<details>`)
   and the FULL transcript.
2. For every factual claim about a person (host or guest) in the
   summary, bullets, timestamps, bio, and quotes: find the supporting
   transcript line. Claims include actions, feelings, numbers, places,
   jobs, credentials, product/book names.
3. Verify each pull quote is VERBATIM (allowing only filler-word
   removal and stitching across line breaks that preserves wording).
4. Verify each URL in the editorial sections appears in the transcript,
   in content_raw (the original post), or is one of the canonical links
   (pod.link/1513991564, the Apple/Spotify/Amazon/YouTube follow links,
   danaskaggs.com/quiz/, danaskaggs.com/link-tree/,
   danaskaggs.com/podcast/<slug>/ internal links).
5. FIX problems by editing the draft file in place: rewrite unsupported
   claims to the transcript's wording, or cut them. Fix broken quote
   wording to verbatim. Remove/replace invented URLs. Do not otherwise
   rewrite style.
6. Also enforce mechanics while you're in there: no spaced em dashes
   (` — `) outside the transcript block; external links carry
   target="_blank" rel="noopener"; internal danaskaggs.com links don't.

Write `work/bulk/ep<ID>-factcheck.txt`: one line per issue found and
fixed (`CLAIM-FIXED: ...` / `QUOTE-FIXED: ...` / `URL-CUT: ...` /
`MECH-FIXED: ...`), or `CLEAN` if nothing needed fixing.

Return only: `ep<ID>: CLEAN` or `ep<ID>: <n> fixes` lines.
