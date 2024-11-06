<?php

/**
 *
 *    Copyright (C) 2018  onOffice GmbH
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

use onOffice\WPlugin\AddressList;
use onOffice\WPlugin\Types\FieldTypes;

/**
 *
 *  Default template for address lists
 *
 */

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$headline = get_field('headline') ?? null;
// Section ID for pagination anchor
$anchor = $headline['text'] ? clean_id($headline['text']) : '';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

$dont_echo = [
    'Anrede',
    'Name',
    'Vorname',
    'jobTitle',
    'laengengrad',
    'breitengrad',
];

$agent_count = method_exists($pAddressList, 'getAddressOverallCount')
    ? $pAddressList->getAddressOverallCount()
    : count($pAddressList->getRows() ?? []);

$image_width_xs = '384';
$image_width_sm = '508';
$image_width_md = '327';
$image_width_lg = '444';
$image_width_xl = '348';
$image_width_xxl = '412';
$image_width_xxxl = '456';
?>

<section class="c-address-list__inner o-row --<?php echo $settings[
    'bg_color'
]; ?> --<?php echo $settings['bg_color']; ?>-mixed --with-separator">
  <p class="c-address-list__count o-container">
    <?php esc_html_e('Gefundene Berater:', 'oo_theme'); ?>
    <span class="c-address-list__number"><?php echo sprintf(
        '%d',
        $agent_count,
    ); ?></span>
  </p>

  <div class="c-team__container o-container">

    <?php if (!$is_slider) { ?>
      <div class="c-team__members">
    <?php } else { ?>
      <div class="c-team__slider --on-<?php echo $bg_color; ?> c-slider --is-team-slider splide"
        data-splide='{
          "perPage":1,
          "perMove":1,
          "gap":32,
          "pagination":false,
          "snap":true,
          "lazyLoad":"nearby",
          "mediaQuery":"min",
          "breakpoints":{
            "768":{"perPage":2},
            "1200":{"perPage":3}
          }
        }'>
        <div class="c-slider__track splide__track">
          <div class="c-slider__list splide__list">
    <?php } ?>

    <?php foreach ($pAddressList->getRows() as $addressId => $escapedValues) {

        $estate_count = $pAddressList->getCountEstates($addressId);
        $image_url = $escapedValues['imageUrl'];
        $image_alt = $pAddressList->generateImageAlt($addressId);
        $image = [
            'url' => $image_url,
            'alt' => $image_alt,
        ];

        $phone = $escapedValues['Telefon1'] ?? null;
        $phone_url = oo_clean_link_number($phone);

        unset($escapedValues['imageUrl']);

        $job = $escapedValues['jobTitle'] ?? null;
        ?>

      <article class="c-team-card --is-address --is-list-item
        <?php if ($is_slider) {
            echo 'splide__slide';
        } ?>
                ">
                    <?php
                    echo '<a href="' .
                        esc_url($pAddressList->getAddressLink($addressId)) .
                        '" class="c-address-list__link">';
                    if ($estate_count > 0) {
                        echo '<span class="c-address-list__estate-count">';
                        echo sprintf(
                            _n(
                                '%d Inserat',
                                '%d Inserate',
                                $estate_count,
                                'oo_theme',
                            ),
                            $estate_count,
                        );
                        echo '</span>';
                    }
                    if (!empty($image_url)) {
                        oo_get_template('components', '', 'component-image', [
                            'image' => $image,
                            'picture_class' => 'c-team-card__picture o-picture',
                            'image_class' => 'c-team-card__image o-image',
                            'additional_cloudimg_params' =>
                                '&func=crop&gravity=face',
                            'dimensions' => [
                                '575' => [
                                    'w' => $image_width_xs,
                                    'h' => round(($image_width_xs * 2) / 3),
                                ],
                                '1600' => [
                                    'w' => $image_width_xxxl,
                                    'h' => round(($image_width_xxxl * 2) / 3),
                                ],
                                '1400' => [
                                    'w' => $image_width_xxl,
                                    'h' => round(($image_width_xxl * 2) / 3),
                                ],
                                '1200' => [
                                    'w' => $image_width_xl,
                                    'h' => round(($image_width_xl * 2) / 3),
                                ],
                                '992' => [
                                    'w' => $image_width_lg,
                                    'h' => round(($image_width_lg * 2) / 3),
                                ],
                                '768' => [
                                    'w' => $image_width_md,
                                    'h' => round(($image_width_md * 2) / 3),
                                ],
                                '576' => [
                                    'w' => $image_width_sm,
                                    'h' => round(($image_width_sm * 2) / 3),
                                ],
                            ],
                        ]);
                    } else {
                        echo '<picture class="c-team-card__picture o-picture"></picture>';
                    }
                    echo '<div class="c-address-list__overlay">';
                    echo '<span class="c-address-list__button">';
                    esc_html_e('Details anzeigen', 'oo_theme');
                    echo '</span>';
                    echo '</div>';
                    echo '</a>';
                    ?>
        <div class="c-team-card__content">
          <?php
          if (
              (!empty($escapedValues['Vorname']) &&
                  !empty($escapedValues['Name'])) ||
              !empty($escapedValues['Zusatz1'])
          ):
              echo '<p class="c-team-card__name o-headline">';

              if (
                  !empty($escapedValues['Vorname']) &&
                  !empty($escapedValues['Name'])
              ) {
                  echo $escapedValues['Vorname'] . ' ' . $escapedValues['Name'];
              } else {
                  if (!empty($escapedValues['Zusatz1'])) {
                      echo $escapedValues['Zusatz1'];
                      array_push($dont_echo, 'Zusatz1');
                  }
              }
              echo '</p>';
          endif;

          echo '<p class="c-team-card__job">';
          if (!empty($job)) {
              echo $job;
          }
          echo '</p>';

          foreach ($escapedValues as $field => $value) {
              if (
                  $pAddressList->getFieldType($field) ===
                      FieldTypes::FIELD_TYPE_BLOB ||
                  empty($value)
              ) {
                  continue;
              }

              $fieldLabel = $pAddressList->getFieldLabel($field);

              switch ($field) {
                  case 'Email':
                      echo '<dl class="c-team-card__contact --is-email">';
                      echo '<dt class="c-team-card__contact-label">';
                      echo esc_html($fieldLabel) . ':';
                      echo '</dt>';
                      echo '<dd class="c-team-card__contact-value">';
                      oo_get_template('components', '', 'component-email', [
                          'email' => $value,
                          'additional_link_class' => $bg_color
                              ? '--text-color --on-' . $bg_color
                              : '--text-color',
                          'truncate' => true,
                      ]);
                      echo '</dd>';
                      echo '</dl>';
                      break;
                  case 'Telefon1':
                      echo '<dl class="c-team-card__contact --is-phone --hide-mobile">';
                      echo '<dt class="c-team-card__contact-label">';
                      echo esc_html($fieldLabel) . ':';
                      echo '</dt>';
                      echo '<dd class="c-team-card__contact-value">';
                      oo_get_template(
                          'components',
                          '',
                          'component-contact-numbers',
                          [
                              'number' => $value,
                              'additional_link_class' => $bg_color
                                  ? '--text-color --on-' . $bg_color
                                  : '--text-color',
                          ],
                      );
                      echo '</dd>';
                      echo '</dl>';
                      break;
                  case 'Telefax1':
                      echo '<dl class="c-team-card__contact --is-phone">';
                      echo '<dt class="c-team-card__contact-label">';
                      echo esc_html($fieldLabel) . ':';
                      echo '</dt>';
                      echo '<dd class="c-team-card__contact-value">';
                      oo_get_template(
                          'components',
                          '',
                          'component-contact-numbers',
                          [
                              'number' => $value,
                              'additional_link_class' => $bg_color
                                  ? '--text-color --on-' . $bg_color
                                  : '--text-color',
                          ],
                      );
                      echo '</dd>';
                      echo '</dl>';
                      break;
                  default:
                      if (!in_array($field, $dont_echo)) {
                          echo '<dl class="c-team-card__contact">';
                          echo '<dt class="c-team-card__contact-label">';
                          echo esc_html($fieldLabel) . ':';
                          echo '</dt>';
                          echo '<dd class="c-team-card__contact-value">';
                          echo is_array($value)
                              ? implode(', ', array_filter($value))
                              : $value;
                          echo '</dd>';
                          echo '</dl>';
                      }
              }
          }
          echo '<div class="c-address-list__contact-wrapper">';
          echo '<a href="' .
              esc_url($pAddressList->getAddressLink($addressId)) .
              '#contact_form" class="c-address-list__contact c-button">';
          echo __('Zum Kontaktformular', 'oo_theme');
          echo '</a>';

          if (!empty($phone)) {
              echo '<a href="' .
                  esc_url('tel:' . $phone_url) .
                  '" class="c-address-list__contact c-button --ghost --hide-desktop">';
              echo __('Anrufen', 'oo_theme');
              echo '</a>';
          }

          echo '</div>';
          ?>
        </div>

      </article>

    <?php
    } ?>


    <?php if (!$is_slider) { ?>
      </div>

            <?php oo_get_template('components', '', 'component-pagination', [
                'type' => 'property',
                'class' =>
                    'c-property-list__pagination --on-' . $settings['bg_color'],
                'anchor' => $anchor,
            ]); ?>
    <?php } else { ?>
        </div>
      </div>
      <div class="c-slider__navigation splide__navigation">
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
    </div>
</section>
