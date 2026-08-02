<?php
/**
 * The part of <head> that is byte-for-byte identical across every root
 * page: charset/viewport, font preloads, the two framework CSS chunk
 * links, our own style.css and dropdown.css, and the hydration-script
 * preload hint. Verified against all 54 root .php pages before extracting
 * — see the per-page <title>/description/canonical/OG/twitter tags and
 * the async script-chunk list, which legitimately differ per page (code
 * splitting) and stay inline in each page's own <head>.
 */
?><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" href="_next/static/media/83afe278b6a6bb3c-s.p.0q-301v4kxxnr53b6.woff2?dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u" as="font" crossorigin="" type="font/woff2"/><link rel="preload" href="_next/static/media/f7aa21714c1c53f8-s.p.0bhxxck2.9j9153b6.woff2?dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u" as="font" crossorigin="" type="font/woff2"/><link rel="preload" href="fonts/satoshi-700.woff2" as="font" crossorigin="" type="font/woff2"/><link rel="preload" href="fonts/satoshi-400.woff2" as="font" crossorigin="" type="font/woff2"/><link rel="preload" as="image" imageSrcSet="/_next/image/?url=%2Fimages%2Flogo-ecomcircles.png&amp;w=640&amp;q=75&amp;dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u 1x, /_next/image/?url=%2Fimages%2Flogo-ecomcircles.png&amp;w=1080&amp;q=75&amp;dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u 2x"/><link rel="stylesheet" href="_next/static/chunks/17gktt4yfx6k753b6.css?dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u" data-precedence="next"/><link rel="stylesheet" href="_next/static/chunks/087dhsw_ljdag53b6.css?dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u" data-precedence="next"/><link rel="stylesheet" href="style.css"/><link rel="stylesheet" href="dropdown.css"/><link rel="preload" as="script" fetchPriority="low" href="_next/static/chunks/0wam_s8s.fo9f53b6.js?dpl=dpl_AAfjYFNTKKor3QnnhYRaN9JQwV9u"/>
