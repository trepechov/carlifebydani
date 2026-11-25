---
title: Подробен план — миграция на MCP сървърите към трислойна архитектура (funnelwise · webmaster · проект)
type: refactor
date: 2026-11-04
status: planned
---

# Подробен план: MCP → plugin слоеве

**Дата:** 2026-11-04 · **Клон:** `feature/on-site-seo` (планът), изпълнението — по отделни клонове/репота · **Статус:** planned

## 1. Резюме

Днес един и същ източник на данни (GA4, Search Console) се зарежда от два места, секретите са разпръснати в три хранилища (едното — в plaintext), YouTube връзката увисва 30 минути, а единствената защита срещу запис в WordPress и YouTube е текст в skill-ове. Целта е проста граница:

> **Плъгините отчитат (read-only, многократно използваеми). Проектът действа (единственият, който пише в сайта).**

| Слой | Притежава | Права |
|---|---|---|
| **funnelwise** (съществува, `~/Projects/funnelwise`) | GA4, YouTube, Instagram, Facebook, TikTok, Spotify; snapshots/diff; cross-channel отчети | само четене |
| **webmaster** (нов плъгин, `~/Projects/webmaster`) | Search Console, PageSpeed Insights, GTmetrix, DataForSEO; skill-ове `seo-performance-report`, `seo-keyphrase-research`; `seo_cache`, `keyword_tracking` | само четене от трети страни; пише само локални отчети/кеш в проекта |
| **проект** (`carlifebydani`) | WordPress MCP, youtube-rag, write skill-ове (`seo-article-apply`, `ev-news-transcript-content`, `seo-article-optimize`), `webmaster.yaml`, `reports/`, `data/` | единственият с право на запис в сайта, зад approval hook |

Миграцията е на 8 фази (P0–P7). Всяка фаза е обратима и се затваря само след като **parity harness** (§7) покаже, че новият път дава същите данни като стария.

---

## 2. Текущо състояние (одит 2026-11-04)

### 2.1 Регистрирани MCP сървъри

| Сървър | Къде е регистриран | Транспорт / команда | Секрет | Бъдещ собственик |
|---|---|---|---|---|
| `google-search-console` | local (`~/.claude.json`) | `.mcp-local/gsc-mcp.sh` (vendored FastMCP, `mcp<2`) | `.credentials/google-service-account.json` | webmaster |
| `psi` | local | `.mcp-local/psi-mcp.sh` → `psi_mcp.py` (URL hardcoded) | `.credentials/pagespeed-api-key` | webmaster |
| `gtmetrix` | local | remote HTTP `https://gtmetrix.com/mcp` | **Basic auth в plaintext в `~/.claude.json`** | webmaster |
| `dataforseo` | local | `npx -y dataforseo-mcp-server` (непиннат) | **парола в plaintext в `~/.claude.json`**; env е `DATAFORSEO_USERNAME`, а сървърът вече документира `DATAFORSEO_LOGIN` | webmaster |
| `ga4` | local | `pipx run --spec google-analytics-mcp ga4-mcp-server` | `.credentials/ga-service-account.json` + `GA4_PROPERTY_ID` | **funnelwise** (дубликат → отпада) |
| `wordpress` | local | `.mcp-local/wp-mcp.sh` → vendored `server-wp-mcp@1.0.1` | Keychain `carlifebydani-wp-mcp` (добре) | проект |
| `youtube-rag` | project `.mcp.json` (единственият commit-нат) | HTTP `localhost:8000` (Docker stack `~/Projects/youtube-rag-n8n`) | — | проект |
| `semrush` | user scope | remote OAuth | — | **отпада** (умишлено спрян 2026-11-04 заради цена; заменен от Chrome workaround) |
| `github` | user scope | npx | shell env | извън обхвата |
| funnelwise ×8 | plugin (user scope) | `python3 servers/*.py`, `thirdparty.py` → `youtube-studio-mcp==0.1.0`, ig-mcp, facebook-mcp | `~/.funnelwise/credentials/clbd/` | funnelwise |
| video2aeo | plugin (user scope) | `wp_push_draft` → WP REST | `~/.video2aeo/config.json` (**не съществува** → не е конфигуриран) | вж. D9 |

### 2.2 Проблеми, които планът решава

