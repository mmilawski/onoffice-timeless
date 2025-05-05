<?php
/**
 * Sticky Pop-up Template
 */

// Helpers
$content = $args ?? [];
$image = $content['image'] ?? null;
$headline = $content['headline'] ?? null;
$text = $content['text'] ?? null;
$buttons = $content['buttons'] ?? null;
$shortcode = $content['shortcode'] ?? null;
$bg_color = '--' . $content['settings']['bg_color'] ?? '--bg-transparent';

// Image Sizes
$image_width_xs = '396';
$image_width_sm = '396';
$image_width_md = '396';
$image_width_lg = '396';
$image_width_xl = '396';
$image_width_xxl = '396';
$image_width_xxxl = '396';
?>
<div <?php oo_popup_get_template_attributes(
    get_the_id(),
    $content,
    'sticky',
); ?>>
    <div class="c-popup__wrapper">
        <button class="c-popup__close c-icon-button">
            <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                'Fenster schließen',
                'oo_theme',
            ); ?></span>
            <span class="c-icon-button__icon --close"><?php oo_get_icon(
                'close',
            ); ?></span>
        </button>

        <?php if (!empty($image)) {
            oo_get_template('components', '', 'component-image', [
                'image' => $image,
                'picture_class' => 'c-popup__picture o-picture',
                'image_class' => 'c-popup__image o-image',
                'dimensions' => [
                    '575' => [
                        'w' => $image_width_xs,
                        'h' => round(($image_width_xs * 3) / 4),
                    ],
                    '1600' => [
                        'w' => $image_width_xxxl,
                        'h' => round(($image_width_xxxl * 3) / 4),
                    ],
                    '1400' => [
                        'w' => $image_width_xxl,
                        'h' => round(($image_width_xxl * 3) / 4),
                    ],
                    '1200' => [
                        'w' => $image_width_xl,
                        'h' => round(($image_width_xl * 3) / 4),
                    ],
                    '992' => [
                        'w' => $image_width_lg,
                        'h' => round(($image_width_lg * 3) / 4),
                    ],
                    '768' => [
                        'w' => $image_width_md,
                        'h' => round(($image_width_md * 3) / 4),
                    ],
                    '576' => [
                        'w' => $image_width_sm,
                        'h' => round(($image_width_sm * 3) / 4),
                    ],
                ],
            ]);
        } ?>
        <div class="c-popup__content">
        
            <?php if (!empty($headline)) { ?>
                <span class="c-popup__headline o-headline --h3 --span"> 
                <?php echo esc_html($headline); ?>
                </span>
            <?php } ?>
            
            <?php if (!empty($text)) { ?>
            <div class="c-popup__text o-text --is-wysiwyg">
                <?php echo oo_convert_headings_to_spans($text); ?>
            </div>
            <?php } ?>

            <?php if (!empty($buttons['buttons'][0]['link'])) {
                oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' =>
                        'c-popup__button' .
                        ($bg_color ? ' --on-' . $bg_color : ''),
                    'additional_container_class' => 'c-popup__buttons',
                ]);
            } ?>

            <?php if (!empty($shortcode)) { ?>
            <div class="c-popup__form">
                <?php echo do_shortcode($shortcode); ?>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="c-popup__overlay"></div>
</div>