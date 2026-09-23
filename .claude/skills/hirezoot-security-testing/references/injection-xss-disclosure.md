# Injection, XSS, and information disclosure

## Test cases

### XSS-1: Stored XSS via applicant-submitted fields

The application form (`inc/class-awsm-job-openings-form.php:429` onward) sanitizes
`awsm_applicant_name`/`_email`/`_phone` with `sanitize_text_field()`/`sanitize_email()` and
`awsm_applicant_letter` with the plugin's own `awsm_jobs_sanitize_textarea()` — sanitization on
input, not escaping. The actual XSS boundary is on **output**, in the admin applications list/detail
screens. Submit an application with:
- `awsm_applicant_name` = `<script>alert(document.domain)</script>` and HTML-entity/attribute-breakout
  variants (`"><img src=x onerror=alert(1)>`).
- `awsm_applicant_letter` = the same payloads, plus multi-line variants (since this field allows
  line breaks via `awsm_jobs_sanitize_textarea()` — confirm what that function actually strips;
  don't assume it's equivalent to `sanitize_text_field()`).

Then view the application in wp-admin (applications list, single application edit screen, the
dashboard widget at `inc/widgets/class-awsm-job-openings-dashboard-widget.php`, and any email
notification rendering that echoes these fields). **Pass**: payload renders as inert text everywhere
(view source shows escaped entities). **Fail**: script executes or raw HTML renders anywhere —
capture the exact template file/line that echoed it unescaped and check whether it's missing
`esc_html()` or wrongly using `wp_kses_post()`/similar on genuinely-untrusted input.

### XSS-2: The `wp_kses` mail-footer allowlist is the one place admin-authored rich text is allowed

`hirezoot/references/security.md` documents that admin-configurable notification-email content goes
through `wp_kses( $content, $allowed_html )` with `$allowed_html` filterable via
`awsm_jobs_notification_customizer_allowed_html`, rather than plain `esc_html()`. Confirm the default
allowlist genuinely excludes `<script>`, event handler attributes (`onerror`, `onload`, etc.), and
`javascript:`/`data:` URLs in `href`/`src` — as an admin user, save mail-footer/template content
containing each of these and confirm they're stripped from the *sent* email (check the raw email
source, not just the admin preview) rather than merely from the admin-side re-render.

### XSS-3: Reflected XSS via job-listing filter params

`jobfilter`/`loadmore`/`block_jobfilter`/`block_loadmore` build `WP_Query` args from
`sanitize_text_field()`/`intval()`/`absint()`-sanitized request params
(`inc/class-awsm-job-openings-filters.php`, `inc/class-awsm-job-openings-block.php`) — per
`hirezoot/references/security.md`. Confirm any of these params that get **echoed back** into the
response HTML (e.g. a "no results for '{search term}'" message, a filter-state summary, a pagination
link built with `add_query_arg()`) are escaped on that output path, not just sanitized on input.
Try a search/filter term of `"><script>alert(1)</script>` and check the raw AJAX response body for
unescaped reflection. Also check `add_query_arg()` call sites specifically — a well-known WordPress
footgun is `add_query_arg()` returning unescaped output that needs a wrapping `esc_url()` at the
point of echo; grep for `add_query_arg` calls in the filter/block classes and confirm each is wrapped.

### INJ-1: Email header injection via notification Reply-To/Cc

`inc/class-awsm-job-openings-form.php` (~line 875) builds notification mail headers where
`reply_to`/`cc` are produced by `str_replace()`-substituting `{applicant-email}` (and other tags)
into an **admin-configured template string**, using `$applicant_email` — which passed through
`sanitize_email()` and a `filter_var( ..., FILTER_VALIDATE_EMAIL )` gate before this point (invalid
emails abort submission entirely via the error-count gate). Confirm this order actually holds:
1. Attempt to submit an application with `awsm_applicant_email` containing CRLF/header-injection
   payloads disguised as an email, e.g. `attacker@example.test%0d%0aBcc:victim@example.test`,
   `attacker@example.test\nBcc:victim@example.test`. Expected: `sanitize_email()` strips characters
   outside the valid email charset and/or `FILTER_VALIDATE_EMAIL` rejects the result, so the
   submission fails validation before any mail is sent — confirm no email at all goes out in this
   case (not just that headers look clean), since a partial-strip-then-still-invalid-but-mail-sent-
   anyway bug would be the real vulnerability.
