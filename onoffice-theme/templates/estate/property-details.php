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
    'energieeffizienzklasse',
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

$price_fields = get_price_fields();

$iframe_display = filter_var(
    get_field('iframe_display', 'option')['display_as_iframe'] ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

$property_detail_opts = get_field('general', 'option')['property_detail'] ?? [];
$is_share_available = isset($property_detail_opts['property_share_button'])
    ? filter_var(
        $property_detail_opts['property_share_button'],
        FILTER_VALIDATE_BOOLEAN,
    )
    : true;

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
    $floorplans = false;
    $title_image = null;
    $sorted_photos = [];
    $sorted_floors = [];

    // pictures
    $property_pictures = $pEstates->getEstatePictures();
    foreach ($property_pictures as $id) {
        $picture_values = $pEstates->getEstatePictureValues($id);

        if ($picture_values['type'] === 'Grundriss') {
            $floorplans = true;
            $sorted_floors[] = $id;
            continue;
        }

        $photos = true;
        if (
            $picture_values['type'] ===
            \onOffice\WPlugin\Types\ImageTypes::TITLE
        ) {
            $title_image = $id;
        } else {
            $sorted_photos[] = $id;
        }
    }

    if ($title_image) {
        array_unshift($sorted_photos, $title_image);
    }

    $recently_viewed_elements =
        $raw_values->getValueRaw($property_id)['elements'] ?? [];
    $recently_viewed_picture_id = $sorted_photos[0] ?? null;
    $recently_viewed_snapshot = [
        'estate_id' => (int) $property_id,
        'mainLangId' => (int) $property_id,
        'viewed_at' => gmdate('c'),
        'titelbild_url' => $recently_viewed_picture_id
            ? $pEstates->getEstatePictureUrl($recently_viewed_picture_id)
            : null,
        'objekttitel' => $current_property['objekttitel'] ?? null,
        'objektart' => $recently_viewed_elements['objektart'] ?? null,
        '_objektart' => $recently_viewed_elements['_objektart'] ?? null,
        'objekttyp' => $recently_viewed_elements['objekttyp'] ?? null,
        '_objekttyp' => $recently_viewed_elements['_objekttyp'] ?? null,
        'vermarktungsart' =>
            $recently_viewed_elements['vermarktungsart'] ?? null,
        '_vermarktungsart' =>
            $recently_viewed_elements['_vermarktungsart'] ?? null,
        'plz' => $current_property['plz'] ?? null,
        'ort' => $current_property['ort'] ?? null,
        'kaufpreis' => isset($current_property['kaufpreis'])
            ? (string) $current_property['kaufpreis']
            : null,
        'kaltmiete' => isset($current_property['kaltmiete'])
            ? (string) $current_property['kaltmiete']
            : null,
        'warmmiete' => isset($current_property['warmmiete'])
            ? (string) $current_property['warmmiete']
            : null,
        'preisAufAnfrage' => filter_var(
            $recently_viewed_elements['preisAufAnfrage'] ?? false,
            FILTER_VALIDATE_BOOLEAN,
        ),
        'wohnflaeche' => isset($current_property['wohnflaeche'])
            ? (string) $current_property['wohnflaeche']
            : null,
    ];

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
    $is_secret_sale = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['secret_sale'] ??
            null,
        FILTER_VALIDATE_BOOLEAN,
    );

    $show_secret_sale_block = oo_should_show_secret_sale_placeholder(
        $is_secret_sale,
    );

    // link
    $property_link = esc_url($pEstates->getEstateLink());

    // form
    $shortcode_form = $pEstates->getShortCodeForm();

    // fields
    $property_features = [];
    $property_free_texts = [];
    $energy_fields_ordered = [];
    $energy_fields_available = false;
    $price_fields_available = false;

    $is_address_shared = !empty(
        $raw_values->getValueRaw($property_id)['elements']['strasse'] ?? ''
    );

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

        if (
            !$is_address_shared &&
            in_array($field, ['plz', 'hausnummer', 'strasse'], true)
        ) {
            continue;
        }

        if (in_array($field, $energy_fields)) {
            $energy_fields_available = true;
            $energy_fields_ordered[] = $field;
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

    // energy certificate
    $energy_certificate_expiry_date =
        $current_property['energieausweis_gueltig_bis'] ?? '';
    ?>

    <section class="c-property-details o-section --bg-transparent<?php echo $show_secret_sale_block
        ? '--blurry'
        : ''; ?>">

        <?php if ($iframe_display) { ?>
            <a class="c-button c-property-details__back-button" href="javascript:history.back();"><?php esc_html_e(
                'Zurück',
                'oo_theme',
            ); ?></a>
        <?php } ?>

        <div class="c-property-details__header-wrapper">
            <div class="c-property-details__header o-container">
                <div class="c-property-details__header-row o-row">
                    <div class="c-property-details__header-content u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php if ($show_secret_sale_block): ?>
                            <?php
                            // --- SECRET SALE: PLACEHOLDER TITLE & PRICE ---
                            ?>
                            <h1 class="c-property-details__title o-headline --h1">
                                <?php esc_html_e(
                                    'Exklusives Objekt',
                                    'oo_theme',
                                ); ?>
                            </h1>
                            <p class="c-property-details__price o-headline --h3">
                                <?php esc_html_e(
                                    'Kaufpreis',
                                    'oo_theme',
                                ); ?>: xxx.xxx
                            </p>
                        <?php else: ?>
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
                                    $favorite_icon = 'heart';
                                } else {
                                    $favorite_text = esc_html__(
                                        'Zu Favoriten hinzufügen',
                                        'oo_theme',
                                    );
                                    $favorite_icon =
                                        $favorite_label == 'Watchlist'
                                            ? 'heart'
                                            : 'heart';
                                }
                                ?>
                                <button class="c-property-details__favorite c-icon-button" data-onoffice-property-id="<?php echo $property_id; ?>" aria-label="<?php echo $favorite_text; ?>">
                                    <?php oo_get_icon($favorite_icon, true, [
                                        'class' =>
                                            'c-icon-button__icon --favorite',
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
                            <h1 class="c-property-details__title o-headline --h1">
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
                                <p class="c-property-details__price o-headline --h3">
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        ob_start();

        if ($photos && !$show_secret_sale_block) {

            // Load Lightbox
            wp_enqueue_script('oo-glightbox-script');
            wp_enqueue_style('oo-glightbox-style');
            ?>
            <div class="c-property-details__container o-container-fluid">
                <div 
                    class="c-property-details__gallery c-slider splide --auto-height --is-property-details-slider"
                    data-splide='{
                        "type":"loop",
                        "autoWidth":true,
                        "focus":"center",
                        "gap":16,
                        "arrows":true,
                        "snap":true,
                        "lazyLoad":false,
                        "pagination":true,
                        "updateOnMove":true,
                        "classes":{"page":"c-slider__page splide__pagination__page"},
                        "breakpoints": {
                            "992": { "gap": 0 }
                        }
                    }'
                >
                    <div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                            <?php foreach ($sorted_photos as $id) {

                                $picture_values = $pEstates->getEstatePictureValues(
                                    $id,
                                );

                                // Image alt text
                                $image_alt = $picture_values['title']
                                    ? esc_html($picture_values['title'])
                                    : esc_html__('Immobilienbild', 'oo_theme');

                                // Image width variants
                                $image_heights = [
                                    'xs' => 200,
                                    'sm' => 200,
                                    'md' => 400,
                                    'lg' => 400,
                                    'xl' => 640,
                                    'xxl' => 640,
                                    'xxxl' => 640,
                                ];

                                $image = [
                                    'url' => $pEstates->getEstatePictureUrl(
                                        $id,
                                    ),
                                    'alt' => $image_alt,
                                ];

                                // Lightbox Cloud Image
                                $lightbox_url =
                                    'https://acnaayzuen.cloudimg.io/v7/' .
                                    $image['url'] .
                                    '?force_format=webp&org_if_sml=1';

                                $lightbox_image_size_list = [
                                    [
                                        'id' => 'mobile',
                                        'breakpoint' => 767,
                                        'image_size' => 767,
                                    ],
                                    [
                                        'id' => 'tablet',
                                        'breakpoint' => 768,
                                        'image_size' => 1200,
                                    ],
                                    [
                                        'id' => 'desktop',
                                        'breakpoint' => 1200,
                                        'image_size' => 1920,
                                    ],
                                ];

                                // Responsive image helpers
                                $lightbox_image_breakpoints = '';
                                $lightbox_image_sizes = '';

                                foreach (
                                    $lightbox_image_size_list
                                    as $key => $size
                                ) {
                                    $is_first = $key === 0;
                                    $is_last =
                                        $key ===
                                        array_key_last(
                                            $lightbox_image_size_list,
                                        );
                                    $separator = $is_last ? '' : ',';

                                    if ($is_first) {
                                        $lightbox_image_breakpoints .= "(max-width: {$size['breakpoint']}px) {$size['image_size']}px,";
                                        $lightbox_image_sizes .= "{$lightbox_url}&w={$size['image_size']} {$size['breakpoint']}w,";
                                        continue;
                                    }

                                    $lightbox_image_breakpoints .= "(min-width:{$size['breakpoint']}px) {$size['image_size']}px{$separator}";
                                    $lightbox_image_sizes .= "{$lightbox_url}&w={$size['image_size']} {$size['breakpoint']}w{$separator}";
                                }
                                ?>
                                
                                <a class="c-property-details__gallery-link glightbox c-slider__slide splide__slide"
                                data-gallery="gallery"
                                href="<?php echo esc_url($lightbox_url) .
                                    '&w=' .
                                    end($lightbox_image_size_list)[
                                        'image_size'
                                    ]; ?>"
                                data-sizes="<?php echo esc_attr(
                                    $lightbox_image_breakpoints,
                                ); ?>"
                                data-srcset="<?php echo esc_attr(
                                    $lightbox_image_sizes,
                                ); ?>"
                                data-caption="<?php echo esc_attr(
                                    $image['alt'],
                                ); ?>"
                                title="<?php echo esc_attr($image['alt']); ?>"
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
                                            'loading' => 'eager',
                                            'picture_class' =>
                                                'c-property-details__gallery-picture o-picture',
                                            'image_class' =>
                                                'c-property-details__gallery-image o-image',
                                            'dimensions' => [
                                                '575' => [
                                                    'h' => $image_heights['xs'],
                                                ],
                                                '1600' => [
                                                    'h' =>
                                                        $image_heights['xxxl'],
                                                ],
                                                '1400' => [
                                                    'h' =>
                                                        $image_heights['xxl'],
                                                ],
                                                '1200' => [
                                                    'h' => $image_heights['xl'],
                                                ],
                                                '992' => [
                                                    'h' => $image_heights['lg'],
                                                ],
                                                '768' => [
                                                    'h' => $image_heights['md'],
                                                ],
                                                '576' => [
                                                    'h' => $image_heights['sm'],
                                                ],
                                            ],
                                        ],
                                    ); ?>
                                    <div class="c-slider__fullscreen c-icon-button splide__fullscreen">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Vergrößern',
                                            'oo_theme',
                                        ); ?></span>
                                        <?php echo oo_get_icon('resize', true, [
                                            'class' => 'c-icon-button__icon',
                                        ]); ?>
                                    </div>
                                </a>
                            <?php
                            } ?>
                        </div>
                    </div>
                    <div class="c-slider__slider-container o-container">
                        <div class="c-slider__navigation splide__navigation">
                            <div class="c-slider__arrows splide__arrows">
                                <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                                    <span class="u-screen-reader-only"><?php esc_html_e(
                                        'Vorheriges',
                                        'oo_theme',
                                    ); ?></span>
                                    <?php echo oo_get_icon(
                                        'chevron-left',
                                        true,
                                        [
                                            'class' =>
                                                'c-slider__icon splide__icon',
                                        ],
                                    ); ?>
                                </button>
                                <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                                    <span class="u-screen-reader-only"><?php esc_html_e(
                                        'Nächstes',
                                        'oo_theme',
                                    ); ?></span>
                                    <?php echo oo_get_icon(
                                        'chevron-right',
                                        true,
                                        [
                                            'class' =>
                                                'c-slider__icon splide__icon',
                                        ],
                                    ); ?>
                                </button>
                            </div>

                            <div class="c-slider__pagination-wrapper">
                                <ul class="c-slider__pagination splide__pagination"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        $main_gallery_content = ob_get_clean();

        if (!empty($main_gallery_content)) {
            echo '<div class="c-property-details__gallery-wrapper">';
            echo $main_gallery_content;
            echo '</div>';
        }
        ?>

        <div class="c-property-details__fields-wrapper">
            <div class="c-property-details__container o-container">

                <?php $filtered_features = array_filter(
                    $property_features,
                    function ($category) {
                        return !empty($category);
                    },
                ); ?>
                
                <div class="c-property-details__fields-row o-row">
                    <div class="c-property-details__fields-content u-offset-lg-1 o-col-12 o-col-lg-10">

                        <?php echo '<h2 class="c-property-details__headline o-headline --h2">' .
                            esc_html__('Immobiliendetails', 'oo_theme') .
                            '</h2>'; ?>
                        <div class="c-property-details__fields-main o-row">

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


                                <div class="c-property-details__fields-group o-col-md-6">
                                    <span class="c-property-details__headline">
                                        <?php echo esc_html($group_name); ?>
                                    </span>

                                    <?php if (
                                        !empty($true_boolean_features)
                                    ): ?>
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
                                                    ($raw_values->getValueRaw(
                                                        $property_id,
                                                    )['elements'][
                                                        'provisionsfrei'
                                                    ] ??
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
                                                    <dd class="c-item-fields__value">
                                                        <?php echo is_array(
                                                            $feature['value'],
                                                        )
                                                            ? esc_html(
                                                                implode(
                                                                    ', ',
                                                                    $feature[
                                                                        'value'
                                                                    ],
                                                                ),
                                                            )
                                                            : esc_html(
                                                                $feature[
                                                                    'value'
                                                                ],
                                                            ); ?>
                                                    </dd>
                                                    <dt class="c-item-fields__label"><?php echo esc_html(
                                                        $feature['label'],
                                                    ); ?></dt>
                                                </dl>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="c-property-details__buttons-wrapper">
            <div class="c-property-details__container o-container">
                <div class="c-property-details__row o-row">
                    <div class="c-property-details__main u-offset-lg-1 o-col-12 o-col-lg-10">

                        <div class="c-property-details__buttons-content c-buttons">
                            <?php
                            if (!empty($shortcode_form)) { ?>
                                <a href="#request" class="c-property-details__request c-button"><?php esc_html_e(
                                    'Sofortanfrage',
                                    'oo_theme',
                                ); ?></a>
                            <?php }
                            if ($pEstates->getDocument() != '') {
                                oo_get_template(
                                    'components',
                                    '',
                                    'component-expose-button',
                                    [
                                        'pEstates' => $pEstates,
                                        'property_id' => $property_id,
                                        'property_link' => $property_link,
                                    ],
                                );
                            }
                            ?>
                            <?php if ($is_share_available) { ?>
                            <div class="c-property-details__share">
                                <?php
                                global $wp;

                                $property_detail_page =
                                    get_field('general', 'option')[
                                        'property_detail'
                                    ] ?? [];
                                $property_share_button =
                                    filter_var(
                                        $property_detail_page[
                                            'property_share_button'
                                        ],
                                        FILTER_VALIDATE_BOOLEAN,
                                    ) ?? false;

                                if ($property_share_button) {
                                    oo_get_template(
                                        'components',
                                        '',
                                        'component-share',
                                        [
                                            'button_class' =>
                                                'c-property-details__share-button c-button --ghost',
                                            'button_icon' => 'share',
                                            'popup_id' =>
                                                'property-detail-share',
                                            'share_link' => $property_link,
                                        ],
                                    );
                                }
                                ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php ob_start(); ?>
        <div class="c-property-details__container o-container">
            <div class="c-property-details__row o-row">
                <?php require_once 'property-contact.php'; ?>
            </div>
        </div>
        <?php
        $contacts_content = ob_get_clean();
        if (!empty(trim(strip_tags($contacts_content)))) {
            echo '<div class="c-property-details__contacts-wrapper">';
            echo $contacts_content;
            echo '</div>';
        }
        ?>
    
        <?php if (!empty($pEstates->getEstateUnits())) { ?>
            <?php echo $pEstates->getEstateUnits(); ?>
        <?php } ?>

        <?php ob_start(); ?>
            <div class="c-property-details__container o-container">

            <?php // Ogulo

    if (!empty($property_ogulo_embeds) || !empty($property_ogulo_links)) {
                echo '<div class="c-property-details__row o-row">';
                echo '<div class="c-property-details__media u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">';
                echo '<h2 class="c-property-details__headline o-headline --h2">' .
                    esc_html__('360° Rundgänge', 'oo_theme') .
                    '</h2>';

                if (
                    !empty($property_ogulo_embeds) &&
                    is_array($property_ogulo_embeds)
                ) {
                    echo '<div class="c-property-details__embeds">';

                    foreach ($property_ogulo_embeds as $property_ogulo_embed) {
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
                    foreach ($property_ogulo_links as $property_ogulo_link) {
                        // Button Text
                        $button_title = !empty($property_ogulo_link['title'])
                            ? esc_attr($property_ogulo_link['title'])
                            : esc_attr__('360°-Rundgang starten', 'oo_theme');

                        echo '<a class="c-button" href="' .
                            esc_attr($property_ogulo_link['url']) .
                            '" target="_blank" rel="noopener noreferrer" aria-label="' .
                            esc_attr(
                                sprintf(
                                    __(
                                        '360°-Rundgang von %s starten (Öffnet in neuem Tab)',
                                        'oo_theme',
                                    ),
                                    $button_title,
                                ),
                            ) .
                            '">' .
                            $button_title .
                            '</a>';
                    }
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            } ?>


            <?php
            // Movie
            if (
                !empty($property_movie_players) ||
                !empty($property_movie_links)
            ) {
                echo '<div class="c-property-details__row o-row">';
                echo '<div class="c-property-details__media u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">';
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
                            strpos($property_movie_player['player'], '<a') !==
                                false
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
                    foreach ($property_movie_links as $property_movie_link) {
                        // Button Text
                        $button_title = !empty($property_movie_link['title'])
                            ? esc_attr($property_movie_link['title'])
                            : esc_html__('Video starten', 'oo_theme');

                        echo '<a class="c-button" href="' .
                            esc_attr($property_movie_link['url']) .
                            '" rel="noopener noreferrer" aria-label="' .
                            esc_attr(
                                sprintf(
                                    __(
                                        'Video von %s starten (Öffnet in neuem Tab)',
                                        'oo_theme',
                                    ),
                                    $button_title,
                                ),
                            ) .
                            '" target="_blank">' .
                            $button_title .
                            '</a>';
                    }
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            }

            // Links
            if (!empty($property_links) || !empty($property_link_embeds)) {
                echo '<div class="c-property-details__row o-row">';
                echo '<div class="c-property-details__media u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">';
                echo '<h2 class="c-property-details__headline o-headline --h2">' .
                    esc_html__('Links', 'oo_theme') .
                    '</h2>';

                if (
                    !empty($property_link_embeds) &&
                    is_array($property_link_embeds)
                ) {
                    echo '<div class="c-property-details__embeds">';

                    foreach ($property_link_embeds as $property_link_embed) {
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

                if (!empty($property_links) && is_array($property_links)) {
                    echo '<div class="c-property-details__buttons c-buttons">';
                    foreach ($property_links as $property_link) {
                        // Button Text
                        $button_title = !empty($property_link['title'])
                            ? esc_attr($property_link['title'])
                            : esc_attr__('Link öffnen', 'oo_theme');

                        echo '<a class="c-button" href="' .
                            esc_attr($property_link['url']) .
                            '" rel="noopener noreferrer" aria-label="' .
                            esc_attr(
                                sprintf(
                                    __(
                                        'Link von %s öffnen (Öffnet in neuem Tab)',
                                        'oo_theme',
                                    ),
                                    $button_title,
                                ),
                            ) .
                            '" target="_blank">' .
                            $button_title .
                            '</a>';
                    }
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            }
            ?>

            <?php // Objects

    if (!empty($property_object_embeds) || !empty($property_object_links)) {
                echo '<div class="c-property-details__row o-row">';
                echo '<div class="c-property-details__media u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">';
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
                    foreach ($property_object_links as $property_object_link) {
                        // Button Text
                        $button_title = !empty($property_object_link['title'])
                            ? esc_attr($property_object_link['title'])
                            : esc_attr__('Objekt-Link öffnen', 'oo_theme');

                        echo '<a class="c-button" href="' .
                            esc_attr($property_object_link['url']) .
                            '" rel="noopener noreferrer" aria-label="' .
                            esc_attr(
                                sprintf(
                                    __(
                                        'Objekt-Link von %s öffnen (Öffnet in neuem Tab)',
                                        'oo_theme',
                                    ),
                                    $button_title,
                                ),
                            ) .
                            '" target="_blank" title="' .
                            $button_title .
                            '">' .
                            $button_title .
                            '</a>';
                    }
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            } ?>
            </div>
        <?php
        $media_content = ob_get_clean();
        if (!empty(trim(strip_tags($media_content)))) {
            echo '<div class="c-property-details__media-wrapper">';
            echo $media_content;
            echo '</div>';
        }
        ?>
        
        <?php ob_start(); ?>
            <div class="c-property-details__texts o-container">
                <div class="c-property-details__texts-row o-row">
                <?php if (!empty($property_free_texts)) {
                    foreach ($property_free_texts as $field) {
                        if (
                            $field['field'] === 'objektbeschreibung' &&
                            $field['has_value']
                        ) {

                            $content = $field['value'];
                            $field_toggle_id =
                                'more-property-features-' . $field['field'];
                            ?>
                            <div class="c-property-details__text u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                                <h2 class="c-property-details__headline o-headline --h2">
                                    <?php esc_html_e(
                                        $field['label'],
                                        'oo_theme',
                                    ); ?>
                                </h2>
                                
                                <div class="c-property-details__text-content" id="<?php echo esc_attr(
                                    $field_toggle_id,
                                ); ?>">
                                    <?php echo nl2br(
                                        $show_secret_sale_block
                                            ? '...'
                                            : esc_html($content),
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
                                        aria-expanded="false" 
                                        aria-controls="<?php echo esc_attr(
                                            $field_toggle_id,
                                        ); ?>"
                                        style="display: none;"> <?php echo esc_html_e(
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
                        <?php
                        }
                    }
                } ?>
                </div>
            </div>
        <?php
        $text_group_one = ob_get_clean();

        if (!empty(trim(strip_tags($text_group_one)))) {
            echo '<div class="c-property-details__texts-wrapper">';
            echo $text_group_one;
            echo '</div>';
        }
        ?>

        <?php
        ob_start();

        if ($floorplans && !$show_secret_sale_block) {

            wp_enqueue_script('oo-glightbox-script');
            wp_enqueue_style('oo-glightbox-style');
            ?>
            <div class="c-property-details__container o-container">
                <div class="c-property-details__row o-row">
                    <div class="u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                        <h2 class="c-property-details__headline o-headline --h2">
                            <?php esc_html_e('Grundrisse', 'oo_theme'); ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="c-property-details__container o-container-fluid">
                <div class="c-property-details__gallery c-slider splide --auto-height --is-floorplan-slider" data-splide='{
                    "type":"loop",
                    "autoWidth":true,
                    "focus":"center",
                    "gap":16,
                    "arrows":true,
                    "snap":true,
                    "lazyLoad":false,
                    "pagination":true,
                    "updateOnMove":true,
                    "classes":{"page":"c-slider__page splide__pagination__page"},
                    "breakpoints": {
                        "992": { "gap": 0 }
                    }
                }'>
                    <div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                            <?php foreach ($sorted_floors as $id) {

                                $picture_values = $pEstates->getEstatePictureValues(
                                    $id,
                                );
                                $image_alt = $picture_values['title']
                                    ? esc_html($picture_values['title'])
                                    : esc_html__('Grundriss', 'oo_theme');

                                $image_heights = [
                                    'xs' => 200,
                                    'sm' => 200,
                                    'md' => 400,
                                    'lg' => 400,
                                    'xl' => 400,
                                    'xxl' => 400,
                                    'xxxl' => 400,
                                ];

                                $image = [
                                    'url' => $pEstates->getEstatePictureUrl(
                                        $id,
                                    ),
                                    'alt' => $image_alt,
                                ];

                                $lightbox_url =
                                    'https://acnaayzuen.cloudimg.io/v7/' .
                                    $image['url'] .
                                    '?force_format=webp&org_if_sml=1';

                                $lightbox_image_size_list = [
                                    [
                                        'id' => 'mobile',
                                        'breakpoint' => 767,
                                        'image_size' => 767,
                                    ],
                                    [
                                        'id' => 'tablet',
                                        'breakpoint' => 768,
                                        'image_size' => 1200,
                                    ],
                                    [
                                        'id' => 'desktop',
                                        'breakpoint' => 1200,
                                        'image_size' => 1920,
                                    ],
                                ];

                                $lightbox_image_breakpoints = '';
                                $lightbox_image_sizes = '';

                                foreach (
                                    $lightbox_image_size_list
                                    as $key => $size
                                ) {
                                    $is_first = $key === 0;
                                    $is_last =
                                        $key ===
                                        array_key_last(
                                            $lightbox_image_size_list,
                                        );
                                    $separator = $is_last ? '' : ',';

                                    if ($is_first) {
                                        $lightbox_image_breakpoints .= "(max-width: {$size['breakpoint']}px) {$size['image_size']}px,";
                                        $lightbox_image_sizes .= "{$lightbox_url}&w={$size['image_size']} {$size['breakpoint']}w,";
                                        continue;
                                    }

                                    $lightbox_image_breakpoints .= "(min-width:{$size['breakpoint']}px) {$size['image_size']}px{$separator}";
                                    $lightbox_image_sizes .= "{$lightbox_url}&w={$size['image_size']} {$size['breakpoint']}w{$separator}";
                                }
                                ?>
                                
                                <a class="c-property-details__gallery-link glightbox c-slider__slide splide__slide"
                                data-gallery="gallery-floorplan"
                                href="<?php echo esc_url($lightbox_url) .
                                    '&w=' .
                                    end($lightbox_image_size_list)[
                                        'image_size'
                                    ]; ?>"
                                data-sizes="<?php echo esc_attr(
                                    $lightbox_image_breakpoints,
                                ); ?>"
                                data-srcset="<?php echo esc_attr(
                                    $lightbox_image_sizes,
                                ); ?>"
                                data-caption="<?php echo esc_attr(
                                    $image['alt'],
                                ); ?>"
                                title="<?php echo esc_attr($image['alt']); ?>"
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
                                            'loading' => 'eager',
                                            'picture_class' =>
                                                'c-property-details__gallery-picture o-picture',
                                            'image_class' =>
                                                'c-property-details__gallery-image o-image',
                                            'dimensions' => [
                                                '575' => [
                                                    'h' => $image_heights['xs'],
                                                ],
                                                '1600' => [
                                                    'h' =>
                                                        $image_heights['xxxl'],
                                                ],
                                                '1400' => [
                                                    'h' =>
                                                        $image_heights['xxl'],
                                                ],
                                                '1200' => [
                                                    'h' => $image_heights['xl'],
                                                ],
                                                '992' => [
                                                    'h' => $image_heights['lg'],
                                                ],
                                                '768' => [
                                                    'h' => $image_heights['md'],
                                                ],
                                                '576' => [
                                                    'h' => $image_heights['sm'],
                                                ],
                                            ],
                                        ],
                                    ); ?>
                                    <div class="c-slider__fullscreen c-icon-button splide__fullscreen">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Vergrößern',
                                            'oo_theme',
                                        ); ?></span>
                                        <?php echo oo_get_icon('resize', true, [
                                            'class' => 'c-icon-button__icon',
                                        ]); ?>
                                    </div>
                                </a>
                            <?php
                            } ?>
                        </div>
                    </div>
                    <div class="c-slider__slider-container o-container">
                        <div class="c-slider__navigation splide__navigation">
                            <div class="c-slider__arrows splide__arrows">
                                <button class="c-slider__arrow c-slider__arrow--prev splide__arrow splide__arrow--prev">
                                    <span class="u-screen-reader-only"><?php esc_html_e(
                                        'Vorheriges',
                                        'oo_theme',
                                    ); ?></span>
                                    <?php echo oo_get_icon(
                                        'chevron-left',
                                        true,
                                        [
                                            'class' =>
                                                'c-slider__icon splide__icon',
                                        ],
                                    ); ?>
                                </button>
                                <button class="c-slider__arrow c-slider__arrow--next splide__arrow splide__arrow--next">
                                    <span class="u-screen-reader-only"><?php esc_html_e(
                                        'Nächstes',
                                        'oo_theme',
                                    ); ?></span>
                                    <?php echo oo_get_icon(
                                        'chevron-right',
                                        true,
                                        [
                                            'class' =>
                                                'c-slider__icon splide__icon',
                                        ],
                                    ); ?>
                                </button>
                            </div>

                            <div class="c-slider__pagination-wrapper">
                                <ul class="c-slider__pagination splide__pagination"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        $floorplan_content = ob_get_clean();

        if (!empty($floorplan_content)) {
            echo '<div class="c-property-details__gallery-wrapper">';
            echo $floorplan_content;
            echo '</div>';
        }
        ?>
        
        <?php ob_start(); ?>
            <div class="c-property-details__texts o-container">
                <div class="c-property-details__texts-row o-row">
                    <?php if (!empty($property_free_texts)) {
                        foreach ($property_free_texts as $field) {
                            if (
                                $field['field'] !== 'objektbeschreibung' &&
                                $field['field'] !== 'sonstige_angaben' &&
                                $field['has_value']
                            ) {

                                $content = $field['value'];
                                $field_toggle_id =
                                    'property-features-' . $field['field'];
                                ?>
                                <div class="c-property-details__text u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                                    <h2 class="c-property-details__headline o-headline --h2">
                                        <?php esc_html_e(
                                            $field['label'],
                                            'oo_theme',
                                        ); ?>
                                    </h2>
                                    
                                    <div class="c-property-details__text-content" id="<?php echo esc_attr(
                                        $field_toggle_id,
                                    ); ?>">
                                        <?php echo nl2br(
                                            $show_secret_sale_block
                                                ? '...'
                                                : esc_html($content),
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
                                            aria-expanded="false" 
                                            aria-controls="<?php echo esc_attr(
                                                $field_toggle_id,
                                            ); ?>"
                                            style="display: none;">
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
                            <?php
                            }
                        }
                    } ?>

                    <?php if (!empty($pEstates->getTotalCostsData())) {
                        $totalCostsData = $pEstates->getTotalCostsData(); ?>
                             <div class="c-property-details__calculator-content u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
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
                                                $totalCostsData['kaufpreis'][
                                                    'raw'
                                                ],
                                                $totalCostsData['bundesland'][
                                                    'raw'
                                                ],
                                                $totalCostsData[
                                                    'aussen_courtage'
                                                ]['raw'],
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
                                                $totalCostsData[
                                                    'aussen_courtage'
                                                ]['default'],
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
                    } ?>

                    <?php if (!empty($area_butler_url)) { ?>
                        <div class="c-property-details__text-content u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                            <h2 class="c-property-details__headline o-headline --h2">
                                <?php echo __('Infrastruktur', 'oo_theme'); ?>
                            </h2>
                            <span class="c-property-details__iframe --is-areabutler">
                                <iframe src="
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
                                    ); ?>">
                                </iframe>
                            </span>
                            <?php if (!empty($infrastructure_info)) { ?>
                                <div class="c-property-details__text">
                                    <div class="c-item-fields">
                                    <?php foreach (
                                        $infrastructure_info
                                        as $info
                                    ) { ?>
                                        <dl class="c-item-fields__item">
                                            <dt class="c-item-fields__value">
                                                <?php echo $info['value']; ?>
                                            </dt>
                                            <dd class="c-item-fields__label">
                                                <?php echo $info['label']; ?>
                                            </dd>
                                        </dl>
                                    <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php ob_start(); ?>
            <div class="c-property-details__texts o-container">
                <div class="c-property-details__texts-row o-row">
                    <?php if (!empty($property_free_texts)) {
                        foreach ($property_free_texts as $field) {
                            if (
                                $field['field'] === 'sonstige_angaben' &&
                                $field['has_value']
                            ) {

                                $content = $field['value'];
                                $field_toggle_id =
                                    'property-other-' . $field['field'];
                                ?>
                                <div class="c-property-details__text u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                                    <h2 class="c-property-details__headline o-headline --h2">
                                        <?php esc_html_e(
                                            $field['label'],
                                            'oo_theme',
                                        ); ?>
                                    </h2>
                                    
                                    <div class="c-property-details__text-content" id="<?php echo esc_attr(
                                        $field_toggle_id,
                                    ); ?>">
                                        <?php echo nl2br(
                                            $show_secret_sale_block
                                                ? '...'
                                                : esc_html($content),
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
                                            aria-expanded="false" 
                                            aria-controls="<?php echo esc_attr(
                                                $field_toggle_id,
                                            ); ?>">
                                        <?php echo esc_html_e(
                                            'Mehr anzeigen',
                                            'oo_theme',
                                        ); ?>
                                    </button>
                                </div>
                            <?php
                            }
                        }
                    } ?>

                    <?php if ($energy_fields_available) {

                        // Fetch required values
                        $energy_class =
                            $raw_values->getValueRaw($property_id)['elements'][
                                'energyClass'
                            ] ?? '';
                        $energy_class_permitted_values = $pEstates->getShowEnergyCertificate()
                            ? $pEstates->getPermittedValues('energyClass')
                            : [];
                        $energy_certificate_type =
                            $raw_values->getValueRaw($property_id)['elements'][
                                'energieausweistyp'
                            ] ?? '';
                        $labels = oo_get_energy_certificate_values()[
                            'value_ranges'
                        ]['energy_certificate'];
                        ?>

                        <div class="c-property-details__energy u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                            <h2 class="c-property-details__headline o-headline --h2">
                                <?= esc_html(
                                    $pEstates->getFieldLabel(
                                        'energieausweistyp',
                                    ),
                                ) ?>
                            </h2>

                            <div class="c-property-details__energy-content">
                                <?php if (
                                    !empty($energy_class_permitted_values) &&
                                    !empty($energy_class) &&
                                    $pEstates->getShowEnergyCertificate()
                                ): ?>
                                    <?php oo_render_energy_certificate(
                                        $energy_class_permitted_values,
                                        $energy_class,
                                        $labels,
                                        $energy_certificate_expiry_date,
                                    ); ?>
                                <?php endif; ?>

                                <div class="c-item-fields">
                                    <?php
                                    // Ensure required field is present depending on type
                                    if (
                                        $energy_certificate_type ===
                                            'Endenergiebedarf' &&
                                        !in_array(
                                            'endenergiebedarf',
                                            $energy_fields,
                                            true,
                                        )
                                    ) {
                                        $energy_fields[] = 'endenergiebedarf';
                                    } elseif (
                                        $energy_certificate_type ===
                                            'Energieverbrauchskennwert' &&
                                        !in_array(
                                            'energieverbrauchskennwert',
                                            $energy_fields,
                                            true,
                                        )
                                    ) {
                                        $energy_fields[] =
                                            'energieverbrauchskennwert';
                                    }

                                    foreach ($energy_fields_ordered as $field):

                                        if (!isset($current_property[$field])) {
                                            continue;
                                        }

                                        $value = $current_property[$field];
                                        if (
                                            oo_is_invalid_energy_value(
                                                $value,
                                                $field,
                                                $property_id,
                                                $raw_values,
                                            )
                                        ) {
                                            continue;
                                        }
                                        ?>
                                        <dl class="c-item-fields__item">
                                            <dd class="c-item-fields__value">
                                                <?php if (is_array($value)): ?>
                                                    <?= esc_html(
                                                        implode(', ', $value),
                                                    ) ?>
                                                <?php else: ?>
                                                    <?= esc_html($value) ?>
                                                <?php endif; ?>
                                            </dd>
                                            <dt class="c-item-fields__label"><?= esc_html(
                                                $pEstates->getFieldLabel(
                                                    $field,
                                                ),
                                            ) ?></dt>
                                        </dl>
                                    <?php
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
        <?php
        $text_group_two = ob_get_clean();

        if (!empty(trim(strip_tags($text_group_two)))) {
            echo '<div class="c-property-details__texts-wrapper">';
            echo $text_group_two;
            echo '</div>';
        }
        ?>

        <?php if (!empty($shortcode_form)) { ?>
            <div class="c-property-details__form-wrapper">
                <div class="c-property-details__form o-container">
                    <div class="c-property-details__form-row o-row">
                        <div id="request" class="c-property-details__form-content o-col-12 o-col-lg-12">
                            <?php echo do_shortcode($shortcode_form); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>

        <?php echo $pEstates->getSimilarEstates(); ?>

    </section>
<?php
}

if ($show_secret_sale_block): ?>
    <a id="secret-sale-trigger" href="#" style="display: none;" class="--open-popup" data-popup="customer-login" data-forceurl="<?php echo $property_link; ?>"></a>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        triggerSecretSalePopup();
    });
    </script>
