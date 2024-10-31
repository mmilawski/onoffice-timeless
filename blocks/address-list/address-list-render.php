<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$shortcode = get_field('shortcode') ?? null;
$buttons = get_field('buttons') ?? null;

// Settings
$settings = get_field('settings') ?? null;
$slider = get_field('slider') ?? null;
?>

<section <?php echo oo_block_id(
    $block,
); ?> class="c-address-list o-section --<?php echo $settings[
     'bg_color'
 ]; ?> --<?php echo $settings['bg_color']; ?>-mixed --with-separator">
    <div class="c-address-list__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-address-list__content o-row">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' =>
                            'c-address-list__headline o-col-12 o-col-xl-8',
                    ]);
                } ?>
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-address-list__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if (!empty($shortcode)) {
            echo do_shortcode($shortcode);
        } ?>

        <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-address-list__content o-row">
							<?php oo_get_template('components', '', 'component-buttons', [
           'buttons' => $buttons['buttons'],
           'additional_button_class' => $settings['bg_color']
               ? '--on-' . $settings['bg_color']
               : '',
           'additional_container_class' =>
               'c-address-list__buttons o-col-12 o-col-xl-8',
       ]); ?>
            </div>
        <?php } ?>
    </div>
</section>