2. If the admin's `reply_to`/`cc` **template** itself is user-configurable in a way that isn't
   admin-only (it shouldn't be — confirm it's only reachable via the settings screen, gated by
   `manage_awsm_jobs`), that would be a separate, more direct injection path — verify no
   applicant-facing input reaches these template strings directly (only via the fixed
   `{applicant-email}`/`{admin-email}`/etc. tag substitution).
3. Repeat with `awsm_applicant_name` (used in the `From:` display name, not the address) — this
   field only goes through `sanitize_text_field()`, which strips line breaks but not all
  RFC 5322-unsafe characters; try a name like `Foo\nBcc: victim@example.test` (literal newline) and
  confirm `sanitize_text_field()`'s line-break stripping actually prevents header injection here (it
  should, but this is the field with the weakest sanitizer of the three, so it's worth an explicit
  check rather than assuming from the email-field result).

### INFO-1: REST API / post-type exposure

`awsm_job_openings` (the job posting CPT) has `show_in_rest => true`
(`inc/class-awsm-job-openings-core.php:103`) — this is intentional and exposes only public job-posting
data, equivalent to what's already publicly listed on the site. **The critical check**:
`awsm_job_application` (the CPT holding applicant PII — name, email, phone, resume attachment
reference) must **not** have `show_in_rest` set. Confirm directly:
```bash
curl -s 'http://<site>/wp-json/wp/v2/types' | grep -i awsm
curl -s 'http://<site>/wp-json/wp/v2/awsm_job_application'   # expect 404/no route
curl -s 'http://<site>/wp-json/wp/v2/awsm_job_openings'      # expect this one to exist (by design)
```
If a future plugin update or a site-specific customization (a `register_post_type_args` filter, a
must-use plugin, another add-on) ever adds `show_in_rest` to the application post type or to its
custom fields, that would expose applicant PII (and, depending on the default REST post-type
capability checks, potentially to unauthenticated requests) — treat any positive result on the second
command above as a high-severity finding.

### INFO-2: Direct access to template/include files

56 of this repo's ~91 PHP files carry an `if ( ! defined( 'ABSPATH' ) ) exit;` guard; files that
currently don't include per-directory `index.php` stubs (e.g. `inc/templates/theme-compat/header.php`,
`inc/templates/theme-compat/footer.php`) but no equivalent guard in the file itself. Confirm:
1. Directory listing is blocked (the `index.php` stubs handle this) — request
   `http://<site>/wp-content/plugins/wp-job-openings/inc/templates/` directly and confirm no
   directory listing appears.
2. Direct requests to individual template files without the ABSPATH guard (e.g.
   `.../inc/templates/theme-compat/header.php`) don't leak anything beyond a PHP notice/fatal error
   (undefined variable/function notices, or a blank page) — this is a low-severity information
   disclosure at most (confirms the file exists, may show a partial path in a notice if
   `display_errors` is on) rather than code execution, since there's no attacker-controlled input
   reaching these files directly. Note the site's `display_errors`/`WP_DEBUG` configuration in the
   finding, since severity depends entirely on whether errors are visible.

### INJ-2: `maybe_unserialize()` on an admin-configured option

`admin/class-awsm-job-openings-settings.php:1216` calls
`maybe_unserialize( $awsm_jobs_listing_display_type )`, where the value comes from
`get_option( 'awsm_jobs_listing_display_type' )` — an option only ever written by this plugin's own
`manage_awsm_jobs`-gated settings screen (`update_option()` calls throughout
`admin/class-awsm-job-openings-settings.php`), not from any lower-privileged or public input path.
Confirm this by tracing every write site for this specific option — if all of them are behind the
`manage_awsm_jobs` capability check, PHP object injection here would require the attacker to already
hold that capability (i.e., not a privilege-escalation primitive, since they could already change
plugin settings directly) — report as verified-safe rather than a finding, *unless* you find a write
path to this option that bypasses the capability check (e.g. via the `settings_switch` whitelist in
AC-3 above, if `awsm_jobs_listing_display_type` were ever added to that filterable whitelist with an
attacker-controlled value).

### INJ-3: SQL injection (expected non-finding, verify by grep)

There is no raw `$wpdb` query anywhere in this codebase (confirmed:
`grep -rn "\$wpdb->query\|\$wpdb->get_" --include=*.php .` returns nothing outside `WP_Query`/
`get_posts()`/meta-API usage) — SQL injection is not a realistic vector here today. Still worth a
quick re-grep at the start of any full audit (a new feature could introduce a raw query), since this
is a "verify the premise still holds" check, not a deep test in its own right.
