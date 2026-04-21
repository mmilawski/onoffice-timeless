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
    $reset_handle = oo_make_handle(
        'theme',
        OO_PARENT_PATH . '/build/css/reset.css',
    );
    $oo_styles = [
        OO_PARENT_PATH . '/build/css/reset.css',
        OO_SHARED_PATH . '/build/css/tomselect/tom-select.css',
        OO_SHARED_PATH . '/build/css/splide/splide.css',
        [
            OO_PARENT_PATH . '/build/css/style.css',
            [$reset_handle, 'oo-glightbox-style'],
        ],
        OO_SHARED_PATH . '/build/css/nouislider/nouislider.css',
    ];
    // SCRIPTS
    $oo_scripts = [
        [OO_SHARED_PATH . '/build/js/tomselect/tom-select.min.js', ['jquery']],
        [OO_SHARED_PATH . '/build/js/splide/splide.js', []],
        [OO_SHARED_PATH . '/build/js/usercentrics-helpers.js', []],
        [OO_PARENT_PATH . '/build/js/success-counter.js', []],
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
    // Note: the $pin / $custom_pin access should be refactored and can be handled in shared, I'm out of time currently
    add_action(
        'wp_enqueue_scripts',
        function () use ($app_script_handle) {
            $pins = get_field('pins', 'option');
            $custom_pin = $pins['custom_pin'] ?? null;
            wp_localize_script($app_script_handle, 'ajaxNews', [
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]);
            wp_localize_script($app_script_handle, 'ooTimelessTheme', [
                'translations' => [
                    'noResults' => __('Keine Ergebnisse', 'oo_theme'),
                    'removeThisItem' => __(
                        'Entferne dieses Element',
                        'oo_theme',
                    ),
                    'previous' => __('Vorheriger', 'oo_theme'),
                    'next' => __('Nächster', 'oo_theme'),
                    'close' => __('Schließen', 'oo_theme'),
                    'invalidEmail' => __(
                        'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
                        'oo_theme',
                    ),
                    'invalidDate' => __(
                        'Bitte füllen Sie das Feld im korrekten Format aus. ',
                        'oo_theme',
                    ),
                    'requiredField' => __(
                        'Bitte füllen Sie das Pflichtfeld aus',
                        'oo_theme',
                    ),
                    'requiredSelect' => __(
                        'Bitte wählen Sie einen Wert aus.',
                        'oo_theme',
                    ),
                    'numberTooSmall' => __(
                        'Bitte geben Sie einen größeren Wert ein.',
                        'oo_theme',
                    ),
                    'requiredCheckbox' => __(
                        'Bitte bestätigen Sie das Feld.',
                        'oo_theme',
                    ),
                ],
                'urls' => [
                    'propertyList' => function_exists(
                        'oo_find_property_list_page_url',
                    )
                        ? oo_find_property_list_page_url()
                        : home_url('/'),
                ],
                'acfData' => [
                    'customPin' => $custom_pin,
                ],
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
        'oo/success-counter' => [
            'path' => OO_SHARED_PATH . '/blocks/success-counter',
            'override-parent-render' =>
                OO_PARENT_PATH .
                '/blocks/success-counter/success-counter-render.php',
        ],
    ]);
});

if (is_plugin_active('oo-vue-addons/on-office-vue-addons.php')) {
    add_filter('onoffice_block_setup', function ($blocks) {
        return array_merge($blocks, [
            'oo/customer-area' => [
                'path' => OO_SHARED_PATH . '/blocks/customer-area',
                'override-parent-render' =>
                    OO_PARENT_PATH .
                    '/blocks/customer-area/customer-area-render.php',
            ],
            'oo/appointment' => [
                'path' => OO_SHARED_PATH . '/blocks/appointment',
                'override-parent-render' =>
                    OO_PARENT_PATH .
                    '/blocks/appointment/appointment-render.php',
            ],
            'oo/leadgenerator' => [
                'path' => OO_SHARED_PATH . '/blocks/leadgenerator',
                'override-parent-render' =>
                    OO_PARENT_PATH .
                    '/blocks/leadgenerator/leadgenerator-render.php',
            ],
            'oo/search-request' => [
                'path' => OO_SHARED_PATH . '/blocks/search-request',
                'override-parent-render' =>
                    OO_PARENT_PATH .
                    '/blocks/search-request/search-request-render.php',
            ],
        ]);
    });
}

// UPDATE SYSTEM
add_filter('oo_theme_updates_data', function ($data) {
    $data['slug'] = 'onoffice-timeless';
    $data['json'] =
        'https://onoffice-wp-updates.de/releases/themes/onoffice-timeless/updater.json';

    return $data;
});
