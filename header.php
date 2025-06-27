<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package oo_theme
 */

$company = get_field('company', 'option') ?? [];
$company_name = $company['name'] ?? (get_bloginfo('name') ?? null);
$logo = get_field('logo', 'option') ?? [];
$header_content = get_field('header_content', 'option') ?? [];
$header_content_left = $header_content['left'] ?? [];
$header_content_right = $header_content['right'] ?? [];
$header_modules_left = $header_content_left['modules'] ?? [];
$header_modules_right = $header_content_right['modules'] ?? [];

$has_bg_picture = false;
$post = get_post();

if (has_blocks($post->post_content)) {
    $blocks = parse_blocks($post->post_content);
    $is_banner = $blocks[0]['blockName'] === 'oo/banner';
    $is_property_details = $blocks[0]['blockName'] === 'oo/property-details';
    $blocks_count = is_array($blocks) ? count($blocks) : 0;
    if ($blocks_count > 0 && ($is_banner || $is_property_details)) {
        $has_bg_picture = true;
    }
}

$has_no_meta = '';
if (empty($header_modules_left) && empty($header_modules_right)) {
    $has_no_meta = '--has-no-meta';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class('o-body'); ?>>
    <?php wp_body_open(); ?>
    <a class="u-screen-reader-only" href="#primary"><?php esc_html_e(
        'Zum Inhalt wechseln',
        'oo_theme',
    ); ?></a>

    <header id="masthead" class="c-header --bg-header <?php if (
        $logo['appearance']
    ) {
        echo '--logo-' . $logo['appearance'];
    } ?> <?php if ($logo['size']) {
     echo '--logo-' . $logo['size'];
 } ?> <?php if (!$has_bg_picture) {
     echo '--has-no-bg-picture --fixed';
 } ?>">

        <?php if (
            !empty($header_modules_left) ||
            !empty($header_modules_right)
        ): ?>
            <div class="c-header__meta-wrapper --hide-mobile">
                <div class="c-header__meta o-container">
                    <div class="c-header__meta-row">
                        <?php if (!empty($header_modules_left)): ?>
                            <div class="c-header__meta-column --left c-modules --is-header">
                                <?php oo_load_modules_flexible_content(
                                    $header_content_left,
                                    'header',
                                ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($header_modules_right)): ?>
                            <div class="c-header__meta-column --right c-modules --is-header">
                                <?php oo_load_modules_flexible_content(
                                    $header_content_right,
                                    'header',
                                ); ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="c-header__container o-container <?php echo $has_no_meta; ?>">
            <div class="c-header__main-wrapper">
                <div class="c-header__main o-container">
                    <div class="o-logo <?php echo empty($logo['image'])
                        ? '--no-image '
                        : ''; ?> <?= '--' . $logo['size'] ?> <?= '--' .
     $logo['appearance'] ?>">
                        <a class="o-logo__link" href="<?= esc_url(
                            home_url('/'),
                        ) ?>">
                            <?php if (!empty($logo['image']['url'])):
                                $file_type = wp_check_filetype(
                                    $logo['image']['url'],
                                );
                                if ($file_type['ext'] == 'svg') {
                                    $svg_content = file_get_contents(
                                        $logo['image']['url'],
                                    );
                                    if (empty($svg_content)) {
                                        return;
                                    }

                                    $svg = new SimpleXMLElement($svg_content);

                                    // Default fallback size
                                    $fallback_size_width = 340;
                                    $fallback_size_height = 100;

                                    // Check if width or height is missing
                                    $has_width = isset($svg['width']);
                                    $has_height = isset($svg['height']);
                                    $needs_size_fix =
                                        !$has_width || !$has_height;

                                    // Try to extract dimensions from viewBox if available
                                    if (
                                        $needs_size_fix &&
                                        isset($svg['viewBox'])
                                    ) {
                                        $viewbox = explode(
                                            ' ',
                                            $svg['viewBox'],
                                        ); // Expected format: "0 0 width height"
                                        if (
                                            count($viewbox) === 4 &&
                                            $viewbox[2] > 0 &&
                                            $viewbox[3] > 0
                                        ) {
                                            $width = (float) $viewbox[2];
                                            $height = (float) $viewbox[3];
                                        }
                                    }

                                    // If width or height is still missing, set fallback sizes
                                    $width = $width ?? $fallback_size_width;
                                    $height = $height ?? $fallback_size_height;

                                    unset(
                                        $svg['width'],
                                        $svg['height'],
                                        $svg['class'],
                                    );
                                    $svg->addAttribute('width', $width);
                                    $svg->addAttribute('height', $height);
                                    $svg->addAttribute('role', 'img');
                                    $svg->addAttribute(
                                        'aria-label',
                                        $company_name,
                                    );
                                    $svg->addAttribute(
                                        'class',
                                        'o-logo__image --is-svg',
                                    );
                                    echo "{$svg->asXML()}";
                                } else {
                                    echo '<img class="o-logo__image" src="' .
                                        $logo['image']['url'] .
                                        '" alt="' .
                                        $company_name .
                                        '">';
                                }
                            endif; ?>
                        </a>
                    </div>

                    <button class="c-main-nav__button c-icon-button --small-corners" data-open-text="<?php esc_html_e(
                        'Menü öffnen',
                        'oo_theme',
                    ); ?>" data-close-text="<?php esc_html_e(
    'Menü schließen',
    'oo_theme',
); ?>" aria-label="<?php esc_html_e(
    'Menü öffnen',
    'oo_theme',
); ?>" aria-expanded="false" aria-controls="main-nav">
                        <?php echo oo_get_icon('bars', true, [
                            'class' => 'c-icon-button__icon --open',
                        ]); ?>
                        <?php echo oo_get_icon('close', true, [
                            'class' => 'c-icon-button__icon --close',
                        ]); ?>
                    </button>

                </div><!-- #container -->
            </div>

            <div class="c-header__wrapper">
                <div class="c-header__nav-wrapper">
                    <nav id="main-nav" class="c-header__nav c-main-nav o-container" aria-label="<?php echo esc_html_e(
                        'Hauptmenü',
                        'oo_theme',
                    ); ?>" role="navigation">
                        <?php wp_nav_menu([
                            'theme_location' => 'main-nav',
                            'menu_class' => 'c-main-nav__list',
                            'container' => false,
                            'before' => '',
                            'after' => '',
                            'link_before' => '',
                            'link_after' => '',
                            'items_wrap' =>
                                '<ul id="%1$s" class="%2$s" role="menu">%3$s</ul>',
                            'walker' => new No_Sub_Submenu_Walker_Nav_Menu(),
                        ]); ?>
                    </nav>
                </div>
                <?php if (
                    !empty($header_modules_left) ||
                    !empty($header_modules_right)
                ): ?>
                    <div class="c-header__meta-wrapper --hide-desktop">
                        <div class="c-header__meta o-container">
                            <div class="c-header__meta-row">
                                <?php if (!empty($header_modules_left)): ?>
                                    <div class="c-header__meta-column --left c-modules --is-header">
                                        <?php oo_load_modules_flexible_content(
                                            $header_content_left,
                                            'header',
                                        ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($header_modules_right)): ?>
                                    <div class="c-header__meta-column --right c-modules --is-header">
                                        <?php oo_load_modules_flexible_content(
                                            $header_content_right,
                                            'header',
                                        ); ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header><!-- #masthead -->
