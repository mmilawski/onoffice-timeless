<?php
$iframe_display = filter_var(
    get_field('iframe_display', 'option')['display_as_iframe'] ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

if (!$iframe_display) {
    get_header();
} else {
    oo_get_template('templates', 'iframe-display', 'header', []);
}

do_action('oo_page_main_top');
?>

<main id="primary" class="o-main">
	
	<?php
 do_action('oo_page_content_top');

 while (have_posts()):
     the_post();
     the_content();
 endwhile;

 do_action('oo_page_content_bottom');
 ?>

</main><!-- #main -->

<?php
do_action('oo_page_main_bottom');

if (!$iframe_display) {
    get_footer();
} else {
    oo_get_template('templates', 'iframe-display', 'footer', []);
}

