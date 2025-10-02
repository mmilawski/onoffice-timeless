<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$type = get_field('type') ?? null;
$shortcode = get_field('shortcode') ?? null;

// Settings
$settings = get_field('settings') ?? null;
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-forms --is-<?php echo $type; ?>-form o-section --<?php echo $bg_color; ?>">
    <div class="c-forms__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-forms__content pt-5 o-col-md-5">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' => 'c-forms__headline',
                    ]);
                } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-forms__text o-text --is-wysiwyg">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if (!empty($shortcode)) { ?>
            <div class="c-forms__wrapper u-offset-md-1 o-col-md-6 --<?php echo $bg_color; ?>">
                <div class="c-forms__form">
                    <?php echo do_shortcode($shortcode); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>