<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$type = get_field('type') ?? 'custom';
$filter = get_field('filter') ?? 'all';
$reviews = get_field('reviews') ?? null;
$number = get_field('number') ?? null;
$buttons = get_field('buttons') ?? [];
$sort_by = get_field('sort_by') ?? 'date';

$show_images =
    get_field('general', 'option')['ratings']['show_images'] ?? 'images';
$placeholder_image =
    get_field('general', 'option')['ratings']['placeholder_image'] ?? null;
$show_date = filter_var(
    get_field('general', 'option')['ratings']['show_date'] ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

$is_sticky_overall_google_rating = filter_var(
    get_field('sticky_overall_google_rating') ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

// Google APIs
$google_api_key = get_option('onoffice-settings-googlemaps-key') ?? null;
$place_id = $third_parties['google']['place_id'] ?? null;
$place_id_override = get_field('place_id') ?? null;

if (!empty($place_id_override)) {
    $place_id = $place_id_override;
}

if (
    function_exists('oo_get_google_place') &&
    !empty($google_api_key) &&
    !empty($place_id)
) {
    $rating = oo_get_google_place($place_id, $google_api_key, 5, 'rating');
}

$tooltip_headline = esc_html__('Echtheit der Bewertungen', 'oo_theme');

$info_text = esc_html__(
    'Die hier veröffentlichten Bewertungen stammen ausschließlich von Personen, die unsere Dienstleistungen / unser Angebot tatsächlich in Anspruch genommen
haben. Eine Überprüfung der Echtheit der erfolgten Bewertungen, stammt durch manuelle Prüfung vor Veröffentlichung auf dieser Webseite.',
    'oo_theme',
);

if ($type === 'google') {
    $info_text = esc_html__(
        'Die hier veröffentlichten Bewertungen stammen von Personen, die unsere Dienstleistungen auf Portalen von Dritten bewertet haben. Eine Prüfung der Echtheit kann derzeit nicht sicher gestellt werden, da die bewertenden
    Personen teilweise unter Synonymen diese auf den Seiten von Dritten hinterlassen.',
        'oo_theme',
    );
}

ob_start(); // Start buffer
oo_get_template('components', '', 'component-tooltip', [
    'text' => $info_text,
    'headline' => $tooltip_headline,
]);
$tooltip_html = ob_get_clean();

// Reviews Query
$paged = is_home() || is_front_page() ? 'page' : 'paged';
$no_found_rows = false;

$query_args = [
    'post_type' => 'oo_reviews',
    'post_status' => 'publish',
    'hide_empty' => true,
    'no_found_rows' => $no_found_rows,
    'paged' => get_query_var($paged) ? get_query_var($paged) : true,
];

if ($filter == 'all') {
    $query_args += [
        'posts_per_page' => get_option('posts_per_page'),
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'review_date',
                'compare' => 'NOT EXISTS',
            ],
            [
                'relation' => 'OR',
                [
                    'key' => 'review_date',
                    'value' => 'on',
                ],
                [
                    'key' => 'review_date',
                    'value' => 'on',
                    'compare' => '!=',
                ],
            ],
        ],
        'orderby' =>
            $sort_by === 'date' || !$sort_by
                ? [
                    'meta_value' => 'DESC',
                    'date' => 'DESC',
                ]
                : [
                    'menu_order' => 'ASC',
                ],
    ];
} elseif ($filter == 'latest') {
    $query_args += [
        'posts_per_page' => !empty($number)
            ? $number
            : get_option('posts_per_page'),
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'review_date',
                'compare' => 'NOT EXISTS',
            ],
            [
                'relation' => 'OR',
                [
                    'key' => 'review_date',
                    'value' => 'on',
                ],
                [
                    'key' => 'review_date',
                    'value' => 'on',
                    'compare' => '!=',
                ],
            ],
        ],
        'orderby' => [
            'meta_value' => 'DESC',
            'date' => 'DESC',
        ],
        'no_found_rows' => true,
    ];
} elseif ($filter == 'individual') {
    $query_args += [
        'posts_per_page' => -1,
        'post__in' => $reviews,
        'orderby' => [
            'post__in' => 'ASC',
        ],
        'no_found_rows' => true,
    ];
}

$reviews_query = new WP_Query($query_args);

$max_num_pages = $reviews_query->max_num_pages ?? null;
?>

<section <?php oo_block_id($block); ?> class="c-reviews <?php echo '--is-' .
     $type .
     '-reviews'; ?> o-section --<?php echo $bg_color; ?>">
    <div class="c-reviews__container o-container">

        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-reviews__content o-row --is-<?php echo $type; ?>-reviews">



                <div class="c-reviews__headline-wrapper o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
 <?php if (!empty($headline['text'])) { ?>
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-headline',
                                [
                                    'headline' => $headline,
                                    'additional_headline_class' =>
                                        'c-reviews__headline',
                                ],
                            ); ?>
                        <?php } ?>

                        <?php if ($type !== 'google') {
                            echo $tooltip_html;
                        } ?>

