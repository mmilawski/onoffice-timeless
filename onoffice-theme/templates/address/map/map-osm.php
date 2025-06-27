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

use onOffice\WPlugin\AddressList;
use onOffice\WPlugin\ViewFieldModifier\AddressViewFieldModifierTypes;

/* @var $pAddresses AddressList */

return function (AddressList $pAddressClone) {
    $pAddressClone->resetAddressesIterator();
    $address_data = [];

    foreach (
        $pAddressClone->getRows(
            AddressViewFieldModifierTypes::MODIFIER_TYPE_MAP,
        )
        as $address_id => $current_address
    ) {
        $position = [
            'lat' => (float) $current_address['breitengrad'],
            'lng' => (float) $current_address['laengengrad'],
        ];

        if (0.0 !== $position['lng'] && 0.0 !== $position['lat']) {
            $address_data[] = [
                'position' => $position,
                'title' =>
                    $current_address['Vorname'] .
                    ' ' .
                    $current_address['Name'],
                'company' => $current_address['Zusatz1'],
                'link' =>
                    esc_url($pAddressClone->getAddressLink($address_id)) ?? '',
            ];
        }
    }

    if ($address_data === []) {
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

    <div class="c-map --is-open-street-map" data-max-zoom="12" data-marker-color="<?php echo $primary_color; ?>" style="width: 100%;" aria-label="<?php echo esc_html__(
    'Karte mit Adressenstandorten',
    'oo_theme',
); ?>">
        <?php foreach ($address_data as $address) {

            $position = $address['position'] ?? [];
            $lat = $position['lat'] ?? null;
            $lng = $position['lng'] ?? null;
            $title = $address['title'] ?? null;
            $company = $address['company'] ?? null;
            $link = $address['link'] ?? null;

            if (empty($lat) || empty($lng)) {
                continue;
            }
            ?>
            <div class="c-map__marker" data-lat="<?php echo esc_attr(
                $lat,
            ); ?>" data-lng="<?php echo esc_attr(
    $lng,
); ?>" data-aria-label="<?php echo oo_get_map_marker_aria_label(
    ['title' => $title],
    'Adressstandort',
); ?>">
                <div class="c-map__info --bg-transparent">
                    <?php
                    if (!empty($title)) {
                        echo '<h3 class="c-map__headline o-headline --h3">' .
                            $title .
                            '</h3>';
                    }
                    if (!empty($company)) {
                        echo '<p class="c-map__text">' . $company . '</p>';
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
