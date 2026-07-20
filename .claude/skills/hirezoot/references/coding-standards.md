# Coding standards

Use this file when writing or reviewing PHP/JS for style, naming, documentation, and file
organization in this repo.

## This repo's actual PHPCS ruleset

Don't assume generic WPCS — this repo's `phpcs.xml` has specific overrides. Run it, don't guess:

```bash
composer phpcs      # phpcs --colors -p -s
composer phpcs-l     # leaner output, better for reviewing a diff
composer phpcbf      # auto-fix what's fixable
```

What it actually enforces (`phpcs.xml`):

- `PHPCompatibilityWP` with `testVersion="5.6-"`, plus `WordPress-Core` + `WordPress-Extra`,
  **except**:
  - `WordPress.WhiteSpace.PrecisionAlignment.Found` is excluded — don't hand-align `=>` operators
    expecting the sniff to require it, and don't "fix" existing misalignment.
  - `WordPress.PHP.YodaConditions` is excluded — **this repo does not require Yoda conditions**.
    Write `if ( $value === true )`, not `if ( true === $value )`; don't rewrite existing conditions
    either way as a drive-by change.
- `Generic.CodeAnalysis.UnusedFunctionParameter` is enabled — an unused `$param` in a callback
  (common in filter/action callbacks) will flag; either use it, prefix-ignore per the sniff's
  convention, or confirm the hook signature genuinely requires it.
- `WordPress.WP.I18n` is configured with **only two** allowed text domains: `default` and
  `wp-job-openings` — a string using any other domain (including a sibling add-on plugin's own
  domain, e.g. `pro-pack-for-wp-job-openings`) is a real PHPCS error here. This is simpler than the
  sibling Pro Pack repo, which has to allow two domains for its own split-ownership reasons — this
  repo never needs to reach for another plugin's domain. See `i18n.md`.
- `WordPress.Security.EscapeOutput` is configured with one custom auto-escaped function,
  `awsm_jobs_paginate_links` — don't add a redundant `esc_*()` wrapper around its return value
  expecting the sniff to still flag its absence; it won't, by design.
- `vendor/`, `node_modules/`, and `build/` are excluded.
- `wp-job-openings.php` is exempted from `WordPress.Files.FileName.InvalidClassFileName` — that's
  intentional (it's the plugin bootstrap containing the main `AWSM_Job_Openings` class, not a
  single-class-named file); don't split it to satisfy that sniff.

### A stale config value worth knowing about

`wp-job-openings.php`'s plugin header declares `Requires at least: 6.0`, but `phpcs.xml` still sets
`minimum_supported_wp_version="4.8"`. These are out of sync — the PHPCS config is stale relative to
the actual supported-WP-version claim in the header. Don't silently "fix" this as a drive-by change
inside an unrelated task (it changes which WP-version-gated sniffs fire), but do flag it if you
notice it while touching `phpcs.xml` for another reason. `testVersion="5.6-"` (PHP) does match the
header's `Requires PHP: 5.6` — only the WP-side value is stale.

## PHPStan

```bash
composer phpstan     # ./vendor/bin/phpstan analyse
```

- Runs at level 5, with `bootstrapFiles` pointed at `tests/phpstan/bootstrap.php`,
  `inc/helper-functions.php`, and `inc/template-functions.php`.
- `paths:` in `phpstan.neon.dist` lists **specific files, not a blanket `inc/**`/`admin/**`**.
  **A new file under `inc/` or `admin/` is silently unanalyzed until it's added to that list** —
  add it when creating a new class file there. Notably, files like `inc/class-awsm-job-openings-block.php`,
  `inc/template-functions-block.php`, `inc/class-awsm-job-openings-third-party.php`, and everything
  under `blocks/` are **not** in the `paths:` list today — PHPStan doesn't currently check them.
  Don't assume "PHPStan passed" means those files were checked.
- Two errors are pre-baked into `ignoreErrors` (a variadic `apply_filters`/`apply_filters_ref_array`
  count mismatch, and a known always-false `&&` in `wp-job-openings.php`) — don't try to "fix" those
  specific spots without understanding why they were suppressed first.

## Naming conventions

- Global prefix: `awsm_` (functions/hooks/options) and `AWSM_` (classes/constants) — every new
  identifier needs it; this is the plugin's collision defense in an install that may also have
  several sibling `awsm_`-prefixed add-ons active.
- Classes: `AWSM_Job_Openings_*` (e.g. `AWSM_Job_Openings_Core`, `AWSM_Job_Openings_Settings`),
  underscore style (not PSR-4 namespaces) — matches the whole codebase; don't introduce a
  namespaced class alongside these.
- Filenames: `class-awsm-job-openings-*.php`, one class per file.
- Singleton pattern is consistent across the codebase: a private static `$instance` property plus a
  public static `init()`/`get_instance()` accessor, hooks registered from the constructor
  (`AWSM_Job_Openings`, `AWSM_Job_Openings_Core`, `AWSM_Job_Openings_Settings`, etc. all follow this).
  `AWSM_Job_Openings_Uninstall` is a deliberate exception — it's all-static with no instantiation,
  since it only ever needs to run once, at uninstall. Match whichever shape fits the new class's
  actual lifecycle rather than defaulting to the singleton out of habit.
- Hooks this plugin defines that sibling add-ons rely on (`awsm_jobs_block_*`,
  `awsm_jobs_listings_block_attributes`, `awsm_application_form_*`, etc.) are a stable cross-plugin
  contract — see `wp-best-practices.md` before renaming or changing their signature.

## PHPDoc

Every function/method/class gets a docblock with typed `@param`/`@return`, matching the existing
style throughout `inc/` and `admin/`. When adding a new filter/action another plugin might hook into,
document it at the `apply_filters`/`do_action` call site itself (see the existing calls in
`inc/class-awsm-job-openings-block.php` for the density/level of detail already used there) — don't
write a docblock that only restates the function name.

## File organization

- `inc/` — core runtime logic loaded on **every** request: CPT/taxonomy registration
  (`class-awsm-job-openings-core.php`), the application form and its AJAX/file-upload handling
  (`class-awsm-job-openings-form.php`), mail customization, third-party integrations, the uninstall
  class, shared helper/template functions, and `templates/`/`widgets/`.
- `admin/` — admin-only UI classes (`overview`, `meta`, `settings`, `info`), loaded only when
  `is_admin()` (see `wp-job-openings.php`'s `load_classes()`), plus their own `admin/templates/`.
- `blocks/` — a self-contained Gutenberg block sub-project with its **own** `package.json`, `src/`,
  and `build/` (built with `@wordpress/scripts`, independent of the root `gulpfile.js`). Match this
  split for any new file — don't add a third top-level code directory without a reason, and don't
  mix a block-editor JS file into `assets/js/` or vice versa.
- Root — plugin bootstrap (`wp-job-openings.php`), `uninstall.php`, packaging/build tooling
  (`composer.json`, `phpcs.xml`, `phpstan.neon.dist`, `gulpfile.js`, `config.js`), `assets/`,
  `languages/`, `tests/`.

## What NOT to do

- Don't run `phpcbf`/`phpcbfx` across the whole repo as part of an unrelated change — it will touch
  every file with a fixable violation and turn a small diff into a large one.
- Don't add a new abstraction (interface, factory, DI container) for a single implementation.
- Don't rename existing functions/classes/hooks to "fix" naming inconsistency noticed while doing
  unrelated work — hook names especially are a cross-plugin contract consumed by several sibling
  add-ons (see `wp-best-practices.md`).
