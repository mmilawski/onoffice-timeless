<?php
// Content
$slider = get_field('slider') ?? [];
$slide_count = is_array($slider) ? count($slider) : 0;
$first_slide = true;

// Settings
$settings = get_field('settings') ?? null;

$autoslide = filter_var(
    get_field('autoslide') ?? false,
    FILTER_VALIDATE_BOOLEAN,
);
$slide_interval = intval(get_field('slide_interval')) ?? 5;
$pause_on_hover = filter_var(
    get_field('pause_on_hover') ?? false,
    FILTER_VALIDATE_BOOLEAN,
);
$slide_speed = intval(get_field('slide_speed')) ?? 1000;
?>

<div <?php oo_block_id($block); ?> class="c-banner --<?php echo $settings[
     'bg_color'
 ]; ?> --<?php echo $settings['bg_color']; ?>-mixed">

<?php if ($slide_count > 1) { ?>
    <div class="c-banner__slider --on-<?php echo $settings[
        'bg_color'
    ]; ?> c-slider splide --auto-height --is-banner-slider" data-splide='{
    "perPage":1,
    "perMove":1,
    "pagination":false,
    "arrows":true,
    "snap":true,
    "lazyLoad":"nearby",
    "type":"<?php echo $autoslide ? 'loop' : 'slide'; ?>",
    "pagination":true,
    "updateOnMove":true,
    "classes":{
        "page":"c-slider__page splide__pagination__page"
    },
    "autoplay": <?php echo $autoslide ? 'true' : 'false'; ?>,
    "pauseOnHover": <?php echo $pause_on_hover ? 'true' : 'false'; ?>,
    "interval": <?php echo json_encode($slide_interval) * 1000; ?>,
    "speed": <?php echo json_encode($slide_speed); ?>
    }'>
        <div class="c-slider__track splide__track">
            <div class="c-slider__list splide__list">
