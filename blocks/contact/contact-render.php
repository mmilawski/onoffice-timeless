<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$filter = get_field('filter') ?? 'all';
$places = get_field('places') ?? [];
$position_map = get_field('position_map') ?? 'right';

$is_address = filter_var(get_field('show_address'), FILTER_VALIDATE_BOOLEAN);
$is_contact_numbers = filter_var(
    get_field('show_contact'),
    FILTER_VALIDATE_BOOLEAN,
);
$is_opening_hours = filter_var(
    get_field('show_openinghours'),
    FILTER_VALIDATE_BOOLEAN,
);
$is_map = filter_var(get_field('show_map'), FILTER_VALIDATE_BOOLEAN);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$map_color = $settings['map_color'] ?? 'colored';

// Marker color
$colors = get_field('colors', 'option') ?? [];
$marker_color = match ($bg_color) {
    'bg-transparent' => !empty($colors['global']['primary'])
        ? $colors['global']['primary']
        : 'currentColor',
    'bg-light' => !empty($colors['variations']['light']['primary'])
        ? $colors['variations']['light']['primary']
        : (!empty($colors['global']['primary'])
            ? $colors['global']['primary']
            : 'currentColor'),
    'bg-dark' => !empty($colors['variations']['dark']['primary'])
        ? $colors['variations']['dark']['primary']
        : (!empty($colors['global']['primary'])
            ? $colors['global']['primary']
            : 'currentColor'),
    'bg-primary' => !empty($colors['variations']['primary']['primary'])
        ? $colors['variations']['primary']['primary']
        : (!empty($colors['global']['primary'])
            ? $colors['global']['primary']
            : 'currentColor'),
    'bg-secondary' => !empty($colors['variations']['secondary']['primary'])
        ? $colors['variations']['secondary']['primary']
        : (!empty($colors['global']['primary'])
            ? $colors['global']['primary']
            : 'currentColor'),
    default => 'currentColor',
};

// Map
$third_parties = get_field('third_parties', 'option') ?? null;
$map_provider = get_option('onoffice-maps-mapprovider') ?? null;

$google_api_key = $third_parties['google']['maps'] ?? null;

$is_google_map = false;
$is_open_street_map = false;
if ($is_map) {
    if ($map_provider == 'google-maps') {
        $map_type = 'google-map';
        $is_google_map = true;
    } else {
        $map_type = 'open-street-map';
        $is_open_street_map = true;
    }
}

// Adresses
$addresses = [];

global $post;

if (
    $filter === 'all' ||
    $filter === 'company' ||
    $filter === 'company_and_individual_places'
) {
    $addresses = [get_field('company', 'option') ?? []];
}

