# AGENTS.md - Morimoflix v9.0 Theme

# Available specialists:

- ui-design.md
- secuirty.md
- penetration.md

**When checking for Penetration,Refer to penetration.md**.
**When testing for Security and Vulnerabilities,Refer to security.md**
**When designing UI and Fronted,Refer to ui-desgin.md.**

## Site Information

- **Primary Site:** https://tgmovies.morimoflix.xyz
- **Brand:** TGMovies by MorimoFlix
- **Niche:** Movie/TV streaming & download platform
- **Content:** 231+ movies imported via TMDB
- **Adult Site:** https://adult.morimoflix.xyz
- **Anime Site:** https://hentaidekho.mom
- **Telegram:** https://t.me/MorimoFlix_Zone

## Critical Facts

- **Theme Name:** Morimoflix Flicker
- **Text domain:** `morimoflix-flicker` (not `morimoflix`)
- **Version:** `5.1.0` (in `style.css` header AND `functions.php` `MORIMOFLIX_VERSION` — keep in sync)
- **PHP:** 7.4+ required
- **Structure:** Flat — ALL PHP functions in `functions.php`. No `inc/`, `production/`, or `templates/` directories.
- **CSS:** Tailwind CDN in `header.php` `<style>` block. `style.css` contains ONLY the WordPress theme header comment — no CSS rules.
- **JS:** Single file at `assets/js/main.js`. Additional inline JS in `header.php` (movie request modal) and `footer.php` (mobile menu, welcome popup).

## ⚠️ Mobile Users Priority (CRITICAL)

**This site has MORE mobile users than desktop.** Every code change MUST be checked for mobile (≤640px viewport) before shipping.

### Mobile Checklist (run before every version bump)

- [ ] Notification bars don't overflow on 375px screens
- [ ] Font sizes are readable (min 11px) on mobile
- [ ] Buttons/touch targets are at least 26px
- [ ] No horizontal scroll appears on mobile
- [ ] Text doesn't get clipped or pushed off-screen
- [ ] Icons (Material Symbols) load and display correctly
- [ ] Padding/margins don't crowd the close (×) button
- [ ] Flex containers use `flex-wrap` or `min-width:0` for shrinking

### Mobile Fix Pattern

When desktop styles don't fit mobile, add a `<style>` block with `@media (max-width: 640px)`:

```html
<style>
@media (max-width: 640px) {
    #elementId {
        padding: 10px 50px 10px 14px !important;
        font-size: 11px !important;
    }
}
</style>
```

Use `!important` to override inline styles. Place the `<style>` block in the same file as the HTML element.

## Versioning (CRITICAL)

**Version format:** `morimoflix-v8`, `morimoflix-v8.1`, `morimoflix-v8.2`, `morimoflix-v8.3`, ...

| Rule | Description |
|------|-------------|
| Base version | `v8` — current working folder/zip name |
| Minor bumps | `v8.1`, `v8.2`, `v8.3` — after each code change |
| Major bumps | `v9`, `v10` — only for major rewrites |
| **NEVER** create new folders | Always work in the SAME folder (`morimoflix-v8-final/`) |
| **NEVER** create multiple zips | Only ONE zip file at a time |
| How to version | Rename the zip: `morimoflix-v8.zip` → `morimoflix-v8.1.zip` |
| AGENTS.md title | Must reflect current version: `# AGENTS.md - Morimoflix v9.0 Theme` |
| File Structure path | Must reflect current version: `morimoflix-v9.0/` |
| Current version | **v9.0** (Fixed taxonomy pagination — genre & actor pages) |

### Version Bump Workflow

1. Make code changes inside `morimoflix-v8-final/`
2. Delete the old zip: `Remove-Item "C:\Users\MR.PC\Desktop\theme\morimoflix-v8.6.zip"`
3. Bump version in AGENTS.md title and file structure path
4. Recreate zip with bumped name: `morimoflix-v9.0.zip`
5. No new folders. No extra copies. One zip only.

### Zip Packaging Command

```powershell
# After every change, bump version and recreate zip
Set-Location "C:\Users\MR.PC\Desktop\theme\morimoflix-v8-final"
Compress-Archive -Path "style.css","functions.php","header.php","footer.php","front-page.php","index.php","single.php","single-movie.php","search.php","archive.php","page.php","page-list.php","taxonomy-genre.php","taxonomy-actor.php","assets","AGENTS.md","README.md" -DestinationPath "C:\Users\MR.PC\Desktop\theme\morimoflix-v9.0.zip" -Force
```

## File Structure