| # | Проблем | Доказателство | Тежест | Фаза |
|---|---|---|---|---|
| X1 | **YouTube auth увисва 30 мин и блокира целия сървър.** `run_local_server(timeout=None)` в sync tool на event loop-а на FastMCP; auth URL се печата в stdout (JSON-RPC канала); всеки read tool може тихо да стартира browser flow при неуспешен refresh (`except: pass`). | инцидент 2026-11-04 (сесия `12659fa0`), `youtube_mcp/auth.py` | висока | P0, P1 |
| X2 | **Вероятна причина за повтарящото се `authenticated:false`:** OAuth consent screen в режим „Testing“ — refresh token изтича след 7 дни. | история 2026-07 → 2026-11 | висока | P0 |
| X3 | YouTube токенът има **write scopes** (`youtube`, `youtube.upload`), сървърът излага 10 write tools; read-only се пази само от name-heuristic hook; `youtube_reporting_create_job` минава през guard-а. Analytics е hardcoded `channel==MINE`. | `write_guard.py`, `analytics.py:35` | висока | P1 |
| X4 | **Дублирани GA4 и GSC** (проект + funnelwise), с различни credential копия. | §2.1 | средна | P3, P4 |
| X5 | **Plaintext секрети** (GTmetrix, DataForSEO) в `~/.claude.json`. | inventory | висока | P3 |
| X6 | **Три credential хранилища** (`.credentials/`, `~/.funnelwise/credentials/clbd/`, `~/.claude.json`) + Keychain. | inventory | средна | P3, P7 |
| X7 | Конфигурацията не е възпроизводима: 6 от 7 проектни сървъра са само в `~/.claude.json`. | §2.1 | средна | P6 |
| X8 | **Няма технически guard за WordPress запис**: проектът е в `bypassPermissions` + `Bash(*)`; единствено `_shared/approval-gate.md`. Вторият WP писател (video2aeo) също зависи само от gate в `episode.json`. | settings.json | висока | P6 |
| X9 | funnelwise `write_guard` (regex `analytics|ga4|search.?console|gsc|semrush`) **прихваща и чужди сървъри** (проектните ga4/gsc) и ги scope-ва по brand.yaml; изпълнява `python3` при всяко `mcp__*` извикване във всяка сесия. | funnelwise report §5, §8.6 | средна | P1 |
| X10 | Процесен товар: ~8 funnelwise процеса на сесия, включително за несвързани платформи (Spotify) и в чужди проекти; ~8 YouTube инстанции наведнъж. | `ps`, 2026-11-04 | ниска | P1 |
| X11 | Meta credentials са в `meta.json`, launcher-ът търси `instagram.json`/`facebook.json` → IG/FB работят като stub. TikTok — само `.pending`. | `fw_connection_status` | средна | P1 |
| X12 | `fw_connection_status` проверява само съществуване на файл, не валидност/изтичане. | core.py | ниска | P1 |
| X13 | Skill-овете съдържат hardcoded факти за сайта (`_shared/constants.md:31-60`, perf-report, research) → не могат да се преместят в плъгин без конфиг. | inventory §4 | блокер за P5 | P5 |
| X14 | `seo-keyphrase-research` (read skill) чете WordPress с auth (`context=edit` за Yoast + `news_csv`, tags, search) → плъгинът не бива да има WP достъп. | inventory §7 | блокер за P5 | P5 |
| X15 | Остаряла документация: `MCP_SERVERS.md` (Yoast „не е writable“, грешно място на Wordfence toggle, Semrush активен, „2 skill-а“), `constants.md:40` (DataForSEO „blocked“), `wp-mcp.sh` header „PLACEHOLDER“. | history report | ниска | P7 |
| X16 | Нулеви тестове за MCP слоя в проекта (само `tools/test_keyword_tracking.py`). | inventory §8 | висока за миграцията | P0 |
| X17 | youtube-rag Docker stack е спрян (ECONNREFUSED към 17:40). | сесия днес | baseline условие | P0 |

---

## 3. Изисквания

- **R1** Всеки източник на данни има **точно един собственик** и се зарежда от точно едно място.
- **R2** Плъгините нямат write достъп до сайта и до платформите — гарантирано **в кода на сървъра** (няма write tools / read-only OAuth scopes), не само с hook или prompt.
- **R3** Плъгините не съдържат факти за конкретен сайт/бранд; всичко идва от валидиран конфиг (`brand.yaml` / `webmaster.yaml`).
- **R4** Секретите живеят извън репото и извън `~/.claude.json`, в per-brand/per-site папка с mode 600 или в Keychain.
- **R5** Всеки WordPress запис от проекта минава през технически approval guard (hook), независимо от `bypassPermissions`.
- **R6** **Без деградация:** за всеки преместен tool parity harness-ът доказва еднакви данни преди старият път да бъде изключен; всеки skill се пуска end-to-end върху вече известен случай.
- **R7** OAuth потоците никога не блокират сървъра безкрайно: timeout, URL се връща на потребителя, read tools никога не стартират auth flow сами.
- **R8** Възпроизводимост: нова машина/клон → документирани стъпки + `connection-check` и за двата плъгина дават зелено.
- **R9** Цената е под контрол: DataForSEO минава през кеша и allowlist на endpoint-и с лимит на заявки; GTmetrix ≤ 1 тест на snapshot.

---

## 4. Ключови решения

