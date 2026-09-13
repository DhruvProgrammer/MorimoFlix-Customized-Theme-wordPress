# Morimoflix Flicker Theme

A cyber-cinematic media discovery WordPress theme with neon aesthetics, Tailwind CSS, and TMDB integration.

## Version

5.1.0

## Requirements

- WordPress 5.9+
- PHP 7.4+
- TMDB API Key (for movie importer plugin)

## Theme Structure

```
morimoflix-v8/
├── style.css              # WordPress theme header ONLY (no CSS rules)
├── functions.php          # ALL PHP: CPTs, taxonomies, meta boxes, enqueue, helpers, AJAX
├── header.php             # Tailwind CDN config, <style> block, navbar, mobile menu, modals
├── footer.php             # Footer, Telegram button, welcome popup, inline JS
├── front-page.php         # Static front page: hero + filter + grid + pagination + categories
├── index.php              # Homepage fallback: hero + filter + grid + pagination + categories
├── single-movie.php       # Movie detail: poster, meta, description, downloads, related
├── single.php             # Blog post template
├── search.php             # Search results
├── archive.php            # Archive pages
├── page.php               # Static page template
├── page-list.php          # A-Z Library template (alphabetical movie listing)
├── taxonomy-genre.php     # Genre archive
├── taxonomy-actor.php     # Actor archive
├── assets/
│   └── js/
│       └── main.js        # Mobile menu, keyboard shortcuts, resize handler
└── AGENTS.md              # Development guidelines
```

## Key Features

- **Tailwind CSS CDN** — All styling via Tailwind utility classes + custom `<style>` block
- **TMDB Movie Importer** — Search, single/bulk import movies with A-Z language selection
- **Movie Requests** — Users can request movies via modal form
- **A-Z Library** — Alphabetical listing of all movies/shows
- **Welcome Popup** — "Can't find your movie?" popup with 10-min cooldown
- **Telegram Integration** — Floating Telegram button
- **Mobile Responsive** — Full mobile menu, search overlay, touch-friendly
- **Pinch-zoom disabled** — Prevents horizontal overflow on mobile

## Installation

1. Go to **Appearance → Themes → Add New → Upload Theme**
2. Upload `morimoflix-v8.zip`
3. Click **Install Now** then **Activate**
4. Go to **Settings > Permalinks** > Click **Save Changes**
5. Create a page called "List" with the "A-Z Library" template

## TMDB Importer Plugin

1. Go to **Plugins → Add New → Upload Plugin**
2. Upload `tmdb-movie-importer.zip`
3. Activate the plugin
4. Go to **TMDB Importer** in admin menu
5. Enter your TMDB API key (get from https://www.themoviedb.org/settings/api)

## Customization

### Adding Movies
- Use **TMDB Importer** → Search & Import (single or bulk)
- Or manually create Movie posts with meta fields

### Movie Meta Fields
| Field | Key | Default |
|-------|-----|---------|
| Rating | `_movie_rating` | 8.0 |
| Duration | `_movie_duration` | 120M |
| Quality | `_movie_quality` | HD |
| Language | `_movie_language` | English |
| Year | `_movie_year` | current year |
| Tagline | `_movie_tagline` | empty |
| Trailer | `_movie_trailer` | empty |
| Backdrop | `_tmdb_backdrop` | empty |
| Cast | `_tmdb_cast` | empty |
| Download Links | `_download_links` | empty |

### Download Links Format
```
Quality|URL|Size
4K ULTRA|https://example.com/file.mp4|2.1 GB
```

## Credits

- Theme by MorimoFlix
- https://cinealert.in
