<?php
/**
 * Module Name: Seals
 * @param $args
 * Get values from the parameter
 */

// Helpers
$content = $args['content'] ?? [];
$location = $args['location'] ?? 'footer';

$headline = $content['headline'] ?? null;
$seals = $content['seals']['repeater'] ?? [];

$is_slider = filter_var($content['slider']['slider'], FILTER_VALIDATE_BOOLEAN);

if (!empty($headline)):
    oo_get_template('components', '', 'component-headline', [
        'headline' => [
            'text' => strip_tags($headline),
            'size' => 'span',
        ],
        'additional_headline_class' => 'c-module-seals__headline',
    ]);
endif;

if (empty($seals)) {
    return;
}

if ($is_slider) {
    $image_width_xs = '100';
    $image_width_sm = '130';
    $image_width_md = '80';
    $image_width_lg = '120';
    $image_width_xl = '90';
    $image_width_xxl = '100';
    $image_width_xxxl = '130';

    $image_height_xs = '';
    $image_height_sm = '';
    $image_height_md = '';
    $image_height_lg = '';
    $image_height_xl = '';
    $image_height_xxl = '';
    $image_height_xxxl = '';
} else {
    $image_width_xs = '240';
    $image_width_sm = '240';
    $image_width_md = '160';
    $image_width_lg = '210';
    $image_width_xl = '160';
    $image_width_xxl = '190';
    $image_width_xxxl = '140';

    $image_height_xs = '';
    $image_height_sm = '';
    $image_height_md = '';
    $image_height_lg = '';
    $image_height_xl = '';
    $image_height_xxl = '';
    $image_height_xxxl = '';
}

if ($is_slider) { ?>
    <div class="c-seals --is-slider">
        <div class="c-seals__slider c-slider --is-seals-slider --on-bg-<?php echo $location; ?> splide" 
            data-splide='{
                "gap": 32,
                "perMove": 1,
                "perPage": 3,
                "pagination": false,
                "snap":true,
                "lazyLoad":"nearby"
        }'>
            <div class="c-slider__track splide__track">
                <div class="c-seals__list c-slider__list splide__list">
                    <?php foreach ($seals as $seal) {
                        echo '<div class="c-seals__item c-slider__slide splide__slide">';
                        echo '<div class="c-seals__wrapper c-slider__wrapper">';
                        echo '<div class="c-seals__cover --is-' .
                            $seal['type'] .
                            '">';

                        if ($seal['type'] === 'image') {
                            $image = $seal['image']['image'] ?? [];
                            $link = $seal['image']['link'] ?? [];

                            if (empty($image['alt'])) {
                                //BFSG: fallback if no alt text is set
                                $image['alt'] = sprintf(
                                    esc_html__('Siegel für %s', 'oo_theme'),
                                    esc_html($image['title']),
                                );
                            }
                            if (!empty($link)) {
                                echo '<a class="c-seals__link" ' .
                                    oo_set_link_attr($link) .
                                    '>';
                            }

                            oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $image,
                                    'picture_class' =>
                                        'c-seals__picture o-picture',
                                    'image_class' => 'c-seals__image o-image',
                                    'additional_cloudimg_params' =>
                                        '&func=bound&gravity=center&org_if_sml=1',
                                    'dimensions' => [
                                        '575' => [
                                            'w' => $image_width_xs,
                                            'h' => $image_height_xs,
                                        ],
                                        '1600' => [
                                            'w' => $image_width_xxxl,
                                            'h' => $image_height_xxxl,
                                        ],
                                        '1400' => [
                                            'w' => $image_width_xxl,
                                            'h' => $image_height_xxl,
                                        ],
                                        '1200' => [
                                            'w' => $image_width_xl,
                                            'h' => $image_height_xl,
                                        ],
                                        '992' => [
                                            'w' => $image_width_lg,
                                            'h' => $image_height_lg,
                                        ],
                                        '768' => [
                                            'w' => $image_width_md,
                                            'h' => $image_height_md,
                                        ],
                                        '576' => [
                                            'w' => $image_width_sm,
                                            'h' => $image_height_sm,
                                        ],
                                    ],
                                ],
                            );

                            if (!empty($link)) {
                                echo '</a>';
                            }
                        } else {
                            oo_set_seal_content($seal, 'c-seals', $is_slider);
                        }

                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    } ?>
                </div>
            </div>
            <div class="c-slider__navigation splide__navigation">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                <div class="c-slider__arrows splide__arrows">
                <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
<span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
    'chevron-left',
); ?></span>                    </button>
                                <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
<span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
    'chevron-right',
); ?></span>                            </button>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>

<div class="c-seals --is-grid">
    <?php foreach ($seals as $seal) {
        echo '<div class="c-seals__item --is-' . $seal['type'] . '">';

        if ($seal['type'] === 'image') {
            $image = $seal['image']['image'] ?? [];
            $link = $seal['image']['link'] ?? [];
            if (empty($image['alt'])) {
                //BFSG: fallback if no alt text is set
                $image['alt'] = sprintf(
                    esc_html__('Siegel für %s', 'oo_theme'),
                    esc_html($image['title']),
                );
            }
            if (!empty($link)) {
                echo '<a class="c-seals__link" ' .
                    oo_set_link_attr($link) .
                    '>';
            }

            oo_get_template('components', '', 'component-image', [
                'image' => $image,
                'picture_class' => 'c-seals__picture o-picture',
                'image_class' => 'c-seals__image o-image',
                'additional_cloudimg_params' =>
                    '&func=bound&gravity=center&org_if_sml=1',
                'dimensions' => [
                    '575' => [
                        'w' => $image_width_xs,
                        'h' => $image_height_xs,
                    ],
                    '1600' => [
                        'w' => $image_width_xxxl,
                        'h' => $image_height_xxxl,
                    ],
                    '1400' => [
                        'w' => $image_width_xxl,
                        'h' => $image_height_xxl,
                    ],
                    '1200' => [
                        'w' => $image_width_xl,
                        'h' => $image_height_xl,
                    ],
                    '992' => [
                        'w' => $image_width_lg,
                        'h' => $image_height_lg,
                    ],
                    '768' => [
                        'w' => $image_width_md,
                        'h' => $image_height_md,
                    ],
                    '576' => [
                        'w' => $image_width_sm,
                        'h' => $image_height_sm,
                    ],
                ],
            ]);

            if (!empty($link)) {
                echo '</a>';
            }
        } else {
            oo_set_seal_content($seal, 'c-seals', is_slider: $is_slider);
        }
        echo '</div>';
    } ?>
</div>
<?php } ?>
