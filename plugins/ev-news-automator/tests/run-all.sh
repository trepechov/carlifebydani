#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# EV News Automator — integration smoke-test suite
#
# Run from the WordPress root (where wp-config.php lives):
#
#   bash wp-content/plugins/ev-news-automator/tests/run-all.sh
#
# Flags:
#   --skip-docs   Skip test-03 (it appends to a real Google Doc)
#   --continue    Don't stop the suite on the first failed test
# ─────────────────────────────────────────────────────────────────────────────

set -euo pipefail

PLUGIN_PATH="wp-content/plugins/ev-news-automator"
TESTS_DIR="${PLUGIN_PATH}/tests"

SKIP_DOCS=false
STOP_ON_FAIL=true

for arg in "$@"; do
  case "$arg" in
    --skip-docs) SKIP_DOCS=true ;;
    --continue)  STOP_ON_FAIL=false ;;
  esac
done

# ── Helpers ───────────────────────────────────────────────────────────────────

BOLD="\033[1m"
GREEN="\033[0;32m"
RED="\033[0;31m"
YELLOW="\033[0;33m"
RESET="\033[0m"

pass() { echo -e "${GREEN}  ✓  $1${RESET}"; }
fail() { echo -e "${RED}  ✗  $1${RESET}"; }
info() { echo -e "${BOLD}$1${RESET}"; }
warn() { echo -e "${YELLOW}  ⚠  $1${RESET}"; }

RESULTS=()   # "PASS|label" or "FAIL|label" or "SKIP|label"

run_test() {
  local number="$1"
  local label="$2"
  local file="$3"
  local extra_args="${4:-}"

  echo ""
  info "════════════════════════════════════════════════════════════"
  info " Test ${number}: ${label}"
  info "════════════════════════════════════════════════════════════"

  if wp eval-file "${TESTS_DIR}/${file}" ${extra_args} 2>&1; then
    pass "Test ${number} passed"
    RESULTS+=("PASS|Test ${number}: ${label}")
  else
    fail "Test ${number} FAILED (exit code $?)"
    RESULTS+=("FAIL|Test ${number}: ${label}")
    if [ "$STOP_ON_FAIL" = true ]; then
      echo ""
      warn "Stopping suite. Run with --continue to run all tests regardless of failures."
      print_summary
      exit 1
    fi
  fi
}

print_summary() {
  echo ""
  info "════════════════════════════════════════════════════════════"
  info " Summary"
  info "════════════════════════════════════════════════════════════"

  local passed=0 failed=0 skipped=0

  for entry in "${RESULTS[@]}"; do
    local status="${entry%%|*}"
    local label="${entry##*|}"
    case "$status" in
      PASS) pass "$label"; (( passed++  )) ;;
      FAIL) fail "$label"; (( failed++  )) ;;
      SKIP) warn "SKIP  $label"; (( skipped++ )) ;;
    esac
  done

  echo ""
  echo -e "  Passed: ${GREEN}${passed}${RESET}  Failed: ${RED}${failed}${RESET}  Skipped: ${YELLOW}${skipped}${RESET}"
  echo ""

  [ "$failed" -eq 0 ]
}

# ── Suite ─────────────────────────────────────────────────────────────────────

echo ""
info "EV News Automator — Smoke Test Suite"
info "$(date '+%Y-%m-%d %H:%M:%S')"

# 1. Scraper + OpenRouter
run_test "01" "Scraper + OpenRouter (BG summaries)" \
  "test-01-scraper-openrouter.php"

# 2. Google Sheets
run_test "02" "Google Sheets (auth, read, write, cleanup)" \
  "test-02-google-sheets.php"

# 3. Google Docs + Drive
if [ "$SKIP_DOCS" = true ]; then
  warn "Test 03 skipped (--skip-docs). Note: test-03 appends to a real Google Doc."
  RESULTS+=("SKIP|Test 03: Google Docs + Drive")
else
  warn "Test 03 appends content to a real Google Doc. Review the output URL and clean up manually if needed."
  run_test "03" "Google Docs + Drive (create, append, move)" \
    "test-03-google-docs.php"
fi

# 4. Google Analytics
run_test "04" "Google Analytics GA4 (auth, ev_news_click events)" \
  "test-04-google-analytics.php"

print_summary
