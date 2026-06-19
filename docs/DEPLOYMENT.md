# Deployment Infrastructure

## Current State (Manual)

Deploying the theme today requires these manual steps:

1. Run `npm run pack` inside `theme/` — compiles PostCSS/Tailwind and zips the output
2. Manually update the `Version:` hash in `theme/style.css` (used as an identifier to compare installed vs uploaded theme)
3. Log in to WP Admin → Appearance → Upload and install the ZIP
4. Repeat for any plugin updates

This is slow, error-prone, and does not scale to multiple plugins.

---

## Target State: GitHub Actions + SSH Deploy

### Principle

Push code → CI builds and deploys automatically. No ZIP uploads, no WP Admin clicks, no manual version edits.

### Trigger strategy

| Event | Action |
|---|---|
| Push to `main` | Build and deploy to production (continuous deployment) |
| Push a semver tag (`v1.x.x`) | Same, but stamps the tag as the `Version:` in `style.css` |

For a more conservative workflow, restrict production deploys to tags only and use `main` pushes for a staging environment if one exists.

### Pipeline steps (per asset — theme or plugin)

```
git push
    ↓
GitHub Actions runner:
  1. npm ci                          — install deps from lockfile
  2. npm run build                   — compile PostCSS → css/style.min.css
  3. Inject version into style.css   — git SHA (main) or semver tag (release)
  4. rsync over SSH                  — push built files to server theme directory
    ↓
Live site updated
```

### Version injection

Replace the `Version:` line in `theme/style.css` during CI — no manual edits ever:

- On `main` push: use the short git SHA (e.g. `Version: a3f9c12`)
- On semver tag: use the tag (e.g. `Version: 1.4.2`)

### Directory mapping

| Repo path | Server path |
|---|---|
| `theme/` | `/var/www/.../wp-content/themes/carlifebydani/` |
| `plugins/my-plugin/` | `/var/www/.../wp-content/plugins/my-plugin/` |

Each plugin gets its own job in the workflow (or its own workflow file) following the same pattern.

---

## Setup Checklist

### One-time server setup

- [ ] Create a dedicated deploy SSH key pair (`ssh-keygen -t ed25519 -C "github-actions-deploy"`)
- [ ] Add the public key to `~/.ssh/authorized_keys` on the production server
- [ ] Confirm the deploy user has write access to the WP theme/plugin directories
- [ ] Note the exact server path to each theme and plugin directory

### GitHub repository secrets

Add these under **Settings → Secrets and variables → Actions**:

| Secret | Value |
|---|---|
| `SSH_PRIVATE_KEY` | The private key generated above |
| `SSH_HOST` | Production server hostname or IP |
| `SSH_USER` | SSH username on the server |
| `THEME_PATH` | Absolute path to the theme directory on the server |

Add `PLUGIN_PATH_<NAME>` per plugin as needed.

### Workflow file

Create `.github/workflows/deploy.yml` in the repo root. See the implementation task below.

---

## Implementation Tasks

- [ ] Write `.github/workflows/deploy.yml` with build + rsync deploy for the theme
- [ ] Add a job per plugin as plugins are promoted to this repo
- [ ] Add server secrets to GitHub
- [ ] Test with a non-breaking change on `main`
- [ ] Document rollback procedure (rsync previous tag's build, or keep last-good copy on server)

---

## Rollback

Since rsync overwrites files in place, keep a simple rollback option:

- Tag every production deploy in git — rolling back means re-running the workflow for a previous tag
- Optionally: have the deploy script copy the current theme to a `/backups/` directory on the server before overwriting

---

## Alternatives Considered

**WordPress Update Checker (YahnisElsts library)** — works well for themes/plugins distributed to many sites. For a single owned site it adds unnecessary complexity (still requires a manual trigger from WP Admin, just with a nicer UI).

**Composer/Satis private registry** — appropriate for enterprise multi-site setups. Overkill here.
