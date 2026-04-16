<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$accordion = get_field('accordion') ?? null;

// Settings
$settings = get_field('settings') ?? null;
$enable_faq_schema = (get_field('enable_faq_schema') ?? 'no') === 'yes';

// set header level
$header_level = !empty($headline['text'])
    ? sanitize_header_level($headline['size']) + 1
    : 2;
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-accordion o-section --<?php echo $settings['bg_color']; ?>">
    <div class="c-accordion__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-accordion__content o-row">
                <?php if (!empty($headline['text'])) { ?>
										<?php oo_get_template('components', '', 'component-headline', [
              'headline' => $headline,
              'additional_headline_class' =>
                  'c-accordion__headline o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
          ]);} ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-accordion__text o-text --is-wysiwyg o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($accordion)) { ?>
            <div class="c-accordion__accordions-wrapper o-row">
                <div class="c-accordion__accordions o-col-12 o-col-lg-10 u-offset-lg-1">
                    <?php foreach ($accordion as $key => $card) {
                        $accordion_headline = $card['headline'] ?? ''; ?>
                        <details class="c-accordion-card">
                            <summary class="c-accordion-card__title">
                                <?php echo "<h{$header_level} " .
                                    'class="c-accordion-card__headline o-headline --h3 --span">' .
                                    esc_html($accordion_headline) .
                                    "</h{$header_level}>"; ?>
                                <span class="c-accordion-card__icon-wrapper">
                                    <?php echo oo_get_icon(
                                        'chevron-right',
                                        true,
                                        [
                                            'class' => 'c-accordion-card__icon',
                                        ],
                                    ); ?>
                                    <span class="u-screen-reader-only --open" aria-hidden="false">
                                        <?php echo sprintf(
                                            esc_html_x(
                                                'Mehr anzeigen zum Thema %s',
                                                'Screenreader-Text für Akkordeon-Klappe',
                                                'oo_theme',
                                            ),
                                            $accordion_headline,
                                        ); ?>
                                    </span>
                                    <span class="u-screen-reader-only --close" aria-hidden="true">
                                        <?php echo sprintf(
                                            esc_html_x(
                                                'Weniger anzeigen zum Thema %s',
                                                'Screenreader-Text für Akkordeon-Klappe',
                                                'oo_theme',
                                            ),
                                            $accordion_headline,
                                        ); ?>
                                    </span>
                                </span>
                            </summary>
                            <div class="c-accordion-card__content">
                                <div class="c-accordion-card__text o-text --is-wysiwyg o-col-12 o-col-lg-10 ">
                                    <?php echo $card['text']['wysiwyg']; ?>
                                </div>
                            </div>
                        </details>
                    <?php
                    } ?>
                </div>
            </div>
        <?php } ?>

    </div>

</section>

<?php if ($enable_faq_schema && !empty($accordion)) {
    oo_register_faq_schema($accordion);
}
?>
