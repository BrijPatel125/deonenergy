# Deon Energy — Redesign Spec (client change log v1, July 2026)

Binding contract for all redesign work. Source: `Website-Changes.pdf` (issues + header)
and `Revised_Website_Change_Log_v1/Changes.pdf` (consolidated brief). Where the two
disagree, the **revised** log wins (client confirmed).

Wave 0 (tokens, fonts, spacing, overflow guard) is **already applied**. Do not redo it.

## Palette — exactly four brand colours

| Token | Value | Use |
|---|---|---|
| `--color-accent` / `text-deon-accent` | `#F29020` | primary orange, buttons, icons, rules |
| `--color-surface` / `bg-white` | `#FFFFFF` | cards, white sections |
| `--color-dark` / `bg-deon-dark` | `#1D1B20` | footer, nav dropdown panel, standout bands |
| `--color-bg` / `bg-deon-bg` | `#fbf9f5` | off-white page + alternating sections |

`#f5f3ef` is retired — `--color-bg-warm` / `bg-deon-warm` now alias `#fbf9f5`.
Never hardcode a hex that isn't in this table. Use the token.

## Type

- Display face: **Plus Jakarta Sans** (`var(--font-head)` / `font-display`). Playfair Display is **removed** — do not reference it.
- Body face: **Inter** (`var(--font-body)` / `font-sans`).
- No serif anywhere. Avoid `italic` on `font-display` (no true italic cut; browsers synthesise a poor oblique).

### Scale — use the tokens, not arbitrary px

| Token | CSS var | Tailwind | Desktop cap |
|---|---|---|---|
| Hero / page H1 | `--fs-hero` | `text-hero` | 48px |
| Section H2 | `--fs-h2` | `text-h2` | 34px |
| Card H3 | `--fs-h3` | `text-h3` | 22px |
| Lead / intro | `--fs-lead` | `text-lead` | 17px |
| Body | `--fs-body` | (default) | 15px |
| Eyebrow | `--fs-eyebrow` | `text-eyebrow` | 13px |

The client's #1 complaint was "the whole site looks zoomed to 125–150%". When you
touch a file, **replace `text-[NNpx]` with the scale token** for anything that is a
heading, lead, or body. Leave genuinely fixed-size UI chrome (badges, 11–12px
labels, icon boxes) alone.

## Spacing

- Section padding: `var(--section-pad)` — now `clamp(40px, 4.4vw, 64px)` (was up to 84px). Tailwind pages: `py-[var(--section-pad)]`.
- Container: `var(--container-max)` = **1280px** (was 1440px), gutter `clamp(20px, 5vw, 64px)`.
- Hero sections must be **shorter** — no `min-h-screen`, no 80vh heroes. Target 380–480px desktop content height.
- Goal: more than one content block visible per screen on a 1366×768 laptop.

## Section rhythm

Alternate backgrounds down every page so it stops reading as one beige slab.
Helpers exist in `main.css`: `.deon-section--white`, `.deon-section--offwhite`,
`.deon-section--dark`. Tailwind pages: `bg-white` / `bg-deon-bg` / `bg-deon-dark`.
Use **one** dark band per page max (stats or CTA), as the standout.

## Horizontal scroll

`html, body { overflow-x: clip; max-width: 100% }` is in place as a guard, but it
only hides the symptom. If you find the real offender in a file you own
(full-bleed `100vw` element, negative margin, unclipped absolute, oversized grid),
fix it at the source and note it in your report.

## Non-negotiables

- Escape on output: `esc_html`, `esc_url`, `esc_attr`. Text domain `deon-energy`.
- `if ( ! defined( 'ABSPATH' ) ) exit;` in any template with logic.
- **Do not edit `functions.php`.** Put PHP registrations (CPT meta boxes, helper
  functions, nav walkers) in a new file under `inc/` and say so in your report —
  the main thread wires the `require`.
- **Do not edit `assets/css/main.css` `:root`** or `assets/css/tailwind.css`
  `@theme`. Those are Wave 0's and are shared.
- Phase 1 pages (Home, About, Solutions) = BEM CSS in `main.css`. All other pages =
  Tailwind utilities in PHP. Run `npm run build:tw` if you touched Tailwind classes.
- Never invent client data (MW figures, addresses, names). Real content is being
  scraped to `bin/data/*.json`. If it isn't there yet, use a clearly-marked
  placeholder and list it in your report.
