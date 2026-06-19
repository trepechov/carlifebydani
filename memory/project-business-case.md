---
name: project-business-case
description: carlifebydani brand purpose, audience, content strategy, monetisation model, and the two active development workstreams (EV News Automation + On-Site SEO)
metadata:
  type: project
---

CarLife by Dani (CLBD) is a Bulgarian-language EV media brand. Primary product is the YouTube channel (weekly EV news podcast + interview series). The website (carlifebydani.com) is being developed from a passive archive into a self-sustaining tool.

**Why:** Bulgarian EV audience has no local-language, community-rooted EV media equivalent. The website is meant to be the durable, discoverable backbone that converts ephemeral social reach into an owned asset.

**How to apply:** All build-vs-defer decisions should be evaluated against `docs/BUSINESS_CASE.md`, specifically the 8-principle Decision Framework in §8.

Active workstreams:
1. EV News Automation — `plugins/ev-news-automator/`; requirements at `docs/brainstorms/2026-06-17-ev-news-automation-requirements.md`
2. On-Site SEO — `docs/SEO_PROPOSALS.md`; fixes in `theme/functions.php` and template-parts

Key category IDs: EV News=1, EV Reviews=3, EV Masters=45, News=6 (from `theme/constants.php`).

Monetisation levers (inferred): Patreon, events (Watts on the Grill, CLBD Coffee Day, CLBD Trip), sponsorships/affiliate, CLBD Parts. Actual revenue split unknown.

Related: [[project-youtube-mcp]]
