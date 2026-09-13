# test.md — Morimoflix Flicker Theme (v8.7)

> Product documentation for smarter test generation. Covers all features, user flows, interactive elements, edge cases, and mobile-specific behaviors.

---

## 1. PAGE TEMPLATES

| Template | File | Loaded When | Key Features |
|----------|------|-------------|--------------|
| Front Page | `front-page.php` | Settings > Reading = static page | Hero carousel, share notification, movie grid, pagination, category browse |
| Homepage Fallback | `index.php` | No specific template matches | Same as front-page.php |
| Single Movie | `single-movie.php` | Viewing a `movie` or `tv` post | Blurred backdrop, poster, meta, downloads, related |
| A-Z Library | `page-list.php` | `/list/` (Template Name: A-Z Library) | Alphabet nav, grouped movies, back-to-top button |
| Archive | `archive.php` | Date/author/CPT archive | Title, grid, WP pagination |
| Search Results | `search.php` | `?s=query` | Search header, results grid, no-results state |
| Genre Archive | `taxonomy-genre.php` | `/genre/{slug}/` | Back arrow, genre title, grid, pagination |
| Actor Archive | `taxonomy-actor.php` | `/actor/{slug}/` | Back button, actor name, count, grid, custom pagination |
| Single Post | `single.php` | Blog post | Date, author, title, image, content |
| Static Page | `page.php` | Static page | Title, content |

---

## 2. USER FLOWS

### Flow 1: Browse Homepage
1. Land on homepage → share notification visible (cyan bar, top)
2. Share notification auto-dismisses after 60 seconds
3. Hero carousel auto-rotates every 2 seconds (5 latest movies)
4. Hover carousel → auto-slide pauses
5. Leave carousel → auto-slide resumes
6. Click prev/next buttons → manual navigation
7. Click carousel slide → navigates to single movie page
8. Scroll to filter bar → genre dropdown
9. Scroll through movie grid (18 per page)
10. Click movie card → single movie page
11. Click pagination → loads next page of results

### Flow 2: Download a Movie
1. Navigate to single movie page
2. Scroll past poster/meta section
3. Click "Download Link" anchor → jumps to `#download-section`
4. Select quality from dropdown (e.g., "4K ULTRA — 2.1 GB")
5. Click "Download Now" → opens download URL in new tab (`_blank`)

### Flow 3: Request a Movie
1. Click "REQUEST" button (desktop nav) or "Request Movie" (mobile menu)
2. Movie request modal opens (z-index 300)
3. Fill form: Movie Name (required), Release Year (required), IMDb/TMDB Link (optional)
4. Click "Submit Request"
5. Button disables, shows spinner + "Submitting..."
6. AJAX POST to `admin-ajax.php` with action `morimoflix_submit_movie_request`
7. On success: green message, modal auto-closes after 2 seconds, form resets
8. On error: red error message displayed
9. On complete: button re-enabled with original text

### Flow 4: Welcome Popup
1. New visitor (or 10+ minutes since last dismissal) sees welcome popup (z-index 400)
2. Body overflow set to `hidden`
3. "Request Now" → closes popup, opens movie request modal
4. "No Thanks" / close button / overlay click / Escape → closes popup
5. Timestamp stored in `localStorage` key `morimoflix_welcome_dismissed_at`
6. Cooldown: 600000ms (10 minutes)

### Flow 5: Search
1. Desktop: Type in navbar search input, submit → `/?s=query`
2. Mobile: Tap search icon → overlay opens (z-index 200) → type query → submit
3. Search results page shows matching `post`, `movie`, `tv` posts
4. Click result → navigates to single page
5. No results: "NO RESULTS FOUND" + query text + "Return Home" button

### Flow 6: A-Z Library
1. Click "LIST" button (desktop) or "A-Z List" (mobile menu)
2. Land on `/list/` — all movies grouped alphabetically
3. Sticky alphabet bar (z-index 30) → click letter to jump to `#letter-{letter}`
4. Scroll past 500px → back-to-top button appears (z-index 50)
5. Click movie card → single movie page

### Flow 7: Mobile Navigation
1. Tap hamburger icon → mobile menu slides in (z-index 95)
2. Menu: Home, A-Z List, Request Movie
3. Tap overlay / hamburger / Escape → menu closes
4. Resize > 1024px → auto-closes menu
5. "Request Movie" → closes menu, opens movie request modal

### Flow 8: Genre/Actor Browse
1. Click genre card on homepage → genre archive (`/genre/{slug}/`)
2. Click actor name (from single movie) → actor archive (`/actor/{slug}/`)
3. Both show filtered grid with pagination
4. Actor page has back button + "X Movies Found" count

