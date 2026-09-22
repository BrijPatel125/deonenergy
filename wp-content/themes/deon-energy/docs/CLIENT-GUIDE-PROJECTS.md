# Client Guide — Adding & Editing Projects

How to add a project to the **Project Gallery** and control everything on its
detail page. No code needed — everything is in **wp-admin → Projects**.

## Add a new project

1. wp-admin → **Projects → Add New**.
2. **Title** — the project name (e.g. *Sonoran Sun Array*). Shows as the hero heading.
3. **Featured image** (right sidebar → *Featured image*) — the main project photo.
   Used as the gallery-card thumbnail and as a fallback hero/poster image.
4. **Project Type** (right sidebar) — pick or add a type (Rooftop, Utility Scale…).
   Drives the filter chips and the "Related Infrastructure" matching.
5. Fill the boxes below, then **Publish**.

## Project description (rich text)

Write the description in the **main editor** (the big content area under the title) —
it is a full rich-text editor. You can:

- **Bold**, *italic*, headings, bullet/numbered lists, links, blockquotes.
- Add images inline with the **Add Media** button.
- Switch to the **Text** tab for raw HTML if needed.

Leave it empty to hide the "Overview" section entirely. The first heading in the
reference design ("Architecting the future of desert energy.") is simply the first
line of this description — style it as a Heading if you want that large look.

## Project Details (right sidebar box)

Only the rows you fill appear on the page. Key ones:

- **Capacity** — e.g. `45.0 MW` (shows in the hero fact row and cards).
- **Location** — e.g. `Phoenix, AZ` (hero fact row, map heading, cards).
- **Project Status** — e.g. `Operational` (hero fact row).
- Commissioned, Grid Voltage, Annual Output, Technology, EPC Partner, Client — optional.

## Hero Background (image / video) — optional

Box: **Hero Background (image / video)**.

- **Type = None** → plain colour hero (default).
- **Type = Image** → pick/upload a background photo shown behind the hero title.
- **Type = Video** → either upload an MP4 or paste a YouTube/Vimeo URL (add a poster
  image for the best first paint). The video plays muted/looping behind the title.

Leave it on **None** to keep the clean text hero.

## Project Media & Story box

- **Story Video** — one cinematic video for the page. Paste a **YouTube/Vimeo link**
  or **Upload MP4**. Add a **Caption** (e.g. *The Art of Infrastructure*) shown over
  the poster. Optional **poster image** (used for uploaded MP4s). Leave the URL blank
  to hide the video section.
- **Location Map** — paste a **Google Maps** link or embed URL:
  open Google Maps → find the site → **Share → Embed a map → Copy HTML** (paste the
  whole thing, we extract the map) *or* just paste the normal share link. Blank = no map.
- **Technical Milestones** — the numbered **01 / 02 / 03** build steps. Click
  **+ Add Milestone**, enter a **title** and a one-line **description** for each,
  drag none needed — they number automatically. Remove a row with the **×**.
  Leave empty to hide the section.

## Project Gallery box (bento photo grid)

- Click **Add / Edit Images** and select multiple photos.
- **Drag thumbnails to reorder** — order controls the bento layout (first image gets
  the large tile). Remove with the **×** on a thumbnail.
- On the page they render as an uneven **bento grid** with a click-to-zoom lightbox.
  Photos are cropped to fill their tile, so pick images that look good cropped.

## Ordering projects in the gallery

The gallery and related lists sort by **Order** (right sidebar → *Page Attributes → Order*,
lower number first). Set it to control which projects appear first.

## What shows vs. stays hidden

Every optional section **auto-hides** when its field is empty — an empty video URL,
no milestones, no map, or no gallery images simply removes that block. So a minimal
project (title + description + featured image) still looks complete.
