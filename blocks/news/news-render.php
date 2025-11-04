<?php
global $post;

if (is_singular('post')) {
    $current_post_id = get_the_ID() ?? null;
} else {
    $current_post_id = $post->ID ?? 0;
}

$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$filter = get_field('filter') ?? 'all';
$number = get_field('number') ?? null;
$number_posts_per_page = get_field('posts_per_page') ?? null;
$posts = get_field('posts') ?? null;
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Query Posts Settings
$global_posts_per_page = get_option('posts_per_page') ?? 12;
$post_not_in = isset($current_post_id) ? [$current_post_id] : [];

// Position
$posiiton_center = !empty($text['wysiwyg']) ? ' --position-center' : '';

switch ($filter) {
    case 'all':
        $posts_per_page = !empty($number_posts_per_page)
            ? $number_posts_per_page
            : $global_posts_per_page;
        $post_in = null;
        $order_by = [
            'date' => 'DESC',
        ];
        $no_found_rows = false;
        break;

    case 'latest':
        $posts_per_page = !empty($number) ? $number : $global_posts_per_page;
        $post_in = null;
        $order_by = [
            'date' => 'DESC',
        ];
        $no_found_rows = true;
        break;

    case 'individual':
        $posts_per_page = -1;
        $post_in = $posts;
        $order_by = [
            'post__in' => 'ASC',
        ];
        $no_found_rows = true;
        break;
    default:
        $posts_per_page = $global_posts_per_page;
        $post_in = null;
        $order_by = [
            'date' => 'DESC',
        ];
        $no_found_rows = false;
}

if (is_home() || is_front_page()) {
    $paged = get_query_var('page') ? get_query_var('page') : 1;
} else {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
}

$query_args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'hide_empty' => true,
    'posts_per_page' => floatval($posts_per_page),
    'paged' => $paged,
    'post__in' => $post_in,
    'post__not_in' => $post_not_in,
    'orderby' => $order_by,
    'suppress_filters' => false,
    'no_found_rows' => $no_found_rows,
];

$news_query = new WP_Query($query_args);
$found_posts = $news_query->found_posts ?? null;
$max_num_pages = $news_query->max_num_pages ?? null;

// set header level for submodule
$size = !empty($headline['text'])
    ? sanitize_header_level($headline['size'])
    : 1;
set_current_header_level($size);
?>


<section <?php oo_block_id(
    $block,
); ?> class="c-news o-section --<?php echo $bg_color; ?>" <?php if (
    empty($headline['text'])
) {
    echo 'aria-label="' . __('News Übersicht', 'oo_theme') . '"';
} ?>>
    <div class="c-news__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-news__content o-row <?php echo $posiiton_center; ?>">
                <?php if (!empty($headline['text'])) { ?>

                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-news__headline o-col-12 o-col-lg-10 o-col-xl-8',
                        ],
                    ); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-news__text o-text o-col-12 o-col-lg-10 o-col-xl-8 --is-wysiwyg">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

			<?php if ($news_query->have_posts()): ?>
                <?php if (!$is_slider && $filter === 'all') { ?>
                    <div class="c-news__nav o-row">
                        <p class="c-news__count o-col-12">
                            <?php esc_html_e(
                                'Gefundene News:',
                                'oo_theme',
                            ); ?> <span class="c-news__number"><?php echo sprintf(
     '%d',
     $found_posts,
 ); ?></span>
                        </p>
                    </div>
                <?php } ?>

                <?php if ($is_slider) { ?>
                    <div class="c-news__slider --on-<?php echo $bg_color; ?> c-slider --is-news-slider splide" data-splide='{"perPage":1, "perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":3}}}'>
                        <div class="c-slider__track splide__track">
                            <div class="c-slider__list splide__list">
                <?php } else { ?>
                    <div class="c-news__news">
                <?php } ?>

                    <?php while ($news_query->have_posts()):
                        $news_query->the_post();
                        setup_postdata($news_query);
                        require 'news-card.php';
                    endwhile; ?>

                <?php if ($is_slider) { ?>
                    </div>
                        </div>
                        <div class="c-slider__navigation splide__navigation">
                            <div class="c-slider__progress splide__progress">
                                <div class="c-slider__progress-bar splide__progress-bar"></div>
                            </div>
                            <div class="c-slider__arrows splide__arrows">
                                <button class="c-slider__arrow c-icon-button --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
<span class="c-slider__arrow-icon c-icon-button__icon --chevron-left"><?php oo_get_icon(
    'chevron-left',
); ?></span>                                </button>
                                <button class="c-slider__arrow c-icon-button --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
<span class="c-slider__arrow-icon c-icon-button__icon --chevron-right"><?php oo_get_icon(
    'chevron-right',
); ?></span>                                </button>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    </div>
                <?php } ?>
                <?php if (!$is_slider && $filter === 'all') { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-pagination',
                        [
                            'numpages' => $max_num_pages,
                            'class' => 'c-news__pagination --on-' . $bg_color,
                        ],
                    ); ?>
                <?php } ?>
                
            <?php wp_reset_postdata();endif; ?>

        <?php if (!empty($buttons['buttons'])) { ?>

            <div class="o-row">
						<?php oo_get_template('components', '', 'component-buttons', [
          'buttons' => $buttons['buttons'],
          'additional_button_class' => $bg_color ? '--on-' . $bg_color : '',
          'additional_container_class' =>
              'c-news__buttons --position-center o-col-12',
      ]); ?> 
                    <?php } ?>
        </div>
    </div>
</section>