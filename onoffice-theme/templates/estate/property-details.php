<?php

/**
 *
 *    Copyright (C) 2020  onOffice GmbH
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

use onOffice\WPlugin\EstateDetail;
use onOffice\WPlugin\Favorites;

$dont_echo = ['objekttitel', 'vermarktungsstatus'];
$energy_fields = [
    'endenergiebedarf',
    'energieverbrauchskennwert',
    'energieausweistyp',
    'energieausweis_gueltig_bis',
    'energyClass',
    'energietraeger',
    'energiepassAusstelldatum',
    'nutzungsart',
    'erschliessung',
    'energieausweisBaujahr',
    'endenergiebedarfStrom',
    'endenergiebedarfWaerme',
    'endenergieverbrauchStrom',
    'endenergieverbrauchWaerme',
    'warmwasserEnthalten',
];

function oo_feature_item($feature)
{
    $value = $feature['value'];

    echo '<dl class="c-property-features__criteria">';
    echo '<dt class="c-property-features__label">';
    esc_html_e($feature['label']);
    echo '</dt>';
    echo '<dd class="c-property-features__value">';

    if (is_array($value)) {
        esc_html_e(implode(', ', $value));
    } else {
        echo esc_html($value);
    }

    echo '</dd>';
    echo '</dl>';
}

/**
 * Property Link Type
 */

function oo_property_field_type($field, $item)
{
    if ($field == 'phone' || $field == 'Telefon1' || $field == 'Telefon2') {
        if (preg_match('/[0-9]/', $item)) { ?>

			<dl class="c-contact-person__contact --is-phone">
				<dt class="c-contact-person__contact-label">
					<?php echo esc_html('Tel.:', 'oo_theme'); ?>
				</dt>
				<dd class="c-contact-person__contact-value">
					<a class="c-link --text-color --on-bg-transparent" href="tel:<?php echo oo_clean_link_number(
         $item,
     ); ?>">
						<?php echo esc_html($item); ?>
					</a>
				</dd>
			</dl>

		<?php }
    }

    if ($field == 'mobile') {
        if (preg_match('/[0-9]/', $item)) { ?>

			<dl class="c-contact-person__contact --is-mobile">
				<dt class="c-contact-person__contact-label">
					<?php echo esc_html('Mobile:', 'oo_theme'); ?>
				</dt>
				<dd class="c-contact-person__contact-value">
					<a class="c-link --text-color --on-bg-transparent" href="tel:<?php echo oo_clean_link_number(
         $item,
     ); ?>">
						<?php echo esc_html($item); ?>
					</a>
				</dd>
			</dl>

		<?php }
    }

    if (
        $field == 'fax' ||
        $field == 'Telefax' ||
        $field == 'Telefax1' ||
        $field == 'Telefax2'
    ) { ?>
		<dl class="c-contact-person__contact --is-fax">
			<dt class="c-contact-person__contact-label">
				<?php echo esc_html('Fax:', 'oo_theme'); ?>
			</dt>
			<dd class="c-contact-person__contact-value">
				<a class="c-link --text-color --on-bg-transparent" href="fax:<?php echo oo_clean_link_number(
        $item,
    ); ?>">
					<?php echo esc_html($item); ?>
				</a>
			</dd>
		</dl>
	<?php }

    if ($field == 'email' || $field == 'Email') { ?>
		<dl class="c-contact-person__contact --is-email">
			<dt class="c-contact-person__contact-label">
				<?php echo esc_html('E-Mail:', 'oo_theme'); ?>
			</dt>
			<dd class="c-contact-person__contact-value">
				<a class="c-link --text-color --on-bg-transparent" href="mailto:<?= oo_antispambot(
        $item,
    ) ?>">
					<?php echo oo_antispambot($item); ?>
				</a>
			</dd>
		</dl>
		<?php }

    if ($field == 'Homepage' || $field == 'url') {
        $has_protocol = parse_url($item)['scheme'] ?? null;

        if ($has_protocol == 'https' || $has_protocol == 'http'): ?>

			<dl class="c-contact-person__contact --is-website">
				<dt class="c-contact-person__contact-label">
					<?php echo esc_html('Web:', 'oo_theme'); ?>
				</dt>
				<dd class="c-contact-person__contact-value">
					<a class="c-link --text-color --on-bg-transparent" target="_blank" href="<?php echo esc_html(
         $item,
     ); ?>">
						<?php echo esc_html($item); ?>
					</a>
				</dd>
			</dl>

	<?php endif;
    }
}

