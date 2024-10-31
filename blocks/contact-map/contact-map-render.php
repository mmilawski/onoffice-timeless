<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$filter = get_field('filter') ?? 'all';
$places = get_field('places') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$map_color = $settings['map_color'] ?? 'colored';

// Marker color
$colors = get_field('colors', 'option') ?? null;
$primary_color = $colors['global']['primary'] ?? 'currentColor';
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
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-contact-map__content o-row">
                <?php if (!empty($headline['text'])) { ?>
          <?php oo_get_template('components', '', 'component-headline', [
              'headline' => $headline,
              'additional_headline_class' =>
                  'c-contact-map__headline o-col-12 o-col-xl-8',
          ]); ?>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-contact-map__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php  ?>
        <?php if ($is_google_map) { ?>
            <?php
            wp_enqueue_script('oo-google-map-script');
            wp_enqueue_script('oo-init-google-map-script');
            ?>
        <?php } ?>
        <?php if ($is_open_street_map) { ?>
            <?php
            wp_enqueue_style('oo-leaflet-style');
            wp_enqueue_script('oo-leaflet-script');
            wp_enqueue_script('oo-init-open-street-map-script');
            ?>
        <?php } ?>
            <div class="c-contact-map__map c-map --is-<?php echo $map_type; ?> --is-<?php echo $map_color; ?>" data-map-color="<?php echo $map_color; ?>" data-marker-color="<?php echo $primary_color; ?>" style="width: 100%;">
                <?php foreach ($addresses as $address) {

                    $map = $address['maps'] ?? [];
                    if (
                        $map_type == 'open-street-map' &&
                        !empty($address['street']) &&
                        !empty($address['zip'])
                    ) {
                        $q =
                            str_replace(
                                ' ',
                                '+',
                                urlencode($address['street']),
                            ) .
                            '+' .
                            str_replace(' ', '+', urlencode($address['zip']));
                        $map['street_name'] = $address['street'];
                        $url =
                            'https://nominatim.openstreetmap.org/search?q=' .
                            $q .
                            '&format=json&polygon=1&addressdetails=1';
                        $options = [
                            'http' => [
                                'method' => 'GET',
                                'header' =>
                                    "Accept-language: de\r\nUser-Agent: Mozilla/5.0 \r\n",
                            ],
                        ];
                        $context = stream_context_create($options);
                        $response = file_get_contents($url, false, $context);
                        if ($response === false) {
                            error_log(
                                'OpenStreetMap-Request failed: ' . $response,
                            );
                            return;
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
                            return;
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

                    if (empty($map)) {
                        continue;
                    }
                    ?>
                    <div class="c-map__marker" data-lat="<?php echo esc_attr(
                        $map_lat,
                    ); ?>" data-lng="<?php echo esc_attr($map_lng); ?>">
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
                            if (!empty($place_id)) {
                                echo '<p class="c-map__link-wrapper">';
                                echo '<a class="c-map__link c-link --has-icon --chevron-right --on-bg-transparent" href="https://www.google.com/maps/place/?q=place_id:' .
                                    $place_id .
                                    '" target="_blank" rel="noopener noreferrer">';
                                echo esc_html('Zur Routenplanung', 'oo_theme');
                                echo oo_get_icon('chevron-right');
                                echo '</a>';
                                echo '</p>';
                            }
                            ?>
                        </div>
                    </div>
                <?php
                } ?>
            </div>
        <?php  ?>
    </div>
</section>
