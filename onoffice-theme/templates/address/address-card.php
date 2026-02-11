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
 *
 *  Default template
 *
 */

use onOffice\WPlugin\AddressList;
use onOffice\WPlugin\Types\FieldTypes;

$dont_echo = [
    'Anrede',
    'Titel',
    'Vorname',
    'Name',
    'jobTitle',
    'jobPosition',
    'Zusatz1',
    'bewertungslinkWebseite',
    'laengengrad',
    'breitengrad',
];
$network_fields = [
    'facebook',
    'instagram',
    'linkedin',
    'pinterest',
    'tiktok',
    'twitter',
    'xing',
    'youtube',
];

// link
$address_link = esc_url($pAddressList->getAddressLink($address_id));

// properties count
$properties_count = $pAddressList->getCountEstates($address_id);

// name
$name_components = array_filter([
    $current_address['Anrede'] ?? null,
    $current_address['Titel'] ?? null,
    $current_address['Vorname'] ?? null,
    $current_address['Name'] ?? null,
]);
$full_name = join(' ', $name_components);

// phone
$phone = $current_address['Telefon1'] ?? null;
$phone_url =
    !empty($phone) && preg_match('/[0-9]/', $phone)
        ? 'tel:' . oo_clean_link_number($phone)
        : '';

// reviews
$reviews = $current_address['bewertungslinkWebseite'] ?? null;
$reviews_check_url = $reviews
    ? (preg_match('~^https?://~i', $reviews)
        ? $reviews
        : 'https://' . $reviews)
    : null;
$reviews_url = filter_var($reviews_check_url, FILTER_VALIDATE_URL);

// image
$placeholder_image_url =
    get_field('general', 'option')['address_detail']['placeholder_image'][
        'url'
    ] ?? null;
$image_url = !empty($current_address['imageUrl'])
    ? esc_url($current_address['imageUrl'])
    : (!empty($placeholder_image_url)
        ? esc_url($placeholder_image_url)
        : null);
$image_alt = !empty($pAddressList->generateImageAlt($address_id))
    ? esc_html($pAddressList->generateImageAlt($address_id))
    : (!empty($full_name)
        ? esc_html($full_name)
        : esc_html__('Beraterbild', 'oo_theme'));
unset($current_address['imageUrl']);

$image = [
    'url' => $image_url,
    'alt' => $image_alt,
];

$image_width_xs = '542';
$image_width_sm = '510';
$image_width_md = '692';
$image_width_lg = '446';
$image_width_xl = '542';
$image_width_xxl = '414';
$image_width_xxxl = '458';

// get header level from parent block
$header_level = get_current_header_level() + 1;

// fields
$address_features = [];
$address_fields_available = false;
$network_features = [];
$network_fields_available = false;

foreach ($current_address as $field => $value) {
    if (
        (is_numeric($value) && 0 == $value) ||
        (is_array($value) &&
            empty(trim(implode(', ', array_filter($value)), ', '))) ||
        $value == '0000-00-00' ||
        $value == '0000-00-00 00:00:00' ||
        $value == '0.00' ||
        $value == 'Nein' ||
        $value == 'No' ||
        $value == 'Ne' ||
        $value == '' ||
        empty($value) ||
        $pAddressList->getFieldType($field) === FieldTypes::FIELD_TYPE_BLOB ||
        in_array($field, $dont_echo)
    ) {
        continue;
    }

    if (in_array($field, $network_fields)) {
        $check_url = preg_match('~^https?://~i', $value)
            ? $value
            : 'https://' . $value;
        if (!filter_var($check_url, FILTER_VALIDATE_URL)) {
            continue;
        }

        $network_fields_available = true;
        $network_features[] = [
            'field' => $field,
            'label' => $pAddressList->getFieldLabel($field),
            'value' => $value,
        ];
        continue;
    }

    $address_fields_available = true;
    $address_features[] = [
        'field' => $field,
        'label' => $pAddressList->getFieldLabel($field),
        'value' => $value,
    ];
}
?>

