<?php
if (!isset($news_query)) {
    return;
} ?>

<div class="c-news__nav o-row">
    <p class="c-news__count o-col-12">
        <?php esc_html_e(
            'Gefundene News:',
            'oo_theme',
        ); ?> <span class="c-news__number"><?php echo $news_query->found_posts; ?></span>
    </p>
</div>

<div class="c-news__news">
    <?php while ($news_query->have_posts()):
        $news_query->the_post();
        require 'news-card.php';
    endwhile; ?>
</div>

<?php if ($news_query->max_num_pages > 1): ?>
    <?php oo_get_template('components', '', 'component-pagination', [
        'numpages' => $news_query->max_num_pages,
        'current' => isset($page) ? $page : 1,
        'class' => 'c-news__pagination --on-' . ($bg_color ?? 'bg-transparent'),
    ]); ?>
<?php endif; ?>
