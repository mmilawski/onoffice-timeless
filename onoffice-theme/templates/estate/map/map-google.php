<?php

/**
 *
 *    Copyright (C) 2018-2020 onOffice GmbH
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
 *  Map template for Google Maps
 */

use onOffice\WPlugin\EstateList;
use onOffice\WPlugin\ViewFieldModifier\EstateViewFieldModifierTypes;

/* @var $pEstates EstateList */

return function (EstateList $pEstatesClone) {
    $pEstatesClone->resetEstateIterator();
    $estateData = [];

    while (
        $currentEstateMap = $pEstatesClone->estateIterator(
            EstateViewFieldModifierTypes::MODIFIER_TYPE_MAP,
        )
    ) {
        $virtualAddressSet = (bool) $currentEstateMap['virtualAddress'];
        $position = [
            'lat' => (float) $currentEstateMap['breitengrad'],
            'lng' => (float) $currentEstateMap['laengengrad'],
        ];
        $visible = !$virtualAddressSet;

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
            $currentEstateMap['showGoogleMap']
        ) {
            $estateData[] = [
                'position' => $position,
                'title' => $currentEstateMap['objekttitel'],
                'zip' => $currentEstateMap['plz'],
                'city' => $currentEstateMap['ort'],
                'country' => $currentEstateMap['land'],
                'link' => $link,
                'visible' => $visible,
            ];
        }
    }

    if ($estateData === []) {
        return;
    }

    // Styling
    $colors = get_field('colors', 'option') ?? null;
    $primary_color = $colors['global']['primary'] ?? 'currentColor';
    ?>

    <?php
    wp_enqueue_script('oo-google-map-script');
    wp_enqueue_script('oo-init-google-map-script');
    ?>

    <div class="c-map --is-google-map" data-max-zoom="12" data-marker-color="<?php echo $primary_color; ?>" style="width: 100%;">
        <?php foreach ($estateData as $estate) {

            $position = $estate['position'] ?? [];
            $lat = $position['lat'] ?? null;
            $lng = $position['lng'] ?? null;
            $title = $estate['title'] ?? null;
            $zip = $estate['zip'] ?? null;
            $city = $estate['city'] ?? null;
            $country = $estate['country'] ?? null;
            $link = $estate['link'] ?? null;

            if (empty($lat) || empty($lng)) {
                continue;
            }
            ?>
            <div class="c-map__marker" data-lat="<?php echo esc_attr(
                $lat,
            ); ?>" data-lng="<?php echo esc_attr($lng); ?>">
                <div class="c-map__info --bg-transparent">
                    <?php if (
                        !empty($zip) ||
                        !empty($city) ||
                        !empty($country)
                    ) { ?>
                        <p class="c-map__location">
                            <span class="c-map__location-icon"><?php oo_get_icon(
                                'location',
                            ); ?></span>
                            <span class="c-map__location-text">
                                <?php if ($zip) {
                                    echo $zip;
                                } ?> <?php
 if ($city) {
     echo $city;
 }
 if (($zip || $city) && $country) {
     echo ', ';
 }
 if ($country) {
     echo $country;
 }
 ?>
                            </span>
                        </p>
                    <?php } ?>
                    <?php
                    if (!empty($title)) {
                        echo '<h3 class="c-map__headline o-headline">' .
                            $title .
                            '</h3>';
                    }
                    if (!empty($link)) {
                        $button = [
                            [
                                'link' => [
                                    'title' => esc_html__(
                                        'Details',
                                        'oo_theme',
                                    ),
                                    'url' => $link,
                                ],
                            ],
                        ];

                        oo_get_template('components', '', 'component-buttons', [
                            'buttons' => $button,
                            'additional_button_class' =>
                                'c-map__button --on-bg-transparent',
                            'additional_container_class' =>
                                'c-map__button-wrapper',
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