<article class="c-address-card <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">
    <?php
    if (!empty($address_link)) {
        echo '<a href="' .
            $address_link .
            '" class="c-address-card__wrapper" aria-label="' .
            (!empty($full_name)
                ? sprintf(
                    esc_html_x('Details anzeigen zu %s', 'oo_theme'),
                    $full_name,
                )
                : sprintf(
                    esc_html_x(
                        'Details anzeigen zur Adresse Nr. %d',
                        'oo_theme',
                    ),
                    $address_id,
                )) .
            '">';
    } else {
        echo '<div class="c-address-card__wrapper">';
    }

    if (!empty($image_url)) {
        oo_get_template('components', '', 'component-image', [
            'image' => $image,
            'picture_class' => 'c-address-card__picture o-picture',
            'image_class' => 'c-address-card__image o-image',
            'additional_cloudimg_params' => '&func=crop&gravity=face',
            'dimensions' => [
                '575' => [
                    'w' => $image_width_xs,
                    'h' => $image_width_xs,
                ],
                '1600' => [
                    'w' => $image_width_xxxl,
                    'h' => $image_width_xxxl,
                ],
                '1400' => [
                    'w' => $image_width_xxl,
                    'h' => $image_width_xxl,
                ],
                '1200' => [
                    'w' => $image_width_xl,
                    'h' => $image_width_xl,
                ],
                '992' => [
                    'w' => $image_width_lg,
                    'h' => $image_width_lg,
                ],
                '768' => [
                    'w' => $image_width_md,
                    'h' => $image_width_md,
                ],
                '576' => [
                    'w' => $image_width_sm,
                    'h' => $image_width_sm,
                ],
            ],
        ]);
    } else {
        echo '<div class="c-address-card__picture">';
        echo '<span class="u-screen-reader-only">';
        echo __('Details anzeigen', 'oo_theme');
        echo '</span>';
        echo '</div>';
    }
    if ($properties_count > 0) {
        echo '<div class="c-address-card__flag c-flag --property-status">';
        echo sprintf(
            _n('%d Inserat', '%d Inserate', $properties_count, 'oo_theme'),
            $properties_count,
        );
        echo '</div>';
    }

    if (!empty($address_link)) {
        echo '</a>';
    } else {
        echo '</div>';
    }
    ?>

    <div class="c-address-card__content">
        <div class="c-address-card__name-wrapper">
        <?php if (!empty($full_name)) { ?>
            <?php echo "<h{$header_level} " .
                'class="c-address-card__name o-headline --h3">' .
                $full_name .
                "</h{$header_level}>"; ?>
        <?php } ?>

        <?php
        $position = !empty($current_address['Position_iU'])
            ? $current_address['Position_iU']
            : (!empty($current_address['jobPosition'])
                ? $current_address['jobPosition']
                : (!empty($current_address['jobTitle'])
                    ? $current_address['jobTitle']
                    : ''));

        if (!empty($position)) { ?>
                <p class="c-address-card__job">
                    <?php if (!empty($current_address['Zusatz1'])) {
                        echo sprintf(
                            esc_attr_x(
                                '%1$s bei %2$s',
                                'Jobbezeichnung bei Arbeitgeber',
                                'oo_theme',
                            ),
                            $position,
                            $current_address['Zusatz1'],
                        );
                    } else {
                        echo $position;
                    } ?>
            </p>
        <?php }
        ?>
        </div>
        <?php if (!empty($address_fields_available)) { ?>
            <div class="c-address-card__fields c-item-features">
                <?php foreach ($address_features as $address_feature) {
                    $field = $address_feature['field'];
                    $label = $address_feature['label'];
                    $value = $address_feature['value'];

                    $isMultipleItems = is_array($value) && count($value) > 1;

                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    if (
                        $field == 'phone' ||
                        $field == 'Telefon1' ||
                        $field == 'Telefon2'
                    ) {
                        $class = '--is-phone';
                        if (!$isMultipleItems && preg_match('/\d+/', $value)) {
                            $link = 'tel:' . oo_clean_link_number($value);
                        } else {
                            $link = '';
                        }
                    } elseif ($field == 'mobile') {
                        $class = '--is-mobile';

                        if (
                            !$isMultipleItems &&
                            preg_match('/[0-9]/', $value)
                        ) {
                            $link = 'tel:' . oo_clean_link_number($value);
                        } else {
                            $link = '';
                        }
                    } elseif (
                        $field == 'fax' ||
                        $field == 'Telefax' ||
                        $field == 'Telefax1' ||
                        $field == 'Telefax2'
                    ) {
                        $class = '--is-fax';
                        if (
                            !$isMultipleItems &&
                            preg_match('/[0-9]/', $value)
                        ) {
                            $link = 'fax:' . oo_clean_link_number($value);
                        } else {
                            $link = '';
                        }
                    } elseif ($field == 'email' || $field == 'Email') {
                        $class = '--is-email';
                        if (
                            !$isMultipleItems &&
                            filter_var($value, FILTER_VALIDATE_EMAIL)
                        ) {
                            $email_utf8 = oo_clean_acf_email_utf8($value);
                            $email_ascii = oo_clean_acf_email_ascii($value);
                            $value = oo_antispambot(esc_html($email_utf8));
                            $link =
                                'mailto:' . antispambot(esc_html($email_ascii));
                        } else {
                            $link = '';
                        }
                    } elseif ($field == 'Homepage' || $field == 'url') {
                        $class = '--is-website';
                        $check_url = preg_match('~^https?://~i', $value)
                            ? $value
                            : 'https://' . $value;
                        $link = filter_var($check_url, FILTER_VALIDATE_URL)
                            ? esc_url($check_url)
                            : '';
                        $target =
                            'rel="noopener noreferrer" aria-label="' .
                            esc_attr(
                                sprintf(
                                    __(
                                        'Webseite von %s besuchen (Öffnet in neuem Tab)',
                                        'oo_theme',
                                    ),
                                    $full_name,
                                ),
                            ) .
                            '" target="_blank"';
                    } else {
                        $class = '';
                        $link = '';
                        $target = '';
                    }

                    echo '<dl class="c-address-card__item c-item-features__item ' .
                        (!empty($class) ? $class : '') .
                        '">';
                    echo '<dt class="c-address-card__value c-item-features__value">';
                    if (!empty($link)) {
                        echo '<a class="c-link --text-color --on-bg-transparent" href="' .
                            $link .
                            '" ' .
                            (!empty($target) ? $target : '') .
                            '>' .
                            esc_html($value) .
                            '</a>';
                    } else {
                        echo is_array($value)
                            ? esc_html(implode(', ', $value))
                            : esc_html($value);
                    }

                    echo '</dt>';
                    echo '<dd class="c-address-card__label c-item-features__label">';
                    echo esc_html($label);
                    echo '</dd>';
                    echo '</dl>';
                } ?>
            </div>
        <?php } ?>
       



