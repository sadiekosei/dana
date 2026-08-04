#!/usr/bin/env python3
"""Add/standardize a gentle content note on sensitive episodes. Usage: add_content_note.py"""
import json, re, subprocess, os, sys, time

SP = "/tmp/claude-0/-home-user/f65dd320-0205-594b-a4eb-9eaa25ad706a/scratchpad"
os.chdir("/home/user/dana")

TOPICS = json.load(open('work/bulk/trigger-warning-list.json'))

def wp(*args):
    env = os.environ.copy()
    env["WP_BASE"] = "https://danaskaggs.com"; env["WP_USER"] = "sadie"
    env["WP_APP_PASS"] = open(f"{SP}/dana_app_pass.txt").read().strip()
    r = subprocess.run(["bash", "tools/wp.sh", *args], capture_output=True, text=True, env=env, timeout=120)
    return r.stdout

def note_line(topic):
    return (f"<p><em>A gentle note before you dive in: this conversation touches on {topic}. "
            f"Take care of yourself while you listen, and know there's no rush.</em></p>")

def find_draft_path(eid):
    for p in (f"work/bulk/ep{eid}-draft.md", f"work/pilot/ep{eid}-draft.md"):
        if os.path.exists(p):
            return p
    return None

results = []
for eid, topic in TOPICS.items():
    eid = str(eid)
    path = find_draft_path(eid)
    if not path:
        print(f"ep{eid}: DRAFT NOT FOUND"); results.append((eid, "no-draft")); continue
    d = open(path).read()
    hm = re.search(r'## Proposed WordPress content\n\n```html\n(.*?)\n```', d, re.S)
    html = hm.group(1)

    if 'A gentle note before you dive in' in html:
        print(f"ep{eid}: already has the standard note, skipping edit")
    else:
        # remove ep3353-style old note if present anywhere in editorial
        cut = html.index('<details>') if '<details>' in html else len(html)
        editorial, rest = html[:cut], html[cut:]
        editorial = re.sub(r'\n\n<p>This episode includes frank discussion.*?</p>', '', editorial)
        # insert right after the "First aired" line
        m = re.search(r'(<p><em>First aired [^<]+</em></p>\n\n)', editorial)
        if not m:
            print(f"ep{eid}: NO 'First aired' LINE FOUND"); results.append((eid, "no-first-aired-line")); continue
        editorial = editorial[:m.end()] + note_line(topic) + "\n\n" + editorial[m.end():]
        html = editorial + rest
        d = d[:hm.start(1)] + html + d[hm.end(1):]
        open(path, 'w').write(d)
        print(f"ep{eid}: note added/repositioned ({topic})")

    # republish: fetch live, verify player, push
    live_raw = wp("get", "podcast", eid)
    try:
        live = json.loads(live_raw)
    except json.JSONDecodeError:
        print(f"ep{eid}: LIVE FETCH FAILED"); results.append((eid, "fetch-failed")); continue
    cur = live['content']['raw']
    pm = re.search(r'\[iframe[^\]]+\]', cur) or re.search(
        r'<!-- wp:create-block/libsyn-podcasting-block -->.*?<!-- /wp:create-block/libsyn-podcasting-block -->', cur, re.S)
    if not (pm and pm.group(0) in html):
        print(f"ep{eid}: PLAYER MISMATCH"); results.append((eid, "player-mismatch")); continue

    body_path = f"{SP}/note{eid}.json"
    json.dump({"content": html}, open(body_path, "w"))
    resp_raw = wp("update", "podcast", eid, body_path)
    try:
        resp = json.loads(resp_raw)
    except json.JSONDecodeError:
        print(f"ep{eid}: PUBLISH FAILED"); results.append((eid, "publish-failed")); continue
    if 'id' not in resp:
        print(f"ep{eid}: PUBLISH ERROR {resp_raw[:200]}"); results.append((eid, "publish-error")); continue
    note_present = 'A gentle note before you dive in' in resp['content']['raw']
    print(f"ep{eid}: REPUBLISHED -> {resp['link']} | note live: {note_present}")
    results.append((eid, "done" if note_present else "note-missing-after-publish"))
    time.sleep(1)

json.dump(results, open(f"{SP}/note_results.json", "w"))
print("\n=== SUMMARY ===")
for eid, status in results:
    print(eid, status)
