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

		<div class="c-property-list__wrapper o-col-12 o-col-xl-12">
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
		<div class="c-property-list__slider --on-<?php echo $settings[
      'bg_color'
  ]; ?> c-slider --is-properties-slider splide" data-splide='{
   "perPage":1,
   "perMove":1,
   "gap":32,
   "pagination":false,
   "snap":true,
   "lazyLoad":"nearby",
   "mediaQuery":"min",
   "breakpoints":{
      "992":{
         "perPage":2
      },
	  "1400":{
         "perPage":3
      }
   }
}'>
			<div class="c-slider__track splide__track o-col-12 o-col-xl-10">
				<div class="c-slider__list splide__list">
					<?php require 'property-card.php'; ?>
				</div>
			</div>


			<div class="c-slider__navigation splide__navigation">
				<div class="c-slider__progress splide__progress">
					<div class="c-slider__progress-bar splide__progress-bar"></div>
				</div>
				<div class="c-slider__arrows splide__arrows">
					<button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
						<span class="u-screen-reader-only"><?php esc_html_e(
          'Vorheriges',
          'oo_theme',
      ); ?></span>
						<svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41">
							<path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2" />
						</svg>
					</button>
					<button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
						<span class="u-screen-reader-only"><?php esc_html_e(
          'Nächstes',
          'oo_theme',
      ); ?></span>
						<svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41">
							<path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2" />
						</svg>
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
