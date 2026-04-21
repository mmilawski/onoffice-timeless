<?php
// Post ID
$post_id = get_the_ID() ?? null;

$author_id = get_field('author', $post_id) ?? null;
$author = $author_id ? get_field('team', $author_id) : null;
$name = $author['name'] ?? null;
$image = $author['image'] ?? null;
$description = $author['description'] ?? [];
$wysiwyg = $description['wysiwyg'] ?? null;
$networks = $author['networks'] ?? [];

// Image width
$image_width_xs = '543';
$image_width_sm = '512';
$image_width_md = '694';
$image_width_lg = '464';
$image_width_xl = '233';
$image_width_xxl = '273';
$image_width_xxxl = '301';
?>

<?php if (!empty($author)) { ?>
    <section class="c-news-details o-section --bg-transparent">
        <div class="c-news-details__container o-container">
            <div class="c-news-details__author c-author o-col-12 o-col-lg-6">
                <?php if (!empty($image['url'])) { ?>
                    <?php oo_get_template('components', '', 'component-image', [
                        'image' => $image,
                        'picture_class' => 'c-author__picture o-picture',
                        'image_class' => 'c-author__image o-image',
                        'additional_cloudimg_params' =>
                            '&func=crop&gravity=face',
                        'dimensions' => [
                            '575' => [
                                'w' => $image_width_xs,
                                'h' => round(($image_width_xs * 4) / 3),
                            ],
                            '1600' => [
                                'w' => $image_width_xxxl,
                                'h' => round(($image_width_xxxl * 4) / 3),
                            ],
                            '1400' => [
                                'w' => $image_width_xxl,
                                'h' => round(($image_width_xxl * 4) / 3),
                            ],
                            '1200' => [
                                'w' => $image_width_xl,
                                'h' => round(($image_width_xl * 4) / 3),
                            ],
                            '992' => [
                                'w' => $image_width_lg,
                                'h' => round(($image_width_lg * 4) / 3),
                            ],
                            '768' => [
                                'w' => $image_width_md,
                                'h' => round(($image_width_md * 4) / 3),
                            ],
                            '576' => [
                                'w' => $image_width_sm,
                                'h' => round(($image_width_sm * 4) / 3),
                            ],
                        ],
                    ]); ?>
                <?php } else { ?>
                    <div class="c-author__placeholder c-placeholder" aria-hidden="true"></div>
                <?php } ?>
                <?php if (
                    !empty($name) ||
                    !empty($wysiwyg) ||
                    !empty($networks)
                ) { ?>
                    <div class="c-author__content">
                        <?php if (!empty($name)) { ?>
                            <h2 class="c-author__author o-headline --h4">
                                <span class="c-author__label"><?php echo esc_html__(
                                    'Autor:',
                                    'oo_theme',
                                ); ?></span>
                                <?php echo esc_html($name); ?>
                            </h2>
                        <?php } ?>
                        <?php if (!empty($wysiwyg)) { ?>
                            <div class="c-author__description">
                                <?php echo wp_kses_post($wysiwyg); ?>
                            </div>
                        <?php } ?>
                        <?php if (!empty($networks)) { ?>
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-social-media',
                                [
                                    'networks' => $networks,
                                    'additional_container_class' =>
                                        'c-author__social-media --small',
                                ],
                            ); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php }