```
morimoflix-v9.0/
├── style.css              # WordPress theme header ONLY (no CSS rules!)
├── functions.php          # ALL PHP: CPTs, taxonomies, meta boxes, enqueue, helpers, AJAX
├── header.php             # Tailwind config, <style> block, navbar, mobile menu, modals + JS
├── footer.php             # Footer, Telegram button, welcome popup, mobile menu JS, wp_footer()
├── front-page.php         # Static front page: hero + filter + grid + pagination + categories
├── index.php              # Homepage fallback: hero + filter + grid + pagination + categories
├── single-movie.php       # Movie detail: poster, meta, description, downloads, related
├── single.php             # Blog post template
├── search.php             # Search results
├── archive.php            # Archive pages
├── page.php               # Static page template
├── page-list.php          # A-Z Library template (Template Name: A-Z Library)
├── taxonomy-genre.php     # Genre archive
├── taxonomy-actor.php     # Actor archive
├── assets/
│   ├── index.php          # Silence is golden (WordPress security)
│   └── js/
│       ├── index.php      # Silence is golden (WordPress security)
│       └── main.js        # Mobile menu toggle, keyboard shortcuts, resize handler
├── AGENTS.md              # This file
└── README.md              # Theme documentation
```

## File Sizes (v5.1.0)

| File | Size |
|------|------|
| functions.php | 36,353 B |
| header.php | 30,858 B |
| front-page.php | 18,964 B |
| index.php | 17,102 B |
| single-movie.php | 13,833 B |
| taxonomy-actor.php | 8,317 B |
| page-list.php | 8,204 B |
| footer.php | 6,659 B |
| search.php | 5,853 B |
| AGENTS.md | ~6,000 B |
| README.md | ~3,500 B |
| taxonomy-genre.php | 4,890 B |
| archive.php | 3,580 B |
| single.php | 1,241 B |
| page.php | 635 B |
| style.css | 497 B |
| assets/js/main.js | 1,684 B |

## Functions in functions.php

| Function | Purpose | Hook |
|----------|---------|------|
| `morimoflix_setup()` | Theme setup, image sizes | `after_setup_theme` |
| `morimoflix_register_post_types()` | Movie + TV CPTs | `init` |
| `morimoflix_register_taxonomies()` | Genre + Actor taxonomies | `init` |
| `morimoflix_rewrite_flush()` | Flush rewrite rules, auto-create List page | `after_switch_theme` |
| `morimoflix_enqueue_assets()` | Enqueue styles and scripts | `wp_enqueue_scripts` |
| `morimoflix_add_meta_boxes()` | Movie details meta box | `add_meta_boxes` |
| `morimoflix_movie_meta_callback()` | Meta box HTML render | — |
| `morimoflix_download_meta_box_callback()` | Download links meta box | — |
| `morimoflix_save_meta_boxes()` | Save meta box data | `save_post` |
| `morimoflix_get_movie_meta()` | Get movie meta with defaults | — |
| `morimoflix_get_backdrop()` | Get backdrop URL | — |
| `morimoflix_get_actor_link()` | Get actor taxonomy link | — |
| `morimoflix_search_filter()` | Search filter for movies/TV | `pre_get_posts` |
| `morimoflix_fix_front_page_request()` | Fix front page pagination | `pre_get_posts` |
| `morimoflix_excerpt_length()` | Custom excerpt length | `excerpt_length` |
| `morimoflix_excerpt_more()` | Custom excerpt more text | `excerpt_more` |
| `morimoflix_create_requests_table()` | Create movie requests DB table | `after_switch_theme` |
| `morimoflix_check_requests_table()` | Auto-recreate requests table if missing | `admin_init` |
| `morimoflix_submit_movie_request()` | AJAX handler for movie requests | `wp_ajax_*` |
| `morimoflix_add_requests_menu()` | Movie Requests admin page | `admin_menu` |
| `morimoflix_requests_page()` | Movie Requests admin page HTML | — |
| `morimoflix_localize_request_script()` | Localize AJAX URL for request modal | `wp_enqueue_scripts` |

## Z-Index Stack

| Element | z-index | File |
|---------|---------|------|
| Mobile search overlay | 200 | header.php |
| Welcome popup | 400 | footer.php |
| Movie request modal | 300 | header.php |
| Nav bar | 50 | header.php |
| Scanlines overlay | 40 | header.php |
| Noise texture | 50 | header.php |
| Mobile menu | 95 | header.php |
| Mobile menu overlay | 94 | header.php |
| Telegram button | 50 | footer.php |

