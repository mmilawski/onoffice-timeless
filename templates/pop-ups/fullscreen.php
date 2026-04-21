<?php
/**
 * Fullscreen Pop-up Template
 */

// Helpers
$content = $args ?? [];
$image = $content['image'] ?? [];
$headline = $content['headline'] ?? null;
$text = $content['text'] ?? null;
$buttons = $content['buttons'] ?? [];
$shortcode = $content['shortcode'] ?? null;
$bg_color = $content['settings']['bg_color'] ?? 'bg-transparent';

$popup_id = get_the_id() ?? 'fullscreen';
$type = $content['type'] ?? 'fullscreen';

// Image Sizes
$image_width_xxs = '414';
$image_width_xs = '575';
$image_width_sm = '767';
$image_width_md = '991';
$image_width_lg = '320';
$image_width_xl = '384';
$image_width_xxl = '448';
$image_width_xxxl = '492';
?>

<dialog <?php echo oo_popup_get_id_attribute(
    $popup_id,
); ?> <?php echo oo_popup_get_data_attributes(
     $popup_id,
     $content,
     $type,
 ); ?> class="c-popup-fullscreen <?php echo empty($image)
     ? '--no-image'
     : ''; ?> --<?php echo $bg_color; ?> --<?php echo $bg_color; ?>" aria-labelledby="<?php echo $popup_id; ?>-title" <?php if (
    !empty($text)
) { ?>aria-describedby="<?php echo $popup_id; ?>-desc"<?php } ?>>
    <div class="c-popup-fullscreen__container o-container-fluid o-container--lg">
        <div class="c-popup-fullscreen__row o-row --position-center">
            <?php if (!empty($image)) {
                oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' =>
                        'c-popup-fullscreen__picture o-picture o-col-12 o-col-lg-4',
                    'image_class' => 'c-popup-fullscreen__image o-image',
                    'dimensions' => [
                        '414' => [
                            'w' => $image_width_xxs,
                            'h' => round(($image_width_xxs * 2) / 3),
                        ],
                        '575' => [
                            'w' => $image_width_xs,
                            'h' => round(($image_width_xs * 2) / 3),
                        ],
                        '1600' => [
                            'w' => $image_width_xxxl,
                            'h' => $image_width_xxxl,
                        ],
                        '1400' => [
                            'w' => $image_width_xxl,
                            'h' => $image_width_xxl,
                        ],
                        '1200' => [
                            'w' => $image_width_xl,
                            'h' => $image_width_xl,
                        ],
                        '992' => [
                            'w' => $image_width_lg,
                            'h' => $image_width_lg,
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

            <div class="c-popup-fullscreen__inner o-col-12 <?php echo empty(
                $image
            )
                ? 'o-col-lg-10 o-col-xl-8'
                : 'o-col-lg-8 o-col-xl-6'; ?>">

                <div class="c-popup-fullscreen__header">
                    <button class="c-popup-fullscreen__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
                        'Fenster schließen',
                        'oo_theme',
                    ); ?>">
                        <?php oo_get_icon('close', true, [
                            'class' => 'c-icon-button__icon --close',
                        ]); ?>
                    </button>

                    <?php if (!empty($headline)) { ?>
                        <h2 id="<?php echo $popup_id; ?>-title" class="c-popup-fullscreen__headline o-headline --h4"> 
                            <?php echo esc_html($headline); ?>
                        </h2>
                    <?php } ?>
                </div>

                <?php
                $has_content =
                    !empty($text) ||
                    !empty($buttons['buttons'][0]['link']) ||
                    !empty($shortcode);

                if ($has_content): ?>
                    <div class="c-popup-fullscreen__content">

                        <?php if (!empty($text)) { ?>
                            <div id="<?php echo $popup_id; ?>-desc" class="c-popup-fullscreen__text o-text --is-wysiwyg">
                                <?php echo $text; ?>
                            </div>
                        <?php } ?>

                        <?php if (!empty($buttons['buttons'][0]['link'])) {
                            oo_get_template(
                                'components',
                                '',
                                'component-buttons',
                                [
                                    'buttons' => $buttons['buttons'],
                                    'additional_button_class' =>
                                        'c-popup-fullscreen__button' .
                                        ($bg_color ? ' --on-' . $bg_color : ''),
                                    'additional_container_class' =>
                                        'c-popup-fullscreen__buttons',
                                ],
                            );
                        } ?>

                        <?php if (!empty($shortcode)) { ?>
                            <div class="c-popup-fullscreen__form --is-popup --on-<?php echo $bg_color; ?>">
                                <?php echo do_shortcode($shortcode); ?>
                            </div>
                        <?php } ?>

                    </div>
                <?php endif;
                ?>
            </div>
        </div>
   </div>
</dialog>