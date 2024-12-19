<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package oo_theme
 */

$footer_content = get_field('footer_content', 'option') ?? [];
$footer_content_rows = $footer_content['rows'] ?? [];
$company = get_field('company', 'option') ?? [];
$company_name = $company['name'] ?? (get_bloginfo('name') ?? null);
?>

<footer class="c-footer">
    <button class="c-back-to-top">
        <span class="c-back-to-top__text u-screen-reader-only"><?php echo __(
            'Zurück zum Anfang',
            'oo_theme',
        ); ?></span>
        <span class="c-back-to-top__icon --chevron-up"><?php oo_get_icon(
            'chevron-up',
        ); ?></span>
    </button>

    <?php if (is_array($footer_content_rows)) { ?>
        <div class="c-footer__top --bg-footer">
            <div class="c-footer__top-container o-container">
            <?php foreach ($footer_content_rows as $row) { ?>
                    <div class="c-footer__top-row o-row">
                    <?php foreach ($row as $key => $column) {
                        $column_modules = $column['modules'] ?? []; ?>
                                <div class="c-footer__top-column --<?php echo $key; ?> <?php if (
     empty($column_modules)
 ) {
     echo '--is-empty';
 } ?> c-modules --is-footer o-col-xl-4 o-col-md-6 o-col-12">
                                    <?php oo_load_modules_flexible_content(
                                        $column,
                                        'footer',
                                    ); ?>
                                </div>
                        <?php
                    } ?>
                    </div>
            <?php } ?>
            </div>
        </div>
    <?php } ?>
    
    <div class="c-footer__bottom">
        <div class="c-footer__bottom-container o-container">
            <div class="c-footer__bottom-row o-row">
                <div class="c-footer__bottom-column --left o-col-12 o-col-lg-8 o-col-xl-10">
                    <p class="c-footer__bottom-copyright">
                        <span class="c-footer__bottom-text --year">
                            &copy; <?php echo date('Y'); ?>
                        </span>
                        <span class="c-footer__bottom-text --name">
                            <?php echo $company_name; ?>
                        </span>
                    </p>
                    <nav class="c-footer-nav">
                        <?php wp_nav_menu([
                            'theme_location' => 'footer-nav',
                            'menu_class' => '',
                            'container' => false,
                            'before' => '',
                            'after' => '',
                            'link_before' => '',
                            'link_after' => '',
                            'items_wrap' =>
                                '<ul class="c-footer-nav__list">%3$s</ul>',
                        ]); ?>
                    </nav>
                </div>
                <div class="c-footer__bottom-column --right o-col-12 o-col-lg-4 o-col-xl-2">
                    <?php echo do_shortcode('[oo-logo]'); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>