<?php
/**
 * Testimonials section — data-driven.
 *
 * Include it with the defaults:
 *     <?php include __DIR__ . '/include/testimonials.php'; ?>
 *
 * …or set any of these before the include to override for a page:
 *     $testimonials_heading  HTML for the <h2> (may contain <span class="gradient-text">)
 *     $testimonials_sub      plain-text sub-heading
 *     $testimonials          array of cards, each:
 *                              'quote'  plain text
 *                              'name'   plain text
 *                              'role'   plain text
 *                              'avatar' image path, relative to the site root
 *                              'stars'  int 0-5 (optional, defaults to 5)
 */

$testimonials_heading = $testimonials_heading
    ?? 'Loved by eCommerce Sellers <span class="gradient-text">Worldwide</span>';

$testimonials_sub = $testimonials_sub
    ?? 'See how real sellers grow faster and stay competitive with Ecom Circles.';

$testimonials = $testimonials ?? [
    [
        'quote'  => 'I needed a repricer that covered my different selling methods. Using the Ecom Circles Repricer made us significantly more competitive on both Walmart and Amazon.',
        'name'   => 'Matt H.',
        'role'   => '7-Figure Amazon & Walmart Seller',
        'avatar' => 'images/matt-b.webp',
    ],
    [
        'quote'  => 'There\'s no way we could have brought in almost $45k in profit over Black Friday / Cyber Monday weekend without it.',
        'name'   => 'Joshua F.',
        'role'   => '7-Figure Amazon & Walmart Seller',
        'avatar' => 'images/josh-f.webp',
    ],
    [
        'quote'  => 'It\'s the best software I\'ve used. It makes it easy to scale while making sure nothing falls through the cracks.',
        'name'   => 'Kelly L.',
        'role'   => '6-Figure Amazon Seller',
        'avatar' => 'images/kelly-l.webp',
    ],
];

/** escape helper */
$ec_e = static function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};

/** one star glyph — rendered N times per card instead of being pasted 5x per card */
$ec_star = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pgx-80 lucide lucide-star pgi-107" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>';
?>
<section class="pgi-64">
    <div class="pgi-48">
        <div class="pgi-49">
            <h2 class="pgi-50"><?= $testimonials_heading ?></h2>
            <p class="pgi-51"><?= $ec_e($testimonials_sub) ?></p>
        </div>
        <div class="pgx-87">
<?php foreach ($testimonials as $t): ?>
            <div class="pgi-105">
                <div class="pgi-106"><?= str_repeat($ec_star, (int) ($t['stars'] ?? 5)) ?></div>
                <p class="pgi-108"><?= $ec_e($t['quote']) ?></p>
                <div class="pgi-109"><img class="pgi-110" alt="<?= $ec_e($t['name']) ?>" loading="lazy" width="42" height="42" src="<?= $ec_e($t['avatar']) ?>" />
                    <div>
                        <div class="pgi-111"><?= $ec_e($t['name']) ?></div>
                        <div class="pgi-112"><?= $ec_e($t['role']) ?></div>
                    </div>
                </div>
            </div>
<?php endforeach; ?>
        </div>
    </div>
</section>
<?php
/* keep the page namespace clean so a later include starts from the defaults */
unset($testimonials, $testimonials_heading, $testimonials_sub, $ec_e, $ec_star, $t);