<?php
$networks = [];

if ($network_fields_available && !empty($network_features)) {
    foreach ($network_features as $feature) {
        $field = $feature['field'];
        $label = $feature['label'];
        $value = $feature['value'];

        if (!empty($value)) {
            $networks[$field] = [
                'title' => $label,
                'url' => esc_url($value),
                'target' => '_blank',
            ];
        }
    }
}

if (!empty($reviews) || !empty($networks)) { ?>
        <div class="c-address-card__networks"> 
            <?php
            if (!empty($reviews)) {
                echo '<a class="c-link --text-color --on-bg-transparent" href="' .
                    $reviews .
                    '" rel="noopener noreferrer" aria-label="' .
                    esc_attr(
                        sprintf(
                            __(
                                'Bewertungen für %s ansehen (Öffnet in neuem Tab)',
                                'oo_theme',
                            ),
                            $full_name,
                        ),
                    ) .
                    '" target="_blank">' .
                    __('Bewertungen ansehen', 'oo_theme') .
                    '</a>';
            }

            if ($networks && array_filter($networks)): ?>
            <div class="c-address-card__row --social">
            <?php oo_get_template('components', '', 'component-social-media', [
                'networks' => $networks,
                'additional_container_class' =>
                    'c-address-card__social-links --is-address-card',
            ]); ?>
        </div>
        <?php endif;
            ?>

        </div>
        <?php }

if (!empty($address_link)) { ?>
    <div class="c-address-card__footer">

                <div class="c-address-card__buttons c-buttons">
             
                        <a href="<?php echo $address_link; ?>" class="c-address-card__button --is-detail c-button" aria-label="<?php echo !empty(
    $full_name
)
    ? sprintf(esc_html_x('Details anzeigen zu %s', 'oo_theme'), $full_name)
    : sprintf(
        esc_html_x('Details anzeigen zur Adresse Nr. %d', 'oo_theme'),
        $address_id,
    ); ?>">
                            <?php echo __('Details anzeigen', 'oo_theme'); ?>
                        </a>
                  
           
                   
                        <a href="<?php echo $address_link; ?>#contact_form" class="c-address-card__button --is-form c-button --ghost" aria-label="<?php echo !empty(
    $full_name
)
    ? sprintf(esc_html_x('Zum Kontaktformular von %s', 'oo_theme'), $full_name)
    : sprintf(
        esc_html_x('Zum Kontaktformular der Adresse Nr. %d', 'oo_theme'),
        $address_id,
    ); ?>">
                            <?php echo __('Kontaktformular', 'oo_theme'); ?>
                        </a>
               
                </div>
        
        </div>
        
    <?php }
?>
    </div>
</article>