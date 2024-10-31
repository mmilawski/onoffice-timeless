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

$slider['slider'] = 'yes';
$is_slider = true;
?>

<div class="c-property-list__map o-col-12 o-col-xl-10">
	<?php require 'map/map.php'; ?>
</div>

<?php if ($pEstates->getEstateOverallCount() > 0) { ?>
	<div class="c-property-list__slider c-slider --on-bg-transparent --is-address-slider o-col-12 splide" data-splide='{
   "perPage":1,
   "perMove":1,
   "gap":32,
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
