<?php
$image = get_field('image') ?? [];
$title = get_field('title') ? get_field('title') : get_the_title() ?? null;
$text = get_field('text') ?? [];
$date = get_the_date('d.m.Y') ?? null;
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Settings
$settings = get_field('settings') ?? [];
$image_crop = $settings['image_crop'] ?? 'center';
$is_date = filter_var(
    get_field('general', 'option')['news']['show_date'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

// Image height
$image_width_xs = '539';
$image_width_sm = '508';
$image_width_md = '690';
$image_width_lg = '444';
$image_width_xl = '540';
$image_width_xxl = '636';
$image_width_xxxl = '702';
?>
<section <?php oo_block_id(
    $block,
); ?> class="c-news-details o-section --<?php echo $bg_color; ?>">


        <div class="c-news-details__container o-container">
            <div class="c-news-details__row o-row">
            <?php if (!empty($image)) { ?>
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' =>
                        'c-news-details__picture o-picture o-col-12 o-col-lg-6 o-col-xl-4',
                    'image_class' => 'c-news-details__image o-image',
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
                echo '<div class="c-media-text__placeholder c-placeholder o-col-12 o-col-lg-6 o-col-xl-4"></div>';
            } ?>


                <div class="c-news-details__content o-col-12 o-col-xl-8">
                    <?php if ($is_date && !empty($date)) { ?>
                        <span class="c-news-details__date">
                            <?php echo $date; ?>            
                        </span>
                    <?php } ?>
                    <?php if (!empty($title)) { ?>
                        <?php oo_get_template(
                            'components',
                            '',
                            'component-headline',
                            [
                                'headline' => [
                                    'text' => strip_tags($title),
                                    'size' => 'h1',
                                ],
                                'additional_headline_class' =>
                                    'c-news-detail__title --h1',
                            ],
                        ); ?>
                    <?php } ?>
                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-news-detail__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>