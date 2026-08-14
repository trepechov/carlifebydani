#!/usr/bin/env python3
"""Resolve an EV News episode reference to a youtube-rag video_id.

STOPGAP. The youtube-rag MCP has no `resolve_episode` tool yet (requested in
docs/SEO_TRANSCRIPT_MCP_PROPOSALS.md § Requests to the producer side). Until it
ships, this reads Qdrant directly — bypassing the MCP layer for this one lookup
only. Real transcript search still goes through the MCP. Delete this script and
call the server tool instead once it exists.

Usage:
    python3 tools/resolve_episode.py "EV114"
    python3 tools/resolve_episode.py "41"                  # bare number
    python3 tools/resolve_episode.py "cybertruck bulgaria"  # title fuzzy match
    python3 tools/resolve_episode.py --refresh EV114        # force re-scroll
    python3 tools/resolve_episode.py --list                 # dump the whole index

Exit codes: 0 = resolved, 1 = not ingested / not found, 2 = Qdrant unreachable.

Matches BOTH title schemes the corpus uses — "EVN67" and "EV133" — so a plain
`EV\\d+` regex is not enough; see docs/SEO_TRANSCRIPT_MCP_PROPOSALS.md for why
that already dropped every EVN-era episode once.
"""
import json
import re
import sys
import time
import urllib.error
import urllib.request
from pathlib import Path

QDRANT_URL = "http://localhost:6333"
COLLECTION = "clbd"
CACHE_PATH = Path(__file__).resolve().parent.parent / "data" / "seo-cache" / "episodes-index.json"
CACHE_TTL_S = 6 * 3600  # ingestion happens weekly; a few hours' staleness is fine

EP_RE = re.compile(r"#?\s*EVN?\s*[-–]?\s*(\d+)\b", re.I)


def _scroll_index() -> dict:
    """One-time full scroll of video_id -> {title, published_at, n_chunks}."""
    videos: dict[str, dict] = {}
    offset = None
    while True:
        body = {
            "limit": 1000,
            "with_payload": ["video_id", "title", "published_at"],
            "with_vector": False,
        }
        if offset:
            body["offset"] = offset
        req = urllib.request.Request(
            f"{QDRANT_URL}/collections/{COLLECTION}/points/scroll",
            data=json.dumps(body).encode(),
            headers={"Content-Type": "application/json"},
        )
        with urllib.request.urlopen(req, timeout=15) as r:
            result = json.load(r)["result"]
        for p in result["points"]:
            pl = p["payload"]
            v = videos.setdefault(
                pl["video_id"],
                {"title": pl["title"], "published_at": pl.get("published_at", ""), "n_chunks": 0},
            )
            v["n_chunks"] += 1
        offset = result.get("next_page_offset")
        if not offset:
            break
    return videos


def load_index(refresh: bool = False) -> dict:
    if not refresh and CACHE_PATH.exists():
        age = time.time() - CACHE_PATH.stat().st_mtime
        if age < CACHE_TTL_S:
            return json.loads(CACHE_PATH.read_text())
    try:
        videos = _scroll_index()
    except (urllib.error.URLError, TimeoutError) as e:
        print(
            f"ERROR: cannot reach Qdrant at {QDRANT_URL} — is the youtube-rag-n8n "
            f"stack up? (`docker compose up -d` in ~/Projects/youtube-rag-n8n)\n{e}",
            file=sys.stderr,
        )
        sys.exit(2)
    CACHE_PATH.parent.mkdir(parents=True, exist_ok=True)
    CACHE_PATH.write_text(json.dumps(videos, ensure_ascii=False, indent=1))
    return videos


def episode_number(title: str) -> int | None:
    m = EP_RE.search(title)
    return int(m.group(1)) if m else None


def resolve(ref: str, index: dict) -> dict | None:
    # 1. bare or prefixed episode number, e.g. "114", "EV114", "EVN41", "#EV114"
    ref_stripped = ref.strip()
    ep_match = EP_RE.search(ref_stripped)
    n = int(ep_match.group(1)) if ep_match else (int(ref_stripped) if ref_stripped.isdigit() else None)
    if n is not None:
        for vid, d in index.items():
            if episode_number(d["title"]) == n:
                return {"video_id": vid, **d}
        return None
    # 2. fuzzy: fall back to a case-insensitive substring match on the title
    ref_low = ref.lower()
    hits = [(vid, d) for vid, d in index.items() if ref_low in d["title"].lower()]
    if len(hits) == 1:
        vid, d = hits[0]
        return {"video_id": vid, **d}
    if len(hits) > 1:
        print(f"AMBIGUOUS: {len(hits)} titles match {ref!r}:", file=sys.stderr)
        for vid, d in hits:
            print(f"  {vid}  {d['title']}", file=sys.stderr)
    return None


def main() -> None:
    args = sys.argv[1:]
    refresh = "--refresh" in args
    args = [a for a in args if a != "--refresh"]

    index = load_index(refresh=refresh)

    if "--list" in args:
        for vid, d in sorted(index.items(), key=lambda kv: kv[1]["published_at"]):
            print(f"{vid}\t{d['published_at'][:10]}\t{d['title']}")
        return

    if not args:
        print(__doc__, file=sys.stderr)
        sys.exit(2)

    ref = args[0]
    hit = resolve(ref, index)
    if hit is None:
        print(f"NOT_INGESTED: no episode matching {ref!r} in the {len(index)}-video corpus.")
        sys.exit(1)

    print(json.dumps({"video_id": hit["video_id"], "title": hit["title"],
                       "published_at": hit["published_at"], "n_chunks": hit["n_chunks"]},
                      ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
