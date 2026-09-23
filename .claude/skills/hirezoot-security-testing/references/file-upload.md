# File upload security testing

Target: the public, anonymous application-submission handler
(`inc/class-awsm-job-openings-form.php`, action `awsm_applicant_form_submission`). This is the
plugin's only file-upload surface and its largest anonymous-attacker-reachable code path — see
`hirezoot/references/security.md`'s "Secure file uploads" section for the intended design before
testing against it.

## Minimal valid request (baseline to modify per test)

```bash
curl -i -X POST 'http://<site>/wp-admin/admin-ajax.php' \
  -F 'action=awsm_applicant_form_submission' \
  -F 'awsm_nonce=<valid-tier3-nonce>' \
  -F 'awsm_job_id=<published-job-post-id>' \
  -F 'awsm_applicant_name=Test User' \
  -F 'awsm_applicant_email=test@example.test' \
  -F 'awsm_applicant_phone=+1234567890' \
  -F 'awsm_applicant_letter=Test cover letter' \
  -F 'awsm_file=@payload.ext'
```

Get `awsm_nonce` and `awsm_job_id` from an actual published job's application form page (see
`setup-and-tooling.md`). Requests missing a required field should fail with a validation error before
ever reaching the upload code (`insert_application()`, `inc/class-awsm-job-openings-form.php:429`
onward) — confirm that first as a sanity check, then move to the upload-specific cases.

## Test cases

### FU-1: Extension/MIME allowlist bypass

The allowlist is `get_allowed_mime_types()` intersected with the admin-configured
`awsm_jobs_admin_upload_file_ext` option (default `pdf,doc,docx`) — built at
`inc/class-awsm-job-openings-form.php:508-514` and passed to `wp_handle_upload()` as a `mimes`
override. Try, against the default `pdf,doc,docx` config:
1. Upload a file named `shell.php` — must be rejected.
2. Upload a file with a double extension: `resume.pdf.php`, `resume.php.pdf`. WordPress's own
   `wp_check_filetype_and_ext()` should key off the *last* extension and the sniffed MIME, so
   `resume.pdf.php` should be rejected (real extension is `.php`) — confirm.
