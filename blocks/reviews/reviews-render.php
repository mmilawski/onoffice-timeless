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
$posts_per_page = null;
$post_in = null;
$meta_query = null;
$order_by = null;
$no_found_rows = null;

if ($filter == 'all') {
    $posts_per_page = get_option('posts_per_page');
    $post_in = null;
    $meta_query = [
        'relation' => 'OR',
        'sort_clause' => [
            'key' => 'review_date',
        ],
    ];
    $order_by =
        $sort_by === 'date' || !$sort_by
            ? [
                'date' => 'DESC',
                'sort_clause' => 'DESC',
            ]
            : [
                'menu_order' => 'ASC',
            ];
    $no_found_rows = false;
} elseif ($filter == 'latest') {
    $posts_per_page = !empty($number) ? $number : get_option('posts_per_page');
    $post_in = null;
    $meta_query = [
        'relation' => 'OR',
        'date_clause' => [
            'key' => 'review_date',
        ],
    ];
    $order_by = [
        'date' => 'DESC',
        'date_clause' => 'DESC',
    ];
    $no_found_rows = true;
} elseif ($filter == 'individual') {
    $posts_per_page = -1;
    $post_in = $reviews;
    $meta_query = '';
    $order_by = [
        'post__in' => 'ASC',
    ];
    $no_found_rows = true;
}

if (is_home() || is_front_page()) {
    $paged = get_query_var('page') ? get_query_var('page') : 1;
} else {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
}

$query_args = [
    'post_type' => 'oo_reviews',
    'post_status' => 'publish',
    'hide_empty' => true,
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post__in' => $post_in,
    'meta_query' => $meta_query,
    'orderby' => $order_by,
    'suppress_filters' => false,
    'no_found_rows' => $no_found_rows,
];

$reviews_query = new WP_Query($query_args);

$max_num_pages = $reviews_query->max_num_pages ?? null;
?>

<section <?php oo_block_id($block); ?> class="c-reviews <?php echo '--is-' .
     $type .
     '-reviews'; ?> o-section --with-separator --<?php echo $bg_color; ?> <?php echo '--' .
     $bg_color .
     '-mixed'; ?>">
    <div class="c-reviews__container o-container">

        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-reviews__content o-row <?php echo '--is-' .
                $type .
                '-reviews'; ?> ">

                <?php if (!empty($headline['text'])) { ?>
        	<?php oo_get_template('components', '', 'component-headline', [
             'headline' => $headline,
             'additional_headline_class' =>
                 'c-reviews__headline o-col-12 o-col-md-8',
         ]); ?>
                <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-reviews__text o-text --is-wysiwyg o-col-12 o-col-md-8">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
            	</div>
        <?php } ?>

        <?php if ($reviews_query->have_posts() && $type != 'google'): ?>
            <?php if ($is_slider) { ?>
                <div class="c-reviews__slider --on-<?php echo $bg_color; ?> c-slider --is-reviews-slider splide" data-splide='{  "perPage":1,"perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":2}}}'>
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
                            <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                                <span class="u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                            </button>
                            <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                                <span class="u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
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
                <div class="c-reviews__col o-col-12 o-col-md-8">
                	<?php require 'google-review-card.php'; ?>
                </div>
            </div>
        <?php } ?>

        <?php if ($type == 'google' && $is_slider) { ?>
    <div class="c-reviews__google-reviews o-row --position-center">
        <div class="c-reviews__col o-col-md-8">
            <div class="c-reviews__slider --on-<?php echo $bg_color; ?> c-slider --is-reviews-slider splide" data-splide='{"perPage":1,"perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":1}}}'>
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
                        <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                            <span class="u-screen-reader-only"><?php esc_html_e(
                                'Vorheriges',
                                'oo_theme',
                            ); ?></span>
                            <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                        </button>
                        <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                            <span class="u-screen-reader-only"><?php esc_html_e(
                                'Nächstes',
                                'oo_theme',
                            ); ?></span>
                            <svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
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
            'additional_container_class' => 'c-reviews__buttons o-col-12',
        ]);
    } ?>
    </div>
</section>
