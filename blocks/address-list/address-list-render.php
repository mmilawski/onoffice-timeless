<?php
// Content
$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$shortcode = get_field('shortcode') ?? null;
$buttons = get_field('buttons') ?? null;

// Settings
$settings = get_field('settings') ?? null;
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// set header level for submodule
$size = !empty($headline['text'])
    ? sanitize_header_level($headline['size'])
    : 1;
set_current_header_level($size);
?>

<section <?php echo oo_block_id(
    $block,
); ?> class="c-address-list o-section --<?php echo $bg_color; ?>">
    <div class="c-address-list__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-address-list__content o-row ">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' =>
                            'c-address-list__headline o-col-12 o-col-lg-10  u-offset-lg-1',
                    ]);
                } ?>
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-address-list__text o-text --is-wysiwyg o-col-12 o-col-lg-10 u-offset-lg-1">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        </div>

        <?php if (!empty($shortcode)) { ?>
            <div class="c-address-list__container">
            <div class="c-address-list__content">
                <div class="c-address-list__shortcode">
                    <?php echo do_shortcode($shortcode); ?>
                </div>
            </div>
        </div>
        <?php } ?>

 
        <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-address-list__container o-container">   
            <div class="c-address-list__buttons-wrapper o-row">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-address-list__buttons o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
                ]); ?>
            </div>
        </div>
        <?php } ?>
    
</section>