| # | Решение | Защо | Алтернатива (отхвърлена) |
|---|---|---|---|
| D1 | Три слоя по „какъв въпрос отговаря“: funnelwise = аудитория/канали; webmaster = здраве/търсене на сайта; проект = действие. | Решено с потребителя 2026-11-04. | Един мега-плъгин; плъгин на MCP. |
| D2 | **WordPress остава в проекта.** webmaster няма WP конектор, нито read, нито write. | Решено с потребителя: webmaster дава данни от трети страни, не управлява съдържание. | WP read в плъгина (X14 щеше да остане). |
| D3 | **GSC собственик = webmaster.** Сървърът се пренася от funnelwise (`servers/search_console.py` + `gsc_audit/`), заедно със skill-а `seo-opportunities`. funnelwise 0.2.0 махa search-console; cross-channel отчетите четат GSC през webmaster snapshots. | R1. GSC е page/query диагностика, не social канал. | Двата плъгина с GSC (дублирането остава). **→ потвърди, Q1.** |
| D4 | **GA4 собственик = funnelwise.** Проектният `ga4` отпада; `seo-keyphrase-research` ползва `ga_run_report` и декларира зависимост от funnelwise. | GA4 е cross-channel. funnelwise сървърът вече е scoped и first-party. | GA4 в webmaster. |
| D5 | webmaster копира архитектурата на funnelwise: stdlib Python, `lib/mcp_stdio.py` (`readOnlyHint` на всичко), scope от конфиг, enforced в сървъра, stub сървър с `*_connection_help`, `dev/smoke.py`, pytest с изолиран HOME. `lib/` се **копира** (vendored, с version stamp), не се споделя като пакет. | Доказан модел, без install стъпка. Два плъгина не оправдават общ пакет. | Общ pip пакет (излишна сложност засега). |
| D6 | Конфиг на webmaster = **`webmaster.yaml` в корена на проекта** (commit-нат, без секрети), намиран чрез `CLAUDE_PROJECT_DIR` → walk up → `~/.webmaster/active-site`. Credentials: `~/.webmaster/credentials/<site>/`. | Изходите (reports/, data/seo-cache/) живеят в проекта; пътищата стават конфиг. | Разширяване на funnelwise `brand.yaml` (schema-strict, живее на Desktop, друга грижа). |
| D7 | **GTmetrix и DataForSEO → first-party allowlist wrapper-и** (stdlib, REST), не third-party MCP. GTmetrix: само `account_status`, `start_test`, `get_test`, `get_report`, `get_report_history`. DataForSEO: allowlist на използваните endpoint-и (search_volume, keyword_suggestions, serp organic live), интегриран с `seo_cache` (cache-first), лимит на заявки за извикване. | R2, R4, R9. Махат се write-like tools (`delete_page_tag`, `set_page_monitoring`) и generic `api_request`. | Third-party сървъри през launcher (по-малко работа, но без контрол на цената/обхвата) — **приемливо като междинна стъпка, вж. P3**. |
| D8 | **Semrush се маха** от user scope и от `allow` в `.claude/settings.json`. Rank tracker остава през Chrome workaround в проекта. `semrush` слотът в схемата остава празен. | Решение от 2026-11-04 (цена). | — |
| D9 | **video2aeo `wp_push_draft`:** остава неконфигуриран, докато не потрябва; ако се конфигурира, използва същия `seo-bot` и минава през проектния WP guard (hook matcher-ът включва `mcp__plugin_video2aeo_video2aeo__wp_push_draft`). | Вторият писател не бива да заобикаля R5. | — |
| D10 | **YouTube: собствен first-party read-only сървър във funnelwise** (stdlib), със scopes само `youtube.readonly`, `yt-analytics.readonly`, `yt-analytics-monetary.readonly`; analytics с `ids=channel==<channel_id от brand.yaml>`; auth по модела на `tiktok_login_start` (фонова нишка, фиксиран порт, 600 s timeout, връща URL, ръчен paste fallback). `youtube-studio-mcp` се изважда. | X1, X3, R2, R7. Third-party пакетът не може да се поправи отвън. | Fork + patch на youtube-studio-mcp (поддръжка на чужд код); да остане както е (X1/X3 остават). |
| D11 | Пренасяне на сървър = **dual-run**: новият се инсталира до стария → parity → skill-овете се пренасочват → старият се **disable-ва** (не се трие) → трие се след една успешна месечна отчетна итерация. | R6, лесен rollback. | Big-bang смяна. |

---

## 5. Целева архитектура

```
~/Projects/funnelwise  (plugin, user scope)          ~/Projects/webmaster  (plugin, user scope, NEW)
  servers/ core, ga4, youtube(first-party), ig, fb,    servers/ core(wm_*), search_console, psi,
           tiktok, spotify                                      gtmetrix, dataforseo
  config:  <brand>/brand.yaml  (Desktop/CLBD Marketing)  config:  <project>/webmaster.yaml
  creds:   ~/.funnelwise/credentials/clbd/              creds:   ~/.webmaster/credentials/carlifebydani/
  skills:  connection-check, onboarding,                skills:  connection-check, seo-performance-report,
           content-performance, media-kit, start                 seo-keyphrase-research, seo-opportunities
  hooks:   SessionStart (brand ctx), write_guard         hooks:   SessionStart (site ctx), guard (own servers only)
           (САМО funnelwise сървъри)

/Users/dimitar/Projects/carlifebydani  (project)
  .mcp.json        wordpress (tools/mcp/wp-mcp.sh, Keychain), youtube-rag
  webmaster.yaml   site facts + пътища + бюджети (без секрети)
  .claude/skills   seo-article-optimize (orchestrator), seo-article-apply, ev-news-transcript-content, _shared/
  .claude/hooks    wp_write_guard.py  (PreToolUse: WP/video2aeo запис → изисква approval)
  tools/           resolve_episode.py, mcp_parity.py, skill_refs_lint.py
  reports/, data/seo-cache/
```

**Поток на `seo-article-optimize` след миграцията:**

1. **Phase 0 (проект, нова):** WP GET (`context=edit`) → записва `reports/seo-metatags/<slug>/post-context.json` (id, slug, title, category, current Yoast meta, `news_csv`, tags, кандидати за вътрешни линкове).
2. **Phase A (webmaster skill `seo-keyphrase-research`):** чете `post-context.json` + GSC/DataForSEO/autocomplete/GA4 (funnelwise) → отчет със `Status: researched`. Без WP.
3. **Phase B (проект):** `ev-news-transcript-content` (youtube-rag + WP POST зад guard).
4. **Phase C (проект):** `seo-article-apply` (WP POST зад guard).

**Примерен `webmaster.yaml`:**

```yaml
site:
  name: Car Life by Dani
  short: carlifebydani
  url: https://carlifebydani.com/            # PSI/GTmetrix (apex)
  gsc_property: https://www.carlifebydani.com/   # ВНИМАНИЕ: www URL-prefix; bare форма → 403
  locale: bg-BG
  market: { hl: bg, gl: bg, dataforseo_location: Bulgaria, dataforseo_language: bg }
paths:
  reports_dir: reports/seo-performance
  optimizations_ledger: reports/seo-optimizations/ledger.csv
  optimizations_checks: reports/seo-optimizations/checks.csv
  tracked_keywords: reports/seo-performance/tracked-keywords.csv
  metatags_reports: reports/seo-metatags
  seo_cache: data/seo-cache
budgets:
  gtmetrix_tests_per_run: 1
  dataforseo_requests_per_run: 20
  dataforseo_cache_ttl_days: 30
depends_on:
  ga4: funnelwise          # GA4 се чете от funnelwise ga_run_report
```

