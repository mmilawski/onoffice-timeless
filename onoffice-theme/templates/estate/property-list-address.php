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

$slider['slider'] = 'yes';
?>

<?php require 'map/map.php'; ?>

<?php if ($pEstates->getEstateOverallCount() > 0) { ?>
	<div class="c-property-list__slider c-slider --is-properties-slider splide" data-splide='{
   "perPage":1,
   "perMove":1,
   "gap":32,
   "pagination": false,
   "snap":true,
   "lazyLoad":"nearby",
   "mediaQuery":"min",
   "breakpoints":{
      "992":{
         "perPage":4
      }
   }
}'>
		<div class="c-slider__track splide__track">
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
					<svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m9.41.71L1.41,8.71l8,8" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
				</button>
				<button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
					<span class="u-screen-reader-only"><?php esc_html_e(
         'Nächstes',
         'oo_theme',
     ); ?></span>
					<svg class="c-slider__icon splide__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10.12 17.41"><path d="m.71,16.71l8-8L.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"/></svg>
				</button>
			</div>
		</div>
	</div>
<?php } ?>
