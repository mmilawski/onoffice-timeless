<?php

/**
 * Property Link Type
 */

function oo_property_field_type($field, $item)
{
    if ($field == 'phone' || $field == 'Telefon1' || $field == 'Telefon2') {
        if (preg_match('/[0-9]/', $item)) { ?>

            <dl class="c-contact-person__contact --is-phone">
                <dt class="c-contact-person__contact-label">
                    <?php esc_html_e('Tel.:', 'oo_theme'); ?>
                </dt>
                <dd class="c-contact-person__contact-value">
                    <a class="c-link --text-color --on-bg-transparent" href="tel:<?php echo oo_clean_link_number(
                        $item,
                    ); ?>">
                        <?php echo esc_html($item); ?>
                    </a>
                </dd>
            </dl>

        <?php }
    }

    if ($field == 'mobile') {
        if (preg_match('/[0-9]/', $item)) { ?>

            <dl class="c-contact-person__contact --is-mobile">
                <dt class="c-contact-person__contact-label">
                    <?php esc_html_e('Mobile:', 'oo_theme'); ?>
                </dt>
                <dd class="c-contact-person__contact-value">
                    <a class="c-link --text-color --on-bg-transparent" href="tel:<?php echo oo_clean_link_number(
                        $item,
                    ); ?>">
                        <?php echo esc_html($item); ?>
                    </a>
                </dd>
            </dl>

        <?php }
    }

    if (
        $field == 'fax' ||
        $field == 'Telefax' ||
        $field == 'Telefax1' ||
        $field == 'Telefax2'
    ) { ?>
        <dl class="c-contact-person__contact --is-fax">
            <dt class="c-contact-person__contact-label">
                <?php esc_html_e('Fax:', 'oo_theme'); ?>
            </dt>
            <dd class="c-contact-person__contact-value">
                <a class="c-link --text-color --on-bg-transparent" href="fax:<?php echo oo_clean_link_number(
                    $item,
                ); ?>">
                    <?php echo esc_html($item); ?>
                </a>
            </dd>
        </dl>
    <?php }

    if ($field == 'email' || $field == 'Email') {

        $email_utf8 = oo_clean_acf_email_utf8($item);
        $email_ascii = oo_clean_acf_email_ascii($item);
        $email_antispam = oo_antispambot(esc_html($email_utf8));
        $mailto_link = antispambot(esc_html($email_ascii));
        ?>
        <dl class="c-contact-person__contact --is-email">
            <dt class="c-contact-person__contact-label">
                <?php esc_html_e('E-Mail:', 'oo_theme'); ?>
            </dt>
            <dd class="c-contact-person__contact-value">
                <a class="c-link --text-color --on-bg-transparent" href="mailto:<?= $mailto_link ?>">
                    <?php echo $email_antispam; ?>
                </a>
            </dd>
        </dl>
    <?php
    }

    if ($field == 'Homepage' || $field == 'url') {
        $has_protocol = parse_url($item)['scheme'] ?? null;

        if ($has_protocol == 'https' || $has_protocol == 'http'): ?>

            <dl class="c-contact-person__contact --is-website">
                <dt class="c-contact-person__contact-label">
                    <?php esc_html_e('Web:', 'oo_theme'); ?>
                </dt>
                <dd class="c-contact-person__contact-value">
                    <a class="c-link --text-color --on-bg-transparent" target="_blank" href="<?php echo esc_html(
                        $item,
                    ); ?>">
                        <?php echo esc_html($item); ?>
                    </a>
                </dd>
            </dl>

        <?php endif;
    }
}