---

## 6. Мапинг на tool имената (skill-овете трябва да се обновят)

Plugin tools се именуват `mcp__plugin_<plugin>_<server>__<tool>`. ~25 референции в `.claude/skills/**` + `allow` правилата.

| Старо | Ново | Бележка |
|---|---|---|
| `mcp__psi__psi_lighthouse` | `mcp__plugin_webmaster_psi__psi_lighthouse` | URL default от `webmaster.yaml` |
| `mcp__google-search-console__gsc_*` | `mcp__plugin_webmaster_search-console__gsc_*` | `gsc_sites` → `gsc_site` (scoped) |
| `mcp__plugin_funnelwise_search-console__*` | `mcp__plugin_webmaster_search-console__*` | след funnelwise 0.2.0 |
| `mcp__gtmetrix__gtmetrix_{get_account_status,start_test,get_test,get_report}` | `mcp__plugin_webmaster_gtmetrix__gtmetrix_*` | само allowlist |
| `mcp__dataforseo__api_request` / `docs_search` | `mcp__plugin_webmaster_dataforseo__{search_volume,keyword_suggestions,serp_organic}` | generic `api_request` изчезва |
| `mcp__ga4__get_ga4_data` | `mcp__plugin_funnelwise_ga4__ga_run_report` | параметрите се различават, вж. U4.2 |
| `mcp__semrush__*` | — | изтрива се |
| `mcp__wordpress__*` | `mcp__wordpress__*` | без промяна (проект `.mcp.json`) |
| `mcp__youtube-rag__*` | без промяна | |
| `mcp__plugin_funnelwise_youtube__youtube_*` | `mcp__plugin_funnelwise_youtube__yt_*` (нов сървър) | само read tools |

---

## 7. Метод за верификация (без деградация)

Това е гръбнакът на плана. Всеки инструмент се проверява на четири нива; фаза не се затваря без зелено на всички.

### 7.1 Ниво 1 — Contract (статично, при всеки commit)

- **`tools/list` snapshot:** за всеки сървър се commit-ва `tests/contracts/<server>.tools.json` (имена, входна схема, `readOnlyHint`). Тест: живият `tools/list` == snapshot-а; промяна изисква съзнателен update.
- **Read-only инвариант:** във webmaster и funnelwise нито един tool няма име, съвпадащо с `(delete|upload|post|reply|update|create|set|remove|add)_`, освен изрично whitelisted (`gtmetrix_start_test`). Тестът е в двата плъгина.
- **`tools/skill_refs_lint.py`** (проект): обхожда `.claude/skills/**` и плъгин skill-овете, вади всички `mcp__…` референции и проверява, че всяка съществува в contract snapshot-ите на целевия слой. Хваща забравени стари имена след преименуване. *Това е най-евтината защита срещу тиха деградация на skill.*
- **Без site факти в плъгина:** grep тест за `carlifebydani`, `427729375`, `UCtkI6…` в кода на плъгина (funnelwise вече го има → копира се).
- **Без секрети:** grep тест в двата плъгина и в проекта (ключове от `.credentials/`, `Authorization: Basic`, pattern-и за app password).

### 7.2 Ниво 2 — Scope и guard тестове (unit, без мрежа)

- webmaster отказва чужд `site_url`/URL извън домейна от `webmaster.yaml` **преди** мрежово извикване (по модела на funnelwise тестовете).
- DataForSEO wrapper: cache hit → 0 мрежови заявки; заявка над `dataforseo_requests_per_run` → отказ; endpoint извън allowlist → няма такъв tool.
- funnelwise `write_guard`: не прихваща сървъри извън funnelwise (регресионен тест за X9); YouTube write tools ги няма (нов сървър).
- Проектен `wp_write_guard`: `wp_call_endpoint` с `method != GET` без approval маркер → `deny`; с валиден маркер (report файл със `Approved: yes` + съвпадащ post id, ≤ 60 min) → `allow`; GET → винаги `allow`. Същото за `wp_push_draft`.

### 7.3 Ниво 3 — Parity harness (live, при всяко пренасяне на сървър)

Нов `tools/mcp_parity.py`: говори MCP stdio JSON-RPC директно (по модела на funnelwise `dev/smoke.py`), стартира **стария** и **новия** сървър, извиква една и съща фиксирана заявка, нормализира и сравнява.

| Сървър | Фиксирана probe заявка | Критерий за равенство |
|---|---|---|
| GSC | `performance_overview` + `query` (dims query,page; row_limit 250) за **2026-09-01…2026-09-30** (финални данни) | clicks/impressions **точно** равни; position/ctr ±0.01; редовете — същото множество |
| GSC | `inspect_url` за `/ev-news-feed/` и 2 EV News поста | coverage/indexing state равни |
| GSC | `sitemaps` | същият списък |
| GA4 | sessions, users, engagedSessions по `sessionDefaultChannelGroup` за **2026-09** | точно равни |
| GA4 | top 20 `pagePath` по views за 2026-09 | същият ред и стойности |
| PSI | `lighthouse` mobile на homepage | и двата успешни; същите ключове; scores в диапазон ±10 (PSI е шумен — сравнява се форма, не стойност) |
| GTmetrix | `get_report` на **съществуващ** report id от последния snapshot (0 кредита) + `account_status` | същите полета/стойности; `start_test` — само веднъж, в Ниво 4 |
| DataForSEO | search_volume за 5 ключови думи, които **вече са в `data/seo-cache/keywords.csv`** | новият сървър връща кеш стойността без мрежа; един контролиран live call (1 ключова дума) == кеша ±0 |
| YouTube | channel stats, top 10 videos (views, watch time) за 2026-09, по `channel_id` | новият first-party == стария (докато е жив) или == YouTube Studio export |
| WordPress | GET `posts/8851?context=edit&_fields=…` | непроменен (сървърът не се мести, само launcher-ът) |