if ($filter === 'all' || $filter === 'places') {
    $query_places = new WP_Query([
        'posts_per_page' => -1,
        'post_type' => 'oo_places',
        'post_status' => ['publish'],
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
    if ($query_places->have_posts()) {
        while ($query_places->have_posts()) {
            $query_places->the_post();
            $place = get_field('place', $post->ID) ?? [];
            array_push($addresses, $place);
        }
    }
    wp_reset_postdata();
}

if (
    ($filter === 'company_and_individual_places' ||
        $filter === 'individual_places') &&
    !empty($places)
) {
    $query_places = new WP_Query([
        'posts_per_page' => -1,
        'post_type' => 'oo_places',
        'post_status' => ['publish'],
        'post__in' => array_merge($places),
        'orderby' => 'post__in',
    ]);
    if ($query_places->have_posts()) {
        while ($query_places->have_posts()) {
            $query_places->the_post();
            $place = get_field('place', $post->ID) ?? [];
            array_push($addresses, $place);
        }
    }
    wp_reset_postdata();
}

if (empty($addresses)) {
    return;
}
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-contact o-section --<?php echo $bg_color; ?>">
    <div class="c-contact__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-contact__content o-row">
                <?php if (!empty($headline['text'])) { ?>
					<?php oo_get_template('components', '', 'component-headline', [
         'headline' => $headline,
         'additional_headline_class' =>
             'c-contact__headline o-col-12 o-col-xl-8',
     ]); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-contact__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="c-contact__wrapper o-row">
        <?php if (!$is_map) { ?>
            <div class="c-contact__addresses o-col-12 o-col-xl-8"> 
                <?php foreach ($addresses as $address) {
                    $name = $address['name'] ?? null;
                    $street = $address['street'] ?? null;
                    $zip = $address['zip'] ?? null;
                    $country = $address['country']['native'] ?? null;
                    $opening_hours = $address['opening_hours'] ?? null;

                    $contact = $address['contact'] ?? [];
                    $email = $contact['email'] ?? null;
                    $phone_country_code = $contact['phone-country'] ?? null;
                    $phone = $contact['phone'] ?? null;
                    $fax_country_code = $contact['fax-country'] ?? null;
                    $fax = $contact['fax'] ?? null;
                    $mobile_country_code = $contact['mobile-country'] ?? null;
                    $mobile = $contact['mobile'] ?? null;

                    echo '<div class="c-contact-card">';
                    if ($is_address) {
                        echo '<p class="c-contact-card__data --is-address">';
                        if (!empty($name)) {
                            echo '<span class="c-contact-card__title">' .
                                $name .
                                '</span><br>';
                        }
                        if (!empty($street)) {
                            echo $street . '<br>';
                        }

                        if (!empty($zip)) {
                            echo $zip . '<br>';
                        }

                        if (!empty($country)) {
                            echo $country;
                        }
                        echo '</p>';
                    }

                    // Contact Info
                    if (
                        $is_contact_numbers &&
                        (!empty($phone) ||
                            !empty($fax) ||
                            !empty($mobile) ||
                            !empty($email))
                    ) {
                        echo '<div class="c-contact-card__data --is-contact">';
                        if (!empty($phone)) {
                            $phone_number =
                                ($phone_country_code
                                    ? $phone_country_code . '&nbsp;'
                                    : '') . $phone;
                            echo '<dl class="c-contact-card__list">';
                            echo '<dt class="c-contact-card__label">' .
                                __('Tel.:', 'oo_theme') .
                                '</dt>';
                            echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                $bg_color .
                                '" href="tel:' .
                                oo_clean_link_number($phone_number) .
                                '">' .
                                $phone_number .
                                '</a></dd>';
                            echo '</dl>';
                        }

                        if (!empty($fax)) {
                            $fax_number =
                                ($fax_country_code
                                    ? $fax_country_code . '&nbsp;'
                                    : '') . $fax;
                            echo '<dl class="c-contact-card__list">';
                            echo '<dt class="c-contact-card__label">' .
                                __('Fax:', 'oo_theme') .
                                '</dt>';
                            echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                $bg_color .
                                '" href="tel:' .
                                oo_clean_link_number($fax_number) .
                                '">' .
                                $fax_number .
                                '</a></dd>';
                            echo '</dl>';
                        }

                        if (!empty($mobile)) {
                            $mobile_number =
                                ($mobile_country_code
                                    ? $mobile_country_code . '&nbsp;'
                                    : '') . $mobile;
                            echo '<dl class="c-contact-card__list">';
                            echo '<dt class="c-contact-card__label">' .
                                __('Mobile:', 'oo_theme') .
                                '</dt>';
                            echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                $bg_color .
                                '" href="tel:' .
                                oo_clean_link_number($mobile_number) .
                                '">' .
                                $mobile_number .
                                '</a></dd>';
                            echo '</dl>';
                        }

                        if (!empty($email)) {
                            $email_utf8 = oo_clean_acf_email_utf8($email);
                            $email_ascii = oo_clean_acf_email_ascii($email);
                            $email_antispam = oo_antispambot(
                                esc_html($email_utf8),
                            );
                            $mailto_link = antispambot(esc_html($email_ascii));

                            echo '<dl class="c-contact-card__list">';
                            echo '<dt class="c-contact-card__label">' .
                                __('E-Mail:', 'oo_theme') .
                                '</dt>';
                            echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                $bg_color .
                                '" href="mailto:' .
                                $mailto_link .
                                '">' .
                                $email_antispam .
                                '</a></dd>';
                            echo '</dl>';
                        }
                        echo '</div>';
                    }

                    // Opening Hours
                    if ($is_opening_hours && is_array($opening_hours)) {
                        echo '<div class="c-contact-card__data --is-opening-hours">';
                        foreach ($opening_hours as $item) {
                            echo '<dl class="c-contact-card__list --is-opening-hours"><dt class="c-contact-card__label">' .
                                $item['days'] .
                                '</dt>';
                            echo '<dd class="c-contact-card__value">' .
                                $item['times'] .
                                '</dd></dl>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                } ?>
            </div>

            <?php } else { ?>
                <?php if ($is_google_map) { ?>
                    <?php
                    wp_enqueue_script('oo-google-map-script');
                    wp_enqueue_script('oo-init-google-map-script');
                    wp_enqueue_script('oo-google-map-marker-cluster-script');
                    ?>
                <?php } ?>
                <?php if ($is_open_street_map) { ?>
                    <?php
                    wp_enqueue_style('oo-leaflet-style');
                    wp_enqueue_style('oo-leaflet-marker-cluster-style');
                    wp_enqueue_style('oo-leaflet-marker-cluster-default-style');
                    wp_enqueue_script('oo-leaflet-script');
                    wp_enqueue_script('oo-init-open-street-map-script');
                    wp_enqueue_script('oo-init-open-street-map-marker-cluster');
                    ?>
                <?php } ?>
                <div class="c-contact__addresses o-col-12 o-col-xl-8 --is-map"> 
                    <?php foreach ($addresses as $address) {

                        $name = $address['name'] ?? null;
                        $street = $address['street'] ?? null;
                        $zip = $address['zip'] ?? null;
                        $country = $address['country']['native'] ?? null;
                        $opening_hours = $address['opening_hours'] ?? null;

                        $contact = $address['contact'] ?? [];
                        $email = $contact['email'] ?? null;
                        $phone_country_code = $contact['phone-country'] ?? null;
                        $phone = $contact['phone'] ?? null;
                        $fax_country_code = $contact['fax-country'] ?? null;
                        $fax = $contact['fax'] ?? null;
                        $mobile_country_code =
                            $contact['mobile-country'] ?? null;
                        $mobile = $contact['mobile'] ?? null;

                        echo '<div class="c-contact-card --is-map' .
                            ($position_map === 'left'
                                ? ' --position-left'
                                : ($position_map === 'center'
                                    ? ' --position-alternating'
                                    : '')) .
                            '">';
                        if ($is_address) {
                            echo '<p class="c-contact-card__data --is-address">';
                            if (!empty($name)) {
                                echo '<span class="c-contact-card__title">' .
                                    $name .
                                    '</span><br>';
                            }
                            if (!empty($street)) {
                                echo $street . '<br>';
                            }

                            if (!empty($zip)) {
                                echo $zip . '<br>';
                            }

                            if (!empty($country)) {
                                echo $country;
                            }
                            echo '</p>';
                        }

                        // Contact Info
                        if (
                            $is_contact_numbers &&
                            (!empty($phone) ||
                                !empty($fax) ||
                                !empty($mobile) ||
                                !empty($email))
                        ) {
                            echo '<div class="c-contact-card__data --is-contact">';
                            if (!empty($phone)) {
                                $phone_number =
                                    ($phone_country_code
                                        ? $phone_country_code . '&nbsp;'
                                        : '') . $phone;
                                echo '<dl class="c-contact-card__list">';
                                echo '<dt class="c-contact-card__label">' .
                                    __('Tel.:', 'oo_theme') .
                                    '</dt>';
                                echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                    $bg_color .
                                    '" href="tel:' .
                                    oo_clean_link_number($phone_number) .
                                    '">' .
                                    $phone_number .
                                    '</a></dd>';
                                echo '</dl>';
                            }

                            if (!empty($fax)) {
                                $fax_number =
                                    ($fax_country_code
                                        ? $fax_country_code . '&nbsp;'
                                        : '') . $fax;
                                echo '<dl class="c-contact-card__list">';
                                echo '<dt class="c-contact-card__label">' .
                                    __('Fax:', 'oo_theme') .
                                    '</dt>';
                                echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                    $bg_color .
                                    '" href="tel:' .
                                    oo_clean_link_number($fax_number) .
                                    '">' .
                                    $fax_number .
                                    '</a></dd>';
                                echo '</dl>';
                            }

                            if (!empty($mobile)) {
                                $mobile_number =
                                    ($mobile_country_code
                                        ? $mobile_country_code . '&nbsp;'
                                        : '') . $mobile;
                                echo '<dl class="c-contact-card__list">';
                                echo '<dt class="c-contact-card__label">' .
                                    __('Mobile:', 'oo_theme') .
                                    '</dt>';
                                echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                    $bg_color .
                                    '" href="tel:' .
                                    oo_clean_link_number($mobile_number) .
                                    '">' .
                                    $mobile_number .
                                    '</a></dd>';
                                echo '</dl>';
                            }

                            if (!empty($email)) {
                                $email_utf8 = oo_clean_acf_email_utf8($email);
                                $email_ascii = oo_clean_acf_email_ascii($email);
                                $email_antispam = oo_antispambot(
                                    esc_html($email_utf8),
                                );
                                $mailto_link = antispambot(
                                    esc_html($email_ascii),
                                );

                                echo '<dl class="c-contact-card__list">';
                                echo '<dt class="c-contact-card__label">' .
                                    __('E-Mail:', 'oo_theme') .
                                    '</dt>';
                                echo '<dd class="c-contact-card__value"><a class="c-link --text-color --on-' .
                                    $bg_color .
                                    '" href="mailto:' .
                                    $mailto_link .
                                    '">' .
                                    $email_antispam .
                                    '</a></dd>';
                                echo '</dl>';
                            }
                            echo '</div>';
                        }

                        // Opening Hours
                        if ($is_opening_hours && is_array($opening_hours)) {
                            echo '<div class="c-contact-card__data --is-opening-hours">';
                            foreach ($opening_hours as $item) {
                                echo '<dl class="c-contact-card__list --is-opening-hours"><dt class="c-contact-card__label">' .
                                    $item['days'] .
                                    '</dt>';
                                echo '<dd class="c-contact-card__value">' .
                                    $item['times'] .
                                    '</dd></dl>';
                            }
                            echo '</div>';
                        }
                        echo '<div class="c-contact-card__map c-map --is-' .
                            $map_type .
                            ' --is-' .
                            $map_color .
                            ($position_map === 'left'
                                ? ' --position-left'
                                : ($position_map === 'center'
                                    ? ' --position-alternating'
                                    : '')) .
                            '" data-map-color="' .
                            $map_color .
                            '" data-marker-color="' .
                            $marker_color .
                            '" style="width: 100%;" data-max-zoom="15" role="region" aria-label="' .
                            esc_html__(
                                'Karte mit Kontaktinformationen',
                                'oo_theme',
                            ) .
                            '">';
                        $map = $address['maps'] ?? [];
                        if (
                            $map_type == 'open-street-map' &&
                            !empty($address['street']) &&
                            !empty($address['zip'])
                        ) {
                            $q = $address['street'] . ' ' . $address['zip'];
                            $map['street_name'] = $address['street'];
                            $url =
                                'https://nominatim.openstreetmap.org/search?q=' .
                                urlencode($q) .
                                '&format=json&polygon=1&addressdetails=1';
                            $options = [
                                'http' => [
                                    'method' => 'GET',
                                    'header' =>
                                        "Accept-language: de\r\nUser-Agent: Mozilla/5.0 \r\n",
                                ],
                            ];
                            $context = stream_context_create($options);
                            $response = file_get_contents(
                                $url,
                                false,
                                $context,
                            );
                            if ($response === false) {
                                error_log(
                                    'OpenStreetMap-Request failed: ' .
                                        $response,
                                );
                            }
                            if ($response === [] || $response === '[]') {
                                error_log(
                                    'OpenStreetMap-Request no Coordinates found',
                                );
                            }
                            $data = json_decode($response);
                            if (
                                empty($data[0]) ||
                                !isset($data[0]->lat) ||
                                !isset($data[0]->lon)
                            ) {
                                error_log(
                                    'OpenStreetMap-Request no Coordinates found',
                                );
                            }
                            $map['lat'] = $data[0]->lat;
                            $map['lng'] = $data[0]->lon;
                            $map['place_id'] = $data[0]->place_id;
                        }
                        $map_lat = $map['lat'] ?? null;
                        $map_lng = $map['lng'] ?? null;
                        $map_name = $map['name'] ?? null;
                        $map_street_number = $map['street_number'] ?? null;
                        $map_street_name = $map['street_name'] ?? null;
                        $map_street =
                            $map_street_name .
                                ($map_street_number
                                    ? ' ' . $map_street_number
                                    : '') ??
                            null;
                        $map_city = $map['city'] ?? null;
                        $map_country = $map['country'] ?? null;

                        $place_id = $map['place_id'] ?? null;
                        $name = $address['name']
                            ? $address['name']
                            : $map_name ?? null;
                        $street = $address['street']
                            ? $address['street']
                            : $map_street ?? null;
                        $zip = $address['zip']
                            ? $address['zip']
                            : $map_city ?? null;
                        $country = $address['country']['native']
                            ? $address['country']['native']
                            : $map_country ?? null;
                        ?>

                        <div class="c-map__marker" data-lat="<?php echo esc_attr(
                            $map_lat,
                        ); ?>" data-lng="<?php echo esc_attr(
    $map_lng,
); ?>" data-aria-label="<?php echo oo_get_map_marker_aria_label(
    [
        'title' => $name,
        'street' => $street,
        'zip' => $zip,
        'city' => $map_city,
        'country' => $country,
    ],
    'Immobilienstandort',
); ?>">
                            <div class="c-map__info --bg-transparent">
                                <?php
                                if (!empty($name)) {
                                    echo '<h3 class="c-map__headline o-headline --h3">' .
                                        $name .
                                        '</h3>';
                                }
                                if (
                                    !empty($street) ||
                                    !empty($zip) ||
                                    !empty($country)
                                ) {
                                    echo '<p class="c-map__text">';
                                    if (!empty($street)) {
                                        echo $street . '<br>';
                                    }
                                    if (!empty($zip)) {
                                        echo $zip . '<br>';
                                    }
                                    if (!empty($country)) {
                                        echo $country;
                                    }
                                    echo '</p>';
                                }
                                if (
                                    !empty($place_id) ||
                                    $street ||
                                    $zip ||
                                    $country
                                ) {
                                    $full_address = urlencode(
                                        $street . ', ' . $zip . ', ' . $country,
                                    );
                                    $button = [
                                        [
                                            'link' => [
                                                'title' => esc_html__(
                                                    'Zur Routenplanung',
                                                    'oo_theme',
                                                ),
                                                'url' =>
                                                    'https://www.google.com/maps/dir/?api=1&destination=' .
                                                    $full_address,
                                                'target' => '_blank',
                                            ],
                                        ],
                                    ];

                                    oo_get_template(
                                        'components',
                                        '',
                                        'component-buttons',
                                        [
                                            'buttons' => $button,
                                            'additional_container_class' =>
                                                'c-map__button-wrapper',
                                            'additional_button_class' =>
                                                'c-map__button --small-corners --full-width --on-bg-transparent',
                                        ],
                                    );
                                }

                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';

                    } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