if (!empty($pEstates->getEstateContacts())) {
    echo '<div class="c-property-details__contacts">';

    $configured_address_fields = $pEstates->getAddressFields();

    $address_fields = array_diff($configured_address_fields, [
        'imageUrl',
        'Anrede',
        'Anrede-Titel',
        'Titel',
        'Vorname',
        'Name',
        'Zusatz1', // Company
        'jobPosition',
        'Strasse',
        'Plz',
        'Ort',
    ]);

    $labels_fields = [
        'Email',
        'email',
        'fax',
        'Telefax1',
        'Telefon1',
        'mobile',
        'Homepage',
    ];

    $contacts = $pEstates->getEstateContacts();
    $headline = oo_get_contacts_headline($contacts);
    $contact_count = is_array($contacts) ? count($contacts) : 0;

    echo '<h2 class="c-property-details__headline o-headline">';
    echo $headline;
    echo '</h2>';

    if ($contact_count > 1) {
        echo '<div class="c-property-details__contacts-wrapper">';
    }

    foreach ($pEstates->getEstateContacts() as $contact_data) {
        if (is_null($contact_data['id'])) {
            continue;
        } ?>
        <div class="c-property-details__contact c-contact-person">
            <?php
            $contact_link = isset($contact_data['id'])
                ? $pEstates->getAddressLink($contact_data['id'])
                : '';
            if (!empty($contact_link)) {
                echo '<a href="' .
                    esc_url($contact_link) .
                    '" class="c-contact-person__link">';
            }

            if ($contact_data['imageUrl']) {
                $image = '';
                if (
                    $contact_data['Vorname'] !== '' ||
                    $contact_data['Name'] !== ''
                ) {
                    $image_alt =
                        ($contact_data['Titel']
                            ? $contact_data['Titel'] . ' '
                            : '') .
                        $contact_data['Vorname'] .
                        ' ' .
                        $contact_data['Name'];
                } else {
                    $image_alt = esc_html__('Ansprechpartner', 'oo_theme');
                }

                $image = [
                    'url' => $contact_data['imageUrl'],
                    'alt' => $image_alt,
                ];

                // image width
                $contact_image_width_xs = '543';
                $contact_image_width_sm = '512';
                $contact_image_width_md = '694';
                $contact_image_width_lg = '288';
                $contact_image_width_xl = '352';
                $contact_image_width_xxl = '416';
                $contact_image_width_xxxl = '460';

                if (!empty($image)) {
                    oo_get_template('components', '', 'component-image', [
                        'image' => $image,
                        'picture_class' =>
                            'c-contact-person__picture o-picture',
                        'image_class' => 'c-contact-person__image o-image',
                        'additional_cloudimg_params' =>
                            '&func=crop&gravity=face',
                        'dimensions' => [
                            '575' => [
                                'w' => $contact_image_width_xs,
                                'h' => $contact_image_width_xs,
                            ],
                            '1600' => [
                                'w' => $contact_image_width_xxxl,
                                'h' => $contact_image_width_xxxl,
                            ],
                            '1400' => [
                                'w' => $contact_image_width_xxl,
                                'h' => $contact_image_width_xxl,
                            ],
                            '1200' => [
                                'w' => $contact_image_width_xl,
                                'h' => $contact_image_width_xl,
                            ],
                            '992' => [
                                'w' => $contact_image_width_lg,
                                'h' => $contact_image_width_lg,
                            ],
                            '768' => [
                                'w' => $contact_image_width_md,
                                'h' => $contact_image_width_md,
                            ],
                            '576' => [
                                'w' => $contact_image_width_sm,
                                'h' => $contact_image_width_sm,
                            ],
                        ],
                    ]);
                }
            }
            if (!empty($contact_link)) {
                echo '</a>';
            }

            $salutation = $contact_data['Anrede'];
            $title = $contact_data['Titel'];
            $first_name = $contact_data['Vorname'];
            $last_name = $contact_data['Name'];
            $job_title = $contact_data['jobPosition'];
            $email = $contact_data['Email'];
            $phone = $contact_data['defaultphone'];
            $mobile = $contact_data['mobile'];
            $fax = $contact_data['defaultfax'];
            $company = $contact_data['Zusatz1'];
            $street = $contact_data['Strasse'];
            $postCode = $contact_data['Plz'];
            $town = $contact_data['Ort'];

            // Output name, depending on available fields.
            $name_components = [];

            if ($salutation) {
                $name_components[] = $salutation;
            }
            if ($title) {
                $name_components[] = $title;
            }
            if ($first_name) {
                $name_components[] = $first_name;
            }
            if ($last_name) {
                $name_components[] = $last_name;
            }
            $name_output = join(' ', $name_components);

            if ($name_output) {
                echo '<p class="c-contact-person__name o-headline --h3">';
                echo esc_html($name_output);
                echo '</p>';
            }

            // Output all other configured fields.
            foreach ($address_fields as $field) {
                if (empty($contact_data[$field])) {
                    continue;
                } elseif (is_array($contact_data[$field])) {
                    foreach ($contact_data[$field] as $item) {
                        if (in_array($field, $labels_fields)) {
                            oo_property_field_type($field, $item);
                        } else {
                            if (!empty($contact_data[$field])) {
                                echo '<p class="c-contact-person__data --is-' .
                                    strtolower($field) .
                                    '">';
                                echo esc_html($contact_data[$field]);
                                echo '</p>';
                            }
                        }
                    }
                } else {
                    if (in_array($field, $labels_fields)) {
                        oo_property_field_type($field, $contact_data[$field]);
                    } else {
                        if (!empty($contact_data[$field])) {
                            echo '<p class="c-contact-person__data --is-' .
                                strtolower($field) .
                                '">';
                            echo esc_html($contact_data[$field]);
                            echo '</p>';
                        }
                    }
                }
            }

            $street_output = '';
            if ($street) {
                $street_output = $street;
            }
            $city_components = [];
            if ($postCode) {
                $city_components[] = $postCode;
            }
            if ($town) {
                $city_components[] = $town;
            }
            $city_output = join(' ', $city_components);

            if ($street_output && $city_output) {
                echo '<p class="c-contact-person__address">' .
                    esc_html($street_output) .
                    '<br>' .
                    esc_html($city_output) .
                    '</p>';
            } elseif ($street_output) {
                echo '<p class="c-contact-person__address">' .
                    esc_html($street_output) .
                    '</p>';
            } elseif ($city_output) {
                echo '<p class="c-contact-person__address">' .
                    esc_html($city_output) .
                    '</p>';
            }
            ?>

        </div>
    <?php
    }

    if ($contact_count > 1 == true) {
        echo '</div>';
    }

    echo '</div>';
} ?>
