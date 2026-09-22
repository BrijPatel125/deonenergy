# Scrape notes — deonenergy.in

Scraped **2026-07-22**. Source: the client's current live site `https://deonenergy.in`
(hand-coded PHP/Apache site, not WordPress). All copy in these JSON files is reproduced
**verbatim** from the live pages — nothing was rewritten, summarised, or inferred.

## Method

- `https://deonenergy.in/sitemap.xml` and `/sitemap_index.xml` → **404**. `/robots.txt` → **404**.
  There is an HTML sitemap at `/sitemap`, but it lists only the 9 top-level pages — **not**
  the project detail pages.
- Project URLs were therefore enumerated from the `/project-gallery` markup itself.
  There is **no pagination and no "load more"** on that page — every project card is in the
  initial HTML response. `?page=2` style params are not used anywhere on the site.
- Pages were fetched sequentially with `curl -sL` (~0.3–0.4 s apart). WebFetch was not needed;
  the site is plain server-rendered HTML.
- Image URLs were verified with batched `curl -sIL` (HEAD), 15 per call, 0.25 s apart.

## What was fetched

| Target | File | Result |
|---|---|---|
| Project gallery + 34 detail pages | `projects.json` | 34 found / **34 fetched (100%)** |
| Contact page + global footer | `company.json` | complete |
| About Us → "Our Team" | `leadership.json` | 15 people |
| Aggregates + site's own claims | `stats.json` | computed 154.414 MW |
| Every project + team image | `image-manifest.json` | 457 entries, 454 unique URLs, all HTTP 200 |

Top-level pages also fetched and read for context: `/`, `/why-deon`, `/about-us`, `/expertise`,
`/services`, `/project-gallery`, `/investors`, `/career`, `/contact-us`, `/sitemap`.

### Counts

- **Projects found: 34. Projects successfully fetched: 34. No discrepancy.**
  (The brief mentioned "30+"; the live gallery has exactly 34.)
- `/anida` failed once with a transient curl error (exit 0 / no response); it was refetched
  successfully and is complete. Nothing else 404'd or failed.
- Images: 457 manifest rows (thumbnail + gallery images per project, plus 15 team photos).
  454 distinct URLs, all returning **200**, total ~179 MB. `content_type` and `content_length`
  are recorded for every row. Nothing was downloaded.

## Fields that are genuinely absent on the live site (recorded as `null`)

The project detail pages are **image galleries only**. Each one contains a capacity badge,
a name, a location string, a photo grid, an MP4 walkthrough video, and a Google Maps embed —
and nothing else. There is no body copy and no spec table anywhere on the site.

Therefore, for **all 34 projects**, these are `null` and must be supplied by the client:

- `description` — no prose exists on any project page.
- `client` — never named as a separate field. Many project *names* are company names
  (e.g. "Makson Pharmaceuticals Pvt. Ltd."), but the site does not state a client relationship,
  so this was **not** inferred.
- `commissioned_year` / date — not published anywhere.
- `project_type` (rooftop / ground-mount / utility-scale) — not stated per project.
  The About Us page says the portfolio spans "commercial rooftops and ground mounted solar
  power plants" generally, but never per-project. Not inferred.
- `status` (completed / ongoing) — not stated. The gallery implies all are built, but the
  site never says so; left `null`.
- `specs` — empty object on every project; no spec tables exist.

Leadership: **no bios and no LinkedIn URLs** anywhere on the site — `bio` and `linkedin_url`
are `null` for all 15 people. There is no dedicated leadership/team page; the only team
content is the "Our Team" section on `/about-us`.

Company: **no CIN, no GST, no registration number, and no office hours** appear anywhere
on the live site. All `null`.

## Data-shape notes

- **Capacity.** `capacity_raw` preserves the site's exact string (casing is inconsistent on
  the live site: `26Mw`, `13MW`, `7.3mw`, `999kw`). `capacity_mw` is the numeric conversion,
  with kW values divided by 1000 (e.g. `999kw` → `0.999`).
- **Location.** `location.raw` preserves the original slash-separated string. It is usually
  `village / district / state` but **four projects only give two parts** (`agritex-enterprise`,
  `leaspin-textile`, `fiotex-cotspin`, `patel-cotton-industries`), e.g. "Rajula / Gujarat".
  For those, `district` is `null` rather than guessed.
- **Spelling in source.** The live site spells Dhrangadhra inconsistently — "Dhangadhra",
  "Dhrangadhra", "Dhangdhra" all appear. Preserved as-is; do not silently normalise without
  client sign-off. Likewise "jayesh sindhav" is lowercase on the live site, and the slug
  `datt-poluplast` is misspelled while the display name is "Datt Polyplast LLP".
- **Leadership grouping.** The live page does **not** label Board of Directors vs Key
  Management. `group` is `null` for everyone. `display_group` records layout only:
  `featured` = the two large cards (Dharmesh Makadiya, Chirag Kalariya), `team` = the 13-card
  grid below. Job titles suggest a split (Directors vs Heads/Managers) but that classification
  was **not** invented here.
- **Videos and maps.** Every project has an MP4 (`video_url`), a poster image
  (`video_poster_url`) and a Google Maps embed (`map_embed_url`). These are captured in
  `projects.json` but deliberately excluded from `image-manifest.json`.

## ⚠ Discrepancies flagged

1. **The live site contradicts itself on total capacity.**
   - Homepage: *"has installed more then 50MW of solar systems"*
   - About Us **and** Investors: *"has installed more than 300MW of solar systems"*
   Both strings are recorded verbatim in `stats.json → site_claimed`.
2. **Neither claim matches the published portfolio.** Summing every capacity badge in the
   Project Gallery gives **154.414 MW** across 34 projects — 3× the homepage's 50 MW and
   roughly half the About/Investors 300 MW. `stats.json` records the computed value and both
   claims side by side. **Get the real number from the client before publishing any headline
   MW figure on the new site.**
3. **Team size claim.** About Us says *"a dynamic board team of 30 individuals"* but the
   Our Team section shows only **15** people.
4. **Founder name mismatch.** Every page's hidden SEO `<h1>`/`<h2>` names the founder and
   Managing Director as **"Dharmesh Patel"**, while the Our Team section lists the
   Chairman & MD as **"Dharmesh Makadiya"**. `leadership.json` uses the Our Team value
   (Makadiya) since that is the visible, structured content. Worth confirming with the client.
5. **Geography.** All 34 projects are in **Gujarat** — `states_covered` is a single-item list.
   Any "pan-India" / multi-state claim on the new site would not be supported by this data.
6. **Inactive Twitter/X account.** `https://twitter.com/DeonEnergy` exists in the markup but is
   **commented out** on every page, so it is not rendered. It is recorded under
   `social_inactive`, not `social`. Confirm before re-enabling.
7. **Second phone number.** `+91 9737399977` likewise appears only inside commented-out
   header markup. The only live number is the toll-free **18008905933**. Both are in
   `company.json → phone`, with the situation explained in `phone_notes`.
8. **Copyright year.** Footer already reads "© Copyright 2026".
