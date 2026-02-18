<?php
/**
 *
 *    Copyright (C) 2018-2025 onOffice GmbH
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 *
 *  Default template
 *
 */

use onOffice\WPlugin\Favorites;

/* @var $pEstates onOffice\WPlugin\EstateList */

// ACF
// Content
$headline = get_field('headline') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$shortcode = get_field('shortcode') ?? '';
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$map_zoom = $settings['map_zoom'] ?? 'no';
$is_show_map = oo_get_effective_show_map($settings, $shortcode);

$map_color = get_field('map_color');
if (empty($map_color)) {
    $map_color = $settings['map_color'] ?? 'colored';
}

$marker_color = oo_get_marker_color_for_bg($bg_color);

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Presentation / Layout
$presentation_group = get_field('presentation') ?? [];
$presentation = $presentation_group['presentation'] ?? 'tileview';

$permit_switch_view = get_field('permit_switch_view') ?? [];
$is_view_switch = filter_var(
    $permit_switch_view['permit_switch_view'] ?? null,
    FILTER_VALIDATE_BOOLEAN,
);

if ($is_view_switch) {
    $list_setting_cookie = 'oo_theme_view_preference';
    if (
        isset($_COOKIE[$list_setting_cookie]) &&
        in_array($_COOKIE[$list_setting_cookie], ['listview', 'tileview'])
    ) {
        $presentation = $_COOKIE[$list_setting_cookie];
    }
}

if ($presentation === 'tileview' && !$is_view_switch) {
    $layouts = [
        'tile' => [
            'slider_settings' =>
                '{"perPage":1,"perMove":1,"gap":32,"pagination":false,"arrows":false,"page":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":2},"1400":{"perPage":3}}}',
        ],
    ];
} else {
    $layouts = [
        'list' => [
            'slider_settings' =>
                '{"perPage":1,"perMove":1,"gap":0,"pagination":false,"arrows":false,"page":false,"snap":true,"lazyLoad":"nearby"}',
        ],
        'tile' => [
            'slider_settings' =>
                '{"perPage":1,"perMove":1,"gap":32,"pagination":false,"arrows":false,"page":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":2},"1400":{"perPage":3}}}',
        ],
    ];
}

// Map
ob_start();
require 'map/map.php';
$map = ob_get_clean();

// Section ID for pagination anchor
$anchor = isset($headline['text']) ? clean_id($headline['text']) : '';

// Listing ID for pagination query parameter
$list_id = $pEstates->getDataView()->getId();

$uniqueid = $list_id . '-' . uniqid();

$property_count = method_exists($pEstates, 'getEstateOverallCount')
    ? $pEstates->getEstateOverallCount()
    : 0;
?>

