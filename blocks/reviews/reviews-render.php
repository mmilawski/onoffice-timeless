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
        'post_in' => $reviews,
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
            <div class="c-reviews__content o-row <?php echo '--is-' .
                $type .
                '-reviews'; ?> ">

                <?php if (!empty($headline['text'])) { ?>
        	<?php oo_get_template('components', '', 'component-headline', [
             'headline' => $headline,
             'additional_headline_class' =>
                 'c-reviews__headline o-col-12 o-col-lg-10 o-col-xl-8',
         ]); ?>
                <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-reviews__text o-text --is-wysiwyg o-col-12 o-col-lg-10 o-col-xl-8">
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

        <?php if ($type == 'google' && !$is_slider) { ?>
            <div class="c-reviews__google-reviews o-row --position-center">
                <div class="c-reviews__col --is-grid o-col-12">
                	<?php require 'google-review-card.php'; ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($type == 'google' && $is_slider) { ?>
    <div class="c-reviews__google-reviews o-row --position-center">
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
            'additional_container_class' =>
                'c-reviews__buttons --position-center o-col-12',
        ]);
    } ?>
    </div>
</section>