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
    <button class="c-back-to-top" aria-label="<?php echo __(
        'Zurück zum Anfang',
        'oo_theme',
    ); ?>">
        <?php echo oo_get_icon('chevron-up', true, [
            'class' => 'c-back-to-top__icon --chevron-up',
        ]); ?>
    </button>

    <?php if (is_array($footer_content_rows)) { ?>
        <div class="c-footer__top --bg-footer">
            <div class="c-footer__top-container o-container">
                <?php foreach ($footer_content_rows as $row) {
                    $other_columns_open = false; ?>
                <div class="c-footer__top-row o-row">

                    <?php //foreach columns

                    foreach ($row as $key => $column) {
                        $column_modules = $column['modules'] ?? [];
                        if ($key === 'left') { ?>
                        <div class="c-footer__top-column --<?php echo $key; ?> <?php if (
     empty($column_modules)
 ) {
     echo '--is-empty';
 } ?> c-modules --is-footer o-col-xl-4 o-col-12">
                            <?php oo_load_modules_flexible_content(
                                $column,
                                'footer',
                            ); ?>
                        </div>

                    <?php } else {if (!$other_columns_open) {
                                $other_columns_open = true; ?>
                    <div class="o-col-xl-8 o-col-12">
                        <div class="o-row">
                                <?php
                            } ?>
                            <div class="c-footer__top-column --<?php echo $key; ?> <?php if (
     empty($column_modules)
 ) {
     echo '--is-empty';
 } ?> c-modules --is-footer o-col-xl-6 o-col-12">
                                <?php oo_load_modules_flexible_content(
                                    $column,
                                    'footer',
                                ); ?>
                            </div>
                                <?php }
                    } ?>
                        </div>
                    </div>
                </div>
                <?php
                } ?>
            </div>
        </div>
    <?php } ?>
    
    <div class="c-footer__bottom --bg-footer">
        <div class="c-footer__bottom-container o-container">
            <div class="c-footer__bottom-row o-row">
                <div class="c-footer__bottom-column --left o-col-12 o-col-lg-8 o-col-xl-10">
                    <p class="c-footer__bottom-copyright">
                        <span class="c-footer__bottom-text --year">
                            &copy; <?php echo date(
                                'Y',
                            ); ?> <?php echo $company_name; ?>
                        </span>
                    </p>
                    <nav class="c-footer-nav" role="navigation">
                        <ul class="c-footer-nav__list">
                            <?php wp_nav_menu([
                                'theme_location' => 'footer-nav',
                                'menu_class' => '',
                                'container' => false,
                                'before' => '',
                                'after' => '',
                                'link_before' => '',
                                'link_after' => '',
                                'items_wrap' => '%3$s',
                            ]); ?>
                            <li class="c-footer-nav__item --is-top-level">
                                <?php
                                $is_https =
                                    (!empty($_SERVER['HTTPS']) &&
                                        $_SERVER['HTTPS'] !== 'off') ||
                                    (isset(
                                        $_SERVER['HTTP_X_FORWARDED_PROTO'],
                                    ) &&
                                        $_SERVER['HTTP_X_FORWARDED_PROTO'] ===
                                            'https');
                                $scheme = $is_https ? 'https' : 'http';

                                $host = isset($_SERVER['HTTP_HOST'])
                                    ? $_SERVER['HTTP_HOST']
                                    : '';
                                $request_uri = $_SERVER['REQUEST_URI'] ?? '/';

                                $path =
                                    parse_url($request_uri, PHP_URL_PATH) ?:
                                    '/';
                                $raw_query = parse_url(
                                    $request_uri,
                                    PHP_URL_QUERY,
                                );

                                $path = trim($path, '/');
                                if ($path === '') {
                                    $clean_path = '';
                                } else {
                                    $segments = array_filter(
                                        explode('/', $path),
                                        'strlen',
                                    );
                                    $segments = array_map(
                                        'rawurlencode',
                                        $segments,
                                    );
                                    $clean_path = implode('/', $segments);
                                }

                                // rebuild and encode query parameters (if any) using RFC3986
                                $encoded_query = '';
                                if (!empty($raw_query)) {
                                    parse_str($raw_query, $query_params);
                                    $encoded_query = http_build_query(
                                        $query_params,
                                        '',
                                        '&',
                                        PHP_QUERY_RFC3986,
                                    );
                                }

                                // assemble the inner (to-be-passed) URL
                                $inner_path =
                                    $clean_path === ''
                                        ? '/'
                                        : '/' . $clean_path;
                                $inner_url =
                                    $scheme . '://' . $host . $inner_path;
                                if ($encoded_query !== '') {
                                    $inner_url .= '?' . $encoded_query;
                                }

                                // encode the inner URL so it's safe as a query parameter value
                                $param_value = rawurlencode($inner_url);

                                $barrier_found_url =
                                    'https://onoffice.com/barriere-gefunden/?wpf23379_106=' .
                                    $param_value;
                                ?>
                                <a class="c-footer-nav__link --is-top-level"
                                target="_blank"
                                rel="noopener noreferrer"
                                href="<?= esc_url($barrier_found_url) ?>">
                                    <?php esc_html_e(
                                        'Barriere gefunden?',
                                        'oo_theme',
                                    ); ?>
                                </a>
                            </li>
                        </ul>
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