</div>


















            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-reviews__text o-text --is-wysiwyg o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
            	</div>
        <?php } ?>

        <?php if ($reviews_query->have_posts() && $type != 'google'): ?>
            <?php if ($is_slider) { ?>
                <div class="c-reviews__slider --on-<?php echo $bg_color; ?> c-slider --is-reviews-slider splide" 
                    data-splide='{
                        "perPage":1,
                        "perMove":1,
                        "gap":32,
                        "pagination":false,
                        "snap":true,
                        "lazyLoad":"nearby",
                        "mediaQuery":"min",
                        "breakpoints":{
                            "768":{
                                "perPage":2
                            },
                            "1280":{
                                "perPage":3
                            }
                        }
                    }'>
                	<div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                        <?php } else { ?>
                            <div class="c-reviews__reviews --on-<?php echo $bg_color; ?> ">
                        <?php } ?>
                            <?php while ($reviews_query->have_posts()):
                                $reviews_query->the_post();
                                setup_postdata($reviews_query);
                                require 'review-card.php';
                            endwhile; ?>
                        <?php if ($is_slider) { ?>
                            </div>
                        </div>
                    <div class="c-slider__navigation splide__navigation">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                                    'chevron-left',
                                ); ?></span>
                            </button>
                            <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                                    'chevron-right',
                                ); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                </div>
                <?php oo_get_template(
                    'components',
                    '',
                    'component-pagination',
                    [
                        'numpages' => $max_num_pages,
                        'class' => 'c-reviews__pagination --on-' . $bg_color,
                    ],
                ); ?>
            <?php } ?>
        <?php wp_reset_postdata();endif; ?>




        <?php if ($type === 'google') { ?>
            <div class="c-reviews__google-header">
<div class="c-reviews__google-wrapper">
            <span class="c-reviews__google-logo"><?php oo_get_icon(
                'google-logo',
            ); ?>
                                </span>
            <span class="c-reviews__google-headline"><?php esc_html_e(
                'Reviews',
                'oo_theme',
            ); ?></span>
            <?php echo $tooltip_html; ?>
</div>
<?php if ($rating && !$is_sticky_overall_google_rating) { ?>
    <div class="c-reviews__google-wrapper">
        <div class="c-reviews__google-total"><?php echo $rating[
            'rating'
        ]; ?></div>
        <div class="c-google-rating__stars --star-color-bg-gold">
        <?php oo_get_template('components', '', 'component-stars', [
            'rating' => $rating['rating'],
        ]); ?>
        </div>
        <div class="c-reviews__google-count"><?php echo '(' .
            $rating['total_ratings'] .
            ')'; ?></div>
    </div>
<?php } ?>
</div>

            <?php } ?>
        <?php if ($type == 'google' && !$is_slider) { ?>
            <div class="c-reviews__google-reviews o-row ">
                <div class="c-reviews__col --is-grid o-col-12">
                	<?php require 'google-review-card.php'; ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($type == 'google' && $is_slider) { ?>
    <div class="c-reviews__google-reviews o-row">
        <div class="c-reviews__col o-col-12">
            <div class="c-reviews__slider --on-<?php echo $bg_color; ?> c-slider --is-reviews-slider splide" 
                data-splide='{
                    "perPage":1,
                    "perMove":1,
                    "gap":32,
                    "pagination":false,
                    "snap":true,
                    "lazyLoad":"nearby",
                    "mediaQuery":"min",
                    "breakpoints": {
                        "768":{
                            "perPage":2
                        }, 
                        "1280":{
                            "perPage":3
                        }
                    }
                }'>
                <div class="c-slider__track splide__track">
                    <div class="c-slider__list splide__list">
                        <?php require 'google-review-card.php'; ?>
                    </div>
                </div>
                <div class="c-slider__navigation splide__navigation">
                    <div class="c-slider__progress splide__progress">
                        <div class="c-slider__progress-bar splide__progress-bar"></div>
                    </div>
                    <div class="c-slider__arrows splide__arrows">
                        <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                            <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                'Vorheriges',
                                'oo_theme',
                            ); ?></span>
                            <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                                'chevron-left',
                            ); ?></span>
                        </button>
                        <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                            <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                'Nächstes',
                                'oo_theme',
                            ); ?></span>
                            <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                                'chevron-right',
                            ); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($buttons['buttons'][0]['link'])) {
        oo_get_template('components', '', 'component-buttons', [
            'buttons' => $buttons['buttons'],
            'additional_button_class' => $bg_color ? '--on-' . $bg_color : '',
            'additional_container_class' => 'c-reviews__buttons',
        ]);
    } ?>
    </div>
</section>
