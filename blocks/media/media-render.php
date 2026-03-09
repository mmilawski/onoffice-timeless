<?php
// Content
$type = get_field('type') ?? 'image';
$image = get_field('image') ?? [];
$thumbnail = get_field('video_thumbnail') ?? [];
$video = get_field('video') ?? null;
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$width_container = $settings['width_container'] ?? 'content-width';
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$image_crop = $settings['image_crop'] ?? 'center';

// Video
if (!empty($video)) {
    // find iframe src.
    preg_match('/src="(.+?)"/', $video, $matches);
    $src = $matches[1];

    // Add extra parameters to src and replace HTML
    $params = [
        'controls' => 1,
        'hd' => 1,
        'autohide' => 1,
        'enablejsapi' => 1,
        'origin' => home_url(),
        'rel' => 0,
    ];
    $new_src = add_query_arg($params, $src);
    $iframe = str_replace($src, $new_src, $video);

    // Class of video type based on the URL for Usercentrics
    if (str_contains($new_src, 'vimeo')) {
        $video_class = '--is-vimeo';
    } elseif (str_contains($new_src, 'youtu')) {
        $video_class = '--is-youtube';
    } else {
        $video_class = '';
    }

    // Add extra attributes to iframe HTML
    $attributes = 'loading="lazy" class="c-media__iframe" tabindex="0"';
    $iframe = str_replace(' frameborder="0"', '', $iframe);
    $iframe_with_attributes = str_replace(
        '></iframe>',
        ' ' . $attributes . '></iframe>',
        $iframe,
    );
    $iframe_without_source = str_replace(
        ' src="',
        ' data-src="',
        $iframe_with_attributes,
    );
}

// Media width
if ($width_container == 'content-width') {
    $media_width_xs = '544';
    $media_width_sm = '512';
    $media_width_md = '694';
    $media_width_lg = '928';
    $media_width_xl = '736';
    $media_width_xxl = '864';
    $media_width_xxxl = '952';
} elseif ($width_container == 'full-width') {
    $media_width_xs = '576';
    $media_width_sm = '768';
    $media_width_md = '992';
    $media_width_lg = '1200';
    $media_width_xl = '1400';
    $media_width_xxl = '1600';
    $media_width_xxxl = '1920';
}
?>

<?php if ($width_container == 'content-width') { ?>
    <section <?php oo_block_id(
        $block,
    ); ?> class="c-media --is-<?php echo $width_container; ?> o-section --<?php echo $bg_color; ?>">
        <div class="c-media__container o-container">
            <div class="c-media__row o-row">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-media__headline u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                        ],
                    ); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-media__text o-text --is-wysiwyg u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>

                <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-buttons',
                        [
                            'buttons' => $buttons['buttons'],
                            'additional_button_class' => $bg_color
                                ? '--on-' . $bg_color
                                : '',
                            'additional_container_class' =>
                                'c-media__buttons u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                        ],
                    ); ?>
                <?php } ?>

                <?php
                if (!empty($image) && $type == 'image') { ?>
                    <?php oo_get_template('components', '', 'component-image', [
                        'image' => $image,
                        'picture_class' =>
                            'c-media__picture o-picture u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                        'image_class' =>
                            'c-media__image o-image --' . $image_crop,
                        'additional_cloudimg_params' =>
                            '&gravity=' . $image_crop,
                        'dimensions' => [
                            '575' => [
                                'w' => $media_width_xs,
                            ],
                            '1600' => [
                                'w' => $media_width_xxxl,
                            ],
                            '1400' => [
                                'w' => $media_width_xxl,
                            ],
                            '1200' => [
                                'w' => $media_width_xl,
                            ],
                            '992' => [
                                'w' => $media_width_lg,
                            ],
                            '768' => [
                                'w' => $media_width_md,
                            ],
                            '576' => [
                                'w' => $media_width_sm,
                            ],
                        ],
                    ]); ?>
                <?php } elseif ($type == 'image') {
                    echo '<div class="c-media__placeholder c-placeholder u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8"></div>';
                }

                if (!empty($video) && $type == 'video') { ?>
                    <div class="c-media__video <?php echo $video_class; ?> u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php if (!empty($thumbnail)): ?>
                            <div class="c-media__thumbnail-wrapper">
                                <?php oo_get_template(
                                    'components',
                                    '',
                                    'component-image',
                                    [
                                        'image' => $thumbnail,
                                        'picture_class' =>
                                            'c-media__thumbnail-picture o-picture',
                                        'image_class' =>
                                            'c-media__thumbnail-image o-image --' .
                                            $image_crop,
                                        'additional_cloudimg_params' =>
                                            '&func=crop&gravity=' . $image_crop,
                                        'dimensions' => [
                                            '575' => [
                                                'w' => $media_width_xs,
                                                'h' => round(
                                                    ($media_width_xs * 9) / 16,
                                                ),
                                            ],
                                            '1600' => [
                                                'w' => $media_width_xxxl,
                                                'h' => round(
                                                    ($media_width_xxxl * 9) /
                                                        16,
                                                ),
                                            ],
                                            '1400' => [
                                                'w' => $media_width_xxl,
                                                'h' => round(
                                                    ($media_width_xxl * 9) / 16,
                                                ),
                                            ],
                                            '1200' => [
                                                'w' => $media_width_xl,
                                                'h' => round(
                                                    ($media_width_xl * 9) / 16,
                                                ),
                                            ],
                                            '992' => [
                                                'w' => $media_width_lg,
                                                'h' => round(
                                                    ($media_width_lg * 9) / 16,
                                                ),
                                            ],
                                            '768' => [
                                                'w' => $media_width_md,
                                                'h' => round(
                                                    ($media_width_md * 9) / 16,
                                                ),
                                            ],
                                            '576' => [
                                                'w' => $media_width_sm,
                                                'h' => round(
                                                    ($media_width_sm * 9) / 16,
                                                ),
                                            ],
                                        ],
                                    ],
                                ); ?>
                                <button 
                                    class="c-media__play c-player --on-<?php echo $bg_color; ?>" 
                                    aria-label="<?php esc_html_e(
                                        'Video ansehen',
                                        'oo_theme',
                                    ); ?>">
                                    <?php oo_get_icon('play', true, [
                                        'class' => 'c-player__icon',
                                    ]); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (empty($thumbnail)) { ?>
                            <div class="c-media__video-wrapper --has-no-thumbnail <?php echo $video_class; ?>">
                                <?php echo $iframe_with_attributes; ?>
                            </div>
                        <?php } else {echo $iframe_without_source;} ?>
                    </div>
                <?php }
                ?>
            </div>
        </div>
    </section>