/** @var EstateDetail $pEstates */

$pEstates->resetEstateIterator();
while ($current_property = $pEstates->estateIterator()) {

    $property_id = $pEstates->getCurrentMultiLangEstateMainId();
    $raw_values = $pEstates->getRawValues();
    $is_reference = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['referenz'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
    );

    $photos = false;

    // pictures
    $property_pictures = $pEstates->getEstatePictures();
    foreach ($property_pictures as $id) {
        $photos = true;
    }

    // videos
    $property_movie_players = $pEstates->getMovieEmbedPlayers();
    $property_movie_links = $pEstates->getEstateMovieLinks();

    // ogulo
    $property_ogulo_embeds = $pEstates->getLinkEmbedPlayers('ogulo');
    $property_ogulo_links = $pEstates->getEstateLinks('ogulo');

    // objects
    $property_object_embeds = $pEstates->getLinkEmbedPlayers('object');
    $property_object_links = $pEstates->getEstateLinks('object');

    // Links
    $property_link_embeds = $pEstates->getLinkEmbedPlayers('link');
    $property_links = $pEstates->getEstateLinks('link');

    // map
    ob_start();
    require 'map/map.php';
    $map = ob_get_clean();

    // status
    $property_status = $current_property['vermarktungsstatus'];

    // link
    $property_link = esc_url($pEstates->getEstateLink());

    // multiobject
    $multiobjekt = false;

    if ($current_property['stammobjekt'] == 'Ja') {
        $multiobjekt = true;
    }

    // form
    $shortcode_form = $pEstates->getShortCodeForm();

    // fields
    $fields_counter = 0;
    $fields_more = 12;
    $property_features = [];
    $property_free_texts = [];
    foreach ($current_property as $field => $value) {
        if (
            (is_numeric($value) && 0 == $value) ||
            $value == '0000-00-00' ||
            $value == '0.00' ||
            $value == '' ||
            empty($value) ||
            in_array($field, $dont_echo)
        ) {
            continue;
        }

        if (in_array($field, $energy_fields)) {
            $energy_fields_available = true;
        }

        if (!in_array($field, $energy_fields)) {
            $fields_available = true;
        }
    }
    ?>

	<section class="c-property-details o-section --bg-transparent --bg-transparent-mixed --with-separator">

		<?php if ($photos) {

      // Load Lightbox
      wp_enqueue_script('oo-glightbox-script');
      wp_enqueue_style('oo-glightbox-style');
      ?>
			<div class="c-property-details__gallery o-container">

				<?php
    $i = 1;

    foreach ($property_pictures as $id) {

        $picture_values = $pEstates->getEstatePictureValues($id);

        if ($picture_values['title'] == true) {
            $image_alt = esc_html($picture_values['title']);
        } else {
            $image_alt = esc_html('Immobilienbild', 'oo_theme');
        }

        $image = [
            'url' => $pEstates->getEstatePictureUrl($id),
            'alt' => $image_alt,
        ];

        //  Lightbox Cloud Image
        $lightbox_image_options = '&?force_format=webp&?org_if_sml=1';
        $lightbox_url =
            'https://acnaayzuen.cloudimg.io/v7/' .
            $image['url'] .
            $lightbox_image_options;

        $lightbox_image_size_list =
            [
                [
                    'id' => 'mobile',
                    'breakpoint' => '767',
                    'image_size' => '767',
                ],
                [
                    'id' => 'tablet',
                    'breakpoint' => '768',
                    'image_size' => '1200',
                ],
                [
                    'id' => 'desktop',
                    'breakpoint' => '1200',
                    'image_size' => '1920',
                ],
            ] ?? [];

        // Helpers
        $lightbox_mobile_breakpoint = '';
        $lightbox_image_breakpoints = '';
        $lightbox_image_full_size = '';
        $lightbox_image_sizes = '';

        if (is_array($lightbox_image_size_list)) {
            foreach ($lightbox_image_size_list as $key => $size) {
                $is_first = $key == array_key_first($lightbox_image_size_list);
                $is_last = $key == array_key_last($lightbox_image_size_list);
                $separator = !$is_last ? ',' : '';
                $is_last_image_size = $is_last
                    ? ',' . end($lightbox_image_size_list)['image_size'] . 'w'
                    : $separator;

                // Change breakpoints for mobile
                if ($is_first) {
                    $lightbox_image_breakpoints .=
                        '(max-width: ' .
                        $size['breakpoint'] .
                        'px) ' .
                        $size['image_size'] .
                        'px,';
                    $lightbox_image_sizes .=
                        $lightbox_url .
                        '&w=' .
                        $size['image_size'] .
                        ' ' .
                        $size['breakpoint'] .
                        'w,';
                }

                // Skip first Item
                if ($key === 0) {
                    continue;
                }

                // Breakpoints
                $lightbox_image_breakpoints .=
                    '(min-width:' .
                    $size['breakpoint'] .
                    'px) ' .
                    $size['image_size'] .
                    'px' .
                    $separator;

                // Sources
                $lightbox_image_sizes .=
                    $lightbox_url .
                    '&w=' .
                    $size['image_size'] .
                    ' ' .
                    $size['breakpoint'] .
                    'w' .
                    $is_last_image_size;
            }
        }
        ?>
					<a
						class="c-property-details__gallery-link glightbox" data-gallery="gallery"
						href="<?php echo $lightbox_url .
          'w=' .
          end($lightbox_image_size_list)['image_size']; ?>"
						data-sizes="<?php echo $lightbox_image_breakpoints; ?>"
						data-srcset="<?php echo $lightbox_image_sizes; ?>"
						<?php if (!empty($image['alt'])): ?>
						data-caption="<?php echo $image['alt']; ?>"
						title="<?php echo $image['alt']; ?>"
						<?php endif; ?>>
						<?php
      oo_get_template('components', '', 'component-image', [
          'image' => $image,
          'picture_class' => 'c-property-details__gallery-picture o-picture',
          'image_class' => 'c-property-details__gallery-image o-image',
          'dimensions' => [
              '575' => [
                  'h' => round((400 * 2) / 3),
              ],
              '1600' => [
                  'h' => round((460 * 2) / 3),
              ],
              '1400' => [
                  'h' => round((416 * 2) / 3),
              ],
              '1200' => [
                  'h' => round((352 * 2) / 3),
              ],
              '992' => [
                  'h' => round((288 * 2) / 3),
              ],
              '768' => [
                  'h' => round((694 * 2) / 3),
              ],
              '576' => [
                  'h' => round((512 * 2) / 3),
              ],
          ],
      ]);

      if ($i % 3 == 0) { ?>
							<span class="c-property-details__show-all-photos c-button" title="<?php esc_html_e(
           'Alle Fotos ansehen',
           'oo_theme',
       ); ?>">
								<?php esc_html_e('Alle Fotos', 'oo_theme'); ?>
							</span>
						<?php }
      ?>

					</a>
				<?php $i++;
    }
    ?>
			</div>
		<?php
  } ?>

		<div class="c-property-details__container o-container">
			<div class="c-property-details__row o-row">
				<?php
    if ($current_property['objekttitel']) { ?>
					<h1 class="c-property-details__title o-headline --h2">
						<?php echo $current_property['objekttitel']; ?>
					</h1>
				<?php }
    if (
        $property_status ||
        (Favorites::isFavorizationEnabled() && !$is_reference)
    ) { ?>
					<div class="c-property-details__badges <?= !$property_status &&
     Favorites::isFavorizationEnabled()
         ? '--only-favorite'
         : '' ?>">
					<?php }
    ?>

					<?php if ($property_status) { ?>
						<span class="c-property-details__status">
							<?php echo ucfirst($property_status); ?>
						</span>
					<?php } ?>

					<?php
     if (Favorites::isFavorizationEnabled() && !$is_reference) { ?>
						<span class="c-property-details__favorite --on-detail-page" data-onoffice-estateid="<?php echo $property_id; ?>">
							<span class="c-property-details__favorite-text u-screen-reader-only">
								<?php
        $favorite_label = Favorites::getFavorizationLabel();
        if ($favorite_label == 'Watchlist') {
            esc_html_e(__('Zur Merkliste hinzufügen', 'oo_theme'));
        } else {
            esc_html_e(__('Zu Favoriten hinzufügen', 'oo_theme'));
        }
        ?>
							</span>
							<svg class="c-property-details__favorite-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 16.41">
								<path d="m2.24,8.24l6.76,6.76,6.76-6.76c.8-.8,1.24-1.88,1.24-3,0-2.34-1.9-4.24-4.24-4.24-1.12,0-2.2.45-3,1.24l-.76.76-.76-.76c-.8-.8-1.88-1.24-3-1.24C2.9,1,1,2.9,1,5.24c0,1.12.45,2.2,1.24,3Z" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2" />
							</svg>
						</span>
					<?php }

     if (
         $property_status ||
         (Favorites::isFavorizationEnabled() && !$is_reference)
     ) { ?>
					</div>
				<?php }
     ?>

				<?php if ($fields_available) { ?>
					<div class="c-property-details__features">
						<?php
      foreach ($current_property as $field => $value) {
          if (
              (is_numeric($value) && 0 == $value) ||
              $value == '0000-00-00' ||
              $value == '0.00' ||
              $value == '' ||
              empty($value) ||
              in_array($field, $dont_echo) ||
              in_array($field, $energy_fields)
          ) {
              continue;
          }
          $field_infos = $pEstates->getFieldInformation($field);
          $is_free_text_category =
              $field_infos['type'] == 'text' &&
              $field_infos['tablename'] == 'ObjFreitexte';
          $is_text_field_80p =
              $field_infos['type'] == 'text' &&
              !is_array($value) &&
              strlen($value) > 80;
          if ($is_free_text_category || $is_text_field_80p) {
              array_push($property_free_texts, [
                  'field' => $field,
                  'label' => $pEstates->getFieldLabel($field),
                  'value' => $value,
                  'has_value' => !empty($value) ? true : false,
              ]);
          } else {
              array_push($property_features, [
                  'field' => $field,
                  'label' => $pEstates->getFieldLabel($field),
                  'value' => $value,
                  'has_value' => !empty($value) ? true : false,
              ]);

              $fields_counter++;
          }
      }

      if ($fields_counter > $fields_more) {
          $property_features_list_first = array_slice(
              $property_features,
              0,
              $fields_more,
          );
          $property_features_list_last = array_slice(
              $property_features,
              $fields_more,
              $fields_counter,
          );

          // First Part
          echo '<div class="c-property-details__features-items c-property-features --on-detail-page">';
          foreach ($property_features_list_first as $feature) {
              if (function_exists('oo_feature_item')) {
                  oo_feature_item($feature);
              }
          }
          echo '</div>';

          // Last
          echo '<div class="c-property-details__features-items --is-toggle">';
          echo '<div class="c-property-features --on-detail-page">';
          foreach ($property_features_list_last as $feature) {
              if (function_exists('oo_feature_item')) {
                  oo_feature_item($feature);
              }
          }
          echo '</div>';
          echo '</div>';
      } else {
          echo '<div class="c-property-details__features-items c-property-features --on-detail-page">';
          foreach ($property_features as $feature) {
              if (function_exists('oo_feature_item')) {
                  oo_feature_item($feature);
              }
          }
          echo '</div>';
      }
      ?>
					</div>
					<?php if ($fields_counter > $fields_more) { ?>
						<button
							class="c-property-details__more c-button"
							data-open-text="<?php echo esc_html('Mehr anzeigen', 'oo_theme'); ?>"
							data-close-text="<?php echo esc_html('Weniger anzeigen', 'oo_theme'); ?>">
							<?php echo esc_html('Mehr anzeigen', 'oo_theme'); ?>
						</button>
					<?php } ?>
				<?php } ?>


			</div>
		</div>

		<?php $has_accordion_content =
      is_array($property_free_texts) && count($property_free_texts) > 0; ?>
		<div class="c-property-details__container o-container">
			<div class="c-property-details__row o-row">
				<div class="c-property-details__main o-col-12 o-col-xl-8">

					<?php
     if ($has_accordion_content):
         echo '<div class="c-property-details__accordions c-accordion">';
     endif;
     $first_accordion = true;
     if (
         !array_search('lage', array_column($property_free_texts, 'field')) &&
         $map
     ) {
         $property_free_texts[] = [
             'field' => 'lage',
             'label' => $pEstates->getFieldLabel('lage'),
             'value' => '',
         ];
     }
     foreach ($property_free_texts as $field) { ?>
						<div class="c-accordion-card <?php
      if ($first_accordion == true) {
          echo '--is-open';
      } else {
          echo '--is-closed';
      }
      if ($field['field'] == 'lage') {
          echo ' --is-map';
      }
      ?>">
							<div class="c-accordion-card__title">
								<div class="c-accordion-card__icon-wrapper">
									<svg class="c-accordion-card__icon" width="24" viewBox="0 0 23.41 13.12">
										<path d="m.71.71l11,11L22.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"></path>
									</svg>
								</div>
								<h2 class="c-accordion-card__headline o-headline --h2">
									<?php esc_html_e($field['label']); ?>
								</h2>
							</div>
							<span class="c-accordion-card__content o-text">
								<?php echo nl2br($field['value']); ?>
								<?php if ($field['field'] == 'lage' && $map):
            echo $map;
        endif; ?>
							</span>
						</div>
					<?php $first_accordion = false;}
     ?>

					<?php if ($energy_fields_available) { ?>
						<div class="c-accordion-card --is-closed">
							<div class="c-accordion-card__title">
								<div class="c-accordion-card__icon-wrapper">
									<svg class="c-accordion-card__icon" width="24" viewBox="0 0 23.41 13.12">
										<path d="m.71.71l11,11L22.71.71" vector-effect="non-scaling-stroke" fill="none" stroke="currentColor" stroke-width="2"></path>
									</svg>
								</div>
								<h2 class="c-accordion-card__headline o-headline --h2">
									<?php esc_html_e('Energieausweis', 'oo_theme'); ?>
								</h2>
							</div>
							<div class="c-accordion-card__content">
								<div class="c-property-features --is-energy">
									<?php foreach ($energy_fields as $energy_item) {

             $energy_value = $current_property[$energy_item];

             if (
                 (is_numeric($energy_value) && 0 == $energy_value) ||
                 $energy_value == '0000-00-00' ||
                 $energy_value == '0.00' ||
                 $energy_value == '' ||
                 empty($energy_value)
             ) {
                 continue;
             }
             ?>

										<dl class="c-property-features__criteria">
											<dt class="c-property-features__label">
												<?php esc_html_e($pEstates->getFieldLabel($energy_item)); ?>
											</dt>
											<dd class="c-property-features__value">
												<?php echo nl2br($energy_value); ?>
											</dd>
										</dl>
									<?php
         } ?>
								</div>
							</div>
						</div>
					<?php } ?>

					<?php if ($has_accordion_content):
         echo '</div>';
     endif; ?>


					<?php
     // MOVIE ELEMENTS
     if (!empty($property_movie_players) || !empty($property_movie_links)) {
         echo '<div class="c-property-details__media">';
         echo '<h2 class="c-property-details__headline o-headline --h2">' .
             esc_html__('Videos', 'oo_theme') .
             '</h2>';

         // GET MOVIE PLAYERS
         if (
             !empty($property_movie_players) &&
             is_array($property_movie_players)
         ) {
             echo '<div class="c-property-details__embeds">';

             foreach ($property_movie_players as $property_movie_player) {
                 echo '<div class="c-property-details__video --' .
                     oo_get_service_domain($property_movie_player['url']) .
                     '">';
                 echo $property_movie_player['player'];
                 echo '</div>';
             }

             echo '</div>';
         }

         // GET MOVIE LINKS
         if (!empty($property_movie_links) && is_array($property_movie_links)) {
             echo '<div class="c-property-details__buttons c-buttons">';
             foreach ($property_movie_links as $property_movie_link) {
                 // Button Text
                 $button_title = !empty($property_movie_link['title'])
                     ? esc_attr($property_movie_link['title'])
                     : esc_html('Video starten', 'oo_theme');

                 echo '<a class="c-button --ghost" href="' .
                     esc_attr($property_movie_link['url']) .
                     '" target="_blank" title="' .
                     $button_title .
                     '">' .
                     $button_title .
                     '</a>';
             }
             echo '</div>';
         }

         echo '</div>';
     }

     // Ogulo
     if (!empty($property_ogulo_embeds) || !empty($property_ogulo_links)) {
         echo '<div class="c-property-details__media">';
         echo '<h2 class="c-property-details__headline o-headline --h2">' .
             esc_html__('360° Rundgänge', 'oo_theme') .
             '</h2>';

         if (
             !empty($property_ogulo_embeds) &&
             is_array($property_ogulo_embeds)
         ) {
             echo '<div class="c-property-details__embeds">';

             foreach ($property_ogulo_embeds as $property_ogulo_embed) {
                 echo '<div class="c-property-details__iframe --' .
                     oo_get_service_domain($property_ogulo_embed['url']) .
                     '">';
                 echo $property_ogulo_embed['player'];
                 echo '</div>';
             }

             echo '</div>';
         }

         if (!empty($property_ogulo_links) && is_array($property_ogulo_links)) {
             echo '<div class="c-property-details__buttons c-buttons">';
             foreach ($property_ogulo_links as $property_ogulo_link) {
                 // Button Text
                 $button_title = !empty($property_ogulo_link['title'])
                     ? esc_attr($property_ogulo_link['title'])
                     : esc_attr('360°-Rundgang starten', 'oo_theme');

                 echo '<a class="c-button --ghost" href="' .
                     esc_attr($property_ogulo_link['url']) .
                     '" target="_blank" title="' .
                     $button_title .
                     '">' .
                     $button_title .
                     '</a>';
             }
             echo '</div>';
         }

         echo '</div>';
     }

     // Objects
     if (!empty($property_object_embeds) || !empty($property_object_links)) {
         echo '<div class="c-property-details__media">';
         echo '<h2 class="c-property-details__headline o-headline --h2">' .
             esc_html__('Objekte', 'oo_theme') .
             '</h2>';

         if (
             !empty($property_object_embeds) &&
             is_array($property_object_embeds)
         ) {
             echo '<div class="c-property-details__embeds">';

             foreach ($property_object_embeds as $property_object_embed) {
                 echo '<div class="c-property-details__iframe --' .
                     oo_get_service_domain($property_object_embed['url']) .
                     '">';
                 echo $property_object_embed['player'];
                 echo '</div>';
             }

             echo '</div>';
         }

         if (
             !empty($property_object_links) &&
             is_array($property_object_links)
         ) {
             echo '<div class="c-property-details__buttons c-buttons">';
             foreach ($property_object_links as $property_object_link) {
                 // Button Text
                 $button_title = !empty($property_object_link['title'])
                     ? esc_attr($property_object_link['title'])
                     : esc_attr('Objekt-Link öffnen', 'oo_theme');

                 echo '<a class="c-button --ghost" href="' .
                     esc_attr($property_object_link['url']) .
                     '" target="_blank" title="' .
                     $button_title .
                     '">' .
                     $button_title .
                     '</a>';
             }
             echo '</div>';
         }

         echo '</div>';
     }

     // Links
     if (!empty($property_links) || !empty($property_link_embeds)) {
         echo '<div class="c-property-details__media">';
         echo '<h2 class="c-property-details__headline o-headline --h2">' .
             esc_html__('Links', 'oo_theme') .
             '</h2>';

         if (!empty($property_link_embeds) && is_array($property_link_embeds)) {
             echo '<div class="c-property-details__embeds">';

             foreach ($property_link_embeds as $property_link_embed) {
                 echo '<div class="c-property-details__iframe --' .
                     oo_get_service_domain($property_link_embed['url']) .
                     '">';
                 echo $property_link_embed['player'];
                 echo '</div>';
             }

             echo '</div>';
         }

         if (!empty($property_links) && is_array($property_links)) {
             echo '<div class="c-property-details__buttons c-buttons">';
             foreach ($property_links as $property_link) {
                 // Button Text
                 $button_title = !empty($property_link['title'])
                     ? esc_attr($property_link['title'])
                     : esc_attr('Link öffnen', 'oo_theme');

                 echo '<a class="c-button --ghost" href="' .
                     esc_attr($property_link['url']) .
                     '" target="_blank" title="' .
                     $button_title .
                     '">' .
                     $button_title .
                     '</a>';
             }
             echo '</div>';
         }

         echo '</div>';
     }
     ?>

					<?php if (!empty($pEstates->getEstateUnits())) {
         echo '<div class="c-property-details__table">';
         echo $pEstates->getEstateUnits();
         echo '</div>';
     } ?>


					<?php if (!empty($shortcode_form)) { ?>
						<div id="request" class="c-property-details__form">
							<?php echo do_shortcode($shortcode_form); ?>
						</div>
					<?php } ?>

				</div>
				<div class="c-property-details__aside o-col-12 o-col-md-6 o-col-xl-4">

					<?php if (!empty($pEstates->getEstateContacts())) {

         echo '<div class="c-property-details__contacts">';

         $configured_address_fields = $pEstates->getAddressFields();

         $address_fields = array_diff($configured_address_fields, [
             'imageUrl',
             'Anrede',
             'Anrede-Titel',
             'Titel',
             'Vorname',
             'Name',
             'Zusatz1', // Company
             'Strasse',
             'Plz',
             'Ort',
         ]);
         $contacts = $pEstates->getEstateContacts();
         $headline = oo_get_contacts_headline($contacts);
         ?>

						<h2 class="c-property-details__headline o-headline">
							<?php echo $headline; ?>
						</h2>

						<?php
      $contacts_count = is_array($pEstates->getEstateContacts())
          ? count($pEstates->getEstateContacts())
          : 0;
      if ($contacts_count > 1 == true) {
          echo '<div class="c-property-details__contacts-wrapper">';
      }

      foreach ($pEstates->getEstateContacts() as $contact_data) { ?>
							<div class="c-property-details__contact c-contact-person">
								<?php
        if ($contact_data['imageUrl']) {
            $image = '';
            if (
                $contact_data['Vorname'] !== '' ||
                $contact_data['Name'] !== ''
            ) {
                $image_alt =
                    ($contact_data['Titel']
                        ? $contact_data['Titel'] . ' '
                        : '') .
                    $contact_data['Vorname'] .
                    ' ' .
                    $contact_data['Name'];
            } else {
                $image_alt = esc_html('Ansprechpartner', 'oo_theme');
            }

            $image = [
                'url' => $contact_data['imageUrl'],
                'alt' => $image_alt,
            ];

            // image width
            $contact_image_width_xs = '543';
            $contact_image_width_sm = '512';
            $contact_image_width_md = '331';
            $contact_image_width_lg = '448';
            $contact_image_width_xl = '352';
            $contact_image_width_xxl = '416';
            $contact_image_width_xxxl = '460';

            if (!empty($image)) {
                oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' => 'c-contact-person__picture o-picture',
                    'image_class' => 'c-contact-person__image o-image',
                    'additional_cloudimg_params' => '&func=crop&gravity=face',
                    'dimensions' => [
                        '575' => [
                            'w' => $contact_image_width_xs,
                            'h' => round(($contact_image_width_xs * 2) / 3),
                        ],
                        '1600' => [
                            'w' => $contact_image_width_xxxl,
                            'h' => round(($contact_image_width_xxxl * 2) / 3),
                        ],
                        '1400' => [
                            'w' => $contact_image_width_xxl,
                            'h' => round(($contact_image_width_xxl * 2) / 3),
                        ],
                        '1200' => [
                            'w' => $contact_image_width_xl,
                            'h' => round(($contact_image_width_xl * 2) / 3),
                        ],
                        '992' => [
                            'w' => $contact_image_width_lg,
                            'h' => round(($contact_image_width_lg * 2) / 3),
                        ],
                        '768' => [
                            'w' => $contact_image_width_md,
                            'h' => round(($contact_image_width_md * 2) / 3),
                        ],
                        '576' => [
                            'w' => $contact_image_width_sm,
                            'h' => round(($contact_image_width_sm * 2) / 3),
                        ],
                    ],
                ]);
            }
        } else {
            echo '<div class="c-contact-person__picture"></div>';
        }

        $salutation = $contact_data['Anrede'];
        $title = $contact_data['Titel'];
        $first_name = $contact_data['Vorname'];
        $last_name = $contact_data['Name'];
        $job_title = $contact_data['jobPosition'];
        $email = $contact_data['Email'];
        $phone = $contact_data['defaultphone'];
        $mobile = $contact_data['mobile'];
        $fax = $contact_data['defaultfax'];
        $company = $contact_data['Zusatz1'];
        $street = $contact_data['Strasse'];
        $postCode = $contact_data['Plz'];
        $town = $contact_data['Ort'];

        // Output name, depending on available fields.
        $name_components = [];

        if ($salutation) {
            $name_components[] = $salutation;
        }
        if ($title) {
            $name_components[] = $title;
        }
        if ($first_name) {
            $name_components[] = $first_name;
        }
        if ($last_name) {
            $name_components[] = $last_name;
        }
        $name_output = join(' ', $name_components);

        if ($name_output) {
            echo '<p class="c-contact-person__name o-headline --h3">';
            echo esc_html($name_output);
            echo '</p>';
        }

        $labels_fields = [
            'Email',
            'email',
            'fax',
            'Telefax1',
            'Telefon1',
            'mobile',
            'Homepage',
        ];

        // Output all other configured fields.
        foreach ($address_fields as $field) {
            if (empty($contact_data[$field])) {
                continue;
            } elseif (is_array($contact_data[$field])) {
                foreach ($contact_data[$field] as $item) {
                    if (in_array($field, $labels_fields)) {
                        oo_property_field_type($field, $item);
                    } else {
                        if (!empty($contact_data[$field])) {
                            echo '<p class="c-contact-person__data --is-' .
                                strtolower($field) .
                                '">';
                            echo esc_html($contact_data[$field]);
                            echo '</p>';
                        }
                    }
                }
            } else {
                if (in_array($field, $labels_fields)) {
                    oo_property_field_type($field, $contact_data[$field]);
                } else {
                    if (!empty($contact_data[$field])) {
                        echo '<p class="c-contact-person__data --is-' .
                            strtolower($field) .
                            '">';
                        echo esc_html($contact_data[$field]);
                        echo '</p>';
                    }
                }
            }
        }

        $street_output = '';
        if ($street) {
            $street_output = $street;
        }
        $city_components = [];
        if ($postCode) {
            $city_components[] = $postCode;
        }
        if ($town) {
            $city_components[] = $town;
        }
        $city_output = join(' ', $city_components);

        if ($street_output && $city_output) {
            echo '<p class="c-contact-person__address">' .
                esc_html($street_output) .
                '<br>' .
                esc_html($city_output) .
                '</p>';
        } elseif ($street_output) {
            echo '<p class="c-contact-person__address">' .
                esc_html($street_output) .
                '</p>';
        } elseif ($city_output) {
            echo '<p class="c-contact-person__address">' .
                esc_html($city_output) .
                '</p>';
        }
        ?>

							</div>
					<?php }

      if ($contacts_count > 1 == true) {
          echo '</div>';
      }

      echo '</div>';

     } ?>

					<div class="c-property-details__aside-buttons c-buttons --is-column">
						<div class="c-property-details__sharing">
							<?php
       global $wp;

       $property_detail_page =
           get_field('general', 'option')['property_detail'] ?? [];
       $property_share_button = filter_var(
           $property_detail_page['property_share_button'],
           FILTER_VALIDATE_BOOLEAN,
       );

       if ($property_share_button) {
           oo_get_template('components', '', 'component-share', [
               'button_class' => 'c-button',
               'share_link' => home_url(add_query_arg([], $wp->request)),
           ]);
       }
       ?>
						</div>
						<?php
      if ($pEstates->getDocument() != '') {
          echo '<div class="c-property-details__expose">';
          echo '<a class="c-button --ghost" href="' .
              $pEstates->getDocument() .
              '">';
          echo esc_html__('Exposé herunterladen', 'oo_theme');
          echo '</a>';
          echo '</div>';
      }
      echo '</div>';

      // Load SimilarEstates
      echo $pEstates->getSimilarEstates();
      ?>
					</div>
				</div>
			</div>
	</section>
