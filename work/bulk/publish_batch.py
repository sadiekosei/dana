#!/usr/bin/env python3
"""Verify + publish a batch of bulk-drafted episodes. Usage: publish_batch.py <id> [<id>...]"""
import json, re, subprocess, sys, os, time

SP = "/tmp/claude-0/-home-user/f65dd320-0205-594b-a4eb-9eaa25ad706a/scratchpad"
os.chdir("/home/user/dana")

def wp(*args):
    env = os.environ.copy()
    env["WP_BASE"] = "https://danaskaggs.com"
    env["WP_USER"] = "sadie"
    env["WP_APP_PASS"] = open(f"{SP}/dana_app_pass.txt").read().strip()
    r = subprocess.run(["bash", "tools/wp.sh", *args], capture_output=True, text=True, env=env, timeout=120)
    return r.stdout

def checklist(html, youtube_id, url):
    cut = html.index('<details>') if '<details>' in html else len(html)
    editorial = html[:cut]
    ext_missing = [h for h in re.findall(r'<a href="(https?://[^"]+)"(?![^>]*target)', editorial) if 'danaskaggs.com' not in h]
    checks = {
        'player': ('[iframe' in html) or ('libsyn-podcasting-block' in html),
        'first-aired': 'First aired' in editorial,
        'youtube-embed': (youtube_id is None) or (f'youtube.com/embed/{youtube_id}' in html),
        'quiz': 'danaskaggs.com/quiz' in html,
        'podlink': 'pod.link/1513991564' in html,
        'ext-links-newtab': not ext_missing,
        'no-spaced-emdash': ' — ' not in editorial,
        'transcript': '<details>' in html,
        'timestamps': bool(re.search(r'\(\d{1,2}:\d{2}\)', html)),
    }
    bad = [k for k, v in checks.items() if not v]
    return bad, ext_missing

def main(ids):
    results = []
    for eid in ids:
        eid = str(eid)
        inp = json.load(open(f"work/bulk/ep{eid}-input.json"))
        draft_path = f"work/bulk/ep{eid}-draft.md"
        if not os.path.exists(draft_path):
            print(f"ep{eid}: MISSING DRAFT"); results.append((eid, "missing-draft", None)); continue
        d = open(draft_path).read()
        tm = re.search(r'## Proposed title\n\n(.+)', d)
        em = re.search(r'## Proposed excerpt \(meta description\)\n\n(.+)', d)
        hm = re.search(r'## Proposed WordPress content\n\n```html\n(.*?)\n```', d, re.S)
        if not (tm and em and hm):
            print(f"ep{eid}: MALFORMED DRAFT"); results.append((eid, "malformed", None)); continue
        title, excerpt, html = tm.group(1).strip(), em.group(1).strip(), hm.group(1)

        bad, ext_missing = checklist(html, inp.get('youtube_id'), inp['url'])
        if bad:
            print(f"ep{eid}: CHECKLIST FAIL {bad} ext_missing={ext_missing[:2]}")
            results.append((eid, f"checklist-fail:{bad}", None)); continue

        live_raw = wp("get", "podcast", eid)
        try:
            live = json.loads(live_raw)
        except json.JSONDecodeError:
            print(f"ep{eid}: LIVE FETCH FAILED"); results.append((eid, "fetch-failed", None)); continue
        cur = live['content']['raw']
        pm = re.search(r'\[iframe[^\]]+\]', cur) or re.search(
            r'<!-- wp:create-block/libsyn-podcasting-block -->.*?<!-- /wp:create-block/libsyn-podcasting-block -->', cur, re.S)
        if not (pm and pm.group(0) in html):
            print(f"ep{eid}: PLAYER MISMATCH vs live"); results.append((eid, "player-mismatch", None)); continue

        body_path = f"{SP}/pub{eid}.json"
        json.dump({"title": title, "content": html, "excerpt": excerpt}, open(body_path, "w"))
        resp_raw = wp("update", "podcast", eid, body_path)
        try:
            resp = json.loads(resp_raw)
        except json.JSONDecodeError:
            print(f"ep{eid}: PUBLISH FAILED (bad response)"); results.append((eid, "publish-failed", None)); continue
        if 'id' not in resp:
            print(f"ep{eid}: PUBLISH ERROR {resp_raw[:200]}"); results.append((eid, "publish-error", None)); continue

        print(f"ep{eid}: PUBLISHED -> {resp['link']}")
        results.append((eid, "published", resp['link']))
        time.sleep(1)
    return results

if __name__ == "__main__":
    out = main(sys.argv[1:])
    json.dump(out, open(f"{SP}/publish_result.json", "w"))
