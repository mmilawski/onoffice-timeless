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

// Image height
$image_width_xs = '539';
$image_width_sm = '508';
$image_width_md = '690';
$image_width_lg = '444';
$image_width_xl = '540';
$image_width_xxl = '636';
$image_width_xxxl = '702';
?>

<article class="c-review-card --bg-transparent <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">
    <div class="c-review-card__wrapper <?php echo $show_images !== 'images'
        ? '--hide-images'
        : ''; ?>">
        <?php if (!empty($image) && $show_images === 'images') { ?>
            <?php oo_get_template('components', '', 'component-image', [
                'image' => $image,
                'picture_class' => 'c-review-card__picture o-picture',
                'image_class' => 'c-review-card__image o-image',
                'additional_cloudimg_params' => '&func=crop&gravity=face',
                'dimensions' => [
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
        <?php } ?>
        <?php if ($date && $show_date) { ?>
            <div class="c-review-card__date"><?php echo $date; ?></div>
        <?php } ?>
    </div>
    <?php if (!empty($title) || !empty($text['wysiwyg']) || !empty($stars)) { ?>
        <div class="c-review-card__content <?php echo $show_images !== 'images'
            ? '--hide-images'
            : ''; ?> <?php echo !$show_date ? '--hide-date' : ''; ?>">
            <?php if ($title) { ?>
                <h3 class="c-review-card__name o-headline --h3"><?php echo $title; ?></h3>
            <?php } ?>
            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-review-card__text o-text --is-wysiwyg">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
            <?php if ($stars) { ?>
                <div class="c-review-card__stars c-stars">
                    <?php
                    $stars_average = round($stars * 2) / 2;
                    $stars_all = 5;

                    // full stars
                    for ($i = 0; $i < floor($stars_average); $i++) {
                        $stars_all--;
                        echo '<span class="c-stars__star --filled">';
                        oo_get_icon('star');
                        echo '</span>';
                    }

                    // half stars
                    if ($stars - floor($stars_average) === 0.5) {
                        $stars_all--;
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
                    for ($i = 0; $i < $stars_all; $i++) {
                        echo '<span class="c-stars__star --empty">';
                        oo_get_icon('star');
                        echo '</span>';
                    }
                    ?>
                </div>
            <?php } ?>  
        </div>
    <?php } ?>
</article>
