<?php
// Post ID
$post_id = get_the_ID() ?? null;

// Content
$card = get_field('review', $post_id) ?? null;
$date = $card['date'] ?? null;
$image = $card['image'] ?? null;
$title = $card['title'] ?? null;
$text = $card['text'] ?? [];
$stars = $card['stars'] ?? [];

if (!$image && $placeholder_image) {
    $image = $placeholder_image ?? null;
}

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$is_image = empty($image) ? ' --has-no-image' : '';

// Image dimensions
$image_width_card = '100';
$image_width_dialog = '184';

$uniqid = 'review-' . uniqid();
?>

<article class="c-review-card <?php
if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
}
echo $is_image;
?>">
    <div class="c-review-card__header<?php echo $show_images !== 'images'
        ? ' --hide-images'
        : ''; ?>">
        <?php if (!empty($image) && $show_images === 'images') { ?>
            <div class="c-review-card__image-wrapper">
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' => 'c-review-card__picture o-picture',
                    'image_class' => 'c-review-card__image o-image',
                    'additional_cloudimg_params' => '&func=crop&gravity=face',
                    'dimensions' => [
                        '575' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '1600' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '1400' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '1200' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '992' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '768' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                        '576' => [
                            'w' => $image_width_card,
                            'h' => $image_width_card,
                        ],
                    ],
                ]); ?>
            </div>
        <?php } ?>

        <div class="c-review-card__header-right  <?php echo $show_images !==
        'images'
            ? '--hide-images'
            : ''; ?>">
            <?php if ($date && $show_date) { ?>
                <div class="c-review-card__date"><?php echo $date; ?></div>
            <?php } ?>

            <?php if ($stars) { ?>
                <?php
                $stars_average = round($stars * 2) / 2;
                $stars_all = 5;
                ?>
                <div class="c-review-card__stars c-stars --star-color-bg-primary" role="img" aria-label="<?php echo sprintf(
                    esc_attr__('Bewertung: %1$s von %2$s Sternen', 'oo_theme'),
                    $stars_average,
                    $stars_all,
                ); ?>">
                    <?php
                    // full stars
                    for ($i = 0; $i < floor($stars_average); $i++) {
                        echo '<span class="c-stars__star --filled">';
                        oo_get_icon('star');
                        echo '</span>';
                    }

                    // half stars
                    if ($stars - floor($stars_average) === 0.5) {
                        echo '<span class="c-stars__star --half">';
                        echo '<span class="c-stars__star --filled">';
                        oo_get_icon('star');
                        echo '</span>';
                        echo '<span class="c-stars__star --empty">';
                        oo_get_icon('star');
                        echo '</span>';
                        echo '</span>';
                    }

                    // empty stars
                    for ($i = 0; $i < 5 - ceil($stars_average); $i++) {
                        echo '<span class="c-stars__star --empty">';
                        oo_get_icon('star');
                        echo '</span>';
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="c-review-card__content <?php echo $show_images !== 'images'
        ? '--hide-images'
        : ''; ?> <?php echo !$show_date ? '--hide-date' : ''; ?>">
        <div class="c-review-card__content-group">
            <?php if ($title) { ?>
                <h3 class="c-review-card__name o-headline --h4"><?php echo $title; ?></h3>
            <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-review-card__text o-text --is-wysiwyg" id="<?php echo $uniqid; ?>">
                    <?php echo $text['wysiwyg']; ?>
                </div>

            <?php } ?>
            <button class="c-review-card__more c-read-more --open-popup"
                    data-review-show-more
                    data-popup="<?php echo $uniqid; ?>-dialog"
                    aria-haspopup="dialog"
                    data-open-text="<?php esc_html_e(
                        'Mehr anzeigen',
                        'oo_theme',
                    ); ?>"
                    data-close-text="<?php esc_html_e(
                        'Weniger anzeigen',
                        'oo_theme',
                    ); ?>"
                    aria-expanded="false"
                    aria-controls="<?php echo $uniqid; ?>">
                <?php esc_html_e('Mehr anzeigen', 'oo_theme'); ?>
            </button>
        </div>
    </div>

    <dialog id="<?php echo $uniqid; ?>-dialog" class="c-dialog --is-review --<?php echo $bg_color; ?>" aria-labelledby="<?php echo $uniqid; ?>-dialog">
        <div class="c-dialog__wrapper">
            <button class="c-dialog__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
                'Fenster schließen',
                'oo_theme',
            ); ?>">
                <?php oo_get_icon('close', true, [
                    'class' => 'c-icon-button__icon --close',
                ]); ?>
            </button>
            <div class="c-review-detail">
                <div class="c-review-detail__author">
                    <?php if (!empty($image) && $show_images === 'images') { ?>
                        <?php oo_get_template(
                            'components',
                            '',
                            'component-image',
                            [
                                'image' => $image,
                                'picture_class' =>
                                    'c-review-detail__picture o-picture',
                                'image_class' => 'o-image',
                                'additional_cloudimg_params' =>
                                    '&func=crop&gravity=face',
                                'dimensions' => [
                                    '575' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '1600' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '1400' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '1200' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '992' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '768' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                    '576' => [
                                        'w' => $image_width_dialog,
                                        'h' => $image_width_dialog,
                                    ],
                                ],
                            ],
                        ); ?>
                    <?php } ?>
                    <div class="c-review-detail__info">
                    <?php if ($date && $show_date) { ?>
                        <div class="c-review-detail__date"><?php echo $date; ?></div>
                    <?php } ?>

                    <?php if ($stars) { ?>
                        <?php
                        $stars_average = round($stars * 2) / 2;
                        $stars_all = 5;
                        ?>
                        <div class="c-review-detail__stars c-stars --star-color-bg-primary" role="img" aria-label="<?php echo sprintf(
                            esc_attr__(
                                'Bewertung: %1$s von %2$s Sternen',
                                'oo_theme',
                            ),
                            $stars_average,
                            $stars_all,
                        ); ?>">
                            <?php
                            // full stars
                            for ($i = 0; $i < floor($stars_average); $i++) {
                                echo '<span class="c-stars__star --filled">';
                                oo_get_icon('star');
                                echo '</span>';
                            }

                            // half stars
                            if ($stars - floor($stars_average) === 0.5) {
                                echo '<span class="c-stars__star --half">';
                                echo '<span class="c-stars__star --filled">';
                                oo_get_icon('star');
                                echo '</span>';
                                echo '<span class="c-stars__star --empty">';
                                oo_get_icon('star');
                                echo '</span>';
                                echo '</span>';
                            }

                            // empty stars
                            for ($i = 0; $i < 5 - ceil($stars_average); $i++) {
                                echo '<span class="c-stars__star --empty">';
                                oo_get_icon('star');
                                echo '</span>';
                            }
                            ?>
                        </div>
                    <?php } ?>
                    </div>
                    <?php if ($title) { ?>
                        <h2 id="<?php echo $uniqid; ?>-title" class="c-review-detail__title o-headline --h4"><?php echo $title; ?></h2>
                    <?php } ?>
                </div>
                <div class="c-review-detail__content">
                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-review-detail__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </dialog>
</article>
