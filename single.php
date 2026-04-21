<?php get_header(); ?>

<main id="primary" class="o-main"> 
<?php while (have_posts()):
    the_post();

    the_content();

    // author
    oo_get_template('blocks', 'news-details', 'author', [
        'current_category' => get_queried_object(),
    ]);
endwhile; ?>
</main>

<?php get_footer();