<?php } ?>
                <?php foreach ($slider as $slide) {

                    // Content slide
                    $background = $slide['background'] ?? null;
                    $image = $slide['image'] ?? null;
                    $video = $slide['video'] ?? null;
                    $type = $slide['type'] ?? null;
                    $headline = $slide['headline'] ?? null;
                    $text = $slide['text'] ?? null;
                    $buttons = $slide['buttons'] ?? null;
                    $shortcode = $slide['shortcode'] ?? null;

                    // Settings slide
                    $slide_settings = $slide['settings'] ?? null;

                    if ($first_slide === true) {
                        $slide_loading = 'eager';
                        $slide_decoding = 'auto';
                    } else {
                        $slide_loading = 'lazy';
                        $slide_decoding = 'auto';
                    }

                    if (!empty($video)) {
                        // Find iframe src
                        preg_match('/src="(.+?)"/', $video, $matches);
                        $src = $matches[1];

                        // Extra parameters to src and class for Usercentrics
                        if (str_contains($src, 'vimeo')) {
                            $video_class = '--is-vimeo';
                            $params = [
                                'controls' => 0,
                                'autoplay' => 1,
                                'muted' => 1,
                                'autopause' => 0,
                                'loop' => 1,
                            ];
                        } elseif (str_contains($src, 'youtu')) {
                            $video_parse_url = parse_url($src) ?? null;
                            $video_id = !empty($video_parse_url)
                                ? end(explode('/', $video_parse_url['path']))
                                : 0;
                            $video_class = '--is-youtube';
                            $params = [
                                'controls' => 0,
                                'hd' => 1,
                                'autohide' => 1,
                                'rel' => 0,
                                'autoplay' => 1,
                                'mute' => 1,
                                'playsinline' => 1,
                                'loop' => 1,
                                'playlist' => $video_id,
                            ];
                        } else {
                            $video_class = '';
                            $params = [];
                        }
                        $new_src = add_query_arg($params, $src);
                        $iframe = str_replace($src, $new_src, $video);
                        $attributes =
                            'class="c-banner__video" loading="' .
                            $slide_loading .
                            '"';
                        $iframe = str_replace(' frameborder="0"', '', $iframe);
                        $iframe_with_attributes = str_replace(
                            '></iframe>',
                            ' ' . $attributes . '></iframe>',
                            $iframe,
                        );
                    }

                    // Background width
                    $background_width_xs = '576';
                    $background_width_sm = '768';
                    $background_width_md = '992';
                    $background_width_lg = '1200';
                    $background_width_xl = '1400';
                    $background_width_xxl = '1600';
                    $background_width_xxxl = '1920';
                    ?>

                    <div class="c-banner__slide --content-<?php echo $type; ?> --background-<?php echo $background; ?> <?php if (
     $slide_count > 1
 ) { ?>c-slider__slide splide__slide<?php } ?>">
                        <div class="c-banner__background">
                            <?php if (
                                !empty($image) &&
                                $background == 'image'
                            ): ?>
                                <?php oo_get_template(
                                    'components',
                                    '',
                                    'component-image',
                                    [
                                        'image' => $image,
                                        'picture_class' =>
                                            'c-banner__picture o-picture',
                                        'image_class' =>
                                            'c-banner__image o-image --' .
                                            $slide_settings['image_crop'],
                                        'additional_cloudimg_params' =>
                                            '&func=crop&gravity=' .
                                            $slide_settings['image_crop'],
                                        'loading' => $slide_loading,
                                        'decoding' => $slide_decoding,
                                        'dimensions' => [
                                            '575' => [
                                                'w' => $background_width_xs,
                                                'h' => $background_width_xs,
                                            ],
                                            '1600' => [
                                                'w' => $background_width_xxxl,
                                                'h' => round(
                                                    ($background_width_xxxl *
                                                        9) /
                                                        16,
                                                ),
                                            ],
                                            '1400' => [
                                                'w' => $background_width_xxl,
                                                'h' => round(
                                                    ($background_width_xxl *
                                                        9) /
                                                        16,
                                                ),
                                            ],
                                            '1200' => [
                                                'w' => $background_width_xl,
                                                'h' => round(
                                                    ($background_width_xl * 9) /
                                                        16,
                                                ),
                                            ],
                                            '992' => [
                                                'w' => $background_width_lg,
                                                'h' => $background_width_lg,
                                            ],
                                            '768' => [
                                                'w' => $background_width_md,
                                                'h' => $background_width_md,
                                            ],
                                            '576' => [
                                                'w' => $background_width_sm,
                                                'h' => $background_width_sm,
                                            ],
                                        ],
                                    ],
                                ); ?>
                            <?php endif; ?>

                            <?php if (
                                !empty($video) &&
                                $background == 'video'
                            ): ?>
                                <div class="c-banner__video-wrapper <?php echo $video_class; ?>">
                                    <?php echo $iframe_with_attributes; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($type != 'none'): ?>
                            <div class="c-banner__container o-container">
                                <div class="c-banner__row o-row --position-<?php echo $slide_settings[
                                    'position_content'
                                ]; ?>">
                                    <div class="c-banner__content --content-<?php echo $type; ?> o-col-12 o-col-xl-8">
                                        <?php if (!empty($headline['text'])) {
                                            $headline_size =
                                                $headline['size'] == 'span'
                                                    ? '--h1'
                                                    : '';
                                            oo_get_template(
                                                'components',
                                                '',
                                                'component-headline',
                                                [
                                                    'headline' => $headline,
                                                    'additional_headline_class' =>
                                                        'c-banner__headline ' .
                                                        $headline_size,
                                                ],
                                            );
                                        } ?>

                                        <?php if ($type == 'text'): ?>
                                            <?php if (
                                                !empty($text['wysiwyg'])
                                            ) { ?>
                                                <div class="c-banner__text o-text --is-wysiwyg">
                                                    <?php echo $text[
                                                        'wysiwyg'
                                                    ]; ?>
                                                </div>
                                            <?php } ?>

                                            <?php if (
                                                !empty(
                                                    $buttons['buttons'][0][
                                                        'link'
                                                    ]
                                                )
                                            ) {
                                                oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-buttons',
                                                    [
                                                        'buttons' =>
                                                            $buttons['buttons'],
                                                        'additional_button_class' => $settings[
                                                            'bg_color'
                                                        ]
                                                            ? '--on-' .
                                                                $settings[
                                                                    'bg_color'
                                                                ]
                                                            : '',
                                                        'additional_container_class' =>
                                                            'c-banner__buttons',
                                                    ],
                                                );
                                            } ?>
                                        <?php endif; ?>

                                        <?php if (
                                            $type == 'property-search' ||
                                            $type == 'address-search'
                                        ): ?>
                                            <?php if (!empty($shortcode)) { ?>
                                                <?php echo do_shortcode(
                                                    $shortcode,
                                                ); ?>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php $first_slide = false;
                } ?>
<?php if ($slide_count > 1) { ?>
            </div>
            <ul class="c-slider__pagination splide__pagination"></ul>
            <div class="c-slider__navigation splide__navigation">
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
    </div>
<?php } ?>
</div>