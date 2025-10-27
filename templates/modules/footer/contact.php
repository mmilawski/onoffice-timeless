<?php
/**
 * Module Name: Contact
 * @param $args
 * Get values from the parameter
 */

$content = $args['content'] ?? [];
$location = $args['location'] ?? 'footer';
$type = $content['type'] ?? 'company';

$headline = $content['headline'] ?? null;
$data = [];
$data_contact = [];

if ($type === 'company') {
    $data = get_field('company', 'option') ?? [];
    $data_contact = $data['contact'] ?? [];
} elseif ($type === 'place' && !empty($content['place'])) {
    $query_places = new WP_Query([
        'post_type' => 'oo_places',
        'post_status' => ['publish'],
        'post__in' => [$content['place']],
        'orderby' => 'post__in',
    ]);
    if ($query_places->have_posts()) {
        while ($query_places->have_posts()) {
            $query_places->the_post();
            $data = get_field('place', $post->ID) ?? [];
            $data_contact = $data['contact'] ?? [];
        }
    }
    wp_reset_postdata();
}

if (empty($data)) {
    return;
}

$is_name = filter_var($content['show_name'], FILTER_VALIDATE_BOOLEAN);
$is_address = filter_var($content['show_address'], FILTER_VALIDATE_BOOLEAN);
$is_contact_numbers = filter_var(
    $content['show_contact'],
    FILTER_VALIDATE_BOOLEAN,
);
$is_opening_hours = filter_var(
    $content['show_openinghours'],
    FILTER_VALIDATE_BOOLEAN,
);

$name = $data['name'] ?? null;
$street = $data['street'] ?? null;
$zip = $data['zip'] ?? null;
$country = $data['country']['native'] ?? null;
$opening_hours = $data['opening_hours'] ?? null;
$email = $data_contact['email'] ?? null;
$phone_country_code = $data_contact['phone-country'] ?? null;
$phone = $data_contact['phone'] ?? null;
$fax_country_code = $data_contact['fax-country'] ?? null;
$fax = $data_contact['fax'] ?? null;
$mobile_country_code = $data_contact['mobile-country'] ?? null;
$mobile = $data_contact['mobile'] ?? null;

if (!empty($headline)):
    oo_get_template('components', '', 'component-headline', [
        'headline' => [
            'text' => strip_tags($headline),
            'size' => 'h2',
        ],
        'additional_headline_class' => 'c-module-contact__headline',
    ]);
endif;

echo '<div class="c-module-contact__wrapper">';
if ($is_address || $is_name) {
    echo '<p class="c-module-contact__data --is-address">';
    if ($is_name && !empty($name)) {
        echo $name . '<br>';
    }

    if ($is_address) {
        if (!empty($street)) {
            echo esc_html($street) . '<br>';
        }

        if (!empty($zip)) {
            echo esc_html($zip) . '<br>';
        }

        if (!empty($country)) {
            echo esc_html($country);
        }
    }
    echo '</p>';
}

// Contact Info
if ($is_contact_numbers) {
    echo '<div class="c-module-contact__data --is-contact">';
    if (!empty($phone)) {
        $phone_number =
            ($phone_country_code
                ? esc_html($phone_country_code) . '&nbsp;'
                : '') . esc_html($phone);
        echo '<dl class="c-module-contact__list">';
        echo '<dt class="c-module-contact__label">' .
            __('Tel.:', 'oo_theme') .
            '</dt>';
        echo '<dd class="c-module-contact__value"><a class="c-link --text-color --on-bg-' .
            $location .
            '" href="tel:' .
            oo_clean_link_number($phone_number) .
            '">' .
            $phone_number .
            '
            </a></dd>';
        echo '</dl>';
    }

    if (!empty($fax)) {
        $fax_number =
            ($fax_country_code ? esc_html($fax_country_code) . '&nbsp;' : '') .
            esc_html($fax);
        echo '<dl class="c-module-contact__list">';
        echo '<dt class="c-module-contact__label">' .
            __('Fax:', 'oo_theme') .
            '</dt>';
        echo '<dd class="c-module-contact__value"><a class="c-link --text-color --on-bg-' .
            $location .
            '" href="tel:' .
            oo_clean_link_number($fax_number) .
            '">' .
            $fax_number .
            '
            </a></dd>';
        echo '</dl>';
    }

    if (!empty($mobile)) {
        $mobile_number =
            ($mobile_country_code
                ? esc_html($mobile_country_code) . '&nbsp;'
                : '') . esc_html($mobile);
        echo '<dl class="c-module-contact__list">';
        echo '<dt class="c-module-contact__label">' .
            __('Mobile:', 'oo_theme') .
            '</dt>';
        echo '<dd class="c-module-contact__value"><a class="c-link --text-color --on-bg-' .
            $location .
            '" href="tel:' .
            oo_clean_link_number($mobile_number) .
            '">' .
            $mobile_number .
            '
            </a></dd>';
        echo '</dl>';
    }

    if (!empty($email)) {
        $email_utf8 = oo_clean_acf_email_utf8($email);
        $email_ascii = oo_clean_acf_email_ascii($email);
        $email_antispam = oo_antispambot(esc_html($email_utf8));
        $mailto_link = antispambot(esc_html($email_ascii));

        echo '<dl class="c-module-contact__list">';
        echo '<dt class="c-module-contact__label">' .
            __('E-Mail:', 'oo_theme') .
            '</dt>';
        echo '<dd class="c-module-contact__value"><a class="c-link --text-color --on-bg-' .
            $location .
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
    echo '<dl class="c-module-contact__list --is-opening-hours">';
    foreach ($opening_hours as $item) {
        echo '<dt class="c-module-contact__label">' .
            esc_html($item['days']) .
            '</dt>';
        echo '<dd class="c-module-contact__value">' .
            esc_html($item['times']) .
            '</dd>';
    }
    echo '</dl>';
}
echo '</div>';
