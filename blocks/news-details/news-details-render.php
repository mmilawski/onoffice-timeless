<?php
// Post ID
$post_id = get_the_ID() ?? null;

$image = get_field('image') ?? [];
$title = get_field('title') ? get_field('title') : get_the_title() ?? null;
$text = get_field('text') ?? [];
$date = get_the_date('d.m.Y', $post_id) ?? null;
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Settings
$settings = get_field('settings') ?? [];
$image_crop = $settings['image_crop'] ?? 'center';
$is_date = filter_var(
    get_field('general', 'option')['news']['show_date'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

// Image width
$image_width_xxs = '382';
$image_width_xs = '544';
$image_width_sm = '512';
$image_width_md = '694';
$image_width_lg = '448';
$image_width_xl = '544';
$image_width_xxl = '640';
$image_width_xxxl = '706';

// Set Alt attribute for background image
$image['alt'] = $image['alt'] ?? $title;
?>
<section <?php oo_block_id(
    $block,
); ?> class="c-news-details o-section --<?php echo $bg_color; ?>">


        <div class="c-news-details__container o-container">
            <div class="c-news-details__row o-row">
                <div class="c-news-details__content o-col-12 o-col-lg-6">
                    <?php
                    // Get post categories (excluding default "Uncategorized" category)
                    require_once get_template_directory() .
                        '/shared/includes/category.php';
                    $categories = get_filtered_categories($post_id);
                    if (!empty($categories)): ?>
                    <div class="c-news-details__categories">
                        <?php foreach ($categories as $category): ?>
                            <span class="c-news-details__category">
                                <?php echo esc_html($category->name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif;
                    ?>

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
                                    'c-news-details__title --h1',
                            ],
                        ); ?>
                    <?php } ?>
                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-news-detail__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>
                </div>

                <?php if (!empty($image)) { ?>
                <div class="c-news-details__media o-col-12 o-col-lg-6">
                    <?php oo_get_template('components', '', 'component-image', [
                        'image' => $image,
                        'picture_class' => 'c-news-details__picture o-picture',
                        'image_class' => 'c-news-details__image o-image',
                        'dimensions' => [
                            '414' => [
                                'w' => $image_width_xxs,
                                'h' => round(($image_width_xxs * 3) / 4),
                            ],
                            '575' => [
                                'w' => $image_width_xs,
                                'h' => round(($image_width_xs * 3) / 4),
                            ],
                            '1600' => [
                                'w' => $image_width_xxxl,
                                'h' => round(($image_width_xxxl * 3) / 4),
                            ],
                            '1400' => [
                                'w' => $image_width_xxl,
                                'h' => round(($image_width_xxl * 3) / 4),
                            ],
                            '1200' => [
                                'w' => $image_width_xl,
                                'h' => round(($image_width_xl * 3) / 4),
                            ],
                            '992' => [
                                'w' => $image_width_lg,
                                'h' => round(($image_width_lg * 3) / 4),
                            ],
                            '768' => [
                                'w' => $image_width_md,
                                'h' => round(($image_width_md * 3) / 4),
                            ],
                            '576' => [
                                'w' => $image_width_sm,
                                'h' => round(($image_width_sm * 3) / 4),
                            ],
                        ],
                    ]); ?>
                </div>
                <?php } ?>
    </div>
</section>