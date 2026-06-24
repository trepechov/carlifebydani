# CarLife by Dani — Monorepo

This repository contains all code for the carlifebydani.com WordPress site.

## Repository Structure

```
carlifebydani/
  theme/                  WordPress theme (carlifebydani)
  plugins/
    ev-news-automator/    Automated EV news collection plugin (in development)
  docs/
    brainstorms/          Requirements and feature planning documents
```

## Local Development Setup (WP Local)

The repo lives at `~/Projects/carlifebydani/` — **outside** WP Local. Both the theme
and the plugin are wired into WP Local via symlinks.

### First-time setup

> **WP Local site path:** Local by Flywheel stores sites at
> `~/Local Sites/<site-name>/app/public/`. Find the exact path in the Local app
> under **Site → Open Site Folder**.

1. Clone the repo:
   ```bash
   git clone <repo-url> ~/Projects/carlifebydani
   ```

2. Create the theme symlink:
   ```bash
   ln -s ~/Projects/carlifebydani/theme \
     "/Users/<you>/Local Sites/carlifebydani/app/public/wp-content/themes/carlifebydani"
   ```

3. Create the plugin symlink:
   ```bash
   ln -s ~/Projects/carlifebydani/plugins/ev-news-automator \
     "/Users/<you>/Local Sites/carlifebydani/app/public/wp-content/plugins/ev-news-automator"
   ```

WordPress resolves `themes/carlifebydani` → `theme/style.css` through the symlink.
All edits to `~/Projects/carlifebydani/theme/` are live in WP Local immediately — no
copy step needed.

## Theme Development

The theme uses Tailwind CSS. All theme work happens inside `theme/`.

**Install dependencies:**
```bash
cd theme
npm install
```

**Compile CSS (watch mode):**
```bash
npm run dev
```

**Build for production:**
```bash
npm run build
```

**Package for manual upload:**
```bash
npm run pack
# Produces a zip in theme/ ready for wp-admin upload
```
Upload the zip via **wp-admin → Appearance → Themes → Add New → Upload Theme**.

## Plugin Development

Plugin code lives in `plugins/ev-news-automator/`. See [docs/brainstorms/2026-06-17-ev-news-automation-requirements.md](docs/brainstorms/2026-06-17-ev-news-automation-requirements.md) for the feature spec.

**Local development:** the symlink created in setup above means edits to
`plugins/ev-news-automator/` are live in WP Local immediately — no copy step needed.

**Server deployment:** copy the plugin directory to `wp-content/plugins/` on the
production server:
```bash
rsync -av plugins/ev-news-automator/ user@server:/path/to/wp-content/plugins/ev-news-automator/
```
(or upload via SFTP / wp-admin if rsync is not available)

## Editor Setup

Install these VS Code extensions:

- `Prettier - Code formatter`
- `PHP Intelephense`
- `Tailwind CSS IntelliSense` (optional)

Add to your `settings.json`:

```json
{
    "editor.formatOnSave": true,
    "editor.codeActionsOnSave": ["source.formatDocument", "source.fixAll.eslint"],
    "php.format.rules.catchOnNewLine": false,
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client",
        "editor.formatOnSave": true
    },
    "editor.defaultFormatter": "esbenp.prettier-vscode"
}
```

## Constants

Site-wide WordPress constants are defined in `theme/constants.php`.

## References

- https://bonnick.dev/posts/tailwind-css-with-wordpress
