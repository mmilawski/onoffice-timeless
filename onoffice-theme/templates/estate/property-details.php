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

use onOffice\WPlugin\EstateDetail;
use onOffice\WPlugin\Favorites;
use onOffice\WPlugin\EstateCostsChart;

$dont_echo = ['objekttitel', 'vermarktungsstatus'];
$energy_fields = [
    'endenergiebedarf',
    'energieverbrauchskennwert',
    'energieausweistyp',
    'energieausweis_gueltig_bis',
    'energyClass',
    'energietraeger',
    'energiepassAusstelldatum',
    'erschliessung',
    'baujahr',
    'energieausweisBaujahr',
    'endenergiebedarfStrom',
    'endenergiebedarfWaerme',
    'endenergieverbrauchStrom',
    'endenergieverbrauchWaerme',
    'warmwasserEnthalten',
    'co2_Emissionsklasse',
    'co2ausstoss',
    'energieausweisNichtKlassifizierbar',
    'ea_fgee_klasse_at',
    'ea_hwb_klasse_at',
    'ea_hwb_at',
    'ea_fgee_at',
    'energiepassJahrgang',
];

$price_fields = ['kaufpreis', 'kaltmiete'];

$iframe_display = filter_var(
    get_field('iframe_display', 'option')['display_as_iframe'] ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

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
        $first_picture = true;
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
    $area_butler_url = [];

    // status
    $property_status = $current_property['vermarktungsstatus'];

    // link
    $property_link = esc_url($pEstates->getEstateLink());

    // form
    $shortcode_form = $pEstates->getShortCodeForm();

    // fields
    $property_features = [];
    $property_free_texts = [];
    $energy_fields_available = false;
    $price_fields_available = false;

    foreach ($current_property as $field => $value) {
        if (
            (is_numeric($value) && 0 == $value) ||
            $value == '0000-00-00' ||
            $value == '0.00' ||
            (is_string($value) &&
                $value !== '' &&
                !is_numeric($value) &&
                ($raw_values->getValueRaw($property_id)['elements'][$field] ??
                    null) ===
                    '0') || // skip negative boolean fields
            $value == '' ||
            empty($value) ||
            in_array($field, $dont_echo)
        ) {
            continue;
        }

        if (in_array($field, $energy_fields)) {
            $energy_fields_available = true;
            continue;
        }

        if (in_array($field, $price_fields)) {
            $price_fields_available = true;
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
            if (
                $field == 'MPAreaButlerUrlNoAddress' ||
                $field == 'MPAreaButlerUrlWithAddress'
            ) {
                $area_butler_url = [
                    'field' => $field,
                    'label' => $pEstates->getFieldLabel($field),
                    'value' => $value,
                    'has_value' => !empty($value) ? true : false,
                ];
            } else {
                array_push($property_free_texts, [
                    'field' => $field,
                    'label' => $pEstates->getFieldLabel($field),
                    'value' => $value,
                    'has_value' => !empty($value) ? true : false,
                ]);
            }
        } else {
            $group_name = str_replace(
                ['-', '–', '—'],
                ' ',
                $field_infos['content'],
            );

            if (!empty($group_name)) {
                if (!isset($property_features[$group_name])) {
                    $property_features[$group_name] = [];
                }

                $property_features[$group_name][] = [
                    'field' => $field,
                    'label' => $pEstates->getFieldLabel($field),
                    'value' => $value,
                    'type' => $field_infos['type'],
                ];
            }
        }
    }
    // show infrastructure information with map instead of data sheet if possible
    if (!empty($area_butler_url)) {
        $infrastructure_info = $property_features['Infrastruktur'] ?? [];
        unset($property_features['Infrastruktur']);
    }

    $title_image = null;
    $sorted_pictures = [];

    foreach ($property_pictures as $id) {
        $picture_values = $pEstates->getEstatePictureValues($id);

        if (
            $picture_values['type'] ===
            \onOffice\WPlugin\Types\ImageTypes::TITLE
        ) {
            $title_image = $id;
        } else {
            $sorted_pictures[] = $id;
        }
    }

    if ($title_image) {
        array_unshift($sorted_pictures, $title_image);
    }
    ?>

    <section class="c-property-details o-section --bg-transparent">

    <?php if ($iframe_display) { ?>
        <a class="c-button c-property-details__back-button" href="javascript:history.back();"><?php esc_html_e(
            'Zurück',
            'oo_theme',
        ); ?></a>
    <?php } ?>

        <div class="c-property-details__banner">
            <?php if (
                !empty($property_pictures) &&
                is_array($property_pictures)
            ) {
                foreach ($sorted_pictures as $id) {
                    $picture_values = $pEstates->getEstatePictureValues($id);
                    if ($first_picture === true) {
                        if ($picture_values['title'] == true) {
                            $image_alt = esc_html($picture_values['title']);
                        } else {
                            $image_alt = esc_html__(
                                'Immobilienbild',
                                'oo_theme',
                            );
                        }
                        $image = [
                            'url' => $pEstates->getEstatePictureUrl($id),
                            'alt' => $image_alt,
                        ];

                        oo_get_template('components', '', 'component-image', [
                            'image' => $image,
                            'picture_class' =>
                                'c-property-details__banner-picture o-picture',
                            'image_class' =>
                                'c-property-details__banner-image o-image',
                            'loading' => 'eager',
                            'decoding' => 'auto',
                            'dimensions' => [
                                '575' => [
                                    'w' => '400',
                                    'h' => '400',
                                ],
                                '1600' => [
                                    'w' => '1920',
                                    'h' => round((1920 * 9) / 16),
                                ],
                                '1400' => [
                                    'w' => '1600',
                                    'h' => round((1600 * 9) / 16),
                                ],
                                '1200' => [
                                    'w' => '1400',
                                    'h' => round((1400 * 9) / 16),
                                ],
                                '992' => [
                                    'w' => '1200',
                                    'h' => round((1200 * 9) / 16),
                                ],
                                '768' => [
                                    'w' => '992',
                                    'h' => '992',
                                ],
                                '576' => [
                                    'w' => '768',
                                    'h' => '768',
                                ],
                            ],
                        ]);
                    }

                    $first_picture = false;
                }
            } else {
                echo '<div class="c-property-details__banner-picture --is-placeholder"></div>';
            } ?>
            <div class="c-property-details__banner-wrapper o-container">
                <div class="c-property-details__banner-content o-col-12 o-col-lg-10 o-col-xl-8">

                    <?php if (
                        $property_status ||
                        (Favorites::isFavorizationEnabled() &&
                            !$is_reference &&
                            !$iframe_display)
                    ) { ?>
                        <div class="c-property-details__banner-flags c-flags <?= !$property_status &&
                        Favorites::isFavorizationEnabled()
                            ? '--only-favorite'
                            : '' ?>">
                        <?php } ?>

                        <?php if ($property_status) { ?>
                            <span class="c-property-details__status c-flag --property-status">
                                <?php echo ucfirst($property_status); ?>
                            </span>
                        <?php } ?>

                        <?php
                        if (
                            Favorites::isFavorizationEnabled() &&
                            !$is_reference &&
                            !$iframe_display
                        ) {

                            $favorite_label = Favorites::getFavorizationLabel();
                            if ($favorite_label == 'Watchlist') {
                                $favorite_text = esc_html__(
                                    'Zur Merkliste hinzufügen',
                                    'oo_theme',
                                );
                                $favorite_icon = 'bookmark';
                            } else {
                                $favorite_text = esc_html__(
                                    'Zu Favoriten hinzufügen',
                                    'oo_theme',
                                );
                                $favorite_icon = 'star';
                            }
                            ?>
                            <button class="c-property-details__favorite c-icon-button" data-onoffice-property-id="<?php echo $property_id; ?>" aria-label="<?php echo $favorite_text; ?>">
                                <?php oo_get_icon($favorite_icon, true, [
                                    'class' => 'c-icon-button__icon --favorite',
                                ]); ?>
                            </button>
                        <?php
                        }

                        if (
                            $property_status ||
                            (Favorites::isFavorizationEnabled() &&
                                !$is_reference &&
                                !$iframe_display)
                        ) { ?>
                        </div>
                    <?php }

                        if ($current_property['objekttitel']) { ?>
                        <h1 class="c-property-details__title o-headline --h2">
                            <?php echo $current_property['objekttitel']; ?>
                        </h1>
                    <?php }
                        ?>
                    <?php if ($price_fields_available) { ?>
                        <?php foreach ($price_fields as $price_field) {

                            $price_value = $current_property[$price_field];
                            if (
                                (is_numeric($price_value) &&
                                    0 == $price_value) ||
                                $price_value == '0000-00-00' ||
                                $price_value == '0.00' ||
                                $price_value == '' ||
                                empty($price_value)
                            ) {
                                continue;
                            }
                            ?>
                            <p class="c-property-details__price">
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
                            </p>
                        <?php
                        } ?>
                    <?php } ?>

                </div>
            </div>
        </div>

        <div class="c-property-details__container o-container">
            <?php if ($photos) {

                // Load Lightbox
                wp_enqueue_script('oo-glightbox-script');
                wp_enqueue_style('oo-glightbox-style');
                ?>

                <div class="c-property-details__gallery">

                    <?php
                    $i = 1;

                    foreach ($sorted_pictures as $id) {

                        $picture_values = $pEstates->getEstatePictureValues(
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
                            'url' => $pEstates->getEstatePictureUrl($id),
                            'alt' => $image_alt,
                        ];

                        //  Lightbox Cloud Image
                        $lightbox_image_options =
                            '?force_format=webp&org_if_sml=1';
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
                            foreach (
                                $lightbox_image_size_list
                                as $key => $size
                            ) {
                                $is_first =
                                    $key ==
                                    array_key_first($lightbox_image_size_list);
                                $is_last =
                                    $key ==
                                    array_key_last($lightbox_image_size_list);
                                $separator = !$is_last ? ',' : '';
                                $is_last_image_size = $is_last
                                    ? ',' .
                                        end($lightbox_image_size_list)[
                                            'image_size'
                                        ] .
                                        'w'
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
                        <a class="c-property-details__gallery-link glightbox" data-gallery="gallery"
                            href="<?php echo $lightbox_url .
                                '&w=' .
                                end($lightbox_image_size_list)[
                                    'image_size'
                                ]; ?>"
                            data-sizes="<?php echo $lightbox_image_breakpoints; ?>"
                            data-srcset="<?php echo $lightbox_image_sizes; ?>"
                            data-caption="<?php echo $image['alt']; ?>"
                            title="<?php echo $image['alt']; ?>"
                            aria-label="<?php echo sprintf(
                                esc_attr_x(
                                    'Bild %s vergrößert anzeigen',
                                    'oo_theme',
                                ),
                                $image['alt'],
                            ); ?>">
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $image,
                                    'picture_class' =>
                                        'c-property-details__gallery-picture o-picture',
                                    'image_class' =>
                                        'c-property-details__gallery-image o-image',
                                    'dimensions' => [
                                        '575' => [
                                            'h' => round((575 * 2) / 3),
                                        ],
                                        '1600' => [
                                            'h' => round((369 * 2) / 3),
                                        ],
                                        '1400' => [
                                            'h' => round((336 * 2) / 3),
                                        ],
                                        '1200' => [
                                            'h' => round((288 * 2) / 3),
                                        ],
                                        '992' => [
                                            'h' => round((480 * 2) / 3),
                                        ],
                                        '768' => [
                                            'h' => round((726 * 2) / 3),
                                        ],
                                        '576' => [
                                            'h' => round((544 * 2) / 3),
                                        ],
                                    ],
                                ],
                            ); ?>
                            <span class="c-property-details__open-lightbox">
                                <?php oo_get_icon('plus', true, [
                                    'class' =>
                                        'c-property-details__open-lightbox-icon',
                                ]); ?>
                            </span>
                        </a>
                    <?php $i++;
                    }
                    ?>
                </div>
            <?php
            } ?>

            <div class="c-property-details__buttons-wrapper c-buttons">
                <?php
                if (!empty($shortcode_form)) { ?>
                    <a href="#request" class="c-property-details__request c-button"><?php esc_html_e(
                        'Sofortanfrage',
                        'oo_theme',
                    ); ?></a>
                <?php }
                if ($pEstates->getDocument() != '') { ?>
                    <a class="c-property-details__expose-button c-button --has-icon --ghost" href="<?php echo $pEstates->getDocument(); ?>">
                        <span class="c-button__text">
                            <?php esc_html_e('Details als PDF', 'oo_theme'); ?>
                        </span>
                        <span class="c-button__icon"><?php oo_get_icon(
                            'download',
                        ); ?></span>
                    </a>
                <?php }
                ?>

                <div class="c-property-details__share">
                    <?php
                    global $wp;

                    $property_detail_page =
                        get_field('general', 'option')['property_detail'] ?? [];
                    $property_share_button =
                        filter_var(
                            $property_detail_page['property_share_button'],
                            FILTER_VALIDATE_BOOLEAN,
                        ) ?? false;

                    if ($property_share_button) {
                        oo_get_template('components', '', 'component-share', [
                            'button_class' =>
                                'c-property-details__share-button c-button --ghost',
                            'button_icon' => 'share',
                            'popup_id' => 'property-detail-share',
                            'share_link' => home_url(
                                add_query_arg([], $wp->request),
                            ),
                        ]);
                    }
                    ?>
                </div>
            </div>

            <?php $filtered_features = array_filter(
                $property_features,
                function ($category) {
                    return !empty($category);
                },
            ); ?>
            
            <div class="c-property-details__fields-row o-row">
                <?php foreach (
                    $filtered_features
                    as $group_name => $features
                ): ?>
                    <?php
                    $boolean_features = [];
                    $regular_features = [];

                    foreach ($features as $feature) {
                        if ($feature['type'] === 'boolean') {
                            $boolean_features[] = $feature;
                        } else {
                            $regular_features[] = $feature;
                        }
                    }

                    $true_boolean_features = array_filter(
                        $boolean_features,
                        function ($feature) {
                            return $feature['value'] === 'Ja';
                        },
                    );

                    if (
                        empty($true_boolean_features) &&
                        empty($regular_features)
                    ) {
                        continue;
                    }
                    ?>

                    <div class="c-property-details__fields-group o-col-12 o-col-lg-6">
                        <h2 class="c-property-details__headline o-headline --h2">
                            <?php echo esc_html($group_name); ?>
                        </h2>

                        <?php if (!empty($true_boolean_features)): ?>
                            <div class="c-property-details__features c-item-features">
                                <?php foreach (
                                    $true_boolean_features
                                    as $feature
                                ): ?>
                                    <span class="c-item-features__item"><?php echo esc_html(
                                        $feature['label'],
                                    ); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($regular_features)): ?>
                            <div class="c-property-details__fields c-item-fields">
                                <?php foreach (
                                    $regular_features
                                    as $feature
                                ): ?>
                                    <?php if (
                                        ($raw_values->getValueRaw($property_id)[
                                            'elements'
                                        ]['provisionsfrei'] ??
                                            null) ===
                                            '1' &&
                                        in_array(
                                            $field,
                                            [
                                                'innen_courtage',
                                                'aussen_courtage',
                                            ],
                                            true,
                                        )
                                    ) {
                                        continue;
                                    } ?>
                                    <dl class="c-item-fields__item">
                                        <dt class="c-item-fields__label"><?php echo esc_html(
                                            $feature['label'],
                                        ); ?></dt>
                                        <dd class="c-item-fields__value">
                                            <?php echo is_array(
                                                $feature['value'],
                                            )
                                                ? esc_html(
                                                    implode(
                                                        ', ',
                                                        $feature['value'],
                                                    ),
                                                )
                                                : esc_html(
                                                    $feature['value'],
                                                ); ?>
                                        </dd>
                                    </dl>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="c-property-details__row o-row">
                <div class="c-property-details__main o-col-12 o-col-lg-8">
                    <?php // Ogulo

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

                            foreach (
                                $property_ogulo_embeds
                                as $property_ogulo_embed
                            ) {
                                echo '<div class="c-property-details__iframe --is-' .
                                    oo_get_service_domain_without_tld(
                                        $property_ogulo_embed['url'],
                                    ) .
                                    '">';
                                echo $property_ogulo_embed['player'];
                                echo '</div>';
                            }

                            echo '</div>';
                        }

                        if (
                            !empty($property_ogulo_links) &&
                            is_array($property_ogulo_links)
                        ) {
                            echo '<div class="c-property-details__buttons c-buttons">';
                            foreach (
                                $property_ogulo_links
                                as $property_ogulo_link
                            ) {
                                // Button Text
                                $button_title = !empty(
                                    $property_ogulo_link['title']
                                )
                                    ? esc_attr($property_ogulo_link['title'])
                                    : esc_attr__(
                                        '360°-Rundgang starten',
                                        'oo_theme',
                                    );

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
                    } ?>


                    <?php
                    // Movie
                    if (
                        !empty($property_movie_players) ||
                        !empty($property_movie_links)
                    ) {
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

                            foreach (
                                $property_movie_players
                                as $property_movie_player
                            ) {
                                echo '<div class="c-property-details__video --is-' .
                                    oo_get_service_domain_without_tld(
                                        $property_movie_player['url'],
                                    ) .
                                    '">';
                                if (
                                    isset($property_movie_player['player']) &&
                                    strpos(
                                        $property_movie_player['player'],
                                        '<a',
                                    ) !== false
                                ) {
                                    echo '<iframe class="c-property-details__iframe" src="' .
                                        $property_movie_player['url'] .
                                        '" title="' .
                                        esc_attr__(
                                            'Externer Video Inhalt',
                                            'oo_theme',
                                        ) .
                                        '"></iframe>';
                                } else {
                                    echo $property_movie_player['player'];
                                }
                                echo '</div>';
                            }

                            echo '</div>';
                        }

                        // GET MOVIE LINKS
                        if (
                            !empty($property_movie_links) &&
                            is_array($property_movie_links)
                        ) {
                            echo '<div class="c-property-details__buttons c-buttons">';
                            foreach (
                                $property_movie_links
                                as $property_movie_link
                            ) {
                                // Button Text
                                $button_title = !empty(
                                    $property_movie_link['title']
                                )
                                    ? esc_attr($property_movie_link['title'])
                                    : esc_html__('Video starten', 'oo_theme');

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

                    // Links
                    if (
                        !empty($property_links) ||
                        !empty($property_link_embeds)
                    ) {
                        echo '<div class="c-property-details__media">';
                        echo '<h2 class="c-property-details__headline o-headline --h2">' .
                            esc_html__('Links', 'oo_theme') .
                            '</h2>';

                        if (
                            !empty($property_link_embeds) &&
                            is_array($property_link_embeds)
                        ) {
                            echo '<div class="c-property-details__embeds">';

                            foreach (
                                $property_link_embeds
                                as $property_link_embed
                            ) {
                                echo '<div class="c-property-details__iframe --is-' .
                                    oo_get_service_domain_without_tld(
                                        $property_link_embed['url'],
                                    ) .
                                    '">';
                                echo $property_link_embed['player'];
                                echo '</div>';
                            }

                            echo '</div>';
                        }

                        if (
                            !empty($property_links) &&
                            is_array($property_links)
                        ) {
                            echo '<div class="c-property-details__buttons c-buttons">';
                            foreach ($property_links as $property_link) {
                                // Button Text
                                $button_title = !empty($property_link['title'])
                                    ? esc_attr($property_link['title'])
                                    : esc_attr__('Link öffnen', 'oo_theme');

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

                    <?php
                    // Objects
                    if (
                        !empty($property_object_embeds) ||
                        !empty($property_object_links)
                    ) {
                        echo '<div class="c-property-details__media">';
                        echo '<h2 class="c-property-details__headline o-headline --h2">' .
                            esc_html__('Objekte', 'oo_theme') .
                            '</h2>';

                        if (
                            !empty($property_object_embeds) &&
                            is_array($property_object_embeds)
                        ) {
                            echo '<div class="c-property-details__embeds">';

                            foreach (
                                $property_object_embeds
                                as $property_object_embed
                            ) {
                                echo '<div class="c-property-details__iframe --is-' .
                                    oo_get_service_domain_without_tld(
                                        $property_object_embed['url'],
                                    ) .
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
                            foreach (
                                $property_object_links
                                as $property_object_link
                            ) {
                                // Button Text
                                $button_title = !empty(
                                    $property_object_link['title']
                                )
                                    ? esc_attr($property_object_link['title'])
                                    : esc_attr__(
                                        'Objekt-Link öffnen',
                                        'oo_theme',
                                    );

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

                    if (!empty($pEstates->getTotalCostsData())) {
                        $totalCostsData = $pEstates->getTotalCostsData(); ?>
                        <div class="c-property-details__text-wrapper">
                            <h2 class="c-property-details__headline o-headline --h2">
                                <?php echo __(
                                    'Gesamtpreiskalkulator',
                                    'oo_theme',
                                ); ?>
                            </h2>
                            <div class="c-property-details__calculator --is-price-calculator">
                                <div class="c-price-calculator">
                                <div class="c-price-calculator__chart">
                                        <?php
                                        $values = [
                                            $totalCostsData['kaufpreis']['raw'],
                                            $totalCostsData['bundesland'][
                                                'raw'
                                            ],
                                            $totalCostsData['aussen_courtage'][
                                                'raw'
                                            ],
                                            $totalCostsData['notary_fees'][
                                                'raw'
                                            ],
                                            $totalCostsData[
                                                'land_register_entry'
                                            ]['raw'],
                                        ];
                                        $valuesTitle = [
                                            $totalCostsData['kaufpreis'][
                                                'default'
                                            ],
                                            $totalCostsData['bundesland'][
                                                'default'
                                            ],
                                            $totalCostsData['aussen_courtage'][
                                                'default'
                                            ],
                                            $totalCostsData['notary_fees'][
                                                'default'
                                            ],
                                            $totalCostsData[
                                                'land_register_entry'
                                            ]['default'],
                                        ];
                                        $chart = new EstateCostsChart(
                                            $values,
                                            $valuesTitle,
                                        );
                                        echo $chart->generateSVG();
                                        ?>
                                    </div>
                                    <div class="c-price-calculator__overview">
                                        <div class="c-price-calculator__item">
                                            <span class="c-price-calculator__color-indicator oo-donut-chart-color0"></span>
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label">
                                                    <?php echo esc_html_e(
                                                        $pEstates->getFieldLabel(
                                                            'kaufpreis',
                                                        ),
                                                    ); ?>
                                                    </dt>
                                                <dd class="c-price-calculator__value">
                                                    <?php echo esc_html(
                                                        $totalCostsData[
                                                            'kaufpreis'
                                                        ]['default'],
                                                    ); ?>
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__item">
                                            <span class="c-price-calculator__color-indicator oo-donut-chart-color1"></span>
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label"><?php esc_html_e(
                                                    'Grunderwerbsteuer',
                                                    'oo_theme',
                                                ); ?></dt>
                                                <dd class="c-price-calculator__value"><?php echo esc_html(
                                                    $totalCostsData[
                                                        'bundesland'
                                                    ]['default'],
                                                ); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__item">
                                            <span class="c-price-calculator__color-indicator oo-donut-chart-color2"></span>
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label"><?php esc_html_e(
                                                    'Maklerprovision',
                                                    'oo_theme',
                                                ); ?></dt>
                                                <dd class="c-price-calculator__value"><?php echo esc_html(
                                                    $totalCostsData[
                                                        'aussen_courtage'
                                                    ]['default'],
                                                ); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__item">
                                            <span class="c-price-calculator__color-indicator oo-donut-chart-color3"></span>
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label"><?php esc_html_e(
                                                    'Notargebühren *',
                                                    'oo_theme',
                                                ); ?></dt>
                                                <dd class="c-price-calculator__value"><?php echo esc_html(
                                                    $totalCostsData[
                                                        'notary_fees'
                                                    ]['default'],
                                                ); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__item">
                                            <span class="c-price-calculator__color-indicator oo-donut-chart-color4"></span>
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label"><?php esc_html_e(
                                                    'Grundbucheintrag *',
                                                    'oo_theme',
                                                ); ?></dt>
                                                <dd class="c-price-calculator__value"><?php echo esc_html(
                                                    $totalCostsData[
                                                        'land_register_entry'
                                                    ]['default'],
                                                ); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__item --is-total-cost">
                                            <dl class="c-price-calculator__criteria">
                                                <dt class="c-price-calculator__label oo-total-costs-label"><?php esc_html_e(
                                                    'Gesamtkosten',
                                                    'oo_theme',
                                                ); ?></dt>
                                                <dd class="c-price-calculator__value"><?php echo esc_html(
                                                    $totalCostsData[
                                                        'total_costs'
                                                    ]['default'],
                                                ); ?></dd>
                                            </dl>
                                        </div>
                                        <div class="c-price-calculator__notice">
                                            <?php echo esc_html_e(
                                                '* Für die Berechnung der Notar- und Grundbuchkosten wird ein Standardwert von 1,5% bzw. 0,5% verwendet.',
                                                'oo_theme',
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }

                    if (!empty($property_free_texts)) {
                        foreach ($property_free_texts as $field) {
                            if ($field['has_value']) {

                                $content = $field['value'];
                                $field_toggle_id =
                                    'more-property-features' .
                                    '-' .
                                    $field['field'];
                                ?>
                                <div class="c-property-details__text-wrapper">
                                    <h2 class="c-property-details__headline o-headline --h2">
                                        <?php esc_html_e(
                                            $field['label'],
                                            'oo_theme',
                                        ); ?>
                                    </h2>
                                    <div class="c-property-details__text">
                                        <div class="c-property-details__text-content --shorten" id="<?php echo esc_attr(
                                            $field_toggle_id,
                                        ); ?>">
                                            <?php echo nl2br(
                                                esc_html($content),
                                            ); ?>
                                        </div>
                                        <button class="c-property-details__more c-read-more"
                                                data-open-text="<?php esc_html_e(
                                                    'Mehr anzeigen',
                                                    'oo_theme',
                                                ); ?>"
                                                data-close-text="<?php esc_html_e(
                                                    'Weniger anzeigen',
                                                    'oo_theme',
                                                ); ?>"
                                                aria-expanded="false" aria-controls="<?php echo $field_toggle_id; ?>">
                                            <?php echo esc_html_e(
                                                'Mehr anzeigen',
                                                'oo_theme',
                                            ); ?>
                                        </button>
                                        <?php if (
                                            $field['field'] === 'lage' &&
                                            !empty($map)
                                        ): ?>
                                            <div class="c-property-details__map">
                                                <?php echo $map; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                    }
                    ?>
                    <?php if (!empty($area_butler_url)) { ?>
                        <div class="c-property-details__text-wrapper">
                            <h2 class="c-property-details__headline o-headline --h2">
                                <?php echo __('Infrastruktur', 'oo_theme'); ?>
                            </h2>
                            <span class="c-property-details__iframe --is-areabutler"><iframe src="
                                <?php echo esc_html(
                                    $area_butler_url['value'],
                                ); ?>
                                " class="--is-areabutler" data-usercentrics="AreaButler"
                                 title="<?php echo sprintf(
                                     esc_attr__(
                                         'Externer Inhalt von %s',
                                         'oo_theme',
                                     ),
                                     'AreaButler',
                                 ); ?>"></iframe></span>
                            <?php if (!empty($infrastructure_info)) { ?>
                                <div class="c-property-details__text">
                                <div class="c-item-fields">
                                <?php foreach (
                                    $infrastructure_info
                                    as $info
                                ) { ?>
                                    <dl class="c-item-fields__item">
                                        <dt class="c-item-fields__label">
                                            <?php echo $info['label']; ?>
                                        </dt>
                                        <dd class="c-item-fields__value">
                                            <?php echo $info['value']; ?>
                                        </dd>
                                    </dl>
                                <?php } ?>
                                </div></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($energy_fields_available) {

                        $energy_class =
                            $raw_values->getValueRaw($property_id)['elements'][
                                'energyClass'
                            ] ?? '';
                        $energy_class_permitted_values = $pEstates->getPermittedValues(
                            'energyClass',
                        );
                        $energy_certificate_type =
                            $raw_values->getValueRaw($property_id)['elements'][
                                'energieausweistyp'
                            ] ?? '';
                        $energy_certificate_values = oo_get_energy_certificate_values();
                        $energy_certificate_value_range =
                            $energy_certificate_values['value_ranges'];
                        ?>
                        <div class="c-property-details__energy">
                            <h2 class="c-property-details__headline o-headline --h2"><?php echo esc_html(
                                $pEstates->getFieldLabel('energieausweistyp'),
                            ); ?></h2>
                            <?php
                            function renderEnergyCertificate(
                                string $energy_certificate_type,
                                array $energy_class_permitted_values,
                                string $selectedEnergyClass,
                                string $type,
                                array $labels,
                            ) {
                                if ($energy_certificate_type === $type) { ?>
                                    <div class="c-property-details__energy-certificate c-energy-certificate">
                                        <?php
                                        $index = 0;
                                        foreach (
                                            $energy_class_permitted_values
                                            as $key => $label
                                        ) {
                                            $labelIndex = array_keys(
                                                $energy_class_permitted_values,
                                            )[$key]; ?>
                                            <div class="c-energy-certificate__class --<?php echo strtolower(
                                                $label,
                                            ); ?> <?php echo $selectedEnergyClass ==
 $label
     ? ' --is-active'
     : ''; ?>">
                                                <div class="c-energy-certificate__label"><?php echo $label; ?></div>
                                                <div class="c-energy-certificate__value">
                                                    <?php
                                                    echo $labels[$labelIndex];
                                                    if (
                                                        array_key_exists(
                                                            $labelIndex + 1,
                                                            $labels,
                                                        )
                                                    ) {
                                                        echo ' - ' .
                                                            $labels[
                                                                $labelIndex + 1
                                                            ];
                                                    } else {
                                                        echo '+';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php $index++;
                                        }
                                        ?>
                                    </div>
                            <?php }
                            }

                            if (
                                !empty($energy_class_permitted_values) &&
                                !empty($energy_class) &&
                                !empty($energy_certificate_type) &&
                                $pEstates->getShowEnergyCertificate()
                            ) {
                                foreach (
                                    $energy_certificate_value_range
                                    as $type => $labels
                                ) {
                                    renderEnergyCertificate(
                                        $energy_certificate_type,
                                        $energy_class_permitted_values,
                                        $energy_class,
                                        $type,
                                        $labels,
                                    );
                                }
                            }
                            ?>
                            <div class="c-item-fields">
                                <?php
                                if (
                                    $energy_certificate_type ===
                                    'Endenergiebedarf'
                                ) {
                                    if (
                                        !in_array(
                                            'endenergiebedarf',
                                            $energy_fields,
                                        )
                                    ) {
                                        $energy_fields[] = 'endenergiebedarf';
                                    }
                                } elseif (
                                    $energy_certificate_type ===
                                    'Energieverbrauchskennwert'
                                ) {
                                    if (
                                        !in_array(
                                            'energieverbrauchskennwert',
                                            $energy_fields,
                                        )
                                    ) {
                                        $energy_fields[] =
                                            'energieverbrauchskennwert';
                                    }
                                }

                                foreach ($energy_fields as $field) {
                                    if (isset($current_property[$field])) {
                                        $value = $current_property[$field];
                                        if (
                                            (is_numeric($value) &&
                                                0 == $value) ||
                                            $value == '0000-00-00' ||
                                            $value == '0.00' ||
                                            (is_string($value) &&
                                                $value !== '' &&
                                                !is_numeric($value) &&
                                                ($raw_values->getValueRaw(
                                                    $property_id,
                                                )['elements'][$field] ??
                                                    null) ===
                                                    '0') || // skip negative boolean fields
                                            $value == '' ||
                                            empty($value)
                                        ) {
                                            continue;
                                        }

                                        echo '<dl class="c-item-fields__item">';

                                        echo '<dt class="c-item-fields__label">' .
                                            esc_html(
                                                $pEstates->getFieldLabel(
                                                    $field,
                                                ),
                                            ) .
                                            '</dt>' .
                                            "\n" .
                                            '<dd class="c-item-fields__value">' .
                                            (is_array($value)
                                                ? esc_html(
                                                    implode(', ', $value),
                                                )
                                                : esc_html($value)) .
                                            '</dd>' .
                                            "\n";
                                        echo '</dl>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    } ?>



                    <?php if (!empty($pEstates->getEstateUnits())) { ?>
                        <div class="c-property-details__units">
                            <?php echo $pEstates->getEstateUnits(); ?>
                        </div>
                    <?php } ?>


                    <?php if (!empty($shortcode_form)) { ?>
                        <div id="request" class="c-property-details__form">
                            <?php echo do_shortcode($shortcode_form); ?>
                        </div>
                    <?php } ?>

                </div>

                <div class="c-property-details__aside o-col-12 o-col-lg-4">

                    <?php require_once 'property-contact.php'; ?>

                    <?php echo $pEstates->getSimilarEstates(); ?>

                </div>
            </div>
        </div>
    </section>
<?php
}

if (Favorites::isFavorizationEnabled()) { ?>
    <?php wp_enqueue_script('oo-favorites-script'); ?>

    <script>
        jQuery(document).ready(function($) {
            onofficeFavorites = new onOffice.favorites(<?php echo json_encode(
                Favorites::COOKIE_NAME,
            ); ?>);
            onOffice.addFavoriteButtonLabel = function(i, element) {
                var favorite = $(element);
                var propertyId = favorite.attr('data-onoffice-property-id');
                var favoriteIcon = favorite.find('.--favorite');
                var favoriteClass = '--filled';
                if (!onofficeFavorites.favoriteExists(propertyId)) {
                    favorite.attr('aria-label', '<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e('Zur Merkliste hinzufügen', 'oo_theme');
                    } else {
                        esc_html_e('Zu Favoriten hinzufügen', 'oo_theme');
                    } ?>');
                    favoriteIcon.removeClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.add(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                } else {
                    favorite.attr('aria-label', '<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e('Von Merkliste entfernen', 'oo_theme');
                    } else {
                        esc_html_e('Von Favoriten entfernen', 'oo_theme');
                    } ?>');
                    favoriteIcon.addClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.remove(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                }
            };
            $('.c-property-details__favorite').each(onOffice.addFavoriteButtonLabel);
        });
    </script>
<?php } ?>
