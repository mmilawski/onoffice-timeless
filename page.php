<?php
get_header();
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
get_footer();

