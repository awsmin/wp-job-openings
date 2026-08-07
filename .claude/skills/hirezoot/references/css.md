# CSS

Use this file for styling work in this repo. Sources: plain CSS under `assets/css/` (admin,
admin-global, admin-overview, public, general/icomoon icon font) and SCSS for the block editor
(`blocks/src/editor.scss`, `blocks/src/style.scss`, compiled by `@wordpress/scripts` — see
`javascript.md` § build pipelines). Note there is **no Sass compile step for the `assets/css/`
files** — those are plain `.css`, concatenated and minified by gulp, not compiled from `.scss`; only
the block has an SCSS source.

## Naming: prefixed, flat — not BEM

Classes are consistently prefixed but flat rather than strict BEM `block__element--modifier` syntax:
`awsm-` in the legacy stylesheets (e.g. `.awsm-acc-content`, `.awsm-applicant-details`), and a
distinct `awsm-b-` prefix inside the block's SCSS (e.g. `.awsm-b-filter-item`, `.awsm-b-grid-col`) to
namespace block styles separately from the legacy ones. The only `__`/`--` occurrences anywhere are
third-party Select2 classes (`.select2-container--open`) — not this plugin's own convention; don't
mistake those for a BEM pattern to follow.

- **Match the existing flat-prefixed convention** for new classes — don't introduce BEM syntax into
  a file that doesn't already use it.
- Use `awsm-` for anything in `assets/css/`, `awsm-b-` for anything in `blocks/src/*.scss` — the
  split itself is intentional (keeps block styles distinguishable from legacy styles at a glance),
  don't collapse it.

## Specificity and `!important`

Unlike some sibling repos in this product family, **this repo does already use `!important`** in
several places (`admin.css` ~16 occurrences, `admin-global.css` ~5, `public/style.css` ~11,
`editor.scss` ~8, `style.scss` ~21) — don't assume a zero-`!important` policy here or "clean up"
existing usages as an unrelated change. That said, don't add more than necessary for a new rule:
try fixing the selector/specificity or the markup first, and reach for `!important` only when the
existing file's pattern already relies on it for that kind of override (e.g. overriding a
third-party widget's inline styles).

## CSS custom properties

**None of the plugin's stylesheets use CSS custom properties (`--var:`)** today, in either the
plain CSS files or the block SCSS. If a new feature genuinely needs a themeable value shared between
CSS and JS (e.g. a color exposed as a block control), introducing custom properties would be a new
pattern for this repo — reasonable if the use case calls for it, but don't assume there's an
existing convention to extend; there isn't one yet.

## Responsive design

Real `@media` breakpoints already exist and should be matched, not reinvented, per file:

| File | Breakpoints in use |
|---|---|
| `assets/css/admin/admin.css` | 500, 510, 600, 700, 701, 782, 991, 1124, 1250px |
| `assets/css/admin-overview/overview.css` | 992px |
| `assets/css/public/style.css` | 648, 768, 992, 1024px |
| `blocks/src/style.scss` | 600, 648, 767, 768, 1024px |
| `assets/css/admin-global/admin-global.css` | none |
| `blocks/src/editor.scss` | none |

If a new frontend feature needs a breakpoint, reuse one of the existing values for the file you're
in rather than introducing a new arbitrary pixel value.

## What NOT to do

- Don't add a CSS-in-JS solution or a new preprocessor — this repo already has two styling
  pipelines (plain CSS + one SCSS entry point for the block); a third adds build complexity for no
  benefit.
- Don't restyle existing flat-prefixed classes into BEM as a "cleanup" pass.
- Don't remove existing `!important` usages as a drive-by change while touching a nearby rule —
  verify first that nothing depends on the override it was providing.
