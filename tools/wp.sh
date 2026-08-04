#!/usr/bin/env bash
# WordPress REST API helper for Dana's podcast site.
# Ported from the Melinda repo's Kosei Deploy helper; generalized to the
# core WordPress REST API (wp/v2) since Dana's site has no custom deploy
# plugin. Keeps the anti-bot handling (browser UA + same-origin Referer +
# retries) — harmless on hosts without a challenge, essential on hosts
# like SiteGround that front /wp-json with a captcha.
#
# Usage:
#   tools/wp.sh me                                  # sanity-check auth
#   tools/wp.sh types                               # list post types (find the episode CPT)
#   tools/wp.sh posts [type] [page]                 # list posts of a type (default: posts, page 1)
#   tools/wp.sh get <type> <id>                     # fetch one post
#   tools/wp.sh update <type> <id> <file.json>      # update a post from a JSON body
#   tools/wp.sh api <METHOD> <path> [curl-args...]  # raw REST call
#
# Credentials come from env: WP_BASE, WP_USER, WP_APP_PASS (an Application
# Password — WP admin > Users > Profile > Application Passwords). Never
# commit the password to the repo.
set -euo pipefail
BASE="${WP_BASE:?set WP_BASE, e.g. https://example.com}"
USER_="${WP_USER:?set WP_USER}"
PASS="${WP_APP_PASS:?set WP_APP_PASS}"
UA="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36"
call(){
  local out
  for i in 1 2 3 4 5 6 7 8; do
    out=$(curl -sS --max-time 90 -A "$UA" -e "$BASE/" -u "$USER_:$PASS" "$@") || true
    # Retry on anti-bot challenge pages (SiteGround "sgcaptcha" or any HTML
    # response where JSON was expected).
    if ! printf '%s' "$out" | grep -q sgcaptcha; then printf '%s' "$out"; return 0; fi
    sleep 3
  done
  printf '%s' "$out"; return 1
}
case "${1:-}" in
  me)     call "$BASE/wp-json/wp/v2/users/me" ;;
  types)  call "$BASE/wp-json/wp/v2/types" ;;
  posts)  T="${2:-posts}"; P="${3:-1}"; call "$BASE/wp-json/wp/v2/$T?per_page=100&page=$P&status=any&context=edit&orderby=date&order=asc" ;;
  get)    call "$BASE/wp-json/wp/v2/$2/$3?context=edit" ;;
  update) call -X POST -H "Content-Type: application/json" --data-binary @"$4" "$BASE/wp-json/wp/v2/$2/$3" ;;
  api)    M="$2"; P="$3"; shift 3; call -X "$M" "$BASE$P" "$@" ;;
  *) echo "usage: wp.sh me|types|posts [type] [page]|get <type> <id>|update <type> <id> <f.json>|api <M> <path>"; exit 1 ;;
esac