## Viewport (Mobile)

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
```

CSS: `html, body { overflow-x: hidden !important; }` and `body { position: relative; }`

## WordPress Registration

| Item | Slug | Attached To |
|------|------|-------------|
| Movie CPT | `movie` | — |
| TV CPT | `tv` | — |
| Genre taxonomy | `genre` (hierarchical) | movie, tv |
| Actor taxonomy | `actor` (non-hierarchical) | movie, tv |

## Meta Keys

| Key | Field | Default |
|-----|-------|---------|
| `_movie_rating` | IMDb Rating | `8.0` |
| `_movie_duration` | Duration | `120M` |
| `_movie_quality` | Quality (4K/HD/SD) | `HD` |
| `_movie_language` | Language | `English` |
| `_movie_year` | Year | current year |
| `_movie_trailer` | Trailer URL | empty |
| `_movie_live` | Live checkbox | `no` |
| `_download_links` | Download textarea | empty |
| `_tmdb_cast` | Cast array | empty |
| `_tmdb_backdrop` | Backdrop URL | empty |
| `_movie_tagline` | Tagline | empty |

## Image Sizes

| Name | Width | Height | Crop |
|------|-------|--------|------|
| `morimoflix-hero` | 1920 | 585 | hard |
| `morimoflix-poster` | 400 | 600 | hard |
| `morimoflix-poster-large` | 800 | 1200 | hard |
| `morimoflix-thumbnail` | 300 | 170 | hard |

## Movie Requests Table

Custom table `{$prefix}movie_requests` created on theme activation. Auto-recreated on `admin_init` if missing.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint(20) | AUTO_INCREMENT, PRIMARY KEY |
| `movie_name` | varchar(255) | NOT NULL |
| `movie_year` | varchar(4) | NOT NULL |
| `imdb_link` | varchar(500) | DEFAULT '' |
| `user_ip` | varchar(45) | NOT NULL |
| `status` | varchar(20) | DEFAULT 'pending' |
| `created_at` | datetime | DEFAULT CURRENT_TIMESTAMP |

Admin page at `?page=movie-requests`. Supports bulk actions: mark_fulfilled, mark_pending, delete.

## Pagination

- **front-page.php** and **index.php**: Use `$_GET['paged']` (NOT `get_query_var()`)
- 18 posts per page

## A-Z Library (page-list.php)

- Template name: `A-Z Library`
- Auto-created on theme activation via `morimoflix_rewrite_flush()`
- URL: `/list/`
- Groups all movies/shows by first letter (0-9, A-Z)
- Sticky alphabet navigation bar
- Each section shows letter badge, divider, and title count
- Same movie card grid as homepage
- Back-to-top floating button on scroll

## Nav Links

- **Desktop**: Home (logo), LIST button (pink/magenta), REQUEST button (cyan)
- **Mobile menu**: Home, A-Z List, Request Movie
- LIST links to `/list/`
- REQUEST opens movie request modal

## Zip Packaging

Use explicit file list — never compress the parent folder:

```powershell
Set-Location "C:\Users\MR.PC\Desktop\theme\morimoflix-v8"
Compress-Archive -Path "style.css","functions.php","header.php","footer.php","front-page.php","index.php","single.php","single-movie.php","search.php","archive.php","page.php","page-list.php","taxonomy-genre.php","taxonomy-actor.php","assets","AGENTS.md","README.md" -DestinationPath "C:\Users\MR.PC\Desktop\theme\morimoflix-v8.zip" -Force
```

**Verify zip root** — `style.css` must be at the root, NOT inside a subfolder.

## Common Errors

| Error | Cause | Fix |
|-------|-------|-----|
| Stylesheet is missing | `style.css` has CSS rules, or PHP fatal error, or stray files | Keep `style.css` header-only; check for PHP errors; remove stray files |
| Page not found (List) | Page not created or permalink not flushed | Re-activate theme or create page manually; go to Settings → Permalinks → Save |
| Cannot redeclare function | Function in multiple files | All functions in `functions.php` only |
| 404 on pagination | Wrong `paged` usage | Use `$_GET['paged']` for homepage/front-page |
| Text domain mismatch | Mixed `'morimoflix'` and `'morimoflix-flicker'` | Use `'morimoflix-flicker'` everywhere |
| Welcome popup stuck open | JS error blocking closePopup | Check `localStorage` key `morimoflix_welcome_dismissed_at` |
| Mobile horizontal space | Pinch-to-zoom creates overflow | Viewport has `user-scalable=no`; CSS has `overflow-x:hidden` |
| Movie requests table missing | Table not auto-created | Deactivate and reactivate theme, or visit any admin page |

## Rules

1. **style.css** — Theme header comment ONLY. Never add CSS rules here.
2. **functions.php** — ALL PHP functions go here. No separate include files.
3. **header.php** — Tailwind CDN, Tailwind config, ALL custom CSS in `<style>` block, navbar, mobile menu, modals, inline JS.
4. **footer.php** — Footer markup, Telegram button, welcome popup, mobile menu/search JS, `wp_footer()`.
5. **No empty directories** — WordPress cannot copy empty folders.
6. **No duplicate functions** — Each function in ONLY ONE file.
7. **No stray files** — Don't leave binary README.md, .scss, .less, .map files in theme root.
8. **Every directory must have index.php** — WordPress security requirement.
9. **Mobile-first changes (CRITICAL)** — The site has more mobile users than desktop. ANY UI change made for desktop MUST also be tested/fixed for mobile (≤640px). Use `@media (max-width: 640px)` in `<style>` blocks, or mobile-specific Tailwind classes (`md:` prefix means desktop only). After every change, mentally check: "Will this work on a 375px wide screen?" If not, add responsive fixes.

## Coding Principles (Karpathy Guidelines)

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

### 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them — don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

### 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

### 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it — don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

### 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.