<?php endif;

do_action('oo_secretsale_logactivity', $property_id, $is_secret_sale);

if (Favorites::isFavorizationEnabled()) { ?>
    <?php wp_enqueue_script('oo-favorites-script'); ?>

    <script>
    (function() {
        var snapshot = <?php echo wp_json_encode($recently_viewed_snapshot); ?>;
        var storageKey = 'oo_recently_viewed';
        var maxItems = 10;

        if (!snapshot || !snapshot.estate_id) {
            return;
        }

        snapshot.viewed_at = new Date().toISOString();

        try {
            var rawItems = window.localStorage.getItem(storageKey);
            var items = rawItems ? JSON.parse(rawItems) : [];

            if (!Array.isArray(items)) {
                items = [];
            }

            items = items.filter(function(item) {
                return Number(item && item.estate_id) !== Number(snapshot.estate_id);
            });

            items.unshift(snapshot);

            window.localStorage.setItem(
                storageKey,
                JSON.stringify(items.slice(0, maxItems)),
            );
        } catch (error) {
            console.warn('Could not store recently viewed estate.', error);
        }
    })();
    </script>
<?php
$isMPSWatchlistActive = false;

if (class_exists('OnOfficeVueAddons\Service\WatchlistService')) {
    $watchlistService = new OnOfficeVueAddons\Service\WatchlistService();
    $isMPSWatchlistActive = $watchlistService->is_watchlist_active();
}

if ($isMPSWatchlistActive && !$is_reference):
    wp_enqueue_script('on_office_vue_addons-watchlist');
    wp_localize_script('on_office_vue_addons-watchlist', 'watchlist_options', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('on_office_vue_addons_nonce'),
        'labels' => [
            'add' =>
                $favorite_label == 'Watchlist'
                    ? __('Zur Merkliste hinzufügen', 'oo_theme')
                    : __('Zu Favoriten hinzufügen', 'oo_theme'),
            'remove' =>
                $favorite_label == 'Watchlist'
                    ? __('Von Merkliste entfernen', 'oo_theme')
                    : __('Von Favoriten entfernen', 'oo_theme'),
        ],
    ]);
else:
    if (Favorites::isFavorizationEnabled()) {
        wp_enqueue_script('oo-favorites-script'); ?>

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
    <?php
    }
endif;
?>
<?php if (isset($_GET['revocation_token'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var btn = document.querySelector('.c-property-details__expose .c-button');
                if (btn) {
                    window.location.href = btn.getAttribute('href');
                }
            }, 300);
        });
        if (window.location.search) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
<?php endif;} ?>
