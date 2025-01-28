<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$accordion = get_field('accordion') ?? null;

// Settings
$settings = get_field('settings') ?? null;
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
                  'c-accordion__headline o-col-12 o-col-xl-8',
          ]);} ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-accordion__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($accordion)) { ?>
            <div class="c-accordion__accordions o-row">
                <?php foreach ($accordion as $key => $card) { ?>
                    <div class="c-accordion-card o-col-12 o-col-xl-8 <?php if (
                        $key === 0
                    ) {
                        echo '--is-open';
                    } else {
                        echo '--is-closed';
                    } ?>">
                        <div class="c-accordion-card__title">
                            <h3 class="c-accordion-card__headline o-headline --h3"><?php echo $card[
                                'headline'
                            ]; ?></h3>
                            <div class="c-accordion-card__icon-wrapper">
                                <button class="c-accordion-card__icon c-icon-button --close">
                                    <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                                        'Weniger anzeigen',
                                        'oo_theme',
                                    ); ?></span>
                                    <span class="c-icon-button__icon --chevron-up"><?php oo_get_icon(
                                        'chevron-up',
                                    ); ?></span>
                                </button>
                                <button class="c-accordion-card__icon c-icon-button --open">
                                    <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                                        'Mehr anzeigen',
                                        'oo_theme',
                                    ); ?></span>
                                    <span class="c-icon-button__icon --chevron-down"><?php oo_get_icon(
                                        'chevron-down',
                                    ); ?></span>
                                </button>
                            </div>
                        </div>
                        <div class="c-accordion-card__content o-text --is-wysiwyg">
                            <?php echo $card['text']['wysiwyg']; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

</section>