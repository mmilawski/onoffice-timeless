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
unset($current_address['imageUrl']);
if (!empty($pAddressList->generateImageAlt($address_id))) {
    $image_alt = esc_html($pAddressList->generateImageAlt($address_id));
} else {
    $image_alt = esc_html__('Beraterbild', 'oo_theme');
}
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

<article class="c-address-card --bg-transparent <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">
    <?php
    if (!empty($address_link)) {
        echo '<a href="' . $address_link . '" class="c-address-card__wrapper">';
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
        echo '<div class="c-address-card__picture"></div>';
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
        <?php if (!empty($full_name)) { ?>
            <h3 class="c-address-card__name o-headline --h3">
                <?php echo $full_name; ?>
            </h3>
        <?php } ?>

        <?php if (!empty($current_address['jobTitle'])) { ?>
            <p class="c-address-card__job">
                <?php echo $current_address['jobTitle']; ?>
            </p>
        <?php } ?>

        <?php if (!empty($current_address['jobPosition'])) { ?>
            <p class="c-address-card__job">
                <?php echo $current_address['jobPosition']; ?>
            </p>
        <?php } ?>

        <?php if (!empty($current_address['Zusatz1'])) { ?>
            <p class="c-address-card__job">
                <?php echo $current_address['Zusatz1']; ?>
            </p>
        <?php } ?>

        <?php if (!empty($address_fields_available)) { ?>
            <div class="c-address-card__fields">
                <?php
                foreach ($address_features as $address_feature) {
                    $field = $address_feature['field'];
                    $label = $address_feature['label'];
                    $value = $address_feature['value'];

                    if (
                        $field == 'phone' ||
                        $field == 'Telefon1' ||
                        $field == 'Telefon2'
                    ) {
                        $class = '--is-phone';
                        $link = preg_match('/[0-9]/', $value)
                            ? 'tel:' . oo_clean_link_number($value)
                            : '';
                    } elseif ($field == 'mobile') {
                        $class = '--is-mobile';
                        $link = preg_match('/[0-9]/', $value)
                            ? 'tel:' . oo_clean_link_number($value)
                            : '';
                    } elseif (
                        $field == 'fax' ||
                        $field == 'Telefax' ||
                        $field == 'Telefax1' ||
                        $field == 'Telefax2'
                    ) {
                        $class = '--is-fax';
                        $link = preg_match('/[0-9]/', $value)
                            ? 'fax:' . oo_clean_link_number($value)
                            : '';
                    } elseif ($field == 'email' || $field == 'Email') {
                        $email_utf8 = oo_clean_acf_email_utf8($value);
                        $email_ascii = oo_clean_acf_email_ascii($value);
                        $value = oo_antispambot(esc_html($email_utf8));
                        $class = '--is-email';
                        $link = 'mailto:' . antispambot(esc_html($email_ascii));
                    } elseif ($field == 'Homepage' || $field == 'url') {
                        $class = '--is-website';
                        $check_url = preg_match('~^https?://~i', $value)
                            ? $value
                            : 'https://' . $value;
                        $link = filter_var($check_url, FILTER_VALIDATE_URL)
                            ? esc_url($check_url)
                            : '';
                        $target = 'rel="noopener noreferrer" target="_blank"';
                    } else {
                        $class = '';
                        $link = '';
                        $target = '';
                    }

                    echo '<dl class="c-address-card__item ' .
                        (!empty($class) ? $class : '') .
                        '">';
                    echo '<dt class="c-address-card__label">' .
                        esc_html($label) .
                        ':</dt>';
                    echo '<dd class="c-address-card__value">';
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
                    echo '</dd>';
                    echo '</dl>';
                }

                if (!empty($reviews)) {
                    echo '<a class="c-link --underlined --text-color --on-bg-transparent" href="' .
                        $reviews .
                        '" rel="noopener noreferrer" target="_blank">' .
                        __('Bewertungen ansehen', 'oo_theme') .
                        '</a>';
                }
                ?>
            </div>
        <?php } ?>
    </div>

    <?php if (
        $network_fields_available ||
        !empty($address_link) ||
        !empty($phone)
    ) { ?>
        <div class="c-address-card__footer">
            <?php if ($network_fields_available) {
                echo '<ul class="c-address-card__networks c-social-media --is-content">';
                foreach ($network_features as $network_feature) {
                    $field = $network_feature['field'];
                    $label = $network_feature['label'];
                    $value = $network_feature['value'];

                    $icon = match ($label) {
                        'Facebook' => 'facebook',
                        'Instagram' => 'instagram',
                        'LinkedIn' => 'linkedin',
                        'Pinterest' => 'pinterest',
                        'TikTok' => 'tiktok',
                        'Twitter' => 'x',
                        'Xing' => 'xing',
                        'YouTube' => 'youtube',
                        default => null,
                    };

                    echo '<li class="c-social-media__item --' . $icon . '">';
                    echo '<a class="c-social-media__link" href="' .
                        esc_url($value) .
                        '" rel="noopener noreferrer" target="_blank">';
                    oo_get_icon($icon);
                    echo '<span class="c-social-media__text u-screen-reader-only">' .
                        $label .
                        '</span>';
                    echo '</a>';
                    echo '</li>';
                }
                echo '</ul>';
            } ?>

            <?php if (!empty($address_link) || !empty($phone)) { ?>
                <div class="c-address-card__buttons c-buttons">
                    <?php if (!empty($address_link)) { ?>
                        <a href="<?php echo $address_link; ?>" class="c-address-card__button --is-detail c-button --small-corners">
                            <?php echo __('Details anzeigen', 'oo_theme'); ?>
                        </a>
                    <?php } ?>
                    <?php if (!empty($phone_url)) { ?>
                        <a class="c-address-card__button --is-phone c-icon-button --bigger --small-corners" href="<?php echo $phone_url; ?>">
                            <span class="c-icon-button__text u-screen-reader-only"><?php echo __(
                                'Anrufen',
                                'oo_theme',
                            ); ?></span>
                            <span class="c-icon-button__icon --phone"><?php oo_get_icon(
                                'phone',
                            ); ?></span>
                        </a>
                    <?php } ?>
                    <?php if (!empty($address_link)) { ?>
                        <a href="<?php echo $address_link; ?>#contact_form" class="c-address-card__button --is-form c-button --small-corners --ghost">
                            <?php echo __('Kontaktformular', 'oo_theme'); ?>
                        </a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</article>