---

## 3. INTERACTIVE ELEMENTS

### Hero Carousel
- **Auto-slide interval**: 2000ms (2 seconds)
- **Transition**: CSS opacity 700ms ease-in-out
- **Slides**: 5 latest `movie`/`tv` posts, date DESC
- **Navigation**: `#heroPrevBtn`, `#heroNextBtn` (chevron left/right)
- **Pause**: `mouseenter` stops auto-slide, `mouseleave` resumes
- **Mobile**: Excerpt hidden (`hidden md:block`), height 280px vs 450px desktop

### Share Notification
- **Position**: Between nav and carousel (document flow)
- **Color**: Cyan `#00dce6` background, black text
- **Close button**: Black circle with X icon
- **Auto-dismiss**: 60 seconds
- **Animation**: `translateY(-100%)` + `opacity: 0`, DOM removal after 300ms
- **Mobile overrides**: `@media (max-width: 640px)` reduces padding, font sizes, button size

### Movie Request Modal
- **Open triggers**: `#movieRequestBtn` (desktop), `#mobileMovieRequestBtn` (mobile menu)
- **Close triggers**: `#movieRequestClose`, `#movieRequestOverlay` click, form reset
- **Validation**: HTML5 `required` on name + year fields
- **AJAX**: `wp_ajax_morimoflix_submit_movie_request`
- **Nonce**: `morimoflix-request-nonce`
- **Database**: `{$wpdb->prefix}movie_requests`

### Welcome Popup
- **Auto-show**: On page load if localStorage allows (10-min cooldown)
- **Close triggers**: Close button, overlay, "No Thanks", Escape key
- **localStorage key**: `morimoflix_welcome_dismissed_at`
- **Cooldown**: 600000ms (10 minutes)
- **"Request Now"**: Closes popup → opens movie request modal

### Mobile Menu
- **Toggle**: Hamburger icon ↔ close icon
- **Width**: 75% max 320px
- **Close triggers**: Overlay click, hamburger toggle, Escape, resize > 1024px
- **Items**: Home, A-Z List, Request Movie

### Mobile Search Overlay
- **Open**: `#mobileSearchBtn`
- **Close**: `#mobileSearchClose` (back arrow), Escape key
- **Auto-focus**: Input focused after 100ms
- **z-index**: 200 (highest layer)

### A-Z Alphabet Nav
- **Behavior**: Sticky, `top-16 md:top-20`, `z-index: 30`
- **Letters**: `#` + A-Z, anchor links to `#letter-{letter}`
- **Active**: Primary color, hover primary bg
- **Inactive**: Neutral-600, `cursor-default`

### Back-to-Top Button
- **Visibility**: Hidden by default, shows when `scrollY > 500`
- **Position**: Fixed, `bottom-20 right-6`, `z-index: 50`
- **Style**: Primary bg, up arrow icon

### Pagination (Homepage)
- **Type**: Custom numbered (not WP default)
- **Window**: Current ± 2 pages
- **States**: Active (primary bg), disabled (prev on page 1, next on last)
- **Query param**: `$_GET['paged']` (NOT `get_query_var()`)

### Pagination (Archive/Search/Actor)
- **Type**: WordPress default `the_posts_pagination()`
- **Actor page**: Custom numbered with `get_pagenum_link()`

### Download Quality Selector
- **Element**: `<select>` dropdown
- **Format**: `Quality|URL|Size` per line in `_download_links` meta
- **Behavior**: `onchange` updates download button `href`
- **Fallback**: "No valid download links available yet."

---

## 4. AJAX ENDPOINTS

| Action | Handler | Auth | Nonce | Input | Output |
|--------|---------|------|-------|-------|--------|
| `morimoflix_submit_movie_request` | `morimoflix_submit_movie_request()` | Both | `morimoflix-request-nonce` | `movie_name`, `movie_year`, `imdb_link` | JSON message |
| `morimoflix_track_download` | `morimoflix_track_download()` | Both | None | `post_id` | JSON `{count}` |

### Localized Script Data

| Object | ajaxUrl | nonce |
|--------|---------|-------|
| `morimoflixData` | `admin_url('admin-ajax.php')` | `morimoflix-nonce` |
| `morimoflixRequest` | `admin_url('admin-ajax.php')` | `morimoflix-request-nonce` |

---

## 5. CUSTOM POST TYPES & TAXONOMIES

### Post Types

| CPT | Slug | Archive | Supports | Icon |
|-----|------|---------|----------|------|
| Movie | `movie` | `/movie/` | title, editor, thumbnail, excerpt, comments | dashicons-video-alt3 |
| TV Show | `tv` | `/tv/` | title, editor, thumbnail, excerpt, comments | dashicons-desktop |

