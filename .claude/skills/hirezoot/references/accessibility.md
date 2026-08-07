# Accessibility

Use this file for admin UI and frontend job-listing markup this plugin renders.

## Current state — be honest about this, don't assume it's covered

A scan of `inc/templates/*.php`, `admin/templates/*.php`, and the plugin's JS
(`assets/js/admin/admin.js`, `assets/js/admin-overview/overview.js`,
`assets/js/public/job-application.js`, `assets/js/public/job-listings.js`, `blocks/src/view.js`)
found accessibility markup is **minimal and mostly incidental**, not deliberately built in:

- `aria-*` attributes: only 9 matches total, concentrated in two places —
  `job-listings.js`/`blocks/src/view.js` dynamically toggle `aria-current`/`aria-pressed` on
  pagination and filter-toggle buttons as part of their existing interaction logic (not a dedicated
  a11y pass), plus one static `aria-label="Download Resume"` on the applicant resume-download link
  in `admin/templates/meta/applicant-single.php`.
- `role=`: exactly one match, `role="presentation"` on an email `<table>` in
  `inc/templates/mail/header.php` — a standard HTML-email accessibility convention, unrelated to
  the plugin's own app UI.
- `tabindex`: **zero matches anywhere** in the plugin.
- The admin JS (`admin.js`, `overview.js`) and the public application form (`job-application.js`)
  have **no ARIA attributes at all**.

Treat any interactive UI you touch (the repeater-style form builder, the drag/upload area, admin
notices, filter toggles) as needing a real accessibility check — don't rubber-stamp it as "already
handled" based on the presence of the few `aria-*` attributes noted above; they cover a narrow slice
of the UI, not the whole surface.

## Baseline rules for new/changed markup

- **Semantic HTML first.** A `<button>` for actions, a real `<label for>` on form fields, a
  `<table>` for tabular data (e.g. the admin overview's analytics tables) — reach for ARIA only when
  semantic HTML genuinely can't express the relationship (e.g. a live-updating region needing
  `aria-live`).
- **Keyboard operability.** Anything clickable — a filter toggle, a form-builder repeater's
  add/remove control, a file-upload dropzone — must also be operable via keyboard (Tab to focus,
  Enter/Space to activate). If you touch the application form's file upload, verify there's a
  normal, keyboard-reachable `<input type="file">` fallback alongside any drag-and-drop UI —
  drag-and-drop-only implementations are a common accessibility gap and this plugin's current markup
  hasn't been audited for that specifically.
- **Focus management.** When a repeater adds a new form-builder field, or an admin panel/modal
  opens, move focus to the new/relevant element rather than leaving it stranded.
- **Screen reader support.** A non-purely-visual status change (a form validation error, an
  AJAX-loaded analytics chart, a filter applying) needs an accessible announcement (e.g. an
  `aria-live="polite"` region) — today's `aria-current`/`aria-pressed` toggling on pagination/filter
  buttons is the only example of this in the codebase; most other async UI updates have no
  equivalent.

## Frontend job-listing output

- The rendered listing (block/shortcode/widget shared output) should use real heading levels for
  job titles, not styled `<div>`s standing in for headings — check the existing heading level used
  in `inc/template-functions-block.php`/`inc/templates/` and keep new layouts consistent with it.
- Featured images need meaningful `alt` text (the job title, not a filename, and only an empty
  `alt=""` when the image is genuinely decorative).

## Verification

- Keyboard-only pass: tab through any new/changed interactive element, confirm visible focus and
  correct activation.
- There is no automated accessibility check in this repo (no axe-type tooling, no PHPUnit, and this
  repo itself has no e2e suite — see `testing.md`) — treat every accessibility claim as something to
  verify manually, not something CI will catch.
