<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$type = get_field('type') ?? null;
$shortcode = get_field('shortcode') ?? null;

// Settings
$settings = get_field('settings') ?? null;
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-forms --is-<?php echo $type; ?>-form o-section --<?php echo $settings[
    'bg_color'
]; ?> --<?php echo $settings['bg_color']; ?>-mixed --with-separator">
    <div class="c-forms__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-forms__content o-row">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' =>
                            'c-forms__headline o-col-12 o-col-lg-8',
                    ]);
                } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-forms__text o-text --is-wysiwyg o-col-12 o-col-lg-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($shortcode)) { ?>
            <div class="c-forms__wrapper o-row">
                <div class="c-forms__form o-col-12 o-col-lg-8">
                    <?php echo do_shortcode($shortcode); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>