- **Baseline (P0):** всички probe-ове се пускат срещу **текущия** стек и резултатите се записват в `tests/baseline/2026-11-04/*.json` (raw gitignored, нормализирани хешове + обобщение commit-нати). Така имаме „златен стандарт“ дори след като старият сървър бъде изтрит.
- Изход: `PASS/FAIL` таблица + diff; пуска се с `python3 tools/mcp_parity.py --server gsc --old <cmd> --new <cmd>` или `--against-baseline`.

### 7.4 Ниво 4 — End-to-end skill parity (по веднъж при всяка фаза, засягаща skill)

- **`seo-performance-report`:** пуска се за **вече отчетен** период (последният месечен snapshot в `reports/seo-performance/`) в dry-run режим (пише в `reports/seo-performance/_parity/`). Сравнява се: GSC обобщенията (точно), секциите на отчета (същите), `history.csv` редът (същите колони), ledger verify логиката (същите due елементи). GTmetrix: 1 реален тест.
- **`seo-keyphrase-research`:** пуска се на **EV169** (последно оптимизиран пост, commit `57f80d6`) с `post-context.json` от Phase 0; сравнява се с вече commit-нат отчет: същата focus keyphrase, същите обеми (от кеша → 0 разход), същите кандидат-тагове.
- **`seo-article-optimize`:** пълен цикъл на един **draft/тестов пост** (или dry-run на Phase C без POST): guard-ът трябва да поиска approval; след approval POST минава; Yoast backup файлът се създава в `reports/yoast-meta-backup/`.
- **`connection-check`** на funnelwise и на webmaster: всички свързани платформи ✅.

### 7.5 Smoke при старт на сесия (постоянно, след миграцията)

- Всеки плъгин има `dev/smoke.py --live` (handshake + 1 евтин read на платформа). Проектът получава `/mcp-check` команда, която пуска двата smoke-а + WP GET + youtube-rag `list_collections` и печата една таблица. Препоръка: пускай преди всеки голям отчет.

### 7.6 Rollback

- Докато трае dual-run (D11), старата регистрация е само `disabled` в `/mcp`; rollback = enable стария + `git revert` на skill-реф commit-а.
- След изтриване: baseline-ът от 7.3 позволява да се докаже деградация и да се върне сървърът от git историята (`.mcp-local` е gitignored → **преди изтриване се архивира** в `~/Archive/carlifebydani-mcp-local-2026-11.tar.gz`).

---

## 8. Фази и изпълнителни единици

> Ред: от най-нисък към най-висок риск. Всяка единица има **Цел**, **Изисквания**, **Файлове**, **Проверка**.

### P0 — Стабилизация и baseline (без архитектурни промени)

**U0.1 Поправи YouTube сега.**
- Цел: работещ funnelwise YouTube за текущата работа.
- Стъпки: `/mcp` → Reconnect `plugin:funnelwise:youtube` → `youtube_auth_status` → `youtube_get_channel(channel_id=UCtkI6fUo3jwWbeMOrdhc02g)`.
- **Google Cloud Console → OAuth consent screen → Publish app („In production“)**, за да спре 7-дневното изтичане на refresh token (X2). За непотвърдено приложение с <100 потребители е достатъчно; ще има предупредителен екран при sign-in.
- Ако пак е `false`: изтрий `token.json`, вход **като собственик на Brand Account** в правилния Chrome профил (историческа грешка 2026-07: токен от @nighteaglebg).
- Проверка: connection-check YouTube ✅; след 8 дни — пак ✅ без re-auth.

**U0.2 Вдигни youtube-rag** (`~/Projects/youtube-rag-n8n` Docker stack) и потвърди `list_collections` → `clbd` 160 видеа. (X17)

**U0.3 Разчисти процесите:** спри осиротелите `youtube-studio-mcp` инстанции от мъртви сесии (по parent PID, който вече не съществува). Добави в `docs/` one-liner за проверка.

**U0.4 Semrush cleanup (D8):** `claude mcp remove semrush -s user`; махни `mcp__semrush__*` от `.claude/settings.json`; в `seo-keyphrase-research` смени Semrush стъпката (ред ~287) с бележка „Chrome workaround / пропусни“.

**U0.5 Parity harness + baseline (X16).** Напиши `tools/mcp_parity.py`, `tools/skill_refs_lint.py`; пусни всички probe-ове от §7.3 срещу текущия стек → `tests/baseline/2026-11-04/`. Добави `tests/baseline/README.md` (как се пуска, критерии).
- Проверка: lint-ът минава срещу текущите имена; baseline файловете съществуват за GSC, GA4 (и двата сървъра), PSI, GTmetrix, DataForSEO, YouTube, WP.

**U0.6 Архивирай** `.mcp-local/` и `.credentials/` (tar, извън репото, mode 600) — точка за възстановяване.

### P1 — Втвърдяване на funnelwise (репо `~/Projects/funnelwise`, версия 0.2.0)

**U1.1 First-party read-only YouTube сървър (D10, X1, X3, R2, R7).**
- `servers/youtube.py` (stdlib): Data API v3 (`channels`, `videos`, `playlistItems`, `search` за собствения канал, `commentThreads` read), Analytics API (`reports.query` с `ids=channel==<channel_id>`; daily, top videos, traffic sources, geography, demographics, retention, content type, revenue).
- Scopes: само `youtube.readonly`, `yt-analytics.readonly`, `yt-analytics-monetary.readonly` → **нов token файл** (`youtube_token.json`), старият с write scopes се изтрива и grant-ът се отменя в myaccount.google.com/permissions.
- Auth: `youtube_login_start` / `youtube_login_status` / `youtube_login_finish` (копие на TikTok модела: фонова нишка, фиксиран порт, 600 s timeout, URL в отговора, paste fallback). Read tools при невалиден токен **връщат грешка с инструкция**, никога не отварят браузър. Refresh грешките се логват, не се поглъщат.
- Махни `youtube` от `thirdparty.py`.
- Проверка: contract test (няма write tools); parity с baseline YouTube probe; test „expired token → ясна грешка, без блокиране“ (< 2 s).

