<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package oo_theme
 */

$sites = get_field('sites', 'option') ?? [];
$error_id = $sites['error'] ?? null;

get_header();
?>

<main id="primary" class="o-main">

    <?php echo oo_get_blocks_from_page($error_id); ?>

	<?php while (have_posts()):
     the_post();

     if (!is_404()):
        the_content();
     endif;

     // If comments are open or we have at least one comment, load up the comment template.
     if (comments_open() || get_comments_number()):
         comments_template();
     endif;
 endwhile;
// End of the loop.
?>

</main><!-- #main -->

<?php get_footer();
