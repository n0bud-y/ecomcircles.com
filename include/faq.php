<?php
/**
 * "Common Questions" FAQ section — data-driven.
 *
 * Set the questions for the page, then include:
 *
 *     <?php
 *     $faq_questions = [
 *         'Does order management integrate with my 3PL or warehouse?',
 *         'Can I route orders to different fulfillment methods?',
 *     ];
 *     include __DIR__ . '/include/faq.php';
 *     ?>
 *
 * Optional:
 *     $faq_heading   heading text, defaults to 'Common Questions'
 *
 * Styling is intentionally fixed (pg-sec bg-shades) so every FAQ on the site
 * renders identically — pass nothing for it.
 */

$faq_heading   = $faq_heading   ?? 'Common Questions';
$faq_questions = $faq_questions ?? [];

$ec_e = static function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};

/** chevron drawn once per row instead of being pasted into every question */
$ec_chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down pg-faq__chev" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>';
?>
<section class="pg-sec bg-shades">
    <div class="pg-wrap-3xl">
        <h2 class="pg-h2 pg-center pg-mb-12"><?= $ec_e($faq_heading) ?></h2>
        <div>
<?php foreach ($faq_questions as $q): ?>
            <div class="pg-faq__item">
                <button class="pg-faq__btn"><span class="pg-faq__q"><?= $ec_e($q) ?></span><span><?= $ec_chevron ?></span></button>
            </div>
<?php endforeach; ?>
        </div>
    </div>
</section>
<?php
/* keep the page namespace clean so a later include starts from the defaults */
unset($faq_questions, $faq_heading, $ec_e, $ec_chevron, $q);
