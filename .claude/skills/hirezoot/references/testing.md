# Testing

## What verification actually exists in this repo

There is **no PHPUnit suite here** — `tests/` contains exactly one file, `tests/phpstan/bootstrap.php`.
Don't assume a unit/integration suite exists or propose adding test files under a `tests/php/`
convention without confirming that's actually wanted first. The only automated check in *this* repo
is:

- **PHPStan** (static analysis, not a test suite) — `composer phpstan`, level 5. See
  `coding-standards.md` for the gotcha that `phpstan.neon.dist`'s `paths:` is an explicit file list
  (not `inc/**`/`admin/**`) — several real files, including the block-rendering classes, currently
  aren't in that list and so aren't analyzed at all.

## The real e2e coverage for this plugin lives in a sibling repo

This repo has no Playwright/e2e setup of its own. The actual browser-level coverage for this
plugin's features lives in the **sibling `pro-pack-for-wp-job-openings` repo**, at
`tests/e2e/tests/free/` — `Block_settings.spec.ts`, `Block_styles.spec.ts`, `Job-opening-E2E.spec.ts`,
and `job-openings.spec.ts` — run and documented via that repo's own `wp-e2e-playwright` skill
(`.claude/skills/wp-e2e-playwright/`). If you don't have that skill available in this session, that
doesn't mean no e2e coverage exists — it means you're not looking at the repo that owns it.

`job-openings.spec.ts` (already read in full) covers admin-side CPT/taxonomy CRUD: the empty-state
message on the job list, creating and publishing a job via the block editor, editing an existing
job, assigning a specification/taxonomy term via the classic meta box, and trashing a job (with the
empty-state message reappearing). The other three specs in that directory cover the block's editor
settings, block styling, and the broader application-submission flow, per their filenames — read
them directly in that repo if you need the exact assertions rather than assuming from the name.

**When changing something in this plugin that the free e2e specs plausibly cover** (CPT registration
args, the block's attributes/rendering, the application form), check whether the corresponding spec
in the sibling repo still passes, or flag that it needs updating — don't assume this repo's own
`composer phpstan` passing is sufficient signal for those surfaces.

## Which layer actually catches which kind of change

| Change type | What actually verifies it |
|---|---|
| Type errors, wrong function signatures | PHPStan — but only for files in `phpstan.neon.dist`'s `paths:` list |
| PHPCS style/naming/i18n-domain violations | `composer phpcs` (see `coding-standards.md`) |
| Admin CPT/taxonomy CRUD, block settings/styling, application-form flow | Sibling repo's Playwright e2e (`tests/free/*.spec.ts`) — not anything in this repo |
| AJAX handler correctness (nonce/capability/validation logic itself) | Neither, currently — manual QA; consider whether a sibling-repo e2e spec should be extended |
| Shortcode/widget rendering (as opposed to the block) | Neither — no spec currently targets these surfaces specifically; manual QA |
| CSS/visual regressions | Neither — manual QA |
| Accessibility | Neither — see `accessibility.md`; no automated check exists anywhere in the product family today |

## Regression risk when touching shared rendering code

The block, the `[awsmjobs]` shortcode, and the legacy widgets share underlying listing-render logic
(`inc/class-awsm-job-openings-block.php`, `inc/template-functions-block.php`, `inc/class-awsm-job-openings-filters.php`).
A change made while testing one surface can silently affect the others. After any shared-render
change, manually verify the shortcode and widget outputs too, not just whichever surface the
sibling repo's e2e specs happen to cover (today, that's the block specifically).

## Browser/mobile compatibility

No browser matrix or automated check exists in this repo. Mobile responsiveness relies on the
`@media` breakpoints already present in `assets/css/public/style.css` and `blocks/src/style.scss`
(see `css.md`) — verify at a narrow viewport manually when changing frontend layout; there's no
automated regression check for it.
