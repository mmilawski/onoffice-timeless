<?php

if (!defined('_S_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_S_VERSION', '1.0.0');
}

// Load Shared Core
if (file_exists(__DIR__ . '/shared/setup.php')) {
    require __DIR__ . '/shared/setup.php';
} else {
    if (current_user_can('administrator')) {
        add_action('admin_notices', function () {
            ?>
			<div class="notice notice-error">
				<p>
					<strong>
						<?php _e('Shared | Paket fehlt', 'oo_theme'); ?>
					</strong>
				</p>
				<p>
					<?php _e('Überprüfe den Shared Ordner', 'oo_theme'); ?>
				</p>
			</div>
			<?php
        });
    }
}

// Init
if (function_exists('oo_setup_parent_theme')) {
    // STYLES
    $oo_styles = [
        OO_PARENT_PATH . '/build/css/reset.css',
        OO_SHARED_PATH . '/build/css/tomselect/tom-select.css',
        OO_SHARED_PATH . '/build/css/splide/splide.css',
        [OO_PARENT_PATH . '/build/css/style.css', ['oo-glightbox-style']],
        OO_SHARED_PATH . '/build/css/nouislider/nouislider.css',
    ];

    // SCRIPTS
    $oo_scripts = [
        [OO_SHARED_PATH . '/build/js/tomselect/tom-select.min.js', ['jquery']],
        [OO_SHARED_PATH . '/build/js/splide/splide.js', []],
        [OO_SHARED_PATH . '/build/js/firefox-iframe-fix.js', ['jquery']],
        [
            OO_PARENT_PATH . '/build/js/app.js',
            [
                'jquery',
                'oo-glightbox-script',
                'oo-images-loaded-script',
                'oo-favorites-script',
                'oo-sort-list-script',
                'oo-wnumb-script',
                'oo-nouislider-script',
            ],
        ],
    ];

    oo_setup_parent_theme(
        styles: $oo_styles ?? null,
        scripts: $oo_scripts ?? null,
    );

    // localize for app.js
    $app_script_handle = oo_make_handle(
        'theme',
        OO_PARENT_PATH . '/build/js/app.js',
    );

    // localize for app.js
    add_action(
        'wp_enqueue_scripts',
        function () use ($app_script_handle) {
            wp_localize_script($app_script_handle, 'wpAppTranslations', [
                'noResults' => __('Keine Ergebnisse', 'oo_theme'),
                'removeThisItem' => __('Entferne dieses Element', 'oo_theme'),
                'previous' => __('Vorheriger', 'oo_theme'),
                'next' => __('Nächster', 'oo_theme'),
                'close' => __('Schließen', 'oo_theme'),
            ]);
        },
        20,
    );
}

/**
 * Load Theme Blocks
 */
add_filter('onoffice_block_setup', function ($blocks) {
    return array_merge($blocks, [
        'oo/property-list' => [
            'path' => OO_SHARED_PATH . '/blocks/property-list',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/property-list/property-list-render.php',
        ],
        'oo/property-details' => [
            'path' => OO_SHARED_PATH . '/blocks/property-details',
        ],
        'oo/property-search' => [
            'path' => OO_SHARED_PATH . '/blocks/property-search',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/property-search/property-search-render.php',
        ],
        'oo/address-search' => [
            'path' => OO_SHARED_PATH . '/blocks/address-search',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/address-search/address-search-render.php',
        ],
        'oo/address-list' => [
            'path' => OO_SHARED_PATH . '/blocks/address-list',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/address-list/address-list-render.php',
        ],
        'oo/address-details' => [
            'path' => OO_SHARED_PATH . '/blocks/address-details',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/address-details/address-details-render.php',
        ],
        'oo/accordion' => [
            'path' => OO_SHARED_PATH . '/blocks/accordion',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/accordion/accordion-render.php',
        ],
        'oo/banner' => [
            'path' => OO_SHARED_PATH . '/blocks/banner',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/banner/banner-render.php',
        ],
        'oo/media-text' => [
            'path' => OO_SHARED_PATH . '/blocks/media-text',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/media-text/media-text-render.php',
        ],
        'oo/media' => [
            'path' => OO_SHARED_PATH . '/blocks/media',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/media/media-render.php',
        ],
        'oo/gallery' => [
            'path' => OO_SHARED_PATH . '/blocks/gallery',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/gallery/gallery-render.php',
        ],
        'oo/text' => [
            'path' => OO_SHARED_PATH . '/blocks/text',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/text/text-render.php',
        ],
        'oo/forms' => [
            'path' => OO_SHARED_PATH . '/blocks/forms',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/forms/forms-render.php',
        ],
        'oo/team' => [
            'path' => OO_SHARED_PATH . '/blocks/team',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/team/team-render.php',
        ],
        'oo/link-boxes' => [
            'path' => OO_SHARED_PATH . '/blocks/link-boxes',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/link-boxes/link-boxes-render.php',
        ],
        'oo/iframe-script' => [
            'path' => OO_SHARED_PATH . '/blocks/iframe-script',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/iframe-script/iframe-script-render.php',
        ],
        'oo/reviews' => [
            'path' => OO_SHARED_PATH . '/blocks/reviews',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/reviews/reviews-render.php',
        ],
        'oo/news' => [
            'path' => OO_SHARED_PATH . '/blocks/news',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/news/news-render.php',
        ],
        'oo/news-details' => [
            'path' => OO_SHARED_PATH . '/blocks/news-details',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/news-details/news-details-render.php',
        ],
        'oo/contact-map' => [
            'path' => OO_SHARED_PATH . '/blocks/contact-map',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/contact-map/contact-map-render.php',
        ],
        'oo/contact' => [
            'path' => OO_SHARED_PATH . '/blocks/contact',
            'override-parent-render' =>
                OO_PARENT_PATH . '/blocks/contact/contact-render.php',
        ],
        'oo/shortcode' => [
            'path' => OO_SHARED_PATH . '/blocks/shortcode',
        ],
    ]);
});

// UPDATE SYSTEM
add_filter('oo_theme_updates_data', function ($data) {
    $data['slug'] = 'onoffice-classic';
    $data['json'] =
        'https://onoffice-wp-updates.de/releases/themes/onoffice-classic/updater.json';

    return $data;
});
