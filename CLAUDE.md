# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repository is

This is a **static HTTrack mirror** of the live Next.js site ecomcircles.com (an all-in-one platform for
Amazon/Walmart/Mirakl sellers: repricer, inventory management, sourcing, order management, warehouse/fulfillment
services, account acquisition). It is not the Next.js application source — there is no `package.json`, no build
step, no framework source code, and no test suite. Every `.html` file is compiled/exported output that HTTrack
downloaded from the live site (see the `<!-- Mirrored from ... by HTTrack Website Copier -->` comment at the top
and bottom of each page). Editing happens directly on the static HTML/CSS/JS files.

## Running / previewing

**The root pages are now PHP and require a PHP server** — Live Server (static) will serve the raw source instead
of executing the includes. Preview with:

```
php -S localhost:8000
```

then open `http://localhost:8000/index.php`. There is still no build step. The `.vscode/settings.json` Live
Server config (port 5502) remains but only renders the nested `.html` pages correctly.

All internal links are relative (`blog.php`, `blog/<slug>/index.html`), so the site works from any server root
without a base path.

## Directory layout

- `index.php` — homepage. **All 54 root-level pages are `.php`** (`software.php`, `repricer.php`, `blog.php`, …);
  every one of them pulls in the shared chrome with
  `<?php include __DIR__ . '/include/header.php'; ?>` / `.../footer.php` in place of its own copy.
  Nested pages (`blog/<slug>/index.html`, `prep-centers/<slug>/index.html`) are still plain `.html` and still
  embed their own header/footer — edit those there, or convert them the same way.
- `include/testimonials.php`, `include/faq.php` — data-driven section partials. `testimonials.php` renders the
  "Loved by eCommerce Sellers" cards (used by `amazon.php`, `walmart.php`); override `$testimonials`,
  `$testimonials_heading`, `$testimonials_sub` before including, or take the defaults. `faq.php` renders the
  "Common Questions" accordion from a required `$faq_questions` array (used by `order-management`, `extension`,
  `warehouse`, `sourcing`, `repricer`, `inventory-management`, `compare`, `amazon-fba-leads`); its styling is
  fixed at `pg-sec bg-shades` on purpose so every FAQ matches. Both `unset()` their variables at the end.
  Still on their own copies: `index.php`'s testimonials (different design/copy — filled star icons, `data-aos`,
  "Matt B."), and the legacy-Tailwind FAQs on `software`, `services`, `walmart-estimated-sales`, `bulk-editing`,
  plus a second FAQ in `compare.php` that sits **outside `<main>`**.
- `include/header.php`, `include/footer.php` — **live, wired into every root page** (this supersedes the old
  "orphaned leftovers" note). `header.php` is the dark sticky nav (logo, Software/Services/Compare dropdown
  triggers, Pricing/Blogs/Contact, Login, Get Started, announcement bar); `footer.php` is the full link footer.
  Their links are written relative to the site root, so they only work for pages **at the root** — a nested page
  would need a different prefix. Change the nav in one place now, not 54.
- `dropdown.html` — deliberately left as `.html`; it is the standalone demo of the mega-menu component, not a
  site page.
- `_next/` — Next.js build artifacts (JS chunks, CSS chunks, optimized image cache under `_next/image/`). Hashed,
  framework-generated filenames referenced by `?dpl=...` query strings. Treat as opaque; do not hand-edit chunk
  contents.
- **Top-level section pages have been flattened out of their directories**: `blog`, `compare`, `contact`,
  `pricing`, `services`, `software`, `wfs-calculator` now live at the repo root (as `.php`, see above) instead of
  `blog/index.html`, `compare/index.html`, etc. Internal links and the shared nav in `include/header.php` point at
  the flat filenames — when adding a new top-level section, follow this flat pattern, not a subdirectory.
- The former `software/`, `services/`, and `compare/` trees have been **fully flattened, nested pages included**:
  every `software/**/index.html` (22 pages), `services/**/index.html` (13 pages), and `compare/<tool>/index.html`
  (7 pages) now lives at the repo root named after its old folder (`software/repricer/index.html` →
  `repricer.php`, `services/warehouse/fba-prep/index.html` → `fba-prep.php`, `compare/sellify/index.html` →
  `sellify.php`, etc.), and those directories were deleted. Internal links in the moved files and all references
  across the repo were rewritten to the flat names (visible HTML only — hydration payloads untouched, see gotcha
  below).
- Nested/per-item pages under the remaining directories were **not** flattened and still use `dir/slug/index.html`:
  `blog/<post-slug>/index.html` (96 posts) and `prep-centers/<slug>/index.html` (298 listings). These remain
  independent static exports of the same page template — a fix made in one page's markup does not propagate to
  the others.
- `privacy.php`, `tos.php`, `disclaimer.php` — flattened to the repo root (their old directories are gone);
  references across the repo were updated. `about/` and `affiliate-disclosure/` are still single static pages
  under their own directory (`about/index.html`, etc.) — not yet flattened.