<?php } elseif ($width_container == 'full-width') { ?>
    <section <?php oo_block_id(
        $block,
    ); ?> class="c-media --is-<?php echo $width_container; ?> o-section --<?php echo $bg_color; ?>">
        <div class="c-media__container o-container">
            <div class="c-media__row o-row">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-media__headline u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                        ],
                    ); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-media__text o-text --is-wysiwyg u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>

                <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
                    <div class="c-media__container o-container">
                        <div class="c-media__row o-row">
                    
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-buttons',
                                [
                                    'buttons' => $buttons['buttons'],
                                    'additional_button_class' => $bg_color
                                        ? '--on-' . $bg_color
                                        : '',
                                    'additional_container_class' =>
                                        'c-media__buttons u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                                ],
                            ); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="c-media__container-fluid o-container-fluid">
            <?php if (!empty($image) && $type == 'image') { ?>
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' => 'c-media__picture o-picture',
                    'image_class' => 'c-media__image o-image --' . $image_crop,
                    'additional_cloudimg_params' =>
                        '&func=crop&gravity=' . $image_crop,
                    'dimensions' => [
                        '575' => [
                            'w' => $media_width_xs,
                            'h' => round(($media_width_xs * 3) / 4),
                        ],
                        '1600' => [
                            'w' => $media_width_xxxl,
                            'h' => round(($media_width_xxxl * 3) / 4),
                        ],
                        '1400' => [
                            'w' => $media_width_xxl,
                            'h' => round(($media_width_xxl * 3) / 4),
                        ],
                        '1200' => [
                            'w' => $media_width_xl,
                            'h' => round(($media_width_xl * 3) / 4),
                        ],
                        '992' => [
                            'w' => $media_width_lg,
                            'h' => round(($media_width_lg * 3) / 4),
                        ],
                        '768' => [
                            'w' => $media_width_md,
                            'h' => round(($media_width_md * 3) / 4),
                        ],
                        '576' => [
                            'w' => $media_width_sm,
                            'h' => round(($media_width_sm * 3) / 4),
                        ],
                    ],
                ]); ?>
            <?php } elseif ($type == 'image') {
                echo '<div class="c-media__placeholder c-placeholder o-col-12"></div>';
            } ?>

            <?php if (!empty($video) && $type == 'video') { ?>
                <div class="c-media__video">
                    <?php if (!empty($thumbnail)) { ?>
                        <div class="c-media__thumbnail-wrapper <?php echo $video_class; ?>">
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $thumbnail,
                                    'picture_class' =>
                                        'c-media__thumbnail-picture o-picture',
                                    'image_class' =>
                                        'c-media__thumbnail-image o-image --' .
                                        $image_crop,
                                    'additional_cloudimg_params' =>
                                        '&func=crop&gravity=' . $image_crop,
                                    'dimensions' => [
                                        '575' => [
                                            'w' => $media_width_xs,
                                            'h' => round(
                                                ($media_width_xs * 9) / 16,
                                            ),
                                        ],
                                        '1600' => [
                                            'w' => $media_width_xxxl,
                                            'h' => round(
                                                ($media_width_xxxl * 9) / 16,
                                            ),
                                        ],
                                        '1400' => [
                                            'w' => $media_width_xxl,
                                            'h' => round(
                                                ($media_width_xxl * 9) / 16,
                                            ),
                                        ],
                                        '1200' => [
                                            'w' => $media_width_xl,
                                            'h' => round(
                                                ($media_width_xl * 9) / 16,
                                            ),
                                        ],
                                        '992' => [
                                            'w' => $media_width_lg,
                                            'h' => round(
                                                ($media_width_lg * 9) / 16,
                                            ),
                                        ],
                                        '768' => [
                                            'w' => $media_width_md,
                                            'h' => round(
                                                ($media_width_md * 9) / 16,
                                            ),
                                        ],
                                        '576' => [
                                            'w' => $media_width_sm,
                                            'h' => round(
                                                ($media_width_sm * 9) / 16,
                                            ),
                                        ],
                                    ],
                                ],
                            ); ?>

                            <button 
                                class="c-media__play c-player --on-<?php echo $bg_color; ?>" 
                                aria-label="<?php esc_html_e(
                                    'Video ansehen',
                                    'oo_theme',
                                ); ?>">
                                <?php oo_get_icon('play', true, [
                                    'class' => 'c-player__icon',
                                ]); ?>
                            </button>
                        </div>
                    <?php echo $iframe_without_source;} else { ?>
                        <div class="c-media__video-wrapper --has-no-thumbnail <?php echo $video_class; ?>">
                            <?php echo $iframe_with_attributes; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
