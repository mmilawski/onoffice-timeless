<?php

/**
 *
 *    Copyright (C) 2016  onOffice Software AG
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
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Map
ob_start();
require 'map/map.php';
$map = ob_get_clean();

// Section ID for pagination anchor
$anchor = isset($headline['text']) ? clean_id($headline['text']) : '';
?>

<?php if ($pEstates->getEstateOverallCount() > 0) { ?>
    <?php if (!$is_slider) { ?>
        <?php if ($map) { ?>
            <div class="c-property-list__wrapper o-col-12 o-col-xl-12">
                <?php echo $map; ?>
            </div>
        <?php } ?>

        <div class="c-property-list__wrapper">

            <div class="c-property-list__nav o-row">
                <p class="c-property-list__sort o-col-12 o-col-md-6 o-col-xxl-4">
                    <?php esc_html_e(
                        'Gefundene Immobilien:',
                        'oo_theme',
                    ); ?> <span class="c-property-list__number"><?php echo sprintf(
     '%d',
     $pEstates->getEstateOverallCount(),
 ); ?></span>
                </p>
                <?php if ($generateSortDropDown()) { ?>
                    <div class="c-property-list__count o-col-12 o-col-md-6 o-col-xxl-8">
                        <?php if ($generateSortDropDown()) { ?>
                            <?php wp_enqueue_script('oo-sort-list-script'); ?>
                            <label class="o-label" for="onofficeSortListSelector">
                                <?php esc_html_e('Sortieren', 'oo_theme'); ?>
                                <?php echo $generateSortDropDown(); ?>
                            </label>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

            <div class="c-property-list__properties">
                <?php require 'property-card.php'; ?>
            </div>

            <?php oo_get_template('components', '', 'component-pagination', [
                'type' => 'property',
                'class' => 'c-property-list__pagination --on-' . $bg_color,
                'anchor' => $anchor,
            ]); ?>
        </div>
    <?php } else { ?>
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
                    <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev
                    ">
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
    <?php } ?>
<?php } else { ?>
    <p class="c-property-list__count --no-estates">
        <?php esc_html_e(
            'Gefundene Immobilien:',
            'oo_theme',
        ); ?> <span class="c-property-list__number"><?php echo sprintf(
     '%d',
     $pEstates->getEstateOverallCount(),
 ); ?></span>
    </p>
<?php } ?>
