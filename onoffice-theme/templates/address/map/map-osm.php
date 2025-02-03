<?php

/**
 *
 *    Copyright (C) 2024 onOffice GmbH
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
    $addressData = [];

    foreach (
        $pAddressClone->getRows(
            AddressViewFieldModifierTypes::MODIFIER_TYPE_MAP,
        )
        as $addressId => $escapedValues
    ) {
        $position = [
            'lat' => (float) $escapedValues['breitengrad'],
            'lng' => (float) $escapedValues['laengengrad'],
        ];

        if (0.0 !== $position['lng'] && 0.0 !== $position['lat']) {
            $addressData[] = [
                'position' => $position,
                'title' =>
                    $escapedValues['Vorname'] . ' ' . $escapedValues['Name'],
                'company' => $escapedValues['Zusatz1'],
                'link' =>
                    esc_url($pAddressClone->getAddressLink($addressId)) ?? '',
                'visible' => true,
            ];
        }
    }

    if ($addressData === []) {
        return;
    }

    // Styling
    $colors = get_field('colors', 'option') ?? null;
    $primary_color = $colors['global']['primary'] ?? 'currentColor';

    wp_enqueue_style('oo-leaflet-style');
    wp_enqueue_script('oo-leaflet-script');
    wp_enqueue_script('oo-init-open-street-map-script');
    ?>

    <div class="c-map --is-open-street-map" data-max-zoom="12" data-marker-color="<?php echo $primary_color; ?>" style="width: 100%;">
        <?php foreach ($addressData as $address) {

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
            ); ?>" data-lng="<?php echo esc_attr($lng); ?>">
                <div class="c-map__info --bg-transparent">
                    <?php
                    if (!empty($title)) {
                        echo '<h3 class="c-map__headline o-headline --h3">' .
                            $title .
                            '</h3>';
                        echo '<p>' . $company . '</p>';
                    }
                    if (!empty($link)) {
                        echo '<p class="c-map__button-wrapper"><a href="' .
                            $link .
                            '" class="c-map__button c-button">' .
                            esc_html__('Zur Detailansicht', 'oo_theme') .
                            '</a></p>';
                    }
                    ?>
                </div>
            </div>
        <?php
        } ?>
    </div>
<?php
};
