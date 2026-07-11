// Stamps package.json's version into style.css's `Version:` header so the
// two never drift apart. Runs automatically before every `npm run build`
// (and therefore `npm run pack`) via the `prebuild` script — bump the
// version with `npm version patch|minor|major` and it propagates on the
// next build.
const fs = require('fs');
const path = require('path');

const pkg = require('../package.json');
const styleCssPath = path.join(__dirname, '..', 'style.css');

const styleCss = fs.readFileSync(styleCssPath, 'utf8');
const updated = styleCss.replace(/^Version:.*$/m, `Version: ${pkg.version}`);

if (updated === styleCss && !new RegExp(`^Version: ${pkg.version}$`, 'm').test(styleCss)) {
    console.error('sync-version: could not find a "Version:" line in style.css');
    process.exit(1);
}

fs.writeFileSync(styleCssPath, updated);
console.log(`sync-version: style.css Version set to ${pkg.version}`);
