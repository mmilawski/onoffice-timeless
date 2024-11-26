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
    $is_news = $blocks[0]['blockName'] === 'oo/news-details';
    $has_picture = !empty($blocks[0]['attrs']['data']['image']);
    $is_news_with_picture = $is_news && $has_picture;
    $blocks_count = is_array($blocks) ? count($blocks) : 0;
    if ($blocks_count > 0 && ($is_banner || $is_news_with_picture)) {
        $has_bg_picture = true;
    }
}

$has_no_meta = '';
if (empty($header_modules_left) && empty($header_modules_right)) {
    $has_no_meta = ' --has-no-meta';
}
?>
<!doctype html>
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
    <div class="c-header__container o-container<?php echo $has_no_meta; ?>">

			
				<div class="c-header__main-wrapper">
                    <div class="c-header__main o-container">
                        <?php $no_logo = empty($logo['image'])
                            ? '--no-image '
                            : ''; ?>
                        <div class="o-logo <?php
                        echo $no_logo;
                        echo '--' . $logo['size'];
                        ?> <?= '--' . $logo['appearance'] ?>">
                            <a class="o-logo__link" href="<?= esc_url(
                                home_url('/'),
                            ) ?>">
                                <?php if (!empty($logo['image'])): ?>
                                    <img class="o-logo__image" src="<?php echo $logo[
                                        'image'
                                    ][
                                        'url'
                                    ]; ?>" alt="<?php echo $company_name; ?>">
                                <?php endif; ?>
                            </a>
                        </div>

						<button class="c-main-nav__button">
							<span class="u-screen-reader-only"><?php esc_html_e(
           'Menu',
           'oo_theme',
       ); ?></span>

                        <svg class="c-main-nav__button-icon --open" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>

                        <svg class="c-main-nav__button-icon --close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>

						</button>

					</div><!-- #container -->
				</div>

                <div class="c-header__wrapper">
                    <div class="c-header__nav-wrapper">
                        <nav class="c-header__nav c-main-nav o-container">
                            <?php wp_nav_menu([
                                'theme_location' => 'main-nav',
                                'menu_class' => 'c-main-nav__list',
                                'container' => false,
                                'before' => '',
                                'after' => '',
                                'link_before' => '',
                                'link_after' => '',
                                'items_wrap' =>
                                    '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'walker' => new No_Sub_Submenu_Walker_Nav_Menu(),
                            ]); ?>
                        </nav>
                    </div>

                    <?php if (
                        !empty($header_modules_left) ||
                        !empty($header_modules_right)
                    ): ?>
                        <div class="c-header__meta-wrapper">

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
																			<?php oo_load_modules_flexible_content($header_content_right, 'header'); ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>

                        </div>
                    <?php endif; ?>

                </div>
    </div>
</header><!-- #masthead -->