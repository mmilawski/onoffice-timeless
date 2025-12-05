<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$shortcode = get_field('shortcode') ?? null;
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$map_zoom = $settings['map_zoom'] ?? 'no';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// set header level for submodule
$size = !empty($headline['text'])
    ? sanitize_header_level($headline['size'])
    : 1;
set_current_header_level($size);
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-property-list o-section --<?php echo $bg_color; ?>">
    <div class="c-property-list__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-property-list__content o-row">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' =>
                            'c-property-list__headline o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
                    ]);
                } ?>
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-property-list__text o-text --is-wysiwyg o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1" >
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        </div>




        <?php if (!empty($shortcode)) {
            echo '<div class="c-property-list__container">';
            echo do_shortcode($shortcode);
            echo '</div>';
        } ?>

        <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-property-list__container o-container">
            <div class="c-property-list__buttons-wrapper o-row">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-property-list__buttons o-col-12',
                ]); ?>
           
        <?php } ?>
        </div>
    </div>
</section>