**U1.2 `write_guard` само за собствените сървъри (X9).** Matcher `mcp__plugin_funnelwise_.*`; добави `reporting_create_job` към забранените (докато старият YouTube съществува); регресионен тест, че `mcp__google-search-console__*`, `mcp__plugin_webmaster_*`, `mcp__wordpress__*` не се прихващат.

**U1.3 Meta credentials (X11):** `fw_import_credential kind=instagram|facebook` да приема/разделя `meta.json` или launcher-ът да чете `meta.json` с ключове по платформа. Проверка: IG и FB вече не са stub; connection-check ✅. Довърши TikTok login (`tiktok_login_start`).

**U1.4 Лениви конектори (X10):** сървър за платформа без блок в `brand.yaml` (напр. Spotify) стартира като минимален stub без външни зависимости и без мрежа; `thirdparty.py` не стартира `uv` за несвързани платформи.

**U1.5 `fw_connection_status` с валидност (X12):** за OAuth токени — `expiry`, наличие на `refresh_token`, scopes; без мрежа. Отделна `--live` проверка остава в connection-check skill-а.

**U1.6 `gsc_audit` език:** параметър `language` (по brand), default от brand.yaml. *(Ако D3 се приеме, тази работа се прави директно във webmaster.)*

**U1.7 Release 0.2.0:** CHANGELOG, bump в `plugin.json` + `marketplace.json`, `pytest`, `dev/smoke.py --live`, update на marketplace-а, рестарт на сесията.
- Проверка за P1: funnelwise connection-check — GA4, GSC, YouTube, IG, FB, TikTok ✅ (Spotify — документиран Premium блокер).

### P2 — Скелет на webmaster плъгина (репо `~/Projects/webmaster`)

**U2.1 Репо и marketplace:** `.claude-plugin/plugin.json`, `.claude-plugin/marketplace.json` (`source: ./`), `.mcp.json` (stdio сървъри през `${CLAUDE_PLUGIN_ROOT}`), `CLAUDE.md` (правила: stdlib, read-only, без site факти, без секрети), `pyproject.toml` (pytest), `tests/conftest.py` (изолиран `WEBMASTER_HOME`, cwd, env).

**U2.2 `lib/`:** копие на funnelwise `mcp_stdio.py`, `google_auth.py`, `http.py`, `credentials.py`, `workspace.py` с `# vendored from funnelwise@<commit>` header; `lib/site_config.py` — зареждане и валидиране на `webmaster.yaml` по `schemas/webmaster.schema.json` (unknown keys → грешка, като brand.yaml).

**U2.3 `servers/core.py`:** `wm_site` (резюме на конфига), `wm_connection_status` (файлове + валидност), `wm_save_snapshot`/`wm_diff_snapshot` (същият контракт като funnelwise, в `paths.reports_dir`), `wm_init_site` (създава `webmaster.yaml` от template).

**U2.4 Hooks:** SessionStart (site контекст: URL, GSC property, бюджети, правило „cache first“); PreToolUse guard **само** за `mcp__plugin_webmaster_.*` (бюджет на GTmetrix тестове за сесия).

**U2.5 Първи сървър — PSI** (stateless, най-нисък риск): порт на `.mcp-local/psi_mcp.py` към stdlib, URL default от `webmaster.yaml`, отказ за URL извън домейна, ключ от `~/.webmaster/credentials/carlifebydani/pagespeed-api-key`.

**U2.6 `webmaster.yaml`** в проекта (commit) + credentials папка (копие на PSI ключа).
- Проверка: pytest зелен; contract snapshot; parity PSI (стар `mcp__psi__` vs нов); skill_refs_lint с новото име. Dual-run: старият `psi` → disabled.

### P3 — Пренасяне на останалите read сървъри (по един, dual-run)

**U3.1 Search Console** (зависи от Q1/D3): порт на funnelwise `search_console.py` + `gsc_audit/` (с `language`). SA ключът → `~/.webmaster/credentials/carlifebydani/google-service-account.json`. Запази: www property, 250-row cap бележка, single-URL съвета за директен API с page филтър (constants trap).
- Parity: всички GSC probe-ове от §7.3 — **точно равенство** срещу проектния сървър и срещу funnelwise. После: проектният `google-search-console` → disabled; funnelwise 0.2.x маха `search-console` + `seo-opportunities` (премества се тук).

**U3.2 GTmetrix (D7):** first-party wrapper над GTmetrix REST v2.0 (allowlist). Ключът → credentials папка. Parity с `get_report` на съществуващ report id. После: `claude mcp remove gtmetrix` (local) → **ротирай GTmetrix API ключа** (бил е plaintext в `~/.claude.json`, X5).

**U3.3 DataForSEO (D7, R9):**
- *Стъпка 1 (бърза):* launcher в плъгина, който стартира pinned `dataforseo-mcp-server@<версия>` с creds от credentials папка (правилното env име — провери `DATAFORSEO_LOGIN` vs `_USERNAME`, inventory §6f). Махни plaintext от `~/.claude.json`, **ротирай паролата**.
- *Стъпка 2:* first-party wrapper с allowlist + `seo_cache` cache-first + бюджет; премахва third-party сървъра.
- Parity: 5 кеширани ключови думи → 0 заявки; 1 live заявка == кеша. Probe-ът е реален data endpoint, **никога `user_data`** (историческа клопка 40104).

