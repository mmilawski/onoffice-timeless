<?php
/**
 * Sticky Pop-up Template
 */

// Helpers
$content = $args ?? [];
$image = $content['image'] ?? [];
$headline = $content['headline'] ?? null;
$text = $content['text'] ?? null;
$buttons = $content['buttons'] ?? [];
$shortcode = $content['shortcode'] ?? null;
$bg_color = $content['settings']['bg_color'] ?? 'bg-transparent';

$popup_id = get_the_id() ?? 'sticky';
$type = $content['type'] ?? 'sticky';

// Image Sizes
$image_width_xs = '575';
$image_width_sm = '767';
$image_width_md = '460';
$image_width_lg = '460';
$image_width_xl = '460';
$image_width_xxl = '460';
$image_width_xxxl = '460';
?>

<dialog <?php echo oo_popup_get_id_attribute(
    $popup_id,
); ?> <?php echo oo_popup_get_data_attributes(
     $popup_id,
     $content,
     $type,
 ); ?> class="c-popup-sticky-mobile-modal --<?php echo $bg_color; ?>" role="status" aria-live="polite" aria-atomic="true">
    <button class="c-popup-sticky-mobile-modal__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
        'Fenster schließen',
        'oo_theme',
    ); ?>">
        <?php oo_get_icon('close', true, [
            'class' => 'c-icon-button__icon --close',
        ]); ?>
    </button>

    <div class="c-popup-sticky-mobile-modal__wrapper">
        <?php if (!empty($image)) {
            oo_get_template('components', '', 'component-image', [
                'image' => $image,
                'picture_class' =>
                    'c-popup-sticky-mobile-modal__picture o-picture',
                'image_class' => 'c-popup-sticky-mobile-modal__image o-image',
                'dimensions' => [
                    '575' => [
                        'w' => $image_width_xs,
                        'h' => round(($image_width_xs * 2) / 3),
                    ],
                    '1600' => [
                        'w' => $image_width_xxxl,
                        'h' => round(($image_width_xxxl * 2) / 3),
                    ],
                    '1400' => [
                        'w' => $image_width_xxl,
                        'h' => round(($image_width_xxl * 2) / 3),
                    ],
                    '1200' => [
                        'w' => $image_width_xl,
                        'h' => round(($image_width_xl * 2) / 3),
                    ],
                    '992' => [
                        'w' => $image_width_lg,
                        'h' => round(($image_width_lg * 2) / 3),
                    ],
                    '768' => [
                        'w' => $image_width_md,
                        'h' => round(($image_width_md * 2) / 3),
                    ],
                    '576' => [
                        'w' => $image_width_sm,
                        'h' => round(($image_width_sm * 2) / 3),
                    ],
                ],
            ]);
        } ?>

        <?php if (!empty($headline)) { ?>
            <h2 class="c-popup-sticky-mobile-modal__headline o-headline --h4"><?php echo esc_html(
                $headline,
            ); ?></h2>
        <?php } ?>

        <?php
        $has_content =
            !empty($text) ||
            !empty($buttons['buttons'][0]['link']) ||
            !empty($shortcode);

        if ($has_content): ?>
            <div class="c-popup-sticky-mobile-modal__content">

                <?php if (!empty($text)) { ?>
                    <div class="c-popup-sticky-mobile-modal__text o-text --is-wysiwyg">
                        <?php echo $text; ?>
                    </div>
                <?php } ?>

                <?php if (!empty($buttons['buttons'][0]['link'])) {
                    oo_get_template('components', '', 'component-buttons', [
                        'buttons' => $buttons['buttons'],
                        'additional_button_class' =>
                            'c-popup-sticky-mobile-modal__button' .
                            ($bg_color ? ' --on-' . $bg_color : ''),
                        'additional_container_class' =>
                            'c-popup-sticky-mobile-modal__buttons',
                    ]);
                } ?>

                <?php if (!empty($shortcode)) { ?>
                    <div class="c-popup-sticky-mobile-modal__form --is-popup --on-<?php echo $bg_color; ?>">
                        <?php echo do_shortcode($shortcode); ?>
                    </div>
                <?php } ?>

            </div>
        <?php endif;
        ?>
    </div>
</dialog>