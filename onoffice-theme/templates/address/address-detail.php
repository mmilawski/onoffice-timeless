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

use onOffice\WPlugin\AddressList;
global $wp;

$dont_echo = [
    'Anrede',
    'Titel',
    'Vorname',
    'Name',
    'jobTitle',
    'jobPosition',
    'Zusatz1',
    'bewertungslinkWebseite',
    'bildWebseite',
    'imageUrl',
    'id',
    'defaultemail',
    'defaultphone',
    'defaultfax',
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
$text_fields = ['Sonstige1', 'Sonstige2', 'Sonstige3'];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

$current_url = home_url(add_query_arg([], $wp->request)) ?? '';

/* @var $pAddressList AddressList */
$current_address_array = [];
if (
    is_object($pAddressList) &&
    method_exists($pAddressList, 'getCurrentAddress')
) {
    $current_address_array = $pAddressList->getCurrentAddress() ?? [];
}

if (empty($current_address_array) || !is_array($current_address_array)) {
    return;
}

foreach ($current_address_array as $address_id => $current_address) {

    $pAddressList->setAddressId($address_id);

    // Name
    $name_components = array_filter([
        $current_address['Anrede'] ?? null,
        $current_address['Titel'] ?? null,
        $current_address['Vorname'] ?? null,
        $current_address['Name'] ?? null,
    ]);
    $full_name = join(' ', $name_components);

    // Jobs & company
    $job_title = $current_address['jobTitle'] ?? null;
    $job_position = $current_address['jobPosition'] ?? null;
    $company = $current_address['Zusatz1'] ?? null;

    // Reviews
    $reviews = $current_address['bewertungslinkWebseite'] ?? null;
    $reviews_check_url = $reviews
        ? (preg_match('~^https?://~i', $reviews)
            ? $reviews
            : 'https://' . $reviews)
        : null;
    $reviews_url = filter_var($reviews_check_url, FILTER_VALIDATE_URL);

    // Image
    $placeholder_image_url =
        get_field('general', 'option')['address_detail']['placeholder_image'][
            'url'
        ] ?? null;
    $image_url = !empty($current_address['bildWebseite'])
        ? esc_url($current_address['bildWebseite'])
        : (!empty($current_address['imageUrl'])
            ? esc_url($current_address['imageUrl'])
            : (!empty($placeholder_image_url)
                ? esc_url($placeholder_image_url)
                : null));
    $image_alt = !empty($pAddressList->generateImageAlt($address_id))
        ? esc_html($pAddressList->generateImageAlt($address_id))
        : (!empty($full_name)
            ? esc_html($full_name)
            : esc_html__('Beraterbild', 'oo_theme'));
    $image = [
        'url' => $image_url,
        'alt' => $image_alt,
    ];

    $image_width_xs = '543';
    $image_width_sm = '512';
    $image_width_md = '694';
    $image_width_lg = '288';
    $image_width_xl = '352';
    $image_width_xxl = '416';
    $image_width_xxxl = '460';

    // shortcodes
    $shortcode_active_properties = $pAddressList->getShortCodeActiveEstates();
    $shortcode_reference_properties = $pAddressList->getShortCodeReferenceEstates();
    $shortcode_form = $pAddressList->getShortCodeForm();

    // fields
    $address_features = [];
    $address_fields_available = false;
    $network_features = [];
    $network_fields_available = false;
    $text_features = [];
    $text_fields_available = false;

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
            in_array($field, $dont_echo)
        ) {
            continue;
        }

        if (in_array($field, $text_fields)) {
            $text_fields_available = true;
            $text_features[] = [
                'field' => $field,
                'label' => $pAddressList->getFieldLabel($field),
                'value' => $value,
            ];
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

    // read more
    $number = 0;
    $fields_counter = is_array($address_features)
        ? count($address_features)
        : 0;
    $fields_more = (int) (get_field('show_more_count') ?? 12);
    ?>

    <section class="c-address-details o-section --<?php echo $bg_color; ?>">
        <div class="c-address-details__container o-container">
            <div class="c-address-details__row o-row --position-center">
                <div class="c-address-details__main o-col-12 o-col-lg-8">
                    <div class="c-address-details__header">
                        <?php if (!empty($full_name) || !empty($company)) { ?>
                            <h1 class="c-address-details__name o-headline --h1">
                                <?php if (!empty($full_name)) { ?>
                                    <?php echo $full_name; ?>
                                <?php } else { ?>
                                    <?php echo $company; ?>
                                <?php } ?>
                            </h1>
                        <?php } ?>

                        <?php if (!empty($job_title)) { ?>
                            <p class="c-address-details__job">
                                <?php echo $job_title; ?>
                            </p>
                        <?php } ?>

                        <?php if (!empty($job_position)) { ?>
                            <p class="c-address-details__job">
                                <?php echo $job_position; ?>
                            </p>
                        <?php } ?>

                        <?php if (!empty($full_name) && !empty($company)) { ?>
                            <p class="c-address-details__job">
                                <?php echo $company; ?>
                            </p>
                        <?php } ?>

                        <?php if (!empty($reviews)) { ?>
                            <p class="c-address-details__reviews">
                                <a class="c-link --underlined --text-color --on-<?php echo $bg_color; ?>" href="<?php echo $reviews; ?>" aria-label="<?php esc_attr_e(
    'Bewertungen ansehen (Öffnet in neuem Tab)',
    'oo_theme',
); ?>" rel="noopener noreferrer" target="_blank">
                                    <?php echo __(
                                        'Bewertungen ansehen',
                                        'oo_theme',
                                    ); ?>
                                </a>
                            </p>
                        <?php } ?>
                    </div>

                    <?php if (!empty($address_features)): ?>
                        <div class="c-address-details__features c-item-fields">
                            <?php foreach (
                                $address_features
                                as $address_feature
                            ) {
                                $field = $address_feature['field'];
                                $label = $address_feature['label'];
                                $value = $address_feature['value'];

                                if ($fields_counter > $fields_more) {
                                    if ($number === $fields_more) { ?>
                                        <div class="c-address-details__features-wrapper" id="more-address-features">
                                    <?php }
                                }

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
                                } elseif (
                                    $field == 'email' ||
                                    $field == 'Email'
                                ) {
                                    $email_utf8 = oo_clean_acf_email_utf8(
                                        $value,
                                    );
                                    $email_ascii = oo_clean_acf_email_ascii(
                                        $value,
                                    );
                                    $value = oo_antispambot(
                                        esc_html($email_utf8),
                                    );
                                    $class = '--is-email';
                                    $link =
                                        'mailto:' .
                                        antispambot(esc_html($email_ascii));
                                } elseif (
                                    $field == 'Homepage' ||
                                    $field == 'url'
                                ) {
                                    $class = '--is-website';
                                    $check_url = preg_match(
                                        '~^https?://~i',
                                        $value,
                                    )
                                        ? $value
                                        : 'https://' . $value;
                                    $link = filter_var(
                                        $check_url,
                                        FILTER_VALIDATE_URL,
                                    )
                                        ? esc_url($check_url)
                                        : '';
                                    $target =
                                        'rel="noopener noreferrer" aria-label="' .
                                        sprintf(
                                            esc_attr__(
                                                'Webseite von %s besuchen (Öffnet in neuem Tab)',
                                                'oo_theme',
                                            ),
                                            $full_name,
                                        ) .
                                        '" target="_blank"';
                                } else {
                                    $class = '';
                                    $link = '';
                                    $target = '';
                                }

                                echo '<dl class="c-item-fields__item ' .
                                    (!empty($class) ? $class : '') .
                                    '">';
                                echo '<dt class="c-item-fields__label">' .
                                    esc_html($label) .
                                    ':</dt>';
                                echo '<dd class="c-item-fields__value">';
                                if (!empty($link)) {
                                    echo '<a class="c-link --text-color --on-' .
                                        $bg_color .
                                        '" href="' .
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

                                if ($fields_counter > $fields_more) {
                                    if ($number == $fields_counter - 1) { ?>
                                        </div>
                                        <button class="c-address-details__more c-read-more" 
                                            data-open-text="<?php esc_html_e(
                                                'Mehr anzeigen',
                                                'oo_theme',
                                            ); ?>"
                                            data-close-text="<?php esc_html_e(
                                                'Weniger anzeigen',
                                                'oo_theme',
                                            ); ?>"
                                            aria-expanded="false" aria-controls="more-address-features">
                                            <?php echo esc_html__(
                                                'Mehr anzeigen',
                                                'oo_theme',
                                            ); ?>
                                        </button>
                                    <?php }
                                }
                                $number++;
                            } ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="c-address-details__sidebar o-col-12 o-col-lg-4">
                    <?php if (!empty($image_url)) {
                        oo_get_template('components', '', 'component-image', [
                            'image' => $image,
                            'picture_class' =>
                                'c-address-details__picture o-picture',
                            'image_class' => 'c-address-details__image o-image',
                            'additional_cloudimg_params' =>
                                '&func=crop&gravity=face',
                            'loading' => 'eager',
                            'decoding' => 'auto',
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
                    } ?>

                    <div class="c-address-details__sidebar-wrapper">
                        <?php if ($network_fields_available) {
                            echo '<ul class="c-address-details__networks c-social-media --is-content">';
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

                                echo '<li class="c-social-media__item --' .
                                    $icon .
                                    '">';
                                echo '<a class="c-social-media__link" href="' .
                                    esc_url($value) .
                                    '" rel="noopener noreferrer" aria-label="' .
                                    sprintf(
                                        esc_attr__(
                                            'Webseite von %s besuchen (Öffnet in neuem Tab)',
                                            'oo_theme',
                                        ),
                                        $label,
                                    ) .
                                    '" target="_blank">';
                                oo_get_icon($icon);
                                echo '<span class="c-social-media__text u-screen-reader-only">' .
                                    $label .
                                    '</span>';
                                echo '</a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } ?>

                        <div class="c-address-details__buttons c-buttons --is-column --align-start">
                            <?php if (!empty($shortcode_form)) { ?>
                                <a class="c-address-details__button-contact c-button" href="#contact_form"><?php esc_html_e(
                                    'Kontaktieren Sie mich',
                                    'oo_theme',
                                ); ?></a>
                            <?php } ?>

                            <?php oo_get_template(
                                'components',
                                '',
                                'component-share',
                                [
                                    'button_class' =>
                                        'c-address-details__button-share c-button --ghost',
                                    'popup_title' => esc_html__(
                                        'Kontakt teilen',
                                        'oo_theme',
                                    ),
                                    'button_title' => esc_html__(
                                        'Kontakt teilen',
                                        'oo_theme',
                                    ),
                                    'button_icon' => 'share',
                                    'popup_id' => 'address-detail-share',
                                    'share_link' => $current_url,
                                ],
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($shortcode_active_properties)) { ?>
        <section class="c-address-details__properties c-property-list --is-address-details o-section --<?php echo $bg_color; ?>">
            <div class="c-property-list__container o-container">
                <div class="c-property-list__content o-row">
                    <h2 class="c-property-list__headline o-col-12 o-col-xl-8 o-headline --h2"><?php esc_html_e(
                        'Mein Portfolio',
                        'oo_theme',
                    ); ?></h2>
                </div>
                <?php echo do_shortcode($shortcode_active_properties); ?>
            </div>
        </section>
    <?php } ?>

    <?php if (!empty($shortcode_reference_properties)) { ?>
        <section class="c-address-details__properties c-property-list --is-address-details o-section --<?php echo $bg_color; ?>">
            <div class="c-property-list__container o-container">
                <div class="c-property-list__content o-row">
                    <h2 class="c-property-list__headline o-col-12 o-col-xl-8 o-headline --h2"><?php esc_html_e(
                        'Meine Referenzen',
                        'oo_theme',
                    ); ?></h2>
                </div>
                <?php echo do_shortcode($shortcode_reference_properties); ?>
            </div>
        </section>
    <?php } ?>

    <?php foreach ($text_features as $text_feature) {

        $field = $text_feature['field'];
        $label = $text_feature['label'];
        $value = $text_feature['value'];

        if (!empty($value)) { ?>
            <section class="c-text o-section --text-align-left --<?php echo $bg_color; ?>" id="<?php echo clean_id(
    $label,
); ?>">
                <div class="c-text__container o-container">
                    <div class="c-text__row o-row --position-center">
                        <h2 class="c-text__headline o-col-12 o-col-xl-8 o-headline --h2">
                            <?php echo nl2br($label); ?>
                        </h2>
                    </div>
                    <div class="c-text__columns o-row --position-center">
                        <div class="c-text__content o-col-12 o-col-xl-8">
                            <div class="c-text__text o-text --is-wysiwyg">
                                <p>
                                    <?php echo nl2br(
                                        htmlspecialchars(
                                            $value,
                                            ENT_QUOTES,
                                            'UTF-8',
                                        ),
                                    ); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php }
        ?>
    <?php
    } ?>

    <?php if (!empty($shortcode_form)) { ?>
        <section class="c-address-details__form c-forms o-section o-section --<?php echo $bg_color; ?>" id="contact_form">
            <div class="c-forms__container o-container">
                <div class="c-forms__content o-row --position-center">
                    <h2 class="c-forms__headline o-col-12 o-col-lg-10 o-col-xl-8 o-headline --h2">
                        <?php esc_html_e(
                            'Nutzen Sie unser Kontaktformular',
                            'oo_theme',
                        ); ?>
                    </h2>
                </div>
                <div class="c-forms__content o-row --position-center">
                    <div class="c-forms__form detail-contact-form o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php echo do_shortcode($shortcode_form); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
<?php
} ?>
