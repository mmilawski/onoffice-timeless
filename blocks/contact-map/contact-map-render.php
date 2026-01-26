<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$filter = get_field('filter') ?? 'all';
$places = get_field('places') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$map_zoom = $settings['map_zoom'] ?? 'no';

$map_color = get_field('map_color');
if (empty($map_color)) {
    $map_color = $settings['map_color'] ?? 'colored';
}

$marker_color = oo_get_marker_color_for_bg($bg_color);

// Map
$third_parties = get_field('third_parties', 'option') ?? null;
$map_provider = get_option('onoffice-maps-mapprovider') ?? null;

$google_api_key = $third_parties['google']['maps'] ?? null;

$is_google_map = false;
$is_open_street_map = false;
if ($map_provider == 'google-maps') {
    $map_type = 'google-map';
    $is_google_map = true;
} else {
    $map_type = 'open-street-map';
    $is_open_street_map = true;
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
        'orderby' => ['name' => 'ASC'],
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
); ?> class="c-contact-map o-section --<?php echo $bg_color; ?>">
    <div class="c-contact-map__container o-container">
        <div class="c-contact-map__row o-row">
            <?php if (
                !empty($headline['text']) ||
                !empty($text['wysiwyg'])
            ) { ?>
                <div class="c-contact-map__content o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                    <?php if (!empty($headline['text'])) { ?>
                        <?php oo_get_template(
                            'components',
                            '',
                            'component-headline',
                            [
                                'headline' => $headline,
                                'additional_headline_class' =>
                                    'c-contact-map__headline',
                            ],
                        ); ?>
                    <?php } ?>

                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-contact-map__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

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
            <div class="c-contact-map__map c-map --is-<?php echo esc_attr(
                $map_type,
            ); ?> --is-<?php echo esc_attr($map_color); ?>" 
                    data-map-color="<?php echo esc_attr($map_color); ?>" 
                    data-marker-color="<?php echo esc_attr($marker_color); ?>" 
                    style="width: 100%;" 
                    data-max-zoom="<?php echo esc_attr(
                        $map_zoom === 'yes' ? '20' : '15',
                    ); ?>" 
                    data-scroll-zoom="<?php echo esc_attr(
                        $map_zoom === 'yes' ? 'true' : 'false',
                    ); ?>"
                    role="region" 
                    aria-label="<?php echo esc_attr__(
                        'Karte mit Kontaktinformationen',
                        'oo_theme',
                    ); ?>">
                    <?php foreach ($addresses as $address) {

                        $map_field_value = $address['maps'] ?? [];
                        $map = is_array($map_field_value)
                            ? $map_field_value
                            : [];

                        if (
                            $map_type === 'open-street-map' &&
                            function_exists('oo_get_osm_coords')
                        ) {
                            $osm_coords = oo_get_osm_coords(
                                $address['street'] ?? '',
                                $address['zip'] ?? '',
                            );

                            if (is_array($osm_coords)) {
                                $map['lat'] = $osm_coords['lat'] ?? null;
                                $map['lng'] = $osm_coords['lng'] ?? null;
                                $map['place_id'] =
                                    $osm_coords['place_id'] ?? null;
                            }
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

                        if (empty($map) || empty($map_lat) || empty($map_lng)) {
                            continue;
                        }
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
                                                'c-map__button --full-width --on-bg-transparent',
                                        ],
                                    );
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>
