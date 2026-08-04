# Dana — podcast show-notes project

Working repo for improving the show notes of Dana's 177-episode podcast,
on WordPress and Libsyn, using the Riverside transcripts.

- `.claude/skills/dana-podcast/` — the skill: how to connect, the rewrite
  workflow, golden rules, and the SEO playbook. Read `SKILL.md` first.
- `tools/wp.sh` — WordPress REST API helper (auth + anti-bot retries),
  ported from the Melinda repo.
- `transcripts/` — Riverside transcript exports go here (see its README).
- `work/` — (created during the audit) the episode ledger tracking which
  of the 177 episodes have been rewritten on WP and Libsyn.

Credentials (WordPress app password, Libsyn access) are never committed —
they're provided per-session as environment variables.