<?php
}

if (Favorites::isFavorizationEnabled() && !$is_reference) { ?>
	<?php wp_enqueue_script('oo-favorites-script'); ?>

	<script>
		jQuery(document).ready(function($) {
			onofficeFavorites = new onOffice.favorites(<?php echo json_encode(
       Favorites::COOKIE_NAME,
   ); ?>);
			onOffice.addFavoriteButtonLabel = function(i, element) {
				var estateId = $(element).attr('data-onoffice-estateid');
				var elementText = $(element).find('.c-property-card__favorite-text');
				var elementIcon = $(element).find('.c-property-card__favorite-icon path');
				if (!onofficeFavorites.favoriteExists(estateId)) {
					$(elementText).text('<?php if ($favorite_label == 'Watchlist') {
         esc_html_e(__('Zur Merkliste hinzufügen', 'oo_theme'));
     } else {
         esc_html_e(__('Zu Favoriten hinzufügen', 'oo_theme'));
     } ?>');
					$(elementIcon).css({
						fill: 'none'
					});
					$(element).on('click', function() {
						onofficeFavorites.add(estateId);
						onOffice.addFavoriteButtonLabel(0, element);
					});
				} else {
					$(elementText).text('<?php if ($favorite_label == 'Watchlist') {
         esc_html_e(__('Von Merkliste entfernen', 'oo_theme'));
     } else {
         esc_html_e(__('Von Favoriten entfernen', 'oo_theme'));
     } ?>');
					$(elementIcon).css({
						fill: 'currentColor'
					});
					$(element).on('click', function() {
						onofficeFavorites.remove(estateId);
						onOffice.addFavoriteButtonLabel(0, element);
					});
				}
			};
			$('.c-property-card__favorite').each(onOffice.addFavoriteButtonLabel);
		});
	</script>
<?php }
?>