### Taxonomies

| Taxonomy | Slug | Hierarchical | Attached To | Template |
|----------|------|-------------|-------------|----------|
| Genre | `/genre/` | Yes (like categories) | movie, tv | `taxonomy-genre.php` |
| Actor | `/actor/` | No (like tags) | movie, tv | `taxonomy-actor.php` |

---

## 6. META BOXES & META KEYS

### Movie Information Meta Box

| Field | Key | Default | Type |
|-------|-----|---------|------|
| Posted By | `_movie_posted_by` | Author display name | text |
| IMDb Rating | `_movie_rating` | `8.0` | text |
| Duration | `_movie_duration` | `120M` | text |
| Quality | `_movie_quality` | `HD` | select (HD/4K/SD) |
| Language | `_movie_language` | `''` | text |
| Year | `_movie_year` | Current year | text |
| Trailer URL | `_movie_trailer` | `''` | url |
| Live Status | `_movie_live` | `no` | checkbox |

### Download Links Meta Box

| Field | Key | Format |
|-------|-----|--------|
| Download Links | `_download_links` | `Quality|URL|Size` per line |

### Additional Meta Keys

| Key | Purpose | Default |
|-----|---------|---------|
| `_tmdb_cast` | TMDB cast array | `array()` |
| `_tmdb_backdrop` | TMDB backdrop URL | `''` |
| `_movie_tagline` | Movie tagline | `''` |
| `_total_downloads` | Download counter | `0` |

---

## 7. IMAGE SIZES

| Name | Width | Height | Crop | Usage |
|------|-------|--------|------|-------|
| `morimoflix-hero` | 1920px | 585px | Hard | Hero carousel backgrounds |
| `morimoflix-poster` | 400px | 600px | Hard | Poster cards (2:3 ratio) |
| `morimoflix-poster-large` | 800px | 1200px | Hard | Large poster displays |
| `morimoflix-thumbnail` | 300px | 170px | Hard | Thumbnail images (16:9) |

---

## 8. Z-INDEX STACK

| z-index | Element | File |
|---------|---------|------|
| 400 | Welcome popup | `footer.php` |
| 300 | Movie request modal | `header.php` |
| 200 | Mobile search overlay | `header.php` |
| 95 | Mobile menu | `header.php` |
| 94 | Mobile menu overlay | `header.php` |
| 50 | Navbar, Share notification, Telegram button | `header.php`, `index.php`/`front-page.php`, `footer.php` |
| 40 | Scanlines overlay | `header.php` |
| 30 | A-Z alphabet nav | `page-list.php` |
| 20 | Hero carousel nav buttons | `index.php`/`front-page.php` |
| 10 | Active hero slide | `index.php`/`front-page.php` |
| 0 | Inactive hero slides | `index.php`/`front-page.php` |

---

## 9. CSS & JS

### CSS
- **Tailwind CDN**: `cdn.tailwindcss.com` loaded in `header.php`
- **Custom CSS**: In `<style>` block in `header.php` (~400 lines)
- **`style.css`**: WordPress theme header comment ONLY (no CSS rules)
- **Key classes**: `.logo-text`, `.scanlines`, `.poster-aspect`, `.poster-frame`, `.neon-glow-cyan`, `.neon-glow-magenta`, `.brutalism-orange-pink`, `.glass-card`, `.node-card-border`, `.related-card`
- **Global**: `html,body{overflow-x:hidden!important}`, `body{position:relative}`

### JavaScript

| Location | Functionality |
|----------|--------------|
| `assets/js/main.js` | Mobile menu toggle, resize handler, console branding |
| `header.php` (inline) | Movie request modal, hero carousel JS |
| `footer.php` (inline) | Mobile menu toggle, mobile search overlay, welcome popup |
| `index.php` / `front-page.php` (inline) | Share notification auto-dismiss + close |
| `page-list.php` (inline) | Back-to-top button scroll listener |

---

## 10. ADMIN FEATURES

### Movie Requests Admin Page
- **Menu**: "Movie Requests" (position 30, `manage_options`)
- **URL**: `admin.php?page=movie-requests`
- **Filters**: All / Pending / Fulfilled with counts
- **Bulk actions**: Mark as Fulfilled, Mark as Pending, Delete
- **Columns**: Checkbox, Movie Name, Year, Link, IP Address, Date, Status
- **Status badges**: Pending (orange), Fulfilled (green)
- **Mobile**: Custom CSS for horizontal scroll + larger touch targets

