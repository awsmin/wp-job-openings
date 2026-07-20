---
name: hirezoot
description: "Full development checklist for HireZoot (formerly WP Job Openings, this repo's plugin, text domain wp-job-openings): coding standards, security, performance, WP best practices, the Gutenberg block/shortcode/widget rendering surface, JavaScript, CSS, accessibility, i18n, and testing — plus this repo's own concerns (CPTs and custom capabilities, the Settings-API-lite pattern, opt-in destructive uninstall, and the cross-plugin hook surface that Pro Pack, Job Alerts, Resume Scoring, Auto Delete Applications, and User Access Control all depend on). Builds on the wp-plugin-development skill for generic cross-project guidance."
---

# Development concerns — HireZoot (WP Job Openings)

Scoped to this plugin only. Builds on `wp-plugin-development` (the generic core skill) for
cross-project baselines — invoke that too when it's installed. This skill is the one to reach for
on every task in this repo: it holds both the generic domain checklist (coding standards, security,
performance, etc.) and this codebase's own concerns, grounded in the actual code rather than
generic WordPress advice.

## What this plugin is

- **HireZoot** (formerly "WP Job Openings") — the free, standalone **core** plugin. Text domain
  `wp-job-openings`. Main bootstrap: `wp-job-openings.php` (~2700 lines, singleton class
  `AWSM_Job_Openings`). It depends on nothing else and must keep working with no add-on active.
- This is the plugin the rest of the product family is built around. Several sibling add-on
  plugins in this same WordPress install extend it purely through the hooks it exposes:
  `pro-pack-for-wp-job-openings` (Pro Pack, has its own `hirezoot` skill — read it if you're asked
  to change anything both plugins touch), `job-alerts-for-wp-job-openings`, `hirezoot-resume-scoring`,
  `auto-delete-applications-add-on-for-wp-job-openings`, `user-access-control-for-wp-job-openings`.
  **Any hook this plugin defines is a real cross-plugin contract with all of them**, not an internal
  implementation detail — see `references/wp-best-practices.md`.
- No PHPUnit suite exists in this repo (`tests/` contains only `tests/phpstan/bootstrap.php`).
  Verification runs through PHPStan (static analysis) and, indirectly, the **sibling Pro Pack
  repo's** Playwright e2e suite, which has a `tests/free/` split that exercises this plugin's own
  features. See `references/testing.md`.
- No custom REST endpoints exist (`register_rest_route()` — zero matches in this codebase). The
  `awsm_job_openings` CPT has `show_in_rest => true` (core default routes only, e.g. used by the
  sibling e2e suite for taxonomy-term setup) — that's WordPress core behavior, not this plugin's own
  endpoint code. See `references/wp-best-practices.md`.

## The core architectural fact: this plugin *is* the extension point

Unlike an add-on, this plugin doesn't extend anything — it defines the surface everything else
extends. Two consequences:

1. **Renaming, re-signaturing, or removing a hook here breaks other installed plugins silently** —
   no PHP error, the other plugin's callback just stops firing. Treat every `apply_filters`/
   `do_action` call in `inc/class-awsm-job-openings-block.php`, `inc/template-functions-block.php`,
   and the AJAX/form layer as a public API, even though nothing enforces that in code.
2. **New functionality that an add-on would plausibly want to hook into should expose a filter/action
   at the point that matters**, the same way the existing block-rendering path does — not assume
   the add-on will "find a way in" without one.

See `references/wp-best-practices.md` and `references/frontend-rendering.md` for the concrete hook
list and guardrails.

## Procedure — domain checklist for this plugin

Work through the relevant sections for the task at hand; not every task touches every domain.

1. **Coding standards** — this repo's actual `phpcs.xml`/`phpstan.neon.dist` rules (note: only two
   allowed text domains here, and a stale WP-version config worth knowing about), naming, file
   organization. See `references/coding-standards.md`.
2. **Security** — the two AJAX tiers this repo actually has (nonced admin/application-submission
   handlers vs. deliberately nonce-less read-only listing handlers), file-upload validation, the
   custom-capability model. See `references/security.md`.
3. **Performance** — the known unconditional asset-enqueue pattern, query patterns (zero raw SQL —
   everything is `WP_Query`/post meta). See `references/performance.md`.
4. **WordPress best practices** — hook/lifecycle design, the cross-plugin hook-stability rule, what
   little REST surface exists. See `references/wp-best-practices.md`.
5. **Frontend rendering** — the Gutenberg block (dynamic, `ServerSideRender`-backed), the `[awsmjobs]`
   shortcode, the legacy `WP_Widget` + dashboard widget, theme template overriding, and where page-
   builder detection actually does (and doesn't) change behavior. See `references/frontend-rendering.md`.
6. **JavaScript** — the two real eras of JS here (jQuery admin/public vs. React block editor), and
   the one place jQuery leaks into the "modern" block pipeline (`blocks/src/view.js`). See
   `references/javascript.md`.
7. **CSS** — naming/organization conventions actually in use, and where `!important`/media queries
   already exist (don't assume a zero-`!important` policy here — that's the sibling Pro Pack repo,
   not this one). See `references/css.md`.
8. **Accessibility** — an honest inventory of what exists today (very little) so new work isn't
   built on a false assumption of coverage. See `references/accessibility.md`.
9. **Internationalization** — text domain, `translators:` comment conventions, the narrow WPML
   integration for dynamically-created taxonomy labels. See `references/i18n.md`.
10. **Testing** — what verification actually exists (PHPStan only, in this repo) and where the real
    e2e coverage for this plugin's features actually lives. See `references/testing.md`.
11. **This repo's own concerns** — CPTs/custom capabilities/custom role, the Settings-API-lite
    pattern, opt-in destructive uninstall, activation/deactivation specifics. See
    `references/project-specifics.md`.

## Verification

Before considering a task done in this repo:

- `composer phpcs` (or `phpcs-l` for a leaner diff-friendly run) — zero new errors.
- `composer phpstan` — zero new errors against level 5. Remember: `phpstan.neon.dist`'s `paths:` is
  an explicit file list, not `inc/**` — a new class file is silently unanalyzed until added there.
- `npm run build` (gulp, for `assets/`) and/or `cd blocks && npm run build` (wp-scripts, for the
  block) if you touched the relevant JS/CSS — these are two independent pipelines with no shared
  config; touching one doesn't rebuild the other.
- If the change touches a filter/action consumed by an add-on (`awsm_jobs_block_*`, the AJAX action
  names, `awsm_application_form_*`, etc.), treat it as a coordinated cross-plugin change — flag it
  before implementing rather than renaming/re-signaturing unilaterally.
- If the change touches AJAX/file upload/applicant-form code, re-check nonce + capability +
  validation per `references/security.md` — this is the plugin's largest untrusted-input surface.
- For anything user-facing on the block, list-filtering, or application-form surface, check whether
  the sibling repo's `tests/e2e/tests/free/*.spec.ts` (via its `wp-e2e-playwright` skill) already
  covers it or flag the gap — see `references/testing.md`.
