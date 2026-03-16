<?php
// Fields
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$gallery = get_field('gallery') ?? [];
$images = $gallery['repeater'] ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

$image_height_mobile = '180';
$image_height_desktop = '350';
// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$has_content =
    !empty($headline['text']) || !empty($text['wysiwyg']) ? true : false;

// Slider destroy
$slides_count = is_array($images) ? count($images) : 0;
$slides_destroy = $slides_count < 5 ? true : false;
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-gallery o-section --<?php echo $bg_color; ?>">
    <div class="c-gallery__container o-container">
        <div class="c-gallery__row o-row">

            <?php if ($has_content) { ?>
                <div class="c-gallery__content o-col-12 o-col-xl-10 u-offset-0 u-offset-xl-1">
            <?php } ?>

                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-gallery__headline',
                        ],
                    ); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-gallery__text o-text --is-wysiwyg">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>

            <?php if ($has_content) { ?>
                </div>
            <?php } ?>
            
            <?php if ($gallery['repeater'] && !$is_slider): ?>
                <div class="c-gallery__wrapper o-col-12">
                    <div class="c-gallery__inner --is-grid <?php echo '--on-' .
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
                                $image['type'] == 'immowelt' ||
                                $image['type'] == 'trustlocal'
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
                                                '&gravity=center&org_if_sml=1',
                                            'dimensions' => [
                                                '575' => [
                                                    'h' => $image_height_mobile,
                                                ],
                                                '1600' => [
                                                    'h' => $image_height_desktop,
                                                ],
                                                '1400' => [
                                                    'h' => $image_height_desktop,
                                                ],
                                                '1200' => [
                                                    'h' => $image_height_desktop,
                                                ],
                                                '992' => [
                                                    'h' => $image_height_desktop,
                                                ],
                                                '768' => [
                                                    'h' => $image_height_desktop,
                                                ],
                                                '576' => [
                                                    'h' => $image_height_mobile,
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
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($gallery['repeater'] && $is_slider): ?>
        <div class="c-gallery__wrapper --is-full-width">
            <div class="c-gallery__inner --is-slider">
                <div class="c-gallery__slider c-slider --is-gallery-slider <?php echo $slides_destroy
                    ? '--is-destroyed'
                    : ''; ?> --on-<?php echo $bg_color; ?> splide" data-splide='{
                    "pagination":false,
                    "snap":true,
                    "autoWidth": true,
                    "lazyLoad":"nearby",
                    "focus":"center",
                    "type":"loop",
                    "destroy": <?php echo json_encode($slides_destroy); ?>
                }'>
                    <div class="c-slider__track splide__track">
                        <div class="c-gallery__list c-slider__list splide__list">
                            <?php foreach ($images as $image) {
                                $link = $image['image']['link'] ?? '';
                                echo '<div class="c-gallery__item --is-' .
                                    $image['type'] .
                                    ' c-slider__slide splide__slide">';
                                if (!empty($link)) {
                                    echo '<a class="c-gallery__link" ' .
                                        oo_set_link_attr($link) .
                                        '>';
                                }

                                if (
                                    $image['type'] == 'provenexpert' ||
                                    $image['type'] == 'immoscout24' ||
                                    $image['type'] == 'immowelt' ||
                                    $image['type'] == 'trustlocal'
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
                                                    '&gravity=center',
                                                'dimensions' => [
                                                    '575' => [
                                                        'h' => $image_height_mobile,
                                                    ],
                                                    '1600' => [
                                                        'h' => $image_height_desktop,
                                                    ],
                                                    '1400' => [
                                                        'h' => $image_height_desktop,
                                                    ],
                                                    '1200' => [
                                                        'h' => $image_height_desktop,
                                                    ],
                                                    '992' => [
                                                        'h' => $image_height_desktop,
                                                    ],
                                                    '768' => [
                                                        'h' => $image_height_desktop,
                                                    ],
                                                    '576' => [
                                                        'h' => $image_height_mobile,
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
                    <div class="c-slider__navigation splide__navigation o-container">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only">
                                    <?php esc_html_e(
                                        'Vorheriges',
                                        'oo_theme',
                                    ); ?>
                                </span>
                                <span class="c-slider__arrow-icon --chevron-left">
                                    <?php oo_get_icon('chevron-left'); ?>
                                </span>                                
                            </button>
                            <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only">
                                    <?php esc_html_e('Nächstes', 'oo_theme'); ?>
                                </span>
                                <span class="c-slider__arrow-icon --chevron-right">
                                    <?php oo_get_icon('chevron-right'); ?>
                                </span>                                
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>