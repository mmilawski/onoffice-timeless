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

$dont_echo = [
    'objekttitel',
    'objektbeschreibung',
    'lage',
    'ausstatt_beschr',
    'sonstige_angaben',
];
$location_fields = ['plz', 'ort', 'land'];
$price_fields = ['kaufpreis', 'kaltmiete', 'nettokaltmiete'];

$pEstatesClone = clone $pEstates;
$pEstatesClone->resetEstateIterator();

while ($current_property = $pEstatesClone->estateIterator()):

    $property_status = $current_property['vermarktungsstatus'];
    unset($current_property['vermarktungsstatus']);

    $property_url = esc_url($pEstatesClone->getEstateLink());
    $property_id = $pEstatesClone->getCurrentMultiLangEstateMainId();
    $raw_values = $pEstatesClone->getRawValues();
    $is_reference = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['referenz'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
    );
    $is_restricted_view = $pEstatesClone->getViewRestrict();

    $is_location_fields = false;
    $is_price_fields = false;
    $is_fields = false;

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

        if (in_array($field, $location_fields)) {
            $is_location_fields = true;
        }

        if (in_array($field, $price_fields)) {
            $is_price_fields = true;
        }

        if (
            !in_array($field, $location_fields) &&
            !in_array($field, $price_fields)
        ) {
            $is_fields = true;
        }
    }

    $first_picture = true;

    // image width
    $image_width_xs = '542';
    $image_width_sm = '511';
    $image_width_md = '593';
    $image_width_lg = '463';
    $image_width_xl = '463';
    $image_width_xxl = '543';
    $image_width_xxxl = '598';
    ?>

