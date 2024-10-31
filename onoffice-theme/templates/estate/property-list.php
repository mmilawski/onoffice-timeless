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
            <div class="c-property-list__wrapper o-col-12 o-col-xl-10">
                <?php echo $map; ?>
            </div>
        <?php } ?>

        <div class="c-property-list__wrapper o-col-12 o-col-xl-10">
            <div class="c-property-list__nav">
                <?php if ($generateSortDropDown()) { ?>
                    <div class="c-property-list__sort">
                        <?php wp_enqueue_script('oo-sort-list-script'); ?>
                        <label class="o-label" for="onofficeSortListSelector">
                            <?php echo $generateSortDropDown(); ?>
                            <span class="o-label__text"><?php esc_html_e(
                                'Sortiert nach:',
                                'oo_theme',
                            ); ?></span>
                        </label>
                    </div>
                <?php } ?>
                <p class="c-property-list__count">
                    <?php echo sprintf(
                        esc_html__('%d gefundene Immobilien', 'oo_theme'),
                        $pEstates->getEstateOverallCount(),
                    ); ?>
                </p>
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
        <div class="c-property-list__slider --on-<?php echo $bg_color; ?> o-col-12 c-slider --is-properties-slider splide" data-splide='{
    "perPage":1,
    "perMove":1,
    "gap":0,
    "snap":true,
    "lazyLoad":"nearby"
    }'>
            <div class="c-slider__track splide__track o-col-12 o-col-xl-10">
                <div class="c-slider__list splide__list">
                    <?php require 'property-card.php'; ?>
                </div>
            </div>

            <div class="c-slider__arrows splide__arrows">
                <button class="c-slider__arrow --prev c-button --only-icon --square splide__arrow splide__arrow--prev">
                    <span class="u-screen-reader-only"><?php esc_html_e(
                        'Vorheriges',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-button__icon --chevron-left"><?php oo_get_icon(
                        'chevron-left',
                    ); ?></span>
                </button>
                <button class="c-slider__arrow --next c-button --only-icon --square splide__arrow splide__arrow--next">
                    <span class="u-screen-reader-only"><?php esc_html_e(
                        'Nächstes',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-button__icon --chevron-right"><?php oo_get_icon(
                        'chevron-right',
                    ); ?></span>
                </button>
            </div>
            <ul class="c-slider__pagination splide__pagination"></ul>
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="c-property-list__row o-row --position-center">
		<div class="c-property-list__wrapper o-col-12 o-col-xl-10">
			<div class="c-property-list__nav">
				<p class="c-property-list__count">
					<?php echo sprintf(
         esc_html__('%d gefundene Immobilien', 'oo_theme'),
         $pEstates->getEstateOverallCount(),
     ); ?>
				</p>
			</div>
		</div>
	</div>
<?php } ?>