- `images/`, `fonts/` — static assets referenced directly (not through `_next/image`).
- `style.css`, `script.js` — hand-maintained additions (not part of the mirror), linked from `index.php`'s
  `<head>`/end of `<body>`. This is the intended place for new custom CSS/JS going forward.
- **Tailwind/inline-style conversion (in progress)**: the `<main>` of the tool/service pages (sourcing, repricer,
  wholesale, amazon, amazon-fba-leads, walmart, inventory-management, extension, order-management,
  wfs-calculator, amazon-management, walmart-management, flipmine, buy/sell-amazon-seller-account,
  buy-walmart-account, get-approved-on-walmart, warehouse, fba-prep, wfs-prep, perp-center) has been converted
  to external CSS — no Tailwind utilities, no `style=""`. Classes: hand-written semantic `.pg-*` (shared page
  furniture: hero, sections, cards, FAQ, CTA, buttons), generated `.pgx-N` (compiled Tailwind class sets), and
  `.pgi-N` (extracted inline styles). All live in `style.css` and follow the green/dark theme (indigo accents map
  to `--color-primary`). When converting another page, reuse `.pg-*` for the shared template pieces.
- `perp-center.php` — the prep-center directory (was `prep-centers/index.html`); the 298 individual listings
  still live under `prep-centers/<slug>/index.html`.
- `dropdown.css`, `dropdown.js`, `dropdown.html` — hand-maintained dark-theme hover mega-menus for the
  "Software Tools" (two-column), "Services", and "Compare" (single-column, `.ec-mega--list`) header nav items.
  `dropdown.js` injects the panels into `<body>` and attaches hover listeners to the existing header nav items
  (hydration-safe; also hides the original React dropdowns via CSS). Wired into `index.php`; `dropdown.html` is
  a standalone demo of all three menus.
- `split_index.js`, `split_software.js` — leftovers from an earlier abandoned attempt at the same
  de-duplication (they sliced `<main>` out and wrote `.php` wrappers). Superseded by the include setup above;
  they are not part of the current workflow and can be deleted.

## Critical gotcha: every page embeds its content twice

Each page's `<body>` contains the server-rendered visible HTML (with inline `style="..."` attributes matching the
original React props) **and**, in a long series of `<script>self.__next_f.push([1, "..."])</script>` tags near
the end of `<body>`, a duplicate copy of the same content serialized as a React Server Components "Flight"
hydration payload. This is an indexed chunk protocol (e.g. `9:T11aa,` declares chunk `9` as a text chunk of hex
length `0x11aa` bytes) that the framework's JS chunks parse client-side to hydrate the DOM.

Practical consequences:

- **Editing only the visible HTML for anything React manages (styles, classes, image `src`/`srcset`, text inside
  interactive components) is not reliably safe.** If the corresponding component re-renders — mobile menu
  toggle, sticky header state, hero image carousel, testimonial cards, anything with client interactivity — React
  reconciles against the *original* data still sitting in the `__next_f.push` payload and can silently revert the
  HTML edit. This was confirmed by experiment in this repo: extracting inline styles into an external stylesheet
  looked correct on first paint but is not dependable once hydration/re-render happens.
- When an edit must survive hydration, update **both** copies: the literal attribute in the visible `<img>`/element
  *and* the matching value inside the `__next_f.push` JSON blob (search for the same string, e.g. an image
  filename, across both locations).
- Safe, hydration-proof ways to change behavior: add brand-new external `<link>`/`<script>` tags (like
  `style.css`/`script.js`), or attach new `addEventListener` handlers from your own script to existing DOM nodes —
  React hydration does not remove externally attached listeners or unrelated `<link>` tags.
- When reformatting/pretty-printing an HTML file (e.g. with Prettier), verify the `__next_f.push(...)` payload
  strings are byte-identical before/after by evaluating them (e.g. via Node's `vm` module) rather than diffing
  the reformatted text directly — quote-style/whitespace changes in the JS source are cosmetic, but any change to
  the *decoded* string values can break hydration.
- These files are also easy to corrupt by hand: watch for things like an attribute name getting split around its
  own value (e.g. `class="..."` mangled into `c...lass=""`) or `src`/`alt` pairs getting shuffled between
  sibling elements when editing repeated blocks (e.g. avatar images) — cross-check `alt` text against `src` after
  any edit to a repeated element group.

## Third-party scripts

GTM (`GTM-KMG8GFP`), PostHog, Rewardful (affiliate tracking), and Metricool are wired in via `next/script`
components and only appear inside the `__next_f.push` hydration payload (not as plain `<script id="...">` tags in
the raw HTML) — they are injected into the DOM client-side after hydration. Removing/changing them means editing
inside the payload, following the same rules above, and — since they're marketing/affiliate revenue tracking —
should not be done without explicit instruction.

## Known cleanup pattern

Stray files literally named `404` (no extension) sometimes appear inside `prep-centers/` subdirectories — leftover
artifacts from the HTTrack mirroring process, not real pages. Safe to delete on sight if a fresh mirror pull
reintroduces them.