<div class="c-property-card --bg-transparent <?php if ($bg_color) {
    echo '--on-' . $bg_color;
} ?> <?php if ($is_slider) {
     echo '--on-slider c-slider__slide splide__slide';
 } ?>">

 <?php
 $property_pictures = $pEstatesClone->getEstatePictures();
 $pictures_count = is_array($property_pictures) ? count($property_pictures) : 0;
 ?>

    
        <div class="c-property-card__inner --is-properties-images-slider --on-slider <?php if (
            $pictures_count > 0
        ) {
            echo 'c-slider splide';
        } ?>" data-splide='{"perPage":1,"perMove":1,"pagination":false,"arrows":true, "drag":false,"snap":true,"lazyLoad":"nearby", "type":"loop"}'>
                <?php if ($pictures_count > 0) { ?>   
                <div class="c-slider__track splide__track">
                    <div class="c-slider__list splide__list">

                        <?php foreach ($property_pictures as $id) {
                            $picture_values = $pEstatesClone->getEstatePictureValues(
                                $id,
                            );
                            if ($picture_values['title'] == true) {
                                $image_alt = esc_html($picture_values['title']);
                            } else {
                                $image_alt = esc_html__(
                                    'Immobilienbild',
                                    'oo_theme',
                                );
                            }
                            $image = [
                                'url' => $pEstatesClone->getEstatePictureUrl(
                                    $id,
                                ),
                                'alt' => $image_alt,
                            ];

                            oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $image,
                                    'picture_class' =>
                                        'c-property-card__picture o-picture c-slider__slide splide__slide',
                                    'image_class' =>
                                        'c-property-card__image o-image',
                                    'dimensions' => [
                                        '575' => [
                                            'w' => $image_width_xs,
                                            'h' => round(
                                                ($image_width_xs * 2) / 3,
                                            ),
                                        ],
                                        '1600' => [
                                            'w' => $image_width_xxxl,
                                            'h' => round(
                                                ($image_width_xxxl * 2) / 3,
                                            ),
                                        ],
                                        '1400' => [
                                            'w' => $image_width_xxl,
                                            'h' => round(
                                                ($image_width_xxl * 2) / 3,
                                            ),
                                        ],
                                        '1200' => [
                                            'w' => $image_width_xl,
                                            'h' => round(
                                                ($image_width_xl * 2) / 3,
                                            ),
                                        ],
                                        '992' => [
                                            'w' => $image_width_lg,
                                            'h' => round(
                                                ($image_width_lg * 2) / 3,
                                            ),
                                        ],
                                        '768' => [
                                            'w' => $image_width_md,
                                            'h' => round(
                                                ($image_width_md * 2) / 3,
                                            ),
                                        ],
                                        '576' => [
                                            'w' => $image_width_sm,
                                            'h' => round(
                                                ($image_width_sm * 2) / 3,
                                            ),
                                        ],
                                    ],
                                ],
                            );
                        }
                    // }
                    ?>
                    </div>
                </div>
                <?php } else { ?>
                        <div class="c-property-card__picture"></div>
                    <?php } ?>
              <?php if ($pictures_count > 0) { ?>   
                    <div class="c-slider__navigation splide__navigation --is-properties-images-slider">
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
                <?php } ?>      
                    <?php if (
                        $property_status ||
                        (Favorites::isFavorizationEnabled() && !$is_reference)
                    ) { ?>
                <div class="c-property-card__flags c-flags --space-between">
                    <?php if ($property_status) { ?>
                        <span class="c-property-card__status c-flag --property-status">
                            <?php echo ucfirst($property_status); ?>
                        </span>
                    <?php } ?>
                
                    <?php if (
                        Favorites::isFavorizationEnabled() &&
                        !$is_reference
                    ) { ?>
                        <span class="c-property-card__favorite c-icon-button --small-corners" data-onoffice-property-id="<?php echo $property_id; ?>">
                            <span class="c-icon-button__text u-screen-reader-only">
                                <?php
                                $favorite_label = Favorites::getFavorizationLabel();
                                if ($favorite_label == 'Watchlist') {
                                    esc_html_e(
                                        __(
                                            'Zur Merkliste hinzufügen',
                                            'oo_theme',
                                        ),
                                    );
                                    $favorite_icon = 'bookmark';
                                } else {
                                    esc_html_e(
                                        __(
                                            'Zu Favoriten hinzufügen',
                                            'oo_theme',
                                        ),
                                    );
                                    $favorite_icon = 'star';
                                }
                                ?>
                            </span>
                            <span class="c-icon-button__icon --favorite"><?php oo_get_icon(
                                $favorite_icon,
                            ); ?></span>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>  
        </div>  
    <div class="c-property-card__content">
            <?php if ($current_property['objekttitel']) { ?>
                <h3 class="c-property-card__title">
                    <?php echo $current_property['objekttitel']; ?>
                </h3>
            <?php } ?>
            <?php if ($is_fields) { ?>
                <div class="c-property-card__features c-property-features">
                    <?php foreach ($current_property as $field => $value) {
                        if (
                            (is_numeric($value) && 0 == $value) ||
                            $value == '0000-00-00' ||
                            $value == '0.00' ||
                            $value == '' ||
                            empty($value) ||
                            in_array($field, $dont_echo) ||
                            in_array($field, $location_fields) ||
                            in_array($field, $price_fields)
                        ) {
                            continue;
                        } ?>
                        <span class="c-property-features__item">
                          <?php
                          $dont_echo_label = [
                              'objektart',
                              'objekttyp',
                              'vermarktungsart',
                          ];
                          if (!in_array($field, $dont_echo_label)) {
                              esc_html_e(
                                  $pEstates->getFieldLabel($field) . ': ',
                              );
                          }

                          if (is_array($value)) {
                              esc_html_e(implode(', ', $value));
                          } else {
                              echo esc_html($value);
                          }
                          ?>
                          <?php  ?>




                        </span>
                    <?php
                    } ?>

                    



                    <span class="c-property-features__item <?php if (
                        !$current_property['plz'] &&
                        !$current_property['ort'] &&
                        !$current_property['land']
                    ) {
                        echo '--empty';
                    } ?>"><?php if ($current_property['plz']) {
    echo $current_property['plz'];
} ?> <?php
 if ($current_property['ort'] && $current_property['ort'] !== '') {
     echo $current_property['ort'];
 }
 if (
     ($current_property['plz'] || $current_property['ort']) &&
     $current_property['land']
 ) {
     echo ', ';
 }
 if ($current_property['land']) {
     echo $current_property['land'];
 }
 ?></span>
                </div>
            <?php } ?>
            <?php if (!empty($current_property['objektbeschreibung'])) { ?>
                <div class="c-property-card__description">
                    <h4 class="c-property-card__description-headline">
                        <?php esc_html_e(
                            $pEstates->getFieldLabel('objektbeschreibung'),
                        ); ?>:
                    </h4>
                    <p class="c-property-card__description-text">
                        <?php echo nl2br(
                            $current_property['objektbeschreibung'],
                        ); ?>
                    </p>
                </div>
            <?php } ?>
            <?php if ($is_price_fields) { ?>
                    <?php foreach ($price_fields as $price_field) {

                        $price_value = $current_property[$price_field];
                        if (
                            (is_numeric($price_value) && 0 == $price_value) ||
                            $price_value == '0000-00-00' ||
                            $price_value == '0.00' ||
                            $price_value == '' ||
                            empty($price_value)
                        ) {
                            continue;
                        }
                        ?>
                        <div class="c-property-card__price">
                            <h4>
                              <?php
                              esc_html_e(
                                  $pEstates->getFieldLabel($price_field),
                              );

                              echo ':';
                              ?>
                              <?php if (is_array($price_value)) {
                                  esc_html_e(implode(', ', $price_value));
                              } else {
                                  echo esc_html($price_value);
                              } ?>
                            </h4>
                        </div>
                    <?php
                    } ?>
                <?php } ?>
            <div class="c-property-card__footer">
                <?php if (!$is_reference || !$is_restricted_view) { ?>
                    <?php
                    $button = [
                        [
                            'link' => [
                                'title' => esc_html__(
                                    'Zur Detailansicht',
                                    'oo_theme',
                                ),
                                'url' => $property_url,
                            ],
                        ],
                    ];

                    oo_get_template('components', '', 'component-buttons', [
                        'buttons' => $button,
                        'additional_button_class' =>
                            'c-property-card__button --full-width --on-bg-transparent',
                        'additional_container_class' =>
                            'c-property-card__buttons',
                    ]);
                    ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php
endwhile;
?>

<?php if (Favorites::isFavorizationEnabled()) { ?>
    <?php wp_enqueue_script('oo-favorites-script'); ?>

    <script>
        jQuery(document).ready(function($) {
            onofficeFavorites = new onOffice.favorites(<?php echo json_encode(
                Favorites::COOKIE_NAME,
            ); ?>);
            onOffice.addFavoriteButtonLabel = function(i, element) {
                var favorite = $(element);
                var propertyId = favorite.attr('data-onoffice-property-id');
                var favoriteText = favorite.find('.u-screen-reader-text');
                var favoriteIcon = favorite.find('.--favorite');
                var favoriteClass = '--filled';
                if (!onofficeFavorites.favoriteExists(propertyId)) {
                    favoriteText.text('<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e(__('Zur Merkliste hinzufügen', 'oo_theme'));
                    } else {
                        esc_html_e(__('Zu Favoriten hinzufügen', 'oo_theme'));
                    } ?>');
                    favoriteIcon.removeClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.add(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                } else {
                    favoriteText.text('<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e(__('Von Merkliste entfernen', 'oo_theme'));
                    } else {
                        esc_html_e(__('Von Favoriten entfernen', 'oo_theme'));
                    } ?>');
                    favoriteIcon.addClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.remove(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                }
            };
            $('.c-property-card__favorite').each(onOffice.addFavoriteButtonLabel);
        });
    </script>
<?php } ?>