**U3.4 Credentials консолидация (X5, X6):** след U3.1–3.3 `~/.claude.json` няма секрети (`grep -c` тест); `.credentials/` в проекта остава само с това, което ползват проектни инструменти (на практика нищо — WP е в Keychain).

### P4 — GA4 dedupe (D4)

**U4.1** Проектният `ga4` → disabled.
**U4.2** `seo-keyphrase-research` ред ~316: `get_ga4_data` → funnelwise `ga_run_report` (имената на dimensions/metrics се проверяват с `ga_get_metadata`; schema-search функцията на стария сървър се губи — приемливо). Parity: GA4 probe-овете от §7.3 — двата сървъра са били в baseline-а → точно равенство.
**U4.3** След една месечна итерация: `claude mcp remove ga4`, изтрий `.credentials/ga-service-account.json` (дубликат).

### P5 — Пренасяне на analysis skill-овете във webmaster

**U5.1 Разделяне на `_shared/constants.md`:** site факти → `webmaster.yaml`; WP-специфични (alias, seo-bot id 28, Yoast meta keys, title suffix 19 chars, category IDs, Keychain име, Yoast writability, Wordfence бележка) → остават в проекта `_shared/constants.md`. Поправи DataForSEO статуса (X15).

**U5.2 Phase 0 в проекта (X14):** нова стъпка в `seo-article-optimize`: WP GET → `post-context.json` (схема в `webmaster/schemas/post-context.schema.json`, за да е договор между слоевете).

**U5.3 `seo-keyphrase-research` → webmaster:** чете `post-context.json` вместо WP; всички пътища от `webmaster.yaml`; tool имена по §6; `tools/seo_cache.py` → `webmaster/servers/lib/seo_cache.py` + CLI (`python3 ${CLAUDE_PLUGIN_ROOT}/bin/seo_cache …`), data dir от конфига.

**U5.4 `seo-performance-report` → webmaster:** параметризиран; `tools/keyword_tracking.py` + `test_keyword_tracking.py` → плъгина; запис в ledger/checks по пътищата от конфига; премахни директните curl fallback-и с ключове в skill текста (минават през сървърите).

**U5.5 Проектът вика плъгин skill-овете:** `seo-article-optimize` извиква `webmaster:seo-keyphrase-research` за Phase A; изтрий старите копия от `.claude/skills/`.
- Проверка: Ниво 4 (§7.4) за perf-report и за EV169; skill_refs_lint зелен; grep тест „без site факти“ в плъгина.

### P6 — Втвърдяване на проектния WordPress слой

**U6.1 Възпроизводима регистрация (X7):** преместване на `wp-mcp.sh` → `tools/mcp/wp-mcp.sh` (tracked, без секрети; оправи „PLACEHOLDER“ header-а); `tools/mcp/install-wp-mcp.sh` (pinned `server-wp-mcp@1.0.1`, checksum, в gitignored `.vendor/`); запис в commit-нат `.mcp.json`; `claude mcp remove wordpress -s local`.

**U6.2 `wp_write_guard` hook (R5, X8, D9):** `.claude/hooks/wp_write_guard.py` в `.claude/settings.json` (PreToolUse, matcher `mcp__wordpress__wp_call_endpoint|mcp__plugin_video2aeo_video2aeo__wp_push_draft`). Логика от §7.2. Approval маркерът се пише от `_shared/approval-gate.md` процедурата (вече съществува — само се добавя machine-readable ред). **Да се провери**, че hook `deny` се спазва и в `bypassPermissions` (очаквано да — hooks се изпълняват преди permission режима; тест в U6.3).

**U6.3 Тестове:** unit тестове на hook-а (stdin JSON → решение); live тест: POST без маркер → отказан; с маркер към draft пост → минава.

**U6.4 Permissions:** `.claude/settings.json` — махни `mcp__semrush__*`; помисли дали `bypassPermissions` е нужен (Q4).

### P7 — Почистване и документация

**U7.1** Изтрий `.mcp-local/gsc*`, `psi*` (архивирани в U0.6); `.credentials/` legacy (`gsc_token.json`, `token.json`, `client_secret.json`).
**U7.2** Пренапиши `docs/MCP_SERVERS.md` → кратък `docs/ARCHITECTURE_MCP.md`: трите слоя, кой какво притежава, къде са секретите, как се пуска `/mcp-check`, възстановяване на нова машина (R8). Поправи остарелите твърдения (X15).
**U7.3** Обнови memory: `project-wp-mcp-setup.md`, `project-seo-perf-monitoring.md`, `project-seo-skills-refactor.md`; нов запис за архитектурата.
**U7.4** CLBD-Marketing (`~/Downloads/CLBD-Marketing`): T7 cutover във funnelwise плана; DEPRECATED бележка в README; `.credentials/` там → архив и изтриване след като funnelwise е зелен.
**U7.5** Финална проверка: всичките 4 нива от §7, двата `connection-check`, `/mcp-check`, една реална месечна `seo-performance-report` → едва след нея старите disabled регистрации се трият (D11).

---

## 9. Must not break — чеклист

