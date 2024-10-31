<?php
$image = get_field('image') ?? [];
$title = get_field('title') ? get_field('title') : get_the_title() ?? null;
$text = get_field('text') ?? [];
$date = get_the_date('d.m.Y') ?? null;

// Settings
$settings = get_field('settings') ?? [];
$image_crop = $settings['image_crop'] ?? 'center';
$is_date = filter_var(
    get_field('general', 'option')['news']['show_date'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

// Background width
$background_width_xs = '576';
$background_width_sm = '768';
$background_width_md = '992';
$background_width_lg = '1200';
$background_width_xl = '1400';
$background_width_xxl = '1600';
$background_width_xxxl = '1920';
?>

<div <?php oo_block_id($block); ?> class="c-news-details">
    <?php if (!empty($image)): ?>
        <div class="c-news-details__banner --bg-transparent">
            <?php oo_get_template('components', '', 'component-image', [
                'image' => $image,
                'picture_class' => 'c-news-details__picture o-picture',
                'image_class' =>
                    'c-news-details__image o-image --' . $image_crop,
                'additional_cloudimg_params' =>
                    '&func=crop&gravity=' . $image_crop,
                'loading' => 'eager',
                'decoding' => 'auto',
                'dimensions' => [
                    '575' => [
                        'w' => $background_width_xs,
                        'h' => $background_width_xs,
                    ],
                    '1600' => [
                        'w' => $background_width_xxxl,
                        'h' => round(($background_width_xxxl * 9) / 16),
                    ],
                    '1400' => [
                        'w' => $background_width_xxl,
                        'h' => round(($background_width_xxl * 9) / 16),
                    ],
                    '1200' => [
                        'w' => $background_width_xl,
                        'h' => round(($background_width_xl * 9) / 16),
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
            ]); ?>
        </div>
    <?php endif; ?>

    <div class="c-news-details__info o-section --bg-transparent --bg-transparent-mixed --with-separator">
        <div class="c-news-details__container o-container">
            <div class="c-news-details__row o-row">
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
</div>