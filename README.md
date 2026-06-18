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

The repo root is no longer the theme directory — `theme/` holds the theme files. To keep WP Local working, replace the theme directory symlink:

```bash
# From the WP Local themes directory:
cd "/path/to/WP Local/app/public/wp-content/themes"

# Remove or rename the old checkout
mv carlifebydani carlifebydani-old

# Clone the repo one level up (outside themes/)
cd ..
git clone <repo-url> carlifebydani-repo

# Symlink the theme subdirectory into the themes folder
ln -s "$(pwd)/carlifebydani-repo/theme" "themes/carlifebydani"
```

WordPress sees `themes/carlifebydani` and finds `style.css` there as expected.

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

## Plugin Development

Plugin code lives in `plugins/ev-news-automator/`. See [docs/brainstorms/2026-06-17-ev-news-automation-requirements.md](docs/brainstorms/2026-06-17-ev-news-automation-requirements.md) for the feature spec.

When deploying, copy `plugins/ev-news-automator/` to `wp-content/plugins/` on the server alongside the theme.

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
