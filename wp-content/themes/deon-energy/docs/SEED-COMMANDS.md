# Seeder Commands — Deon Energy

WP-CLI seed scripts. Run from the **theme directory** on the target install.

## ⭐ Demo Projects (QA fixtures)

3 fake `deon-demo-*` projects that fill **every** detail-page field (hero
image/video, story video, map, milestones, bento gallery, all specs). Use to
verify the project detail page renders. **Delete before launch.**

```bash
# CREATE / REFRESH the demo projects
wp eval-file bin/seed-demo-projects.php

# DELETE the demos + all their uploaded media
wp eval-file bin/seed-demo-projects.php --remove
```

- Idempotent — re-running wipes each demo + its media, then recreates cleanly.
- Separate from the real portfolio (`seed-projects.php`).

## Running on this machine (local Herd install)

No global `wp` binary here. WordPress lives at `~/Herd/deon` (theme symlinked in).
Use Herd's PHP + a wp-cli.phar:

```bash
cd ~/Herd/deon
PHP="$HOME/Library/Application Support/Herd/bin/php"
# one-time: curl -sSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /tmp/wp-cli.phar

# create/refresh
"$PHP" /tmp/wp-cli.phar eval-file wp-content/themes/deon-energy/bin/seed-demo-projects.php --path="$HOME/Herd/deon"

# delete
"$PHP" /tmp/wp-cli.phar eval-file wp-content/themes/deon-energy/bin/seed-demo-projects.php --remove --path="$HOME/Herd/deon"
```

Local site URL: http://deon.test

## Other seeders (reference)

| Command | What it seeds |
|---|---|
| `wp eval-file seed-projects.php` | Real 34-project portfolio (title/capacity/location/type). `--force` to wipe + reseed. |
| `wp eval-file bin/seed-demo-projects.php` | **QA demo projects** (above). |
| `wp eval-file bin/seed-milestones.php` | About page Growth Timeline milestones. |
| `wp eval-file bin/seed-leaders.php` | Leadership (founders / board / team). |
| `wp eval-file bin/seed-offices.php` | Contact page office locations. |
| `wp eval-file bin/seed-posts.php` | 6 blog posts (Knowledge Hub). |
| `wp eval-file bin/seed-knowledge-hub.php` | Case studies + press + documents. |
| `wp eval-file bin/seed-investor-docs.php` | Investor documents + categories. |
| `wp eval-file bin/seed-faq.php` | Calculator/FAQ entries. |
| `wp eval-file bin/seed-legal-pages.php` | Privacy Policy + Terms of Use pages (footer legal links). |
| `wp eval-file bin/seed-news-media-demo.php` | News & Media demo docs. |
| `wp eval-file bin/seed-technical-papers-demo.php` | Technical papers demo. |
| `wp eval-file bin/build-primary-menu.php` | Builds the primary nav menu. |

Most are idempotent. Demo/QA seeders (`*-demo`, `seed-demo-projects`) are dev
fixtures — clean them before production launch.

## Running from the WordPress root

The paths above assume the theme directory. From the WP install root, prefix
them with the theme path instead:

```bash
wp eval-file wp-content/themes/deon-energy/bin/seed-demo-projects.php
```
