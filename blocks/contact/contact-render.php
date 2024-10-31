<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$filter = get_field('filter') ?? 'all';
$places = get_field('places') ?? [];

$is_address = filter_var(get_field('show_address'), FILTER_VALIDATE_BOOLEAN);
$is_contact_numbers = filter_var(
    get_field('show_contact'),
    FILTER_VALIDATE_BOOLEAN,
);
$is_opening_hours = filter_var(
    get_field('show_openinghours'),
    FILTER_VALIDATE_BOOLEAN,
);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

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

        <div class="c-contact__addresses">
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
                        echo $name . '<br>';
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
                            str_replace(['&nbsp;', ' '], '', $phone_number) .
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
                            str_replace(['&nbsp;', ' '], '', $fax_number) .
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
                            str_replace(['&nbsp;', ' '], '', $mobile_number) .
                            '">' .
                            $mobile_number .
                            '</a></dd>';
                        echo '</dl>';
                    }

                    if (!empty($email)) {
                        $email_utf8 = oo_clean_acf_email_utf8($email);
                        $email_ascii = oo_clean_acf_email_ascii($email);
                        $email_antispam = oo_antispambot(esc_html($email_utf8));
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
                    echo '<dl class="c-contact-card__list --is-opening-hours">';
                    foreach ($opening_hours as $item) {
                        echo '<dt class="c-contact-card__label">' .
                            $item['days'] .
                            '</dt>';
                        echo '<dd class="c-contact-card__value">' .
                            $item['times'] .
                            '</dd>';
                    }
                    echo '</dl>';
                }
                echo '</div>';
            } ?>
        </div>
    </div>
</section>
