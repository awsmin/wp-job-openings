---
name: hirezoot-security-testing
description: "Security *testing* playbook for HireZoot (WP Job Openings, text domain wp-job-openings): concrete, codebase-grounded test cases (not generic OWASP advice) for CSRF/nonce bypass, broken access control & IDOR, file-upload bypass, XSS/injection, and information disclosure — each with exact file:line references, a reproduction method (curl against admin-ajax.php or WP-CLI), and a pass/fail criterion. Use when asked to security-test, pentest, audit, or verify the security of this plugin, as opposed to `hirezoot`'s security.md which is about writing secure code in the first place."
---

# Security testing — HireZoot (WP Job Openings)

Scoped to this plugin only (`awsm_job_openings`/`awsm_job_application` post types, text domain
`wp-job-openings`). This is a **testing** skill: it assumes the defensive patterns described in
`hirezoot`'s `references/security.md` and turns them into concrete attack attempts against a running
install, so you can confirm the controls actually hold rather than just reading that they should.
Read that file first for the *design* rationale — this skill is the verification pass against it.

Use this skill when asked to: security-test, pentest, red-team, audit, or "check the security of"
this plugin — for either the whole plugin or a specific changed area (e.g. "I just touched the
upload handler, security-test it").

## Ground rules for this skill

- **Test against a real running WordPress install** (Local by Flywheel, in this repo's case) with
  the plugin active — these are dynamic tests (HTTP requests, actual file uploads), not static
  analysis. Static/code-level review is `hirezoot`'s `references/security.md` checklist, not this one.
- **Every test case below cites the file:line that's supposed to enforce the control.** If a test
  fails (the attack succeeds), that citation is your starting point for the fix — cross-check against
  `hirezoot`'s `references/security.md` before proposing a change, since some things that look like
  bugs here are documented, intentional tradeoffs (Tier 2's nonce-less handlers, the mail-footer
  `wp_kses` allowlist, etc.).
- **Only test an install you're authorized to test** (your own local/dev environment, or one you have
  explicit permission to assess). Nothing here should be pointed at a production site you don't own
  without authorization.
- Findings go through the same triage every time: reproduce → cite the enforcing code → classify
  severity (does it cross a privilege boundary or leak applicant PII, or is it defense-in-depth) →
  check whether it's a known, documented tradeoff before calling it a bug.

## Procedure

Work through the sections relevant to the task; a full audit does all of them, a targeted review
(e.g. "I changed the upload handler") does just the matching one(s).

1. **Setup** — test users for each role/capability tier, WP-CLI commands to create them, tooling
   (curl/Burp/browser devtools), and how to grab valid nonces per surface. See
   `references/setup-and-tooling.md`. Do this before anything else — most test cases below need a
   specific-capability test user, not just "logged in" vs "logged out".
2. **CSRF and broken access control** — the AJAX/admin-post surface (10 handlers across 3 access
   tiers), capability-boundary tests per custom capability, and IDOR on the resume/attachment
   download handler. See `references/access-control-csrf.md`.
3. **File upload** — MIME/extension allowlist bypass, filename/path handling, upload-directory
   execution protection, the `.htaccess` protection option, and DoS via unbounded file size. See
   `references/file-upload.md`.
4. **Injection, XSS, and information disclosure** — stored/reflected XSS on applicant-controlled and
   admin-controlled fields, email header injection via the notification system, REST API / post-type
   exposure, direct template-file access, and the one `maybe_unserialize()` call site. See
   `references/injection-xss-disclosure.md`.

## Reporting

For each finding: what was attempted, the exact request (method/action/params) or WP-CLI command,
the observed result, the file:line that should have blocked it, and severity (privilege escalation /
PII disclosure / state mutation without authorization = high; defense-in-depth gap with no direct
exploit path = low). Don't report a documented, intentional tradeoff (re-check
`hirezoot/references/security.md` first) as a new finding — note that it was verified instead.