### Database Table
- **Name**: `{$wpdb->prefix}movie_requests`
- **Created**: On theme activation (`after_switch_theme`)
- **Auto-recreated**: On `admin_init` if missing
- **Columns**: id, movie_name, movie_year, imdb_link, user_ip, status, created_at

### Meta Boxes
- **Movie Information**: On movie + tv edit screens (high priority)
- **Download Links**: On movie + tv edit screens (default priority)

---

## 11. TEST SCENARIOS

### A. Navigation Tests
1. Click logo → navigates to homepage
2. Click LIST button → navigates to `/list/`
3. Click REQUEST button → opens movie request modal
4. Click hamburger (mobile) → mobile menu opens
5. Click A-Z List in mobile menu → navigates to `/list/`, menu closes
6. Click Request Movie in mobile menu → modal opens, menu closes
7. Click overlay (mobile menu) → menu closes
8. Press Escape (mobile menu) → menu closes
9. Resize > 1024px (mobile menu open) → menu auto-closes
10. Click search icon (mobile) → search overlay opens
11. Click back arrow (mobile search) → search overlay closes
12. Press Escape (mobile search) → search overlay closes

### B. Hero Carousel Tests
1. Carousel shows 5 latest movies (date DESC)
2. Auto-slides every 2 seconds
3. Hover carousel → auto-slide pauses
4. Leave carousel → auto-slide resumes
5. Click prev button → goes to previous slide
6. Click next button → goes to next slide
7. Click slide → navigates to movie page
8. First slide shows on load (opacity 100%, z-index 10)
9. Inactive slides are invisible (opacity 0%, z-index 0)
10. Transition takes 700ms

### C. Share Notification Tests
1. Cyan bar visible on page load
2. Share icon displays
3. "SHARE WITH YOUR FRIENDS" text visible
4. Close button (black circle with X) clickable
5. Click close → notification slides up and fades out (300ms)
6. Auto-dismiss after 60 seconds
7. Notification removed from DOM after fade-out
8. Mobile: reduced font size (11px), reduced padding, smaller close button

### D. Movie Grid Tests
1. Grid shows 2 columns on mobile, 4 on md, 5 on lg, 6 on xl
2. Movie cards show poster image
3. Movie cards show type badge ("Movie" or "Show")
4. Movie cards show quality badge ("4K" or "HD")
5. Movie cards show title (truncated, uppercase)
6. Movie cards show IMDb rating
7. Movie cards show duration
8. Hover → poster scales 110%, border glows cyan
9. Click card → navigates to single movie page
10. Empty state → "No Poster" placeholder
11. Pagination shows correct page numbers
12. Active page has primary bg + glow shadow
13. Prev disabled on page 1, Next disabled on last page

### E. Filter Tests
1. "ALL" tab shows total movie count (e.g., "ALL (231)")
2. Click ALL tab → homepage with all posts
3. Sort dropdown lists all genres
4. Select genre → navigates to genre archive

### F. Single Movie Page Tests
1. Blurred backdrop image visible
2. Poster image shows (grayscale → color on hover)
3. Genre tags displayed
4. Title displayed with gradient dot
5. Tagline displayed (if exists)
6. Description excerpt (50 words) displayed
7. Metadata row: Rating, Duration, Year, Quality, Language, Posted By
8. "Download Link" button → scrolls to `#download-section`
9. Description section shows full content
10. Download section shows quality dropdown
11. Select quality → download button href updates
12. Click "Download Now" → opens URL in new tab
13. No download links → "No valid download links available yet."
14. Related movies section shows 4 random same-type posts
15. Related movie cards clickable

### G. Movie Request Modal Tests
1. Modal opens on REQUEST button click
2. Modal opens on Request Movie click (mobile menu)
3. Close button closes modal
4. Overlay click closes modal
5. Form has Movie Name (required), Year (required), IMDb Link (optional)
6. Submit with empty name → validation error
7. Submit with empty year → validation error
8. Submit valid form → button disables + spinner
9. AJAX success → green message + auto-close after 2s
10. AJAX error → red error message
11. AJAX complete → button re-enables

### H. Welcome Popup Tests
1. Popup appears on first visit
2. Popup has movie icon, heading, description
3. "Request Now" → closes popup → opens movie request modal
4. "No Thanks" → closes popup
5. Close button → closes popup
6. Overlay click → closes popup
7. Escape key → closes popup
8. localStorage stores dismissal timestamp
9. Revisit within 10 minutes → popup does NOT appear
10. Revisit after 10 minutes → popup appears again
11. Body overflow hidden when popup visible

