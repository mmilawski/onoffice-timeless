<?php
// Fields
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$gallery = get_field('gallery') ?? [];
$images = $gallery['repeater'] ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Image height slider
$slider_image_height = '220';

// Image height masonry
$masonry_image_width_xs = '200';
$masonry_image_width_sm = '218';
$masonry_image_height_md = '158';
$masonry_image_height_lg = '235';
$masonry_image_height_xl = '206';
$masonry_image_height_xxl = '254';
$masonry_image_height_xxxl = '287';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Slider destroy
$slides_count = is_array($images) ? count($images) : 0;
$destroy_xs = $slides_count >= 2 ? false : true;
$destroy_sm = $slides_count >= 3 ? false : true;
$destroy_md = $slides_count >= 4 ? false : true;
$destroy_lg = $slides_count >= 5 ? false : true;
$destroy_xl = $slides_count >= 6 ? false : true;
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-gallery o-section --<?php echo $bg_color; ?>">
    <div class="c-gallery__container o-container">
        <div class="c-gallery__row o-row">
            
            <?php if (!empty($headline['text'])) { ?>
                <?php oo_get_template('components', '', 'component-headline', [
                    'headline' => $headline,
                    'additional_headline_class' =>
                        'c-gallery__headline o-col-12 o-col-xl-8',
                ]); ?>
            <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-gallery__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
            
            <?php if ($gallery['repeater']): ?>
                <div class="c-gallery__wrapper o-col-12">
                    <?php if ($is_slider) { ?>
                        <div class="c-gallery__inner --is-slider">
                            <div class="c-gallery__slider c-slider --is-gallery-slider --loop --on-<?php echo $bg_color; ?> splide" data-splide='{
                                "gap":32,
                                "pagination":false,
                                "snap":true,
                                "autoWidth": true,
                                "lazyLoad":"nearby",
                                "focus":"center",
                                "type":"loop",
                                "mediaQuery":"min",
                                "breakpoints": {
                                        "0": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_xs,
                                            ); ?>
                                        },
                                        "445": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_sm,
                                            ); ?>
                                        },
                                        "576": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_sm,
                                            ); ?>
                                        },
                                        "768": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_md,
                                            ); ?>
                                        },
                                        "1200": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_lg,
                                            ); ?>
                                        },
                                        "1400": {
                                            "destroy": <?php echo json_encode(
                                                $destroy_xl,
                                            ); ?>
                                        }
                                }
                            }'>
                                <div class="c-slider__track splide__track">
                                    <div class="c-gallery__list c-slider__list splide__list">
                                        <?php foreach ($images as $image) {
                                            $link =
                                                $image['image']['link'] ?? '';
                                            echo '<div class="c-gallery__item --is-' .
                                                $image['type'] .
                                                ' c-slider__slide splide__slide">';
                                            if (!empty($link)) {
                                                echo '<a class="c-gallery__link" ' .
                                                    oo_set_link_attr($link) .
                                                    '>';
                                            }
                                            if (
                                                $image['type'] ==
                                                    'provenexpert' ||
                                                $image['type'] ==
                                                    'immoscout24' ||
                                                $image['type'] == 'immowelt'
                                            ) {
                                                oo_set_seal_content(
                                                    $image,
                                                    'c-gallery',
                                                    $is_slider,
                                                );
                                            } else {
                                                if (
                                                    !empty(
                                                        $image['image']['image']
                                                    )
                                                ) {
                                                    oo_get_template(
                                                        'components',
                                                        '',
                                                        'component-image',
                                                        [
                                                            'image' =>
                                                                $image['image'][
                                                                    'image'
                                                                ] ?? null,
                                                            'picture_class' =>
                                                                'c-gallery__picture o-picture',
                                                            'image_class' =>
                                                                'c-gallery__image o-image',
                                                            'additional_cloudimg_params' =>
                                                                '&func=bound&gravity=center',
                                                            'dimensions' => [
                                                                '575' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '1600' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '1400' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '1200' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '992' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '768' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                                '576' => [
                                                                    'w' => $slider_image_height,
                                                                    'h' => $slider_image_height,
                                                                ],
                                                            ],
                                                        ],
                                                    );
                                                } else {
                                                    echo '<div class="c-gallery__placeholder c-placeholder"></div>';
                                                }
                                            }
                                            if (!empty($link)) {
                                                echo '</a>';
                                            }
                                            echo '</div>';
                                        } ?>
                                    </div>
                                </div>

                                <div class="c-slider__arrows splide__arrows">
                                    <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Vorheriges',
                                            'oo_theme',
                                        ); ?></span>
                                        <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    </button>
                                    <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Nächstes',
                                            'oo_theme',
                                        ); ?></span>
                                        <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php wp_enqueue_script('oo-images-loaded-script'); ?>
                        <?php wp_enqueue_script('oo-masonry-script'); ?>

                        
                        <div class="c-gallery__inner --is-masonry <?php echo '--on-' .
                            $bg_color; ?>">
                            <?php foreach ($images as $image) {
                                $link = $image['image']['link'] ?? '';

                                echo '<div class="c-gallery__item --is-' .
                                    $image['type'] .
                                    '">';
                                if (!empty($link)) {
                                    echo '<a class="c-gallery__link" ' .
                                        oo_set_link_attr($link) .
                                        '>';
                                }

                                if (
                                    $image['type'] == 'provenexpert' ||
                                    $image['type'] == 'immoscout24' ||
                                    $image['type'] == 'immowelt'
                                ) {
                                    oo_set_seal_content(
                                        $image,
                                        'c-gallery',
                                        $is_slider,
                                    );
                                } else {
                                    if (!empty($image['image']['image'])) {
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-image',
                                            [
                                                'image' =>
                                                    $image['image']['image'] ??
                                                    null,
                                                'picture_class' =>
                                                    'c-gallery__picture o-picture',
                                                'image_class' =>
                                                    'c-gallery__image o-image',
                                                'additional_cloudimg_params' =>
                                                    '&func=bound&gravity=center',
                                                'dimensions' => [
                                                    '575' => [
                                                        'w' => $masonry_image_width_xs,
                                                    ],
                                                    '1600' => [
                                                        'w' => $masonry_image_height_xxxl,
                                                        'h' => $masonry_image_height_xxxl,
                                                    ],
                                                    '1400' => [
                                                        'w' => $masonry_image_height_xxl,
                                                        'h' => $masonry_image_height_xxl,
                                                    ],
                                                    '1200' => [
                                                        'w' => $masonry_image_height_xl,
                                                        'h' => $masonry_image_height_xl,
                                                    ],
                                                    '992' => [
                                                        'w' => $masonry_image_height_lg,
                                                        'h' => $masonry_image_height_lg,
                                                    ],
                                                    '768' => [
                                                        'w' => $masonry_image_height_md,
                                                        'h' => $masonry_image_height_md,
                                                    ],
                                                    '576' => [
                                                        'w' => $masonry_image_width_sm,
                                                    ],
                                                ],
                                            ],
                                        );
                                    } else {
                                        echo '<div class="c-gallery__placeholder c-placeholder"></div>';
                                    }
                                }
                                if (!empty($link)) {
                                    echo '</a>';
                                }
                                echo '</div>';
                            } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
