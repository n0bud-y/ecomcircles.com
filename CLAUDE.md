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

There is no dev server or build command. Preview pages by opening them directly or serving the folder statically
(the repo has a `.vscode/settings.json` configured for the VS Code **Live Server** extension on port 5502).
All internal links are relative (`services/index.html`, not `/services/`), so the site works from any static
file server or `file://` root without a base path.

## Directory layout

- `index.html` — homepage.
- `_next/` — Next.js build artifacts (JS chunks, CSS chunks, optimized image cache under `_next/image/`). Hashed,
  framework-generated filenames referenced by `?dpl=...` query strings. Treat as opaque; do not hand-edit chunk
  contents.
- `prep-centers/`, `blog/`, `services/`, `software/`, `compare/` — large sets of near-identical programmatic-SEO
  pages (298 prep-center listings, 96 blog posts, plus service/software/comparison subpages), each just its own
  `index.html`. These are independent static exports of the same page template with different content — a fix
  made in one page's markup does not propagate to the others.
- `about/`, `contact/`, `pricing/`, `privacy/`, `tos/`, `disclaimer/`, `affiliate-disclosure/` — single static
  pages.
- `images/`, `fonts/` — static assets referenced directly (not through `_next/image`).
- `style.css`, `script.js` — hand-maintained additions (not part of the mirror), linked from `index.html`'s
  `<head>`/end of `<body>`. This is the intended place for new custom CSS/JS going forward.

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
