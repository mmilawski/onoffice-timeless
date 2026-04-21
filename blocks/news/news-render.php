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
$categories = get_field('categories') ?? null;
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$is_categories = filter_var(
    get_field('general', 'option')['news']['show_categories'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

$unique_id = 'news-' . uniqid();

// set header level for submodule
$size = !empty($headline['text'])
    ? sanitize_header_level($headline['size'])
    : 1;
set_current_header_level($size);

$header_level = get_current_header_level() + 1;

// Position
$posiiton_center = !empty($text['wysiwyg']) ? ' --position-center' : '';

// Query Posts Settings
$global_posts_per_page = get_option('posts_per_page') ?? 12;
$post_not_in = isset($current_post_id) ? [$current_post_id] : [];

$posts_per_page = $global_posts_per_page;
$post_in = null;
$order_by = ['date' => 'DESC'];
$category_in = null;
$include = null;
$no_found_rows = false;

switch ($filter) {
    case 'all':
        $posts_per_page = !empty($number_posts_per_page)
            ? $number_posts_per_page
            : $global_posts_per_page;
        break;
    case 'latest':
        $posts_per_page = !empty($number) ? $number : $global_posts_per_page;
        $no_found_rows = true;
        break;
    case 'individual':
        $posts_per_page = -1;
        $post_in = $posts;
        $order_by = ['post__in' => 'ASC'];
        $no_found_rows = true;
        break;
    case 'category':
        $posts_per_page = !empty($number_posts_per_page)
            ? $number_posts_per_page
            : $global_posts_per_page;
        $include = $categories;
        $category_in = $categories;
        $order_by = ['category__in' => 'DESC'];
        break;
}

if (is_home() || is_front_page()) {
    $paged = get_query_var('page') ? get_query_var('page') : 1;
} else {
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
}

// Initialize helper function
$news_query = oo_get_news_query([
    'posts_per_page' => floatval($posts_per_page),
    'post__in' => $post_in,
    'post__not_in' => $post_not_in,
    'category__in' => $category_in,
    'orderby' => $order_by,
    'no_found_rows' => $no_found_rows,
]);

$categories_query = get_categories([
    'taxonomy' => 'category',
    'include' => $include,
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => true,
]);
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
            <?php if ($is_slider) { ?>
                <div class="c-news__slider --on-<?php echo $bg_color; ?> c-slider --is-news-slider splide" data-splide='{"perPage":1, "perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":3}}}'>
                    <div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                            <?php while ($news_query->have_posts()):
                                $news_query->the_post();
                                setup_postdata($news_query);
                                require 'news-card.php';
                            endwhile; ?>
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
                <?php if ($is_categories && !empty($categories_query)) { ?>

                    <?php wp_enqueue_script('oo-news'); ?>

                    <nav class="c-news__categories c-category-filter">
                        <?php echo "<h{$header_level} " .
                            'class="c-category-filter__headline o-headline --h3">' .
                            esc_html__('Was weckt Ihr Interesse?', 'oo_theme') .
                            "</h{$header_level}>"; ?>
                        <div class="c-category-filter__categories">
                            <div class="c-category-filter__list-item --all">
                                <button class="c-category-filter__item --active" aria-pressed="true" aria-controls="<?php echo $unique_id; ?>" data-category="all"><?php echo esc_html__(
    'Alle',
    'oo_theme',
); ?></button>
                            </div>
                            <ul class="c-category-filter__list">
                                <?php foreach ($categories_query as $category) {
                                    $cat_slug = esc_attr($category->slug ?? '');
                                    $cat_name = esc_html($category->name ?? '');
                                    if ($cat_slug && $cat_name) {
                                        printf(
                                            '<li class="c-category-filter__list-item"><button class="c-category-filter__item" aria-pressed="false" aria-controls="%1$s" data-category="%2$s">%3$s</button></li>',
                                            $unique_id,
                                            $cat_slug,
                                            $cat_name,
                                        );
                                    }
                                } ?>
                            </ul>
                        </div>
                    </nav>
                <?php } ?>

                <div class="c-news__ajax-container" id="<?php echo $unique_id; ?>" 
                    data-ppp="<?php echo $posts_per_page; ?>" 
                    data-exclude="<?php echo implode(',', $post_not_in); ?>"
                    data-categories="<?php echo !empty($categories)
                        ? implode(',', $categories)
                        : ''; ?>"
                    data-bg="<?php echo $bg_color; ?>"
                    data-nonce="<?php echo wp_create_nonce(
                        'news_filter_nonce',
                    ); ?>">
                    <?php require 'news-ajax.php'; ?>
                </div>
            <?php } ?>

            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <?php if (!empty($buttons['buttons'])) { ?>
            <div class="c-news__buttons-wrapper o-row">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-news__buttons --position-center o-col-12',
                ]); ?> 
            </div>
        <?php } ?>

    </div>
</section>