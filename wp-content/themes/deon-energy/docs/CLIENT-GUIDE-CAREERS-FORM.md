# Careers — "Apply Now" application form

The job detail page ends with an **Apply Now** band. What renders inside it depends on one setting:

**Appearance → Customize → Careers → Application form shortcode**

| Setting | What renders | Stores submissions? |
|---|---|---|
| *empty* | The theme's designed form shell | No — the button is a `mailto:` / the job's Apply URL |
| a Contact Form 7 shortcode | Your CF7 form, styled by the theme | Yes — **Flamingo → Inbound Messages** |

## Important: don't style the form in CF7

The theme styles the form for you. Do **not** paste Tailwind classes (`class:w-full`, `class:border` …) into the CF7 editor — those classes are compiled from the theme's source files at build time, so any class typed into the CF7 form template resolves to nothing and the form falls back to raw browser styling.

Use the markup below verbatim. The theme's CSS (`.deon-apply-form` block in `assets/css/main.css`) does the rest: underline-only fields, dashed CV upload panel, dark Submit button, styled validation messages.

## Form template — paste into Contact → your form → Form tab

```html
<div class="deon-apply-row">
  <div class="deon-apply-field">
    <span>Legal First Name</span>
    [text* first-name placeholder "Alexander"]
  </div>
  <div class="deon-apply-field">
    <span>Legal Last Name</span>
    [text* last-name placeholder "Von Neumann"]
  </div>
</div>

<div class="deon-apply-field">
  <span>Corporate Email</span>
  [email* email placeholder "alexander@domain.com"]
</div>

<div class="deon-apply-field">
  <span>Curriculum Vitae (PDF/Word)</span>
  [file cv limit:10mb filetypes:pdf|doc|docx]
  <span class="deon-apply-hint">Limit 10MB · PDF or Word</span>
</div>

<div class="deon-apply-field">
  <span>Executive Summary / Cover Note</span>
  [textarea cover-note rows:5 placeholder "Briefly detail your core project achievements…"]
</div>

[submit "Submit Candidacy"]
```

Rules of thumb when editing:

- Wrap every field in `<div class="deon-apply-field">` — **not** `<label>` or a bare line. WordPress auto-wraps loose text in paragraphs, which breaks the field spacing; a `div` is left alone.
- The caption goes in a plain `<span>` as the first child of the field.
- Pair two fields side by side by putting their two `deon-apply-field` divs inside one `<div class="deon-apply-row">`. Rows collapse to a single column on mobile automatically.
- `<span class="deon-apply-hint">` renders the small uppercase helper line (used under the CV upload).

## Mail tab

CF7 ships a default Mail tab that references `[your-name]`, `[your-email]`, `[your-subject]`, `[your-message]`. **None of those fields exist in this form** — leave it as-is and the notification email arrives blank, the CV is not attached, and Flamingo files every entry under the title `[your-subject]`. Replace it with:

| Field | Value |
|---|---|
| **Subject** | `Application: [_post_title] — [first-name] [last-name]` |
| **From** | `[_site_title] <wordpress@yourdomain.com>` (must be a domain-owned address) |
| **To** | `[_site_admin_email]` — or the HR inbox |
| **Additional headers** | `Reply-To: [email]` |
| **File attachments** | `[cv]` |

**Message body:**

```
New application received via the Deon Energy careers page.

Role: [_post_title]
Role URL: [_post_url]

Applicant: [first-name] [last-name]
Email: [email]

Cover note:
[cover-note]

CV is attached to this email.
```

Every submission is stored under **Flamingo → Inbound Messages** whether or not the email goes out.

## "There was an error trying to send your message"

That is CF7's `mail_failed` status: the submission was saved, but WordPress could not hand the email to a mail server. It is a **hosting/mail configuration** issue, not a form or theme bug. Causes, in order of likelihood:

1. The host blocks PHP's `mail()` function (common on shared hosting).
2. No SMTP is configured.
3. The **From** address is not on the site's own domain, so the receiving server rejects it — never use the applicant's address in From; use `Reply-To` instead (as above).

Fix by installing an SMTP plugin (**WP Mail SMTP**, **FluentSMTP**) and pointing it at the client's real mailbox or a sending service. Then send a test from the plugin's own test tool before retesting the form.

The theme rewrites CF7's confusing default wording (`deon_cf7_failure_message()` in `functions.php`) so an applicant who hits this sees that their details were received and gets a direct email address as a fallback. That copy is theme-side and survives rebuilding the form.

On a local dev machine this error is expected — there is no mail transport at all.

## Reminder

Functional application intake is outside the original scope (SOW §4). CF7 + Flamingo is the recommended client-side add-on; the theme only guarantees the design of whatever CF7 renders.
