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

                            <div class="c-accordion-card__icon-wrapper">
                                <svg class="c-accordion-card__icon" width="24" viewBox="0 0 23.41 13.12"><path d="m.71.71l11,11L22.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                            </div>

                            <h3 class="c-accordion-card__headline o-headline --h3"><?php echo $card[
                                'headline'
                            ]; ?></h3>

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