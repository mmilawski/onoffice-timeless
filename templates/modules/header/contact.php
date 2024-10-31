<?php
/**
 * Module Name: Contact
 * @param $args
 * Get values from the parameter
 */

$id = $args['id'] ?? null;
$content = $args['content'] ?? [];
$location = $args['location'] ?? 'header';

$type = $content['type'] ?? 'company';
$headline = $content['headline'] ?? null;
$data = [];
$data_contact = [];
$module_class = $id ? '--is-' . $id . '-modul' : null;
$headline_class = $headline ? '--has-headline' : null;
$location_class = '--on-' . $location;

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
$is_phone = filter_var($content['show_phone'], FILTER_VALIDATE_BOOLEAN);
$is_fax = filter_var($content['show_fax'], FILTER_VALIDATE_BOOLEAN);
$is_mobile = filter_var($content['show_mobile'], FILTER_VALIDATE_BOOLEAN);
$is_email = filter_var($content['show_email'], FILTER_VALIDATE_BOOLEAN);

$name = $data['name'] ?? null;
$email = $data_contact['email'] ?? null;
$phone_country_code = $data_contact['phone-country'] ?? null;
$phone = $data_contact['phone'] ?? null;
$fax_country_code = $data_contact['fax-country'] ?? null;
$fax = $data_contact['fax'] ?? null;
$mobile_country_code = $data_contact['mobile-country'] ?? null;
$mobile = $data_contact['mobile'] ?? null;

echo '<div class="c-module-contact__wrapper ' .
    ($is_name ? '--has-name' : '') .
    '">';

if ($is_name) {
    echo '<span class="c-module-contact__headline">' . $name . '</span>';
}

if ($is_phone && !empty($phone)) {
    $phone_number =
        ($phone_country_code ? $phone_country_code . '&nbsp;' : '') . $phone;

    echo '<a class="c-module-contact__list c-link --underlined --on-bg-' .
        $location .
        '" href="tel:' .
        str_replace(['&nbsp;', ' '], '', $phone_number) .
        '">';
    echo '<dt class="c-module-contact__label">';
    oo_get_icon('phone');
    echo '</dt>';
    echo '<dd class="c-module-contact__value">' . $phone_number . '</dd>';
    echo '</a>';
}

if ($is_fax && !empty($fax)) {
    $fax_number =
        ($fax_country_code ? $fax_country_code . '&nbsp;' : '') . $fax;

    echo '<a class="c-module-contact__list c-link --underlined --on-bg-' .
        $location .
        '" href="fax:' .
        str_replace(['&nbsp;', ' '], '', $fax_number) .
        '">';
    echo '<dt class="c-module-contact__label">';
    oo_get_icon('fax');
    echo '</dt>';
    echo '<dd class="c-module-contact__value">' . $fax_number . '</dd>';
    echo '</a>';
}

if ($is_mobile && !empty($mobile)) {
    $mobile_number =
        ($mobile_country_code ? $mobile_country_code . '&nbsp;' : '') . $mobile;

    echo '<a class="c-module-contact__list c-link --underlined --on-bg-' .
        $location .
        '" href="tel:' .
        str_replace(['&nbsp;', ' '], '', $mobile_number) .
        '">';
    echo '<dt class="c-module-contact__label">';
    oo_get_icon('mobile');
    echo '</dt>';
    echo '<dd class="c-module-contact__value">' . $mobile_number . '</dd>';
    echo '</a>';
}

if ($is_email && !empty($email)) {
    $email_utf8 = oo_clean_acf_email_utf8($email);
    $email_ascii = oo_clean_acf_email_ascii($email);
    $email_antispam = oo_antispambot(esc_html($email_utf8));
    $mailto_link = antispambot(esc_html($email_ascii));

    echo '<a class="c-module-contact__list c-link --underlined --on-bg-' .
        $location .
        '" href="mailto:' .
        $mailto_link .
        '">';
    echo '<dt class="c-module-contact__label">';
    oo_get_icon('email');
    echo '</dt>';
    echo '<dd class="c-module-contact__value">' . $email_antispam . '</dd>';
    echo '</a>';
}
echo '</div>';