### I. Search Tests
1. Desktop: type in navbar search → submit → search results page
2. Mobile: tap search icon → overlay opens → type → submit → results page
3. Search includes `post`, `movie`, `tv` types
4. Results show matching movie cards
5. Non-movie results show post type + date
6. No results → "NO RESULTS FOUND" + query + "Return Home" button
7. Mobile search overlay auto-focuses input after 100ms

### J. A-Z Library Tests
1. `/list/` page loads with all movies
2. Alphabet nav bar sticky at top
3. Active letters are clickable, inactive are muted
4. Click letter → scrolls to `#letter-{letter}` section
5. Each section shows letter badge + count + grid
6. Movies grouped by first character (0-9 → `#`, A-Z → letter)
7. Back-to-top button appears after scrolling 500px
8. Click back-to-top → scrolls to top
9. Empty state → "NO CONTENT FOUND"

### K. Genre/Actor Archive Tests
1. Genre archive: back arrow → homepage
2. Genre archive: genre title + description displayed
3. Genre archive: movie grid with pagination
4. Actor archive: back button → `history.back()`
5. Actor archive: actor name + movie count displayed
6. Actor archive: movie grid with custom pagination
7. Empty state → "NO MOVIES FOUND" + "Go Back" button

### L. Mobile-Specific Tests
1. Notification bar doesn't overflow on 375px screen
2. Font sizes ≥ 11px on mobile
3. Touch targets ≥ 26px
4. No horizontal scroll on any page
5. Mobile menu doesn't overlap content
6. Mobile search overlay covers full screen
7. Modal is usable on mobile (form fields accessible)
8. Download dropdown works on mobile
9. Hero carousel height is 280px on mobile
10. Poster cards display in 2-column grid on mobile

### M. Pagination Tests
1. Homepage: custom numbered pagination works
2. Homepage: `?paged=` parameter preserved with filter
3. Homepage: prev/next disabled states correct
4. Archive: WordPress default pagination works
5. Actor: custom numbered pagination works
6. 404 on `?paged=2` is prevented by `morimoflix_fix_front_page_request()`

### N. Admin Tests
1. Movie Requests page accessible at `?page=movie-requests`
2. Status filters work (All/Pending/Fulfilled)
3. Bulk actions work (Mark Fulfilled, Mark Pending, Delete)
4. Status badges display correctly (orange/green)
5. Mobile: table scrolls horizontally
6. Mobile: touch targets are larger
7. Movie meta boxes save correctly
8. Download links format: `Quality|URL|Size` per line
9. Theme activation creates `movie_requests` table
10. Deactivated/reactivated theme recreates table if missing

### O. Edge Case Tests
1. No movies in database → empty state messages display
2. Movie with no thumbnail → "No Poster" placeholder
3. Movie with no downloads → "No valid download links available yet."
4. Movie with no genre → genre section hidden
5. Movie with no tagline → tagline hidden
6. Movie with no excerpt → excerpt section hidden
7. Single character search query → results page loads
8. Very long movie title → truncated with CSS `truncate`
9. Multiple rapid carousel clicks → no animation glitch
10. Multiple rapid modal open/close → no state conflict
11. localStorage disabled → welcome popup still shows (no error)
12. JavaScript disabled → notification still visible, carousel static

---

## 12. FILE SIZES (v8.7)

| File | Size |
|------|------|
| functions.php | 37,052 B |
| header.php | 33,509 B |
| front-page.php | 22,874 B |
| index.php | 20,518 B |
| single-movie.php | 13,597 B |
| taxonomy-actor.php | 8,317 B |
| page-list.php | 8,204 B |
| footer.php | 6,659 B |
| search.php | 5,853 B |
| AGENTS.md | ~12,000 B |
| README.md | 3,428 B |
| taxonomy-genre.php | 4,890 B |
| archive.php | 3,580 B |
| single.php | 1,241 B |
| page.php | 635 B |
| style.css | 497 B |
| assets/js/main.js | 1,684 B |
| assets/js/index.php | 27 B |
| assets/index.php | 27 B |

---

## 13. CRITICAL RULES

1. **Mobile-first**: More mobile users than desktop. Every UI change must work on 375px screens.
2. **No CSS in style.css**: Only WordPress theme header comment.
3. **All PHP functions in functions.php**: No separate include files.
4. **`$_GET['paged']`**: Homepage/front-page pagination uses `$_GET['paged']`, NOT `get_query_var()`.
5. **Nonce verification**: All AJAX handlers must check nonce.
6. **Text domain**: Always use `morimoflix-flicker`.
7. **One zip only**: Never create multiple zip files. Version bump = rename same zip.
8. **Every directory has index.php**: WordPress security requirement.
