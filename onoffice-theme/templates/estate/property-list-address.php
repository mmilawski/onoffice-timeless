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
// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Slider
$slider = ['slider' => 'yes'];
$is_slider = true;

// Map
ob_start();
require 'map/map.php';
$map = ob_get_clean();

$property_count = method_exists($pEstates, 'getEstateOverallCount')
    ? $pEstates->getEstateOverallCount()
    : 0;
?>

<?php if ($property_count > 0) { ?>
    <?php if ($map) { ?>
        <div class="c-property-list__map-wrapper">
            <?php echo $map; ?>
        </div>
    <?php } ?>

    <div id="outerslider" class="c-property-list__slider --on-<?php echo $bg_color; ?> c-slider --is-properties-slider splide" data-splide='{"perPage":1,"perMove":1,"gap":32,"pagination":false,"arrows":false,"page":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"992":{"perPage":2},"1400":{"perPage":3}}}'>
        <div class="c-slider__track splide__track">
            <div class="c-slider__list splide__list">
                <?php require 'property-card.php'; ?>
            </div>
        </div>

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
<?php } else { ?>
    <p class="c-property-list__count --no-properties">
        <?php printf(
            esc_html__('%d Immobilien gefunden', 'oo_theme'),
            $property_count,
        ); ?>
    </p>
<?php } ?>