3. Upload a PHP payload with its content given a `.pdf`/`.docx` extension but real content
   `<?php phpinfo(); ?>` (extension matches allowlist, MIME sniff won't match `application/pdf`) —
   must be rejected by the MIME sniff, not just the extension check.
4. Upload a file with an allowed extension but no real content matching that type (garbage bytes
   named `resume.pdf`) — WordPress's finfo-based sniff behavior varies by PHP config; note what
   actually happens (some configurations only weakly validate content vs. extension for certain
   types) rather than assuming pass/fail.
5. Upload `resume.PDF` / `resume.PdF` (case variation) — must still be accepted/rejected consistently
   with the lowercase case (WordPress lowercases before comparing, but confirm).
6. Upload a filename with a null byte before the extension (`resume.php%00.pdf` — most PHP
   versions/servers now block this at the SAPI level, but worth one try) and a filename with path
   traversal characters (`../../../../var/www/html/shell.php`, `..\\..\\shell.php`) — confirm
   `sanitize_file_name()` (applied inside `hashed_file_name()`,
   `inc/class-awsm-job-openings-form.php:395`) strips traversal sequences and the file lands only
   inside the configured upload subdirectory, never outside it.
7. **The Office-XML MIME workaround is the highest-value target here.** `check_filetype_and_ext()`
   (`inc/class-awsm-job-openings-form.php:348`) widens acceptance specifically for
   `docx/dotx/xlsx/xltx/pptx/ppsx/potx/sldx` when WordPress's own sniff comes back empty but a
   secondary `wp_check_filetype( $filename, $mimes )` check by filename succeeds and
   `$real_mime === $filetype['type'] . $filetype['type']` (note: this compares against the type
   string *doubled* — confirm this isn't a typo that accidentally makes the check nearly-always-false
   or, worse, nearly-always-true; read the exact condition before assuming either way). Try:
   - A ZIP file renamed to `.docx` that is **not** actually a valid Office XML document (Office XML
     files are ZIPs, so a bare ZIP with arbitrary contents renamed to `.docx` may pass a naive
     filename-based check). If accepted, check what's actually inside the stored file — a ZIP
     containing a crafted `[Content_Types].xml`/embedded object designed for a downstream
     XXE/zip-slip issue in whatever *opens* the file later (out of scope for this plugin directly,
     but worth flagging since this plugin is the ingestion point).
   - Confirm this widened path is scoped to `$_POST['action'] === 'awsm_applicant_form_submission'`
     only (per the `phpcs:ignore` comment at line 349) and cannot be reached via any other
     upload path in the plugin (there is only this one).

### FU-2: Filename handling / overwrite / stored-XSS-via-filename

- `hashed_file_name()` (`inc/class-awsm-job-openings-form.php:395`) hashes the original name + random
  bytes + time — confirm the *original* filename (which could contain `<script>`, HTML, or path
  traversal sequences) never appears in the stored filename, on-disk path, or later gets echoed
  unescaped anywhere it's displayed in the admin UI (e.g. an "original filename" label, if one
  exists — check `admin/class-awsm-job-openings-meta.php`'s attachment-label rendering for this).
- Upload a file with a filename containing HTML/JS (`<img src=x onerror=alert(1)>.pdf`) and confirm
  wherever the *original* name is retained (post title suffix, attachment label, admin list display)
  is escaped on output (`esc_html()`) — this is a stored-XSS vector through metadata rather than
  through the file content itself.

### FU-3: Upload-directory execution protection

- Confirm `add_index_php_to_folders()` (`inc/class-awsm-job-openings-form.php:380`) actually writes
  an `index.php` "Silence is golden" stub into the upload subdirectory after a successful upload, and
  that this subdirectory (`wp-content/uploads/<AWSM_JOBS_UPLOAD_DIR_NAME>/...`) is **not** directly
  web-executable for PHP — i.e., even though a `.php` upload should never reach disk per FU-1, verify
  defense-in-depth by placing a test `.php` file directly (via filesystem, simulating a hypothetical
  bypass) into that directory and requesting it over HTTP: on a stock Apache/mod_php setup this *will*
  execute unless the site/server config disables PHP execution in uploads — note whatever the actual
  result is, since this plugin does not itself add a `php_flag`/`Files` directive blocking execution,
  only `Options -Indexes` / `deny from all` for listing/access (see FU-4). This is a real gap worth
  flagging as a hardening recommendation (add a `.htaccess` with a "deny PHP execution" directive, not
  just directory-listing/deny-all) even though it's not directly exploitable without a separate
  upload-bypass bug.

### FU-4: `.htaccess` protection is opt-in, not default

`admin/class-awsm-job-openings-settings.php:953` (`update_awsm_hide_uploaded_files`) writes
`Options -Indexes` to the upload directory's `.htaccess` by default, and only writes `deny from all`
if the admin explicitly enables `awsm_hide_uploaded_files`. Confirm:
1. On a fresh install (option not yet touched), directly requesting a known uploaded resume's URL
   (from `.htaccess`'s default `Options -Indexes` state) still succeeds — i.e., direct file access is
   possible by default, and only the randomized filename (FU-2/`hashed_file_name`) prevents casual
   discovery, not an actual access-control mechanism. This is **documented, intentional** behavior
   (see `hirezoot/references/security.md`) — verify it, don't report it as a novel finding, but do
   confirm the randomized name is unguessable enough in practice (256-bit hash input, not just
   `time()`-based).
2. With `awsm_hide_uploaded_files` enabled, confirm the `.htaccess` now contains `deny from all` and
   direct requests to any file in that directory 403 — and separately confirm this protection is
   **Apache-only**: if the test environment runs Nginx (`.htaccess` ignored entirely), direct file
   access remains possible regardless of the setting. Flag this clearly if the target environment is
   Nginx-based — this is the plugin's most likely "false sense of security" gap for real deployments.

### FU-5: No server-side size/count cap (DoS)

There is no plugin-level file-size or file-count cap in code — only PHP's own
`upload_max_filesize`/`post_max_size` ini limits apply, and the form has exactly one file field
(`awsm_file`). Confirm:
1. A single oversized upload (larger than `upload_max_filesize` but attempting to see how the failure
   is handled — should fail cleanly via PHP's own upload-error code check, not a fatal error/resource
   exhaustion) is rejected gracefully.
2. Repeated legitimate-sized submissions in rapid succession (no rate limiting observed anywhere in
   this handler beyond CAPTCHA, which is optional/admin-configured) can fill disk/database over time
   — this is an accepted design tradeoff for a free plugin without a size-cap feature (that's a Pro
   Pack feature per `hirezoot/references/security.md`), so report as a known limitation, not a bug,
   unless CAPTCHA is also absent/disabled in the test config, in which case flag the combination
   (no CAPTCHA + no size cap + no rate limit = practical mass-submission DoS/storage-exhaustion
   vector) as a real finding worth a recommendation.
