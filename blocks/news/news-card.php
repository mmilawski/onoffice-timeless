<?php
// Post ID
$post_id = get_the_ID() ?? null;

// Get data from news-details block
$details = [];
$post_content = get_post_field('post_content', $post_id);
$blocks = parse_blocks($post_content);
foreach ($blocks as $block) {
    if ('oo/news-details' !== $block['blockName']) {
        continue;
    }
    // the below only applies to the specified block above
    $details = $block['attrs']['data'] ?? null;
}

// Content
$card = get_field('news', $post_id) ?? [];
$link = get_the_permalink($post_id) ?? null;
$date = get_the_date('d.m.Y', $post_id) ?? null;

if (isset($card['image']) && $card['image']) {
    $image = $card['image'] ?? null;
} elseif (!empty($details['image'])) {
    $image = [
        'url' =>
            wp_get_attachment_image_url($details['image'], 'original') ?? null,
        'alt' =>
            get_post_meta(
                $details['image'],
                '_wp_attachment_image_alt',
                true,
            ) ?? null,
    ];
} else {
    $image = [];
}
if (isset($card['title']) && $card['title']) {
    $title = $card['title'] ?? null;
} elseif (!empty($details['title'])) {
    $title = $details['title'] ?? null;
} else {
    $title = get_the_title($post_id) ?? null;
}
$excerpt = $card['excerpt']['wysiwyg_excerpt']
    ? $card['excerpt']['wysiwyg_excerpt']
    : $details['text_wysiwyg'] ?? null;

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$is_date = filter_var(
    get_field('general', 'option')['news']['show_date'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

// Image height
$image_width_xs = '539';
$image_width_sm = '508';
$image_width_md = '690';
$image_width_lg = '444';
$image_width_xl = '540';
$image_width_xxl = '636';
$image_width_xxxl = '702';

// Excerpt
$excerpt_limit =
    apply_filters(
        'oo_set_news_excerpt_count_limit',
        OO_NEWS_EXCERPT_WORDS_LIMIT,
    ) ?? null;
$excerpt_delimiter =
    apply_filters('oo_set_news_excerpt_delimiter', '&nbsp;...') ?? null;
$excerpt_trim =
    wp_trim_words($excerpt, $excerpt_limit, $excerpt_delimiter) ?? null;
$excerpt_word_count = str_word_count(strip_tags($excerpt)) ?? null;
?>


<article class="c-news-card --bg-transparent <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">
    <?php if (!empty($link)) { ?>
        <a class="c-news-card__wrapper" href="<?php echo $link; ?>" title="<?php echo esc_html(
    $title,
); ?>">
    <?php } else { ?>
        <div class="c-news-card__wrapper">
    <?php } ?>
			<?php oo_get_template('components', '', 'component-image', [
       'image' => $image,
       'picture_class' => 'c-news-card__picture o-picture',
       'image_class' => 'c-news-card__image o-image',
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

        <?php if ($is_date && !empty($date)) { ?>
            <div class="c-news-card__date"><?php echo $date; ?></div>
        <?php } ?>
    <?php if (!empty($link)) { ?>
        </a>
    <?php } else { ?>
        </div>
    <?php } ?>
    <?php if (!empty($title) || !empty($excerpt) || !empty($link)) { ?>
	    <div class="c-news-card__content">
            <?php if (!empty($title)) { ?>
                <h3 class="c-news-card__title o-headline --h3"><?php echo $title; ?></h3>
            <?php } ?>
			<?php if (!empty($excerpt)) { ?>
				<div class="c-news-card__text o-text --is-wysiwyg">
					<?php if ($excerpt_word_count > $excerpt_limit) {
         echo $excerpt_trim;
     } else {
         echo $excerpt;
     } ?>
				</div>
			<?php } ?>

			<?php if (!empty($link)) {
       echo '<a class="c-news-card__button c-button --full-width --on-bg-transparent" href="' .
           $link .
           '">';
       echo esc_html__('Mehr erfahren', 'oo_theme');
       echo '</a>';
   } ?>
		</div> 
    <?php } ?> 
</article>
