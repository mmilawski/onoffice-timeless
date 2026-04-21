<?php
// Post ID
$post_id = get_the_ID() ?? null;

// Get categories for this post (excluding default "Uncategorized" category)
require_once get_template_directory() . '/shared/includes/category.php';
$categories = get_filtered_categories($post_id);

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

// get header level from parent block
$header_level = get_current_header_level() + 1;

// Content
$card = get_field('news', $post_id) ?? [];

// Ensure $card is an array
if (!is_array($card)) {
    $card = [];
}

$link = get_the_permalink($post_id) ?? null;
$date = get_the_date('d.m.Y', $post_id) ?? null;
$dateYMD = get_the_date('Y-m-d', $post_id) ?? null;

if (isset($card['title']) && $card['title']) {
    $title = $card['title'] ?? null;
} elseif (!empty($details['title'])) {
    $title = $details['title'] ?? null;
} else {
    $title = get_the_title($post_id) ?? null;
}
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
            ) ?? $title,
    ];
} else {
    $image = [];
}

$excerpt = !empty($card['excerpt']['wysiwyg_excerpt'])
    ? $card['excerpt']['wysiwyg_excerpt']
    : $details['text_wysiwyg'] ?? null;

if ($excerpt) {
    $excerpt = oo_excerpt_no_links_trim_words($excerpt);
}

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$is_date = filter_var(
    get_field('general', 'option')['news']['show_date'] ?? true,
    FILTER_VALIDATE_BOOLEAN,
);

// Image dimensions
$image_width_xxs = '382';
$image_width_xs = '544';
$image_width_sm = '512';
$image_width_md = '694';

if (!$is_slider) {
    $image_width_lg = '387';
    $image_width_xl = '467';
    $image_width_xxl = '547';
    $image_width_xxxl = '602';
} else {
    $image_width_lg = '288';
    $image_width_xl = '352';
    $image_width_xxl = '416';
    $image_width_xxxl = '460';
}

// Helpers
$link_title_date = esc_html($title);
if ($is_date && !empty($date)) {
    $link_title_date =
        $link_title_date .
        sprintf(
            esc_html__(', veröffentlicht am %s', 'oo_theme'),
            esc_html($date),
        );
}
$link_title_more = sprintf(
    esc_html__('Mehr erfahren über %s', 'oo_theme'),
    esc_html($title),
);
?>


<article class="c-news-card --bg-transparent <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">
    <?php if (!empty($link)) { ?>
        <a class="c-news-card__link" href="<?php echo $link; ?>" aria-label="<?php echo $link_title_date; ?>">
    <?php } else { ?>
        <div class="c-news-card__wrapper">
    <?php } ?>
			<?php oo_get_template('components', '', 'component-image', [
       'image' => $image,
       'picture_class' => 'c-news-card__picture o-picture',
       'image_class' => 'c-news-card__image o-image',
       'dimensions' => [
           '414' => [
               'w' => $image_width_xxs,
               'h' => round(($image_width_xxs * 2) / 3),
           ],
           '575' => [
               'w' => $image_width_xs,
               'h' => round(($image_width_xs * 2) / 3),
           ],
           '1600' => [
               'w' => $image_width_xxxl,
               'h' => round(($image_width_xxxl * 2) / 3),
           ],
           '1400' => [
               'w' => $image_width_xxl,
               'h' => round(($image_width_xxl * 2) / 3),
           ],
           '1200' => [
               'w' => $image_width_xl,
               'h' => round(($image_width_xl * 2) / 3),
           ],
           '992' => [
               'w' => $image_width_lg,
               'h' => round(($image_width_lg * 2) / 3),
           ],
           '768' => [
               'w' => $image_width_md,
               'h' => round(($image_width_md * 2) / 3),
           ],
           '576' => [
               'w' => $image_width_sm,
               'h' => round(($image_width_sm * 2) / 3),
           ],
       ],
   ]); ?>

    <?php if (!empty($link)) { ?>
        </a>
    <?php } else { ?>
        </div>
    <?php } ?>
    <?php if (!empty($title) || !empty($excerpt) || !empty($link)) { ?>
	    <div class="c-news-card__content">
            <div class="c-news-card__meta">
                <?php if (!empty($categories)) { ?>
                    <div class="c-news-card__categories">
                        <?php foreach ($categories as $category) { ?>
                            <span class="c-news-card__category c-tag"><?php echo esc_html(
                                $category->name,
                            ); ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ($is_date && !empty($date)) { ?>
                    <time class="c-news-card__date c-flag" datetime="<?php echo $dateYMD; ?>"><?php echo $date; ?></time>
                <?php } ?>
            </div>
            
            <?php if (!empty($title)) { ?>
                <?php echo "<h{$header_level} " .
                    'class="c-news-card__title o-headline --h3">' .
                    $title .
                    "</h{$header_level}>"; ?>
            <?php } ?>
			<?php if (!empty($excerpt)) { ?>
				<div class="c-news-card__text o-text --is-wysiwyg">
					<?php echo $excerpt; ?>
				</div>
			<?php } ?>

			<?php if (!empty($link)) {
       echo '<a class="c-news-card__more-link"  
                aria-label="' .
           esc_html__('Weiterlesen...', 'oo_theme') .
           '" href="' .
           $link .
           '">';
       echo esc_html__('Weiterlesen...', 'oo_theme');
       echo '</a>';
   } ?>
		</div> 
    <?php } ?> 
</article>
