# Setup and tooling

## Test users (one per capability tier)

The plugin's access-control tests only mean something if you test as the *right* low-privilege
user, not just "logged out" vs "admin". Create these once per test environment with WP-CLI:

```bash
# Applicant-equivalent: no plugin capabilities at all (baseline "authenticated but unrelated" user)
wp user create sectest_subscriber sectest-sub@example.test --role=subscriber

# hiring_panelist only (limited HR reviewer — see project-specifics.md for the full role/cap map)
wp user create sectest_panelist sectest-panelist@example.test --role=subscriber
wp cap add sectest_panelist hiring_panelist

# edit_applications only (no edit_others_applications, no manage_awsm_jobs)
wp user create sectest_editapp sectest-editapp@example.test --role=subscriber
wp cap add sectest_editapp edit_applications

# edit_others_applications (should be able to read/download any application, but not settings)
wp user create sectest_editothers sectest-editothers@example.test --role=subscriber
wp cap add sectest_editothers edit_others_applications
wp cap add sectest_editothers edit_applications

# manage_awsm_jobs (plugin admin, but not core manage_options)
wp user create sectest_pluginadmin sectest-pluginadmin@example.test --role=subscriber
wp cap add sectest_pluginadmin manage_awsm_jobs
```

Verify each user's actual capability set before testing (`wp cap list <user>` doesn't exist natively —
use `wp user list-caps <user>` or check via `current_user_can()` in a mu-plugin/wp shell if unsure).
Cross-check against `hirezoot/references/project-specifics.md` for the real default role→capability
grants (e.g. the built-in `hr` role) rather than assuming — the `wp cap add` calls above build
isolated single-capability users specifically so each test isolates one boundary.

## Grabbing a valid nonce for a given tier

Nonces are tied to the logged-in session and the specific action string — you can't reuse one
between users or actions. To get one for a real test:

- **Tier 1 (`awsm-admin-nonce`)**: log in as the target test user in a browser, open the plugin's
  settings screen or any admin screen that enqueues `awsm-jobs-admin` script data, and read the
  nonce out of the page source / `wp_localize_script` output (search for `nonce` in the page's
  inline script). Or, faster: use the browser's Network tab, trigger the real UI action once, and
  copy the `nonce` param from that request — then reuse it for follow-up manual requests in the
  *same* session (nonces are valid for ~12–24h and tied to the session, not one-time-use, so this is
  fine for a test).
- **Tier 3 (`awsm_application_nonce`)**: load the actual job application form
  (`inc/templates/single-job/form.php` renders the `awsm_nonce` hidden field) as a logged-out
  browser session, and read it from the page HTML.
- **Download nonce (`awsm_{type}_download`)**: these are generated per-attachment
  (`get_attached_file_download_url()` in `admin/class-awsm-job-openings-meta.php`) — get one from the
  actual "Download resume" link in the applications admin screen while logged in as the target user.
- **Tier 2 handlers intentionally have no nonce** — don't waste time hunting for one; the test there
  is that they work identically with a garbage/missing nonce param (see
  `access-control-csrf.md`).

## Making requests

`admin-ajax.php` is the target for every AJAX test:

```bash
curl -i -X POST 'http://<site>/wp-admin/admin-ajax.php' \
  --cookie 'wordpress_logged_in_...=<session-cookie-for-test-user>' \
  --data-urlencode 'action=<action-name>' \
  --data-urlencode 'nonce=<nonce-or-omit-to-test-bypass>' \
  --data-urlencode '<other-params>=<value>'
```

Get the session cookie from the browser (devtools → Application/Storage → Cookies) after logging in
as the relevant test user, or drive the whole thing through Burp/ZAP with the browser proxied through
it if you want repeatable tampering (param fuzzing, dropping the nonce, swapping the cookie between
two test users to check for IDOR).

For file-upload tests, `curl -F` against the same `admin-ajax.php` endpoint with
`action=awsm_applicant_form_submission` plus the required fields — see
`file-upload.md` for the exact field list and specific payloads to try.