<?php if ($property_count > 0) { ?>
    <?php if (!$is_slider) { ?>
        <?php if ($map && $is_show_map) { ?>
            <div class="c-property-list__container o-container">
                <div class="o-row">
                    <div class="c-property-list__map-wrapper o-col-lg-10 o-col-xl-10 u-offset-lg-1">
                        <?php echo $map; ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="c-property-list__wrapper">
            <div class="c-property-list__nav o-container">
                <div class="o-row">
                    <div class="c-property-list__nav-wrapper o-col-12 o-col-lg-10 u-offset-lg-1">
                        <?php if ($generateSortDropDown()) { ?>
                            <div class="c-property-list__sort-wrapper --<?php echo $bg_color; ?>">
                                <?php wp_enqueue_script(
                                    'oo-sort-list-script',
                                ); ?>
                                <label class="c-property-list__sort o-label" for="onofficeSortListSelector">
                                    <?php esc_html_e(
                                        'Immobilien sortiert nach:',
                                        'oo_theme',
                                    ); ?>
                                    <?php echo $generateSortDropDown(); ?>
                                </label>
                            </div>
                        <?php } ?>
                        <?php if ($is_view_switch) { ?>
                            <fieldset class="c-property-list__switch">
                                <legend class="c-property-list__switch-legend o-label"><?php esc_html_e(
                                    'Ansicht:',
                                    'oo_theme',
                                ); ?></legend>
                                <input class="c-property-list__switch-radio" type="radio" id="view-tile-<?php echo $uniqueid; ?>" name="view-<?php echo $uniqueid; ?>" <?php echo $presentation ===
'tileview'
    ? 'checked'
    : ''; ?> aria-describedby="switchinfo-<?php echo $uniqueid; ?>" />
                                <label for="view-tile-<?php echo $uniqueid; ?>" class="c-property-list__switch-label <?php echo $presentation ===
'tileview'
    ? '--checked'
    : ''; ?>">
                                    <?php oo_get_icon('grid', false, [
                                        'class' =>
                                            'c-property-list__switch-icon',
                                        'role' => 'img',
                                        'aria-label' => esc_html__(
                                            'Kachelansicht',
                                            'oo_theme',
                                        ),
                                    ]); ?>
                                </label>
                                <input class="c-property-list__switch-radio" type="radio" id="view-list-<?php echo $uniqueid; ?>" name="view-<?php echo $uniqueid; ?>" <?php echo $presentation ===
'listview'
    ? 'checked'
    : ''; ?> aria-describedby="switchinfo-<?php echo $uniqueid; ?>" />
                                <label for="view-list-<?php echo $uniqueid; ?>" class="c-property-list__switch-label <?php echo $presentation ===
'listview'
    ? '--checked'
    : ''; ?>">
                                    <?php oo_get_icon('list', false, [
                                        'class' =>
                                            'c-property-list__switch-icon',
                                        'role' => 'img',
                                        'aria-label' => esc_html__(
                                            'Listenansicht',
                                            'oo_theme',
                                        ),
                                    ]); ?>
                                </label>
                                <p id="switchinfo-<?php echo $uniqueid; ?>" class="u-screen-reader-only"><?php esc_html_e(
    'Ändert nur die visuelle Darstellung, Inhalte bleiben gleich',
    'oo_theme',
); ?></p>
                                <p class="c-property-list__switch-status u-screen-reader-only" aria-live="polite" data-list-text="<?php esc_html_e(
                                    'Listenansicht aktiviert',
                                    'oo_theme',
                                ); ?>" data-tile-text="<?php esc_html_e(
    'Kachelansicht aktiviert',
    'oo_theme',
); ?>"></p>
                            </fieldset>
                        <?php } ?>
                        <p class="c-property-list__count">
                            <?php printf(
                                esc_html__(
                                    '%d Immobilien gefunden',
                                    'oo_theme',
                                ),
                                $property_count,
                            ); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($layouts as $layout => $settings): ?>
            <div class="c-property-list__properties --<?= $layout ?> o-container-fluid">
                <?php require 'property-card.php'; ?>
            </div>
        <?php endforeach; ?>

        <?php oo_get_template('components', '', 'component-pagination', [
            'type' => 'property',
            'class' =>
                'c-property-list__pagination o-container --on-' . $bg_color,
            'anchor' => $anchor,
            'list_id' => $list_id,
        ]); ?>
    <?php } else { ?>
        <p class="c-property-list__switch-status u-screen-reader-only" aria-live="polite" data-list-text="<?php esc_html_e(
            'Listenansicht aktiviert',
            'oo_theme',
        ); ?>" data-tile-text="<?php esc_html_e(
    'Kachelansicht aktiviert',
    'oo_theme',
); ?>"></p>
        <?php foreach ($layouts as $layout => $settings): ?>
            <div class="c-property-list__slider --on-<?php echo $bg_color; ?> --<?= $layout ?> c-slider --is-properties-slider splide" data-splide='<?= $settings[
     'slider_settings'
 ] ?>'>
                <div class="c-slider__track splide__track">
                    <div class="c-slider__list splide__list">
                        <?php require 'property-card.php'; ?>
                    </div>
                </div>

                <div class="c-slider__navigation__wrapper o-container">
                    <div class="c-slider__navigation splide__navigation --is-properties-slider">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                                    'chevron-left',
                                ); ?></span>
                            </button>
                            <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                                    'chevron-right',
                                ); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php } ?>
<?php } else { ?>
    <p class="c-property-list__count --no-properties">
        <?php printf(
            esc_html__('%d Immobilien gefunden', 'oo_theme'),
            $property_count,
        ); ?>
    </p>
<?php } ?>
