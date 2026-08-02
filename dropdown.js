/* ============================================================
   Dark header mega dropdowns ("Software Tools" + "Services") —
   attach to the existing React-hydrated header without editing
   its markup (hydration-safe: panels live in <body>, listeners
   are attached externally).
   Usage: <script src="dropdown.js" defer></script>  (+ dropdown.css)
   ============================================================ */
(function () {
  "use strict";

  /* relative prefix back to the site root, so the same script works
     from nested pages (blog/x/, prep-centers/x/, ...) if rolled out */
  function rootPrefix() {
    var parts = window.location.pathname.split("/").filter(Boolean);
    if (parts.length && /\.(html?|php)$/i.test(parts[parts.length - 1])) parts.pop();
    if (window.location.protocol === "file:") return "";
    return new Array(parts.length + 1).join("../");
  }

  function icon(name) {
    var shapes = {
      search: '<circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
      zap: '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
      dollar: '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
      box: '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
      clipboard: '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>',
      tag: '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
      calc: '<rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="11" x2="8" y2="11.01"/><line x1="12" y1="11" x2="12" y2="11.01"/><line x1="16" y1="11" x2="16" y2="11.01"/><line x1="8" y1="15" x2="8" y2="15.01"/><line x1="12" y1="15" x2="12" y2="15.01"/><line x1="16" y1="15" x2="16" y2="15.01"/><line x1="8" y1="19" x2="16" y2="19"/>',
      users: '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
      store: '<rect x="3" y="3" width="18" height="5" rx="1"/><path d="M5 8v11a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/><line x1="10" y1="13" x2="14" y2="13"/>',
      filecheck: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="9 15 11 17 15 13"/>',
      warehouse: '<path d="M22 21V8.5L12 3 2 8.5V21"/><path d="M6 21v-9h12v9"/><line x1="2" y1="21" x2="22" y2="21"/>',
      building: '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><line x1="8" y1="6" x2="8.01" y2="6"/><line x1="12" y1="6" x2="12.01" y2="6"/><line x1="16" y1="6" x2="16.01" y2="6"/><line x1="8" y1="10" x2="8.01" y2="10"/><line x1="12" y1="10" x2="12.01" y2="10"/><line x1="16" y1="10" x2="16.01" y2="10"/><line x1="8" y1="14" x2="8.01" y2="14"/><line x1="12" y1="14" x2="12.01" y2="14"/><line x1="16" y1="14" x2="16.01" y2="14"/>',
      chart: '<path d="M3 3v18h18"/><line x1="8" y1="17" x2="8" y2="12"/><line x1="13" y1="17" x2="13" y2="7"/><line x1="18" y1="17" x2="18" y2="14"/>'
    };
    return '<svg viewBox="0 0 24 24">' + shapes[name] + "</svg>";
  }

  function item(href, ic, title, desc) {
    return (
      '<li><a class="ec-mega__item" href="' + href + '">' +
      '<span class="ec-mega__chip">' + icon(ic) + "</span>" +
      "<span><p class=\"ec-mega__title\">" + title + "</p>" +
      '<p class="ec-mega__desc">' + desc + "</p></span></a></li>"
    );
  }

  function softwareHTML(p) {
    return (
      '<div class="ec-mega__grid">' +
      '<section><h3 class="ec-mega__heading">Sourcing &amp; Research</h3><ul class="ec-mega__list">' +
      item(p + "sourcing.html", "search", "Sourcing &amp; Scanner", "Scan wholesale lists, find profitable products") +
      item(p + "wholesale.html", "search", "Wholesale Scanner", "Dedicated wholesale product discovery") +
      item(p + "amazon-fba-leads.html", "zap", "FBA Leads", "AI-powered product lead discovery") +
      "</ul></section>" +
      '<section><h3 class="ec-mega__heading">Repricing</h3><ul class="ec-mega__list">' +
      item(p + "repricer.html", "dollar", "Repricer", "Automated repricing for Amazon &amp; Walmart") +
      item(p + "amazon.html", "dollar", "Amazon Repricer", "Intelligent repricing for Amazon") +
      item(p + "walmart.html", "dollar", "Walmart Repricer", "Intelligent repricing for Walmart") +
      "</ul></section>" +
      '<section><h3 class="ec-mega__heading">Operations</h3><ul class="ec-mega__list">' +
      item(p + "inventory-management.html", "box", "Inventory Management", "Stock levels, COGS, bulk editing") +
      item(p + "order-management.html", "clipboard", "Order Management", "Unified orders, fulfillment, buy shipping") +
      "</ul></section>" +
      '<section><h3 class="ec-mega__heading">Extension &amp; Tools</h3><ul class="ec-mega__list">' +
      item(p + "extension.html", "tag", "Chrome Extension", "7+ free tools for Amazon &amp; Walmart") +
      item(p + "wfs-calculator.html", "calc", "WFS Calculator", "Estimate Walmart fulfillment fees") +
      "</ul></section>" +
      "</div>" +
      '<div class="ec-mega__footer"><a class="ec-mega__all" href="' + p + 'software.html">View All Software' +
      '<svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
      "</a></div>"
    );
  }

  function servicesHTML(p) {
    return (
      '<div class="ec-mega__grid">' +
      '<section><ul class="ec-mega__list">' +
      item(p + "amazon-management.html", "users", "Amazon Management", "DFY store management with profit share") +
      item(p + "walmart-management.html", "users", "Walmart Management", "DFY Walmart store operations") +
      item(p + "buy-amazon-seller-account.html", "store", "Buy Amazon Account", "Aged, U.S.-based seller accounts") +
      item(p + "sell-amazon-seller-account.html", "store", "Sell Amazon Account", "Sell your Amazon business") +
      item(p + "buy-walmart-account.html", "store", "Buy Walmart Account", "Pre-approved Walmart stores") +
      item(p + "get-approved-on-walmart.html", "filecheck", "Get Approved on Walmart", "DFY approval service") +
      item(p + "warehouse.html", "warehouse", "Warehouse &amp; Fulfillment", "FBA prep, WFS prep, 2-step dropshipping") +
      item(p + "fba-prep.html", "box", "FBA Prep", "$1/unit prep, Chicago &amp; Atlanta") +
      item(p + "wfs-prep.html", "box", "WFS Prep", "$1/unit Walmart prep") +
      item(p + "perp-center.html", "building", "Prep Center Directory", "287 verified prep centers, compared") +
      "</ul></section>" +
      "</div>"
    );
  }

  function compareHTML(p) {
    return (
      '<div class="ec-mega__grid">' +
      '<section><ul class="ec-mega__list">' +
      item(p + "compare.html", "chart", "Compare Hub", "See all comparisons") +
      item(p + "scanunlimited.html", "chart", "vs ScanUnlimited", "Feature comparison") +
      item(p + "flipmine.html", "chart", "vs Flipmine", "Feature comparison") +
      item(p + "sellify.html", "chart", "vs Sellify", "Feature comparison") +
      item(p + "bizmetrica.html", "chart", "vs Bizmetrica (WAMP)", "Feature comparison") +
      item(p + "sourcedart.html", "chart", "vs SourceDart", "Feature comparison") +
      item(p + "getmarter.html", "chart", "vs Marter", "Feature comparison") +
      item(p + "pricelink .html", "chart", "vs PriceLink", "Feature comparison") +
      "</ul></section>" +
      "</div>" +
      '<div class="ec-mega__footer"><a class="ec-mega__all" href="' + p + 'compare.html">View All Comparisons' +
      '<svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>' +
      "</a></div>"
    );
  }

  function attach(trigger, html, extraClass, maxWidth) {
    var wrapper = trigger.parentElement;
    if (wrapper) wrapper.setAttribute("data-ec-mega", "1");

    var panel = document.createElement("div");
    panel.className = "ec-mega ec-mega--floating" + (extraClass ? " " + extraClass : "");
    panel.innerHTML = html;
    document.body.appendChild(panel);

    var hideTimer = null;

    function position() {
      var r = trigger.getBoundingClientRect();
      var w = Math.min(maxWidth, window.innerWidth - 32);
      var left = r.left + r.width / 2 - w / 2;
      left = Math.max(16, Math.min(left, window.innerWidth - w - 16));
      panel.style.width = w + "px";
      panel.style.left = left + "px";
      panel.style.top = r.bottom + 10 + "px";
    }

    function show() { clearTimeout(hideTimer); position(); panel.classList.add("ec-mega--open"); }
    function hide() { hideTimer = setTimeout(function () { panel.classList.remove("ec-mega--open"); }, 130); }
    function isOpen() { return panel.classList.contains("ec-mega--open"); }

    trigger.addEventListener("mouseenter", show);
    trigger.addEventListener("mouseleave", hide);
    trigger.addEventListener("focus", show);
    trigger.addEventListener("blur", hide);
    panel.addEventListener("mouseenter", show);
    panel.addEventListener("mouseleave", hide);

    window.addEventListener("scroll", function () { if (isOpen()) position(); }, { passive: true });
    window.addEventListener("resize", function () { if (isOpen()) position(); });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") { clearTimeout(hideTimer); panel.classList.remove("ec-mega--open"); }
    });
  }

  function findTrigger(links, label) {
    for (var i = 0; i < links.length; i++) {
      if (new RegExp("^\\s*" + label).test(links[i].textContent)) return links[i];
    }
    return null;
  }

  function init() {
    var links = document.querySelectorAll("header nav a");
    var p = rootPrefix();

    var software = findTrigger(links, "Software Tools");
    if (software) attach(software, softwareHTML(p), "", 920);

    var services = findTrigger(links, "Services");
    if (services) attach(services, servicesHTML(p), "ec-mega--list", 440);

    var compare = findTrigger(links, "Compare");
    if (compare) attach(compare, compareHTML(p), "ec-mega--list", 440);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
