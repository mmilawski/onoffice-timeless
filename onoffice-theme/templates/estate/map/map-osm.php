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
 *  Map template for OSM
 */

use onOffice\WPlugin\EstateList;
use onOffice\WPlugin\ViewFieldModifier\EstateViewFieldModifierTypes;

/* @var $pEstates EstateList */

return function (EstateList $pEstatesClone) {
    $pEstatesClone->resetEstateIterator();
    $property_data = [];

    while (
        $current_property = $pEstatesClone->estateIterator(
            EstateViewFieldModifierTypes::MODIFIER_TYPE_MAP,
        )
    ) {
        $virtual_address = (bool) $current_property['virtualAddress'];
        $position = [
            'lat' => (float) $current_property['breitengrad'],
            'lng' => (float) $current_property['laengengrad'],
        ];
        $visible = !$virtual_address;

        $property_url = esc_url($pEstatesClone->getEstateLink());
        $property_id = $pEstatesClone->getCurrentMultiLangEstateMainId();
        $raw_values = $pEstatesClone->getRawValues();
        $is_reference = filter_var(
            $raw_values->getValueRaw($property_id)['elements']['referenz'] ??
                null,
            FILTER_VALIDATE_BOOLEAN,
        );
        $is_restricted_view = $pEstatesClone->getViewRestrict();

        if ($is_reference && $is_restricted_view) {
            $link = '';
        } else {
            $link = $property_url;
        }

        if (
            0.0 !== $position['lng'] &&
            0.0 !== $position['lat'] &&
            $current_property['showGoogleMap']
        ) {
            $property_data[] = [
                'position' => $position,
                'title' => $current_property['objekttitel'],
                'street' => $current_property['strasse'],
                'number' => $current_property['hausnummer'],
                'zip' => $current_property['plz'],
                'city' => $current_property['ort'],
                'country' => $current_property['land'],
                'link' => $link,
                'visible' => $visible,
            ];
        }
    }

    if ($property_data === []) {
        return;
    }

    // Styling
    $colors = get_field('colors', 'option') ?? null;
    $primary_color = $colors['global']['primary'] ?? 'currentColor';

    // Scripts
    wp_enqueue_style('oo-leaflet-style');
    wp_enqueue_style('oo-leaflet-marker-cluster-style');
    wp_enqueue_style('oo-leaflet-marker-cluster-default-style');
    wp_enqueue_script('oo-leaflet-script');
    wp_enqueue_script('oo-init-open-street-map-script');
    wp_enqueue_script('oo-init-open-street-map-marker-cluster');
    ?>

    <div class="c-map --is-open-street-map" data-max-zoom="15" data-marker-color="<?php echo $primary_color; ?>" style="width: 100%;">
        <?php foreach ($property_data as $property) {

            $position = $property['position'] ?? [];
            $lat = $position['lat'] ?? null;
            $lng = $position['lng'] ?? null;
            $title = $property['title'] ?? null;
            $street = $property['street'] ?? null;
            $number = $property['number'] ?? null;
            $zip = $property['zip'] ?? null;
            $city = $property['city'] ?? null;
            $country = $property['country'] ?? null;
            $link = $property['link'] ?? null;

            if (empty($lat) || empty($lng)) {
                continue;
            }
            ?>
            <div class="c-map__marker" data-lat="<?php echo esc_attr(
                $lat,
            ); ?>" data-lng="<?php echo esc_attr($lng); ?>">
                <div class="c-map__info --bg-transparent">
                    <?php
                    if (!empty($title)) {
                        echo '<h3 class="c-map__headline o-headline --h3">' .
                            $title .
                            '</h3>';
                    }
                    if (
                        !empty($street) ||
                        !empty($number) ||
                        !empty($zip) ||
                        !empty($city) ||
                        !empty($country)
                    ) {
                        echo '<p class="c-map__text">';
                        if (!empty($street) || !empty($number)) {
                            if (!empty($street)) {
                                echo $street . ' ';
                            }
                            if (!empty($number)) {
                                echo $number;
                            }
                            if (
                                !empty($zip) ||
                                !empty($city) ||
                                !empty($country)
                            ) {
                                echo '<br>';
                            }
                        }
                        if (!empty($zip) || !empty($city)) {
                            if (!empty($zip)) {
                                echo $zip . ' ';
                            }
                            if (!empty($city)) {
                                echo $city;
                            }
                            if (!empty($country)) {
                                echo '<br>';
                            }
                        }
                        if (!empty($country)) {
                            echo $country;
                        }
                        echo '</p>';
                    }
                    if (!empty($link)) {
                        $button = [
                            [
                                'link' => [
                                    'title' => esc_html__(
                                        'Zur Detailansicht',
                                        'oo_theme',
                                    ),
                                    'url' => $link,
                                ],
                            ],
                        ];
                        oo_get_template('components', '', 'component-buttons', [
                            'buttons' => $button,
                            'additional_container_class' =>
                                'c-map__button-wrapper',
                            'additional_button_class' =>
                                'c-map__button --small-corners --full-width --on-bg-transparent',
                        ]);
                    }
                    ?>
                </div>
            </div>
        <?php
        } ?>
    </div>
<?php
};
