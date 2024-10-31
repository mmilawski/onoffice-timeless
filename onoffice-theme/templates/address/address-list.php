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

$agent_count = count($pAddressList->getRows() ?? []);

$image_width_xs = '387';
$image_width_sm = '508';
$image_width_md = '690';
$image_width_lg = '444';
$image_width_xl = '465';
$image_width_xxl = '543';
$image_width_xxxl = '600';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$headline = get_field('headline') ?? null;

// Section ID for pagination anchor
$anchor = $headline['text'] ? clean_id($headline['text']) : '';
?>
<div class="c-address-list__row o-row --position-center">
    <?php if (!$is_slider) { ?>
        <p class="c-address-list__count o-col-12 o-col-xl-10">
            <?php echo sprintf(
                _n(
                    '%d gefundene Adresse',
                    '%d gefundene Adressen',
                    $agent_count,
                    'oo_theme',
                ),
                $agent_count,
            ); ?>
        </p>
        <div class="c-address-list__agents o-col-12 o-col-xl-10">
            <?php foreach (
                $pAddressList->getRows()
                as $addressId => $escapedValues
            ) {

                $estate_count = $pAddressList->getCountEstates($addressId);
                $image_url = $escapedValues['imageUrl'];
                $image_alt =
                    $pAddressList->generateImageAlt($addressId) ?? null;
                $image = [
                    'url' => $image_url,
                    'alt' => $image_alt,
                ];
                unset($escapedValues['imageUrl']);

                $name = implode(
                    ' ',
                    array_filter([
                        $escapedValues['Vorname'] ?? null,
                        $escapedValues['Name'] ?? null,
                    ]),
                );
                $phone = $escapedValues['Telefon1'] ?? null;
                $phone_url = oo_clean_link_number($phone);
                ?>
                <div class="c-address-list__agent">
                    <div class="c-address-list__media">
                        <?php
                        echo '<a class="c-address-list__link" href="' .
                            esc_url($pAddressList->getAddressLink($addressId)) .
                            '">';

                        if ($estate_count > 0) {
                            echo '<div class="c-address-list__flags c-flags">';
                            echo '<span class="c-address-list__estate-count c-flag">';
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
                            echo '</div>';
                        }

                        if (!empty($image_url)) {
                            oo_get_template(
                                'components',
                                '',
                                'component-image',
                                [
                                    'image' => $image,
                                    'picture_class' =>
                                        'c-address-list__picture o-picture',
                                    'image_class' =>
                                        'c-address-list__image o-image',
                                    'additional_cloudimg_params' =>
                                        '&func=crop&gravity=face',
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
                        } else {
                            echo '<picture class="c-address-list__picture o-picture"></picture>';
                        }

                        echo '</a>';
                        ?>
                    </div>
                    <div class="c-address-list__info">
                        <div class="c-address-list__info-header">
                            <?php if (
                                (!empty($escapedValues['Vorname']) &&
                                    !empty($escapedValues['Name'])) ||
                                !empty($escapedValues['Zusatz1'])
                            ):
                                echo '<p class="c-address-list__name">';

                                if (
                                    !empty($escapedValues['Vorname']) &&
                                    !empty($escapedValues['Name'])
                                ) {
                                    echo $escapedValues['Vorname'] .
                                        ' ' .
                                        $escapedValues['Name'];
                                } else {
                                    if (!empty($escapedValues['Zusatz1'])) {
                                        echo $escapedValues['Zusatz1'];
                                    }
                                }
                                echo '</p>';
                            endif; ?>
                        </div>
                        <div class="c-address-list__info-content">
                            <?php foreach ($escapedValues as $field => $value) {
                                if (
                                    $pAddressList->getFieldType($field) ===
                                        FieldTypes::FIELD_TYPE_BLOB ||
                                    empty($value) ||
                                    (is_numeric($value) && $value == 0) ||
                                    $value === '0000-00-00' ||
                                    $value === '0.00' ||
                                    in_array($field, $dont_echo ?? [])
                                ) {
                                    continue;
                                }

                                $fieldLabel = $pAddressList->getFieldLabel(
                                    $field,
                                );

                                switch ($field) {
                                    case 'Email':
                                        echo '<dl class="c-address-list__contact --is-email">';
                                        echo '<dt class="c-address-list__contact-label">';
                                        echo esc_html($fieldLabel) . ': ';
                                        echo '</dt>';
                                        echo '<dd class="c-address-list__contact-value">';
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-email',
                                            [
                                                'email' => $value,
                                                'additional_link_class' => $bg_color
                                                    ? '--text-color --on-' .
                                                        $bg_color
                                                    : '--text-color',
                                                'truncate' => true,
                                            ],
                                        );
                                        echo '</dd>';
                                        echo '</dl>';
                                        break;
                                    case 'Telefon1':
                                        echo '<dl class="c-address-list__contact --is-phone --hide-mobile">';
                                        echo '<dt class="c-address-list__contact-label">';
                                        echo esc_html($fieldLabel) . ': ';
                                        echo '</dt>';
                                        echo '<dd class="c-address-list__contact-value">';
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-contact-numbers',
                                            [
                                                'number' => $value,
                                                'additional_link_class' => $bg_color
                                                    ? '--text-color --on-' .
                                                        $bg_color
                                                    : '--text-color',
                                            ],
                                        );
                                        echo '</dd>';
                                        echo '</dl>';
                                        break;
                                    case 'Telefax1':
                                        echo '<dl class="c-address-list__contact --is-phone">';
                                        echo '<dt class="c-address-list__contact-label">';
                                        echo esc_html($fieldLabel) . ': ';
                                        echo '</dt>';
                                        echo '<dd class="c-address-list__contact-value">';
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-contact-numbers',
                                            [
                                                'number' => $value,
                                                'additional_link_class' => $bg_color
                                                    ? '--text-color --on-' .
                                                        $bg_color
                                                    : '--text-color',
                                            ],
                                        );
                                        echo '</dd>';
                                        echo '</dl>';
                                        break;
                                    case 'facebook':
                                    case 'instagram':
                                    case 'linkedin':
                                    case 'twitter':
                                    case 'xing':
                                    case 'youtube':
                                        echo '<span class="c-address-list__contact --is-social" rel="noopener noreferrer" target="_blank">';
                                        echo '<a href="' .
                                            esc_url($value) .
                                            '" class="c-address-list__contact-value" rel="noopener noreferrer" target="_blank">';
                                        echo esc_html($fieldLabel);
                                        echo '</a>';
                                        echo '</span>';
                                        break;
                                    default:
                                        echo '<dl class="c-address-list__contact">';
                                        echo '<dt class="c-address-list__contact-label">';
                                        echo esc_html($fieldLabel) . ': ';
                                        echo '</dt>';
                                        echo '<dd class="c-address-list__contact-value">';
                                        echo is_array($value)
                                            ? implode(
                                                ', ',
                                                array_filter($value),
                                            )
                                            : $value;
                                        echo '</dd>';
                                        echo '</dl>';
                                }
                            } ?>
                        </div>
                        <div class="c-address-list__info-footer">
                            <?php
                            if (!empty($phone)) {
                                $phone_button = [
                                    [
                                        'link' => [
                                            'title' => esc_html__(
                                                'Anrufen',
                                                'oo_theme',
                                            ),
                                            'url' => esc_url(
                                                'tel:' . $phone_url,
                                            ),
                                        ],
                                    ],
                                ];

                                oo_get_template(
                                    'components',
                                    '',
                                    'component-buttons',
                                    [
                                        'buttons' => $phone_button,
                                        'icon_first' => 'arrow-right',
                                        'additional_button_class' =>
                                            '--is-address-list --on-bg-transparent --ghost --hide-desktop',
                                        'additional_container_class' =>
                                            'c-address-list__info-buttons',
                                    ],
                                );
                            }

                            oo_get_template(
                                'components',
                                '',
                                'component-buttons',
                                [
                                    'buttons' => [
                                        [
                                            'link' => [
                                                'title' => esc_html__(
                                                    'Zum Kontaktformular',
                                                    'oo_theme',
                                                ),
                                                'url' => esc_url(
                                                    $pAddressList->getAddressLink(
                                                        $addressId,
                                                    ) . '#contact_form',
                                                ),
                                            ],
                                        ],
                                    ],
                                    'icon_first' => 'arrow-right',
                                    'additional_button_class' =>
                                        '--is-address-list --ghost --on-bg-transparent',
                                    'additional_container_class' =>
                                        'c-address-list__info-buttons',
                                ],
                            );

                            oo_get_template(
                                'components',
                                '',
                                'component-buttons',
                                [
                                    'buttons' => [
                                        [
                                            'link' => [
                                                'title' => esc_html__(
                                                    'Details',
                                                    'oo_theme',
                                                ),
                                                'url' => esc_url(
                                                    $pAddressList->getAddressLink(
                                                        $addressId,
                                                    ),
                                                ),
                                            ],
                                        ],
                                    ],
                                    'icon_first' => 'arrow-right',
                                    'additional_button_class' =>
                                        '--is-address-list --on-bg-transparent',
                                    'additional_container_class' =>
                                        'c-address-list__info-buttons',
                                ],
                            );
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            } ?>
        </div>
        <?php oo_get_template('components', '', 'component-pagination', [
            'type' => 'address',
            'class' =>
                'c-address-list__pagination o-col-12 o-col-xl-10 --on-' .
                $bg_color,
            'anchor' => $anchor,
        ]); ?>
    <?php } else { ?>
        <div 
            class="c-address-list__slider --on-<?php echo $bg_color; ?> o-col-12 o-col-xl-10 c-slider --is-address-slider splide" 
            data-splide='{
                "perPage":1,
                "perMove":1,
                "gap":0,
                "snap":true,
                "lazyLoad":"nearby"
            }'>
            <div class="c-slider__track splide__track o-col-12 o-col-xl-10">
                <p class="c-address-list__count">
                    <?php echo sprintf(
                        _n(
                            '%d gefundene Adresse',
                            '%d gefundene Adressen',
                            $agent_count,
                            'oo_theme',
                        ),
                        $agent_count,
                    ); ?>
                </p>
                <div class="c-slider__list splide__list">
                    <?php foreach (
                        $pAddressList->getRows()
                        as $addressId => $escapedValues
                    ) {

                        $estate_count = $pAddressList->getCountEstates(
                            $addressId,
                        );
                        $image_url = $escapedValues['imageUrl'];
                        $image_alt = $pAddressList->generateImageAlt(
                            $addressId,
                        );
                        $image = [
                            'url' => $image_url,
                            'alt' => $image_alt,
                        ];
                        $phone = $escapedValues['Telefon1'] ?? null;
                        $phone_url = oo_clean_link_number($phone);
                        unset($escapedValues['imageUrl']);

                        $name = implode(
                            ' ',
                            array_filter([
                                $escapedValues['Vorname'] ?? null,
                                $escapedValues['Name'] ?? null,
                            ]),
                        );
                        ?>
                        <div class="c-address-list__agent c-slider__slide splide__slide">
                            <div class="c-address-list__media">
                                <?php
                                echo '<a class="c-address-list__link" href="' .
                                    esc_url(
                                        $pAddressList->getAddressLink(
                                            $addressId,
                                        ),
                                    ) .
                                    '">';
                                if ($estate_count > 0) {
                                    echo '<div class="c-address-list__flags c-flags">';
                                    echo '<span class="c-address-list__estate-count c-flag">';
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
                                    echo '</div>';
                                }
                                ?>
                                        <?php
                                        if (!empty($image_url)) {
                                            oo_get_template(
                                                'components',
                                                '',
                                                'component-image',
                                                [
                                                    'image' => $image,
                                                    'picture_class' =>
                                                        'c-address-list__picture o-picture',
                                                    'image_class' =>
                                                        'c-address-list__image o-image',
                                                    'additional_cloudimg_params' =>
                                                        '&func=crop&gravity=face',
                                                    'dimensions' => [
                                                        '575' => [
                                                            'w' => $image_width_xs,
                                                            'h' => round(
                                                                ($image_width_xs *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '1600' => [
                                                            'w' => $image_width_xxxl,
                                                            'h' => round(
                                                                ($image_width_xxxl *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '1400' => [
                                                            'w' => $image_width_xxl,
                                                            'h' => round(
                                                                ($image_width_xxl *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '1200' => [
                                                            'w' => $image_width_xl,
                                                            'h' => round(
                                                                ($image_width_xl *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '992' => [
                                                            'w' => $image_width_lg,
                                                            'h' => round(
                                                                ($image_width_lg *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '768' => [
                                                            'w' => $image_width_md,
                                                            'h' => round(
                                                                ($image_width_md *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '576' => [
                                                            'w' => $image_width_sm,
                                                            'h' => round(
                                                                ($image_width_sm *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                    ],
                                                ],
                                            );
                                        } else {
                                            echo '<picture class="c-address-list__picture o-picture"></picture>';
                                        }
                                        echo '</a>';
                                        ?>
                            </div>
                            <div class="c-address-list__info">
                                <div class="c-address-list__info-header">
                                <?php if (
                                    (!empty($escapedValues['Vorname']) &&
                                        !empty($escapedValues['Name'])) ||
                                    !empty($escapedValues['Zusatz1'])
                                ):
                                    echo '<p class="c-address-list__name">';
                                    if (
                                        !empty($escapedValues['Vorname']) &&
                                        !empty($escapedValues['Name'])
                                    ) {
                                        echo $escapedValues['Vorname'] .
                                            ' ' .
                                            $escapedValues['Name'];
                                    } else {
                                        if (!empty($escapedValues['Zusatz1'])) {
                                            echo $escapedValues['Zusatz1'];
                                        }
                                    }
                                    echo '</p>';
                                endif; ?>
                                </div>
                                <div class="c-address-list__info-content">
                                    <?php foreach (
                                        $escapedValues
                                        as $field => $value
                                    ) {
                                        if (
                                            $pAddressList->getFieldType(
                                                $field,
                                            ) === FieldTypes::FIELD_TYPE_BLOB ||
                                            empty($value)
                                        ) {
                                            continue;
                                        }

                                        $fieldLabel = $pAddressList->getFieldLabel(
                                            $field,
                                        );

                                        switch ($field) {
                                            case 'Email':
                                                echo '<dl class="c-address-list__contact --is-email">';
                                                echo '<dt class="c-address-list__contact-label">';
                                                echo esc_html($fieldLabel) .
                                                    ': ';
                                                echo '</dt>';
                                                echo '<dd class="c-address-list__contact-value">';
                                                oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-email',
                                                    [
                                                        'email' => $value,
                                                        'additional_link_class' => $bg_color
                                                            ? '--text-color --on-' .
                                                                $bg_color
                                                            : '--text-color',
                                                        'truncate' => true,
                                                    ],
                                                );
                                                echo '</dd>';
                                                echo '</dl>';
                                                break;
                                            case 'Telefon1':
                                                echo '<dl class="c-address-list__contact --is-phone --hide-mobile">';
                                                echo '<dt class="c-address-list__contact-label">';
                                                echo esc_html($fieldLabel) .
                                                    ': ';
                                                echo '</dt>';
                                                echo '<dd class="c-address-list__contact-value">';
                                                oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-contact-numbers',
                                                    [
                                                        'number' => $value,
                                                        'additional_link_class' => $bg_color
                                                            ? '--text-color --on-' .
                                                                $bg_color
                                                            : '--text-color',
                                                    ],
                                                );
                                                echo '</dd>';
                                                echo '</dl>';
                                                break;
                                            case 'Telefax1':
                                                echo '<dl class="c-address-list__contact --is-phone">';
                                                echo '<dt class="c-address-list__contact-label">';
                                                echo esc_html($fieldLabel) .
                                                    ': ';
                                                echo '</dt>';
                                                echo '<dd class="c-address-list__contact-value">';
                                                oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-contact-numbers',
                                                    [
                                                        'number' => $value,
                                                        'additional_link_class' => $bg_color
                                                            ? '--text-color --on-' .
                                                                $bg_color
                                                            : '--text-color',
                                                    ],
                                                );
                                                echo '</dd>';
                                                echo '</dl>';
                                                break;
                                            case 'facebook':
                                            case 'instagram':
                                            case 'linkedin':
                                            case 'twitter':
                                            case 'xing':
                                            case 'youtube':
                                                echo '<span class="c-address-list__contact --is-social" rel="noopener noreferrer" target="_blank">';
                                                echo '<a href="' .
                                                    esc_url($value) .
                                                    '" class="c-address-list__contact-value" rel="noopener noreferrer" target="_blank">';
                                                echo esc_html($fieldLabel);
                                                echo '</a>';
                                                echo '</span>';
                                                break;
                                            default:
                                                echo '<dl class="c-address-list__contact">';
                                                echo '<dt class="c-address-list__contact-label">';
                                                echo esc_html($fieldLabel) .
                                                    ': ';
                                                echo '</dt>';
                                                echo '<dd class="c-address-list__contact-value">';
                                                echo is_array($value)
                                                    ? implode(
                                                        ', ',
                                                        array_filter($value),
                                                    )
                                                    : $value;
                                                echo '</dd>';
                                                echo '</dl>';
                                        }
                                    } ?>
                                </div>
                                <div class="c-address-list__info-footer">
                                    <?php
                                    if (!empty($phone)) {
                                        $phone_button = [
                                            [
                                                'link' => [
                                                    'title' => esc_html__(
                                                        'Anrufen',
                                                        'oo_theme',
                                                    ),
                                                    'url' => esc_url(
                                                        'tel:' . $phone_url,
                                                    ),
                                                ],
                                            ],
                                        ];

                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-buttons',
                                            [
                                                'buttons' => $phone_button,
                                                'icon_first' => 'arrow-right',
                                                'additional_button_class' =>
                                                    '--is-address-list --on-bg-transparent --ghost --hide-desktop',
                                                'additional_container_class' =>
                                                    'c-address-list__info-buttons',
                                            ],
                                        );
                                    }

                                    oo_get_template(
                                        'components',
                                        '',
                                        'component-buttons',
                                        [
                                            'buttons' => [
                                                [
                                                    'link' => [
                                                        'title' => esc_html__(
                                                            'Zum Kontaktformular',
                                                            'oo_theme',
                                                        ),
                                                        'url' => esc_url(
                                                            $pAddressList->getAddressLink(
                                                                $addressId,
                                                            ) . '#contact_form',
                                                        ),
                                                    ],
                                                ],
                                            ],
                                            'icon_first' => 'arrow-right',
                                            'additional_button_class' =>
                                                '--is-address-list --ghost --on-bg-transparent',
                                            'additional_container_class' =>
                                                'c-address-list__info-buttons',
                                        ],
                                    );

                                    oo_get_template(
                                        'components',
                                        '',
                                        'component-buttons',
                                        [
                                            'buttons' => [
                                                [
                                                    'link' => [
                                                        'title' => esc_html__(
                                                            'Details',
                                                            'oo_theme',
                                                        ),
                                                        'url' => esc_url(
                                                            $pAddressList->getAddressLink(
                                                                $addressId,
                                                            ),
                                                        ),
                                                    ],
                                                ],
                                            ],
                                            'icon_first' => 'arrow-right',
                                            'additional_button_class' =>
                                                '--is-address-list --on-bg-transparent',
                                            'additional_container_class' =>
                                                'c-address-list__info-buttons',
                                        ],
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>
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
</div>