- [ ] GSC property е **`https://www.carlifebydani.com/`** (www URL-prefix); PSI/GTmetrix — apex.
- [ ] `mcp<2` pin за всеки FastMCP-базиран сървър, който остава (до пълен преход към stdlib).
- [ ] GA4 property `427729375` е pinned (Admin API е изключен в GCP проект `1042858115309` → discovery не работи); Data API `:runReport`.
- [ ] YouTube: винаги `channel_id`, никога `mine:true`; вход като Brand Account собственик.
- [ ] WP: Wordfence app-password toggle (Firewall → Brute Force); curl, не urllib (WAF 403); Yoast post meta е REST-writable с auth; taxonomy SEO не е; backup на postmeta в `reports/yoast-meta-backup/` преди запис; seo-bot = Editor (id 28); Keychain `carlifebydani-wp-mcp`.
- [ ] DataForSEO: cache first (`data/seo-cache/`); probe с data endpoint, не `user_data`; никога ръчно въведени обеми.
- [ ] GTmetrix: ≤ 1 тест на snapshot (5 кредита/ден).
- [ ] След регистрация/промяна на сървър → **рестарт на сесията** (✔ в `claude mcp list` не зарежда tools).
- [ ] funnelwise `brand.yaml` е schema-strict — неизвестен ключ чупи всички `fw_*`.
- [ ] Отчетите на funnelwise — на `bg`; snapshots със `fw_save_snapshot`.
- [ ] EV News правила: tag cap = owned-prose vs cards; `news_csv` ред = редакционен ранг; не публикувай суров ASR.
- [ ] `reports/seo-optimizations/ledger.csv` verify_due цикълът продължава без пропуск на месец.

## 10. Rollout и последователност

| Седмица | Фази | Изход |
|---|---|---|
| 1 | P0 | YouTube стабилен, baseline + harness commit-нати, Semrush махнат |
| 1–2 | P1 | funnelwise 0.2.0 (read-only YouTube, guard fix, IG/FB/TikTok) |
| 2 | P2 | webmaster 0.1.0 с PSI, dual-run |
| 3 | P3, P4 | GSC/GTmetrix/DataForSEO във webmaster; GA4 dedupe; ротирани ключове |
| 3–4 | P5 | analysis skill-ове във webmaster, Phase 0 в проекта |
| 4 | P6 | WP guard + възпроизводим `.mcp.json` |
| следващ месечен отчет | P7 | изтриване на старите регистрации след успешен отчет |

Клонове: проект — `refactor/mcp-layering-p0`, `…-p5`, `…-p6`; funnelwise — `feat/0.2.0-readonly-youtube`; webmaster — `main` от нулата. Всеки клон се merge-ва само със зелен §7 за засегнатите tools.

## 11. Рискове

| Риск | Вероятност | Мярка |
|---|---|---|
| Нов YouTube сървър не покрива метрика, която отчетите ползват | средна | Преди U1.1: grep на funnelwise skill-ове/report templates за ползвани `youtube_*` tools → списък с изисквания; parity probe |
| Google не позволи „In production“ без верификация за analytics scopes | ниска–средна | За <100 потребители unverified app работи с предупреждение; fallback — седмичен re-auth с новия non-blocking flow |
| Hook `deny` не се спазва в някой режим (Desktop/Cowork) | средна | funnelwise spike 0.4 го отбелязва като отворен; за проекта се ползва само Claude Code CLI/VS Code; тест U6.3 |
| Дрейф между копията на `lib/` в двата плъгина | средна | version stamp + тест, който сравнява хеша с funnelwise `lib/` при наличие |
| DataForSEO wrapper изпусне endpoint, ползван рядко | ниска | Стъпка 1 (pinned third-party) остава до доказана пълнота; lint на skill-а |
| Plugin tool имената са различни от очакваните (дефиси/долни черти) | ниска | Contract snapshot-ите се генерират от живия `tools/list`, не на ръка |
| GSC данни се ревизират леко назад | ниска | baseline период ≥ 30 дни назад; при разлика — повторно сравнение на двата живи сървъра едновременно |

## 12. Отворени въпроси (за потребителя)

- **Q1** Приемаш ли D3 — GSC и `seo-opportunities` да излязат от funnelwise и да живеят само във webmaster? (Влияе на funnelwise като самостоятелен продукт за маркетолози.)
- **Q2** Име на плъгина: `webmaster` или друго (напр. `sitewise`, по аналогия с funnelwise)? Публично GitHub репо като funnelwise?
- **Q3** DataForSEO: директно first-party wrapper (D7, повече работа, пълен контрол) или първо само pinned third-party (Стъпка 1) и wrapper по-късно?
- **Q4** Да остане ли проектът в `bypassPermissions`? С WP guard hook-а рискът е покрит за сайта, но `Bash(*)` пак позволява директен curl към WP REST с app password (паролата е в Keychain, което затруднява, но не забранява).
- **Q5** video2aeo: да се конфигурира ли изобщо WP push, или статиите остават ръчни/през проектния pipeline?

## 13. Източници

- Одит на тази сесия (2026-11-04): connection check на всички MCP, YouTube инцидент (PID 9796, порт 60320), `ps`/`lsof`.
- Inventory: `.mcp.json`, `~/.claude.json` (project + user scope), `.mcp-local/`, `.claude/skills/**`, `_shared/constants.md`, `docs/MCP_SERVERS.md`, `docs/SEO_SKILLS_REFACTOR.md`, `tools/`.
- funnelwise: `~/.claude/plugins/cache/funnelwise/funnelwise/0.1.0/` (`.mcp.json`, `servers/`, `hooks/write_guard.py`, `servers/thirdparty.py:89`, `tests/`, `dev/smoke.py`); youtube-studio-mcp 0.1.0 `auth.py`, `tools/analytics.py:35`.
- История: commits `35e4aa2`, `19095a4`, `b50551a`, `9072517`, `7cdb40e`, `8e05ce3`, `d838bdf`; memory файловете в `~/.claude/projects/-Users-dimitar-Projects-carlifebydani/memory/`; `~/Downloads/CLBD-Marketing/plan.md` (D1–D23), `memory/shared/facts.md`.
- video2aeo 0.5.0: `server/server.py:70,117`, `scripts/aeo_net.py`.
