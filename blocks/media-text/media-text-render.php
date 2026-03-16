<?php
// Content
$type = get_field('type') ?? 'image';
$image = get_field('image') ?? [];
$icon = get_field('icon') ?? [];
$thumbnail = get_field('video_thumbnail') ?? [];
$video = get_field('video') ?? null;
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$position_media_text = $settings['position_media_text'] ?? 'left';
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

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
    $attributes = 'loading="lazy" class="c-media-text__iframe" tabindex="0"';
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
$media_width_xs = '544'; // Max stretch at 575 window
$media_width_sm = '512'; // Max stretch at 767 window
$media_width_md = '694'; // Max stretch at 991 window
$media_width_lg = '368'; // Max stretch at 1199 window
$media_width_xl = '448'; // Max stretch at 1399 window
$media_width_xxl = '528'; // Max stretch at 1599 window
$media_width_xxxl = '584';
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-media-text --media-<?php echo $position_media_text; ?> --is-<?php echo $type; ?> o-section --<?php echo $bg_color; ?>">
    <div class="c-media-text__container o-container">
        <div class="c-media-text__row o-row <?php echo $position_media_text ==
        'right'
            ? '--reverse'
            : ''; ?>">
            <?php if (!empty($image) && $type == 'image') { ?>
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' =>
                        'c-media-text__picture o-picture ' .
                        ($position_media_text == 'right'
                            ? 'u-offset-lg-1'
                            : '') .
                        ' o-col-12 o-col-lg-5',
                    'image_class' => 'c-media-text__image o-image',
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
            <?php } elseif (empty($image) && $type == 'image') {
                echo '<div class="c-media-text__placeholder c-placeholder ' .
                    ($position_media_text == 'right' ? 'u-offset-lg-1' : '') .
                    ' o-col-12 o-col-lg-5"></div>';
            } ?>

            <?php if (!empty($icon) && $type == 'icon') { ?>
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $icon,
                    'picture_class' =>
                        'c-media-text__picture ' .
                        ($position_media_text == 'right'
                            ? 'u-offset-lg-1'
                            : '') .
                        ' o-col-12 o-col-lg-5',
                    'image_class' => 'c-media-text__image',
                    'additional_cloudimg_params' => '&func=bound&org_if_sml=1',
                    'dimensions' => [
                        '575' => [
                            'w' => $media_width_xs,
                            'h' => $media_width_xs,
                        ],
                        '1600' => [
                            'w' => $media_width_xxxl,
                            'h' => $media_width_xxxl,
                        ],
                        '1400' => [
                            'w' => $media_width_xxl,
                            'h' => $media_width_xxl,
                        ],
                        '1200' => [
                            'w' => $media_width_xl,
                            'h' => $media_width_xl,
                        ],
                        '992' => [
                            'w' => $media_width_lg,
                            'h' => $media_width_lg,
                        ],
                        '768' => [
                            'w' => $media_width_md,
                            'h' => $media_width_md,
                        ],
                        '576' => [
                            'w' => $media_width_sm,
                            'h' => $media_width_sm,
                        ],
                    ],
                ]); ?>
            <?php } elseif (empty($icon) && $type == 'icon') {
                echo '<div class="c-media-text__placeholder c-placeholder ' .
                    ($position_media_text == 'right' ? 'u-offset-lg-1' : '') .
                    ' o-col-12 o-col-lg-5"></div>';
            } ?>

            <?php if (!empty($video) && $type == 'video') { ?>
                <div class="c-media-text__video <?php echo $position_media_text ==
                'right'
                    ? 'u-offset-lg-1'
                    : ''; ?> o-col-12 o-col-lg-5">
                    <?php if (!empty($thumbnail)) { ?>
                        <div class="c-media-text__thumbnail-wrapper <?php echo $video_class; ?>">
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $thumbnail,
                                    'picture_class' =>
                                        'c-media-text__thumbnail-picture o-picture',
                                    'image_class' =>
                                        'c-media-text__thumbnail-image o-image',
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
                                class="c-media-text__play c-player --on-<?php echo $bg_color; ?> "
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
                        <div class="c-media-text__video-wrapper --has-no-thumbnail <?php echo $video_class; ?>">
                            <?php echo $iframe_with_attributes; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="c-media-text__content <?php echo $position_media_text ==
            'left'
                ? 'u-offset-lg-1'
                : ''; ?> o-col-12 o-col-lg-6">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-media-text__headline',
                        ],
                    ); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-media-text__text o-text --is-wysiwyg">
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
                                'c-media-text__buttons',
                        ],
                    ); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</section>