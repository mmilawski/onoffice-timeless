<?php
/**
 *
 *    Copyright (C) 2020  onOffice GmbH
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

use onOffice\WPlugin\EstateDetail;
use onOffice\WPlugin\Favorites;

$dont_echo = [
    'objekttitel',
    'vermarktungsstatus',
    'objektbeschreibung',
    'lage',
    'ausstatt_beschr',
    'sonstige_angaben',
];
$energy_fields = [
    'endenergiebedarf',
    'energieverbrauchskennwert',
    'energieausweistyp',
    'energieausweis_gueltig_bis',
    'energyClass',
    'energietraeger',
    'energiepassAusstelldatum',
    'nutzungsart',
    'erschliessung',
    'energieausweisBaujahr',
    'endenergiebedarfStrom',
    'endenergiebedarfWaerme',
    'endenergieverbrauchStrom',
    'endenergieverbrauchWaerme',
    'warmwasserEnthalten',
];

function oo_feature_item($feature)
{
    $value = $feature['value'];

    echo '<dl class="c-property-features__criteria">';
    echo '<dt class="c-property-features__label">';
    esc_html_e($feature['label']);
    echo '</dt>';
    echo '<dd class="c-property-features__value">';

    if (is_array($value)) {
        esc_html_e(implode(', ', $value));
    } else {
        echo esc_html($value);
    }

    echo '</dd>';
    echo '</dl>';
}

/** @var EstateDetail $pEstates */

$pEstates->resetEstateIterator();
while ($current_property = $pEstates->estateIterator()) {

    $property_id = $pEstates->getCurrentMultiLangEstateMainId();
    $raw_values = $pEstates->getRawValues();
    $is_reference = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['referenz'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
    );

    // pictures
    $property_pictures = $pEstates->getEstatePictures();
    $picture_count = is_array($property_pictures)
        ? count($property_pictures)
        : 0;

    // videos
    $property_movie_players = $pEstates->getMovieEmbedPlayers();
    $property_movie_links = $pEstates->getEstateMovieLinks();

    // ogulo
    $property_ogulo_embeds = $pEstates->getLinkEmbedPlayers('ogulo');
    $property_ogulo_links = $pEstates->getEstateLinks('ogulo');

    // objects
    $property_object_embeds = $pEstates->getLinkEmbedPlayers('object');
    $property_object_links = $pEstates->getEstateLinks('object');

    // Links
    $property_link_embeds = $pEstates->getLinkEmbedPlayers('link');
    $property_links = $pEstates->getEstateLinks('link');

    $has_video =
        !empty($property_movie_players) || !empty($property_movie_links)
            ? true
            : false;

    $has_ogulo =
        !empty($property_ogulo_embeds) || !empty($property_ogulo_links)
            ? true
            : false;

    $has_object =
        !empty($property_object_embeds) || !empty($property_object_links)
            ? true
            : false;

    $has_links =
        !empty($property_link_embeds) || !empty($property_links) ? true : false;

    // map
    ob_start();
    require 'map/map.php';
    $map = ob_get_clean();

    // status
    $property_status = $current_property['vermarktungsstatus'];

    // link
    $property_link = esc_url($pEstates->getEstateLink());

    // multiobject
    $multiobjekt = false;

    if ($current_property['stammobjekt'] == 'Ja') {
        $multiobjekt = true;
    }

    // form
    $shortcode_form = $pEstates->getShortCodeForm();

    // Check if Accordion has content
    $has_tabs_content =
        !empty($current_property['objektbeschreibung']) ||
        !empty($current_property['ausstatt_beschr']) ||
        !empty($current_property['energieausweistyp']) ||
        !empty($current_property['lage']) ||
        !empty($has_video) ||
        !empty($has_ogulo) ||
        !empty($has_object) ||
        !empty($has_links) ||
        !empty($current_property['sonstige_angaben']);

    $tab_fields = [
        'objektbeschreibung',
        'ausstatt_beschr',
        'energieausweistyp',
        'lage',
        'video',
        'ogulo',
        'object',
        'links',
        'sonstige_angaben',
    ];

    $tabs = array_filter(
        array_map(function ($field) use (
            $current_property,
            $pEstates,
            $energy_fields,
            $has_video,
            $has_ogulo,
            $has_object,
            $has_links,
        ) {
            if ($field == 'energieausweistyp') {
                // Check if any of the energy fields has value
                $has_energy_fields = array_reduce(
                    $energy_fields,
                    function ($carry, $energy_field) use ($current_property) {
                        return $carry ||
                            !empty($current_property[$energy_field]);
                    },
                    false,
                );

                // If no energy fields have value, return null to skip this tab
                if (!$has_energy_fields) {
                    return null;
                }

                return [
                    'key' => $field,
                    'title' => __('Energieausweis', 'oo_theme'),
                    'content' => $energy_fields,
                ];
            }
            if ($field == 'video' && !empty($has_video)) {
                return [
                    'key' => $field,
                    'title' => __('Video', 'oo_theme'),
                    'content' => $has_video,
                ];
            }
            if ($field == 'ogulo' && !empty($has_ogulo)) {
                return [
                    'key' => $field,
                    'title' => __('3D-Rundgang', 'oo_theme'),
                    'content' => $has_ogulo,
                ];
            }

            if ($field == 'object' && !empty($has_object)) {
                return [
                    'key' => $field,
                    'title' => __('Objekt', 'oo_theme'),
                    'content' => $has_object,
                ];
            }

            if ($field == 'links' && !empty($has_links)) {
                return [
                    'key' => $field,
                    'title' => __('Link', 'oo_theme'),
                    'content' => $has_links,
                ];
            }

            if (!empty($current_property[$field])) {
                return [
                    'key' => $field,
                    'title' => $pEstates->getFieldLabel($field),
                    'content' => $current_property[$field],
                ];
            }
            return null;
        }, $tab_fields),
    );

    // fields
    $tab_fields_counter = 0;
    $tab_fields_more = 15;
    $property_features = [];
    foreach ($current_property as $field => $value) {
        if (
            (is_numeric($value) && 0 == $value) ||
            $value == '0000-00-00' ||
            $value == '0.00' ||
            $value == '' ||
            empty($value) ||
            in_array($field, $dont_echo)
        ) {
            continue;
        }

        if (in_array($field, $energy_fields)) {
            $energy_fields_available = true;
        }

        if (!in_array($field, $energy_fields)) {
            $fields_available = true;
        }
    }
    ?>

    <section class="c-property-details o-section --bg-transparent">
        <div class="c-property-details__container o-container">
            <div class="c-property-details__row o-row">
                <div class="c-property-details__banner o-col-12">
                    <?php if ($picture_count > 0) {

                        // Load Lightbox
                        wp_enqueue_script('oo-glightbox-script');
                        wp_enqueue_style('oo-glightbox-style');
                        ?>
                        <div class="c-property-details__gallery c-slider splide --is-property-details-slider" data-splide='{"perPage":1,"perMove":1,"gap":0,"lazyLoad":"nearby","snap":true}'>
                            <div class="c-slider__track splide__track">
                            
                                <div class="c-slider__list splide__list">
                                    <?php foreach ($property_pictures as $id) {

                                        $picture_values = $pEstates->getEstatePictureValues(
                                            $id,
                                        );

                                        // Image alt text
                                        if ($picture_values['title'] == true) {
                                            $image_alt = esc_html(
                                                $picture_values['title'],
                                            );
                                        } else {
                                            $image_alt = esc_html(
                                                'Immobilienbild',
                                                'oo_theme',
                                            );
                                        }

                                        // Image width
                                        $image_width_xs = '543';
                                        $image_width_sm = '512';
                                        $image_width_md = '694';
                                        $image_width_lg = '608';
                                        $image_width_xl = '736';
                                        $image_width_xxl = '864';
                                        $image_width_xxxl = '952';

                                        $image = [
                                            'url' => $pEstates->getEstatePictureUrl(
                                                $id,
                                            ),
                                            'alt' => $image_alt,
                                        ];

                                        //  Lightbox Cloud Image
                                        $lightbox_image_options =
                                            '&?force_format=webp&?org_if_sml=1';
                                        $lightbox_url =
                                            'https://acnaayzuen.cloudimg.io/v7/' .
                                            $image['url'] .
                                            $lightbox_image_options;

                                        $lightbox_image_size_list =
                                            [
                                                [
                                                    'id' => 'mobile',
                                                    'breakpoint' => '767',
                                                    'image_size' => '767',
                                                ],
                                                [
                                                    'id' => 'tablet',
                                                    'breakpoint' => '768',
                                                    'image_size' => '1200',
                                                ],
                                                [
                                                    'id' => 'desktop',
                                                    'breakpoint' => '1200',
                                                    'image_size' => '1920',
                                                ],
                                            ] ?? [];

                                        // Helpers
                                        $lightbox_mobile_breakpoint = '';
                                        $lightbox_image_breakpoints = '';
                                        $lightbox_image_full_size = '';
                                        $lightbox_image_sizes = '';

                                        if (
                                            is_array($lightbox_image_size_list)
                                        ) {
                                            foreach (
                                                $lightbox_image_size_list
                                                as $key => $size
                                            ) {
                                                $is_first =
                                                    $key ==
                                                    array_key_first(
                                                        $lightbox_image_size_list,
                                                    );
                                                $is_last =
                                                    $key ==
                                                    array_key_last(
                                                        $lightbox_image_size_list,
                                                    );
                                                $separator = !$is_last
                                                    ? ','
                                                    : '';
                                                $is_last_image_size = $is_last
                                                    ? ',' .
                                                        end(
                                                            $lightbox_image_size_list,
                                                        )['image_size'] .
                                                        'w'
                                                    : $separator;

                                                // Change breakpoints for mobile
                                                if ($is_first) {
                                                    $lightbox_image_breakpoints .=
                                                        '(max-width: ' .
                                                        $size['breakpoint'] .
                                                        'px) ' .
                                                        $size['image_size'] .
                                                        'px,';
                                                    $lightbox_image_sizes .=
                                                        $lightbox_url .
                                                        '&w=' .
                                                        $size['image_size'] .
                                                        ' ' .
                                                        $size['breakpoint'] .
                                                        'w,';
                                                }

                                                // Skip first Item
                                                if ($key === 0) {
                                                    continue;
                                                }

                                                // Breakpoints
                                                $lightbox_image_breakpoints .=
                                                    '(min-width:' .
                                                    $size['breakpoint'] .
                                                    'px) ' .
                                                    $size['image_size'] .
                                                    'px' .
                                                    $separator;

                                                // Sources
                                                $lightbox_image_sizes .=
                                                    $lightbox_url .
                                                    '&w=' .
                                                    $size['image_size'] .
                                                    ' ' .
                                                    $size['breakpoint'] .
                                                    'w' .
                                                    $is_last_image_size;
                                            }
                                        }
                                        ?>
                                            <a class="c-property-details__gallery-link glightbox c-slider__slide splide__slide" data-gallery="gallery" href="<?php echo $lightbox_url .
                                                'w=' .
                                                end($lightbox_image_size_list)[
                                                    'image_size'
                                                ]; ?>" data-sizes="<?php echo $lightbox_image_breakpoints; ?>" data-srcset="<?php echo $lightbox_image_sizes; ?>" <?php if (
    !empty($image['alt'])
): ?>data-caption="<?php echo $image['alt']; ?>" title="<?php echo $image[
    'alt'
]; ?>"<?php endif; ?>>
                                            <?php oo_get_template(
                                                'components',
                                                '',
                                                'component-image',
                                                [
                                                    'image' => $image,
                                                    'picture_class' =>
                                                        'c-property-details__gallery-picture o-picture',
                                                    'image_class' =>
                                                        'c-property-details__gallery-image o-image',
                                                    'dimensions' => [
                                                        '575' => [
                                                            'w' => $image_width_xs,
                                                            'h' => round(
                                                                ($image_width_xs *
                                                                    2) /
                                                                    3,
                                                            ),
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
                                                            'h' => round(
                                                                ($image_width_md *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                        '576' => [
                                                            'w' => $image_width_sm,
                                                            'h' => round(
                                                                ($image_width_sm *
                                                                    2) /
                                                                    3,
                                                            ),
                                                        ],
                                                    ],
                                                ],
                                            ); ?>
                    
                                        </a>
                                    <?php
                                    } ?>
                                </div>

                                <?php if (
                                    $property_status ||
                                    (Favorites::isFavorizationEnabled() &&
                                        !$is_reference)
                                ) { ?>
                                    <div class="c-property-details__flags c-flags">
                                        <?php if ($property_status) { ?>
                                            <span class="c-property-details__status c-flag">
                                                <?php echo ucfirst(
                                                    $property_status,
                                                ); ?>
                                            </span>
                                        <?php } ?>
                                    
                                        <?php if (
                                            Favorites::isFavorizationEnabled() &&
                                            !$is_reference
                                        ) { ?>
                                            <span class="c-property-details__favorite c-button --only-icon --square" data-onoffice-property-id="<?php echo $property_id; ?>">
                                                <span class="u-screen-reader-only">
                                                    <?php
                                                    $favorite_label = Favorites::getFavorizationLabel();
                                                    if (
                                                        $favorite_label ==
                                                        'Watchlist'
                                                    ) {
                                                        esc_html_e(
                                                            __(
                                                                'Zur Merkliste hinzufügen',
                                                                'oo_theme',
                                                            ),
                                                        );
                                                    } else {
                                                        esc_html_e(
                                                            __(
                                                                'Zu Favoriten hinzufügen',
                                                                'oo_theme',
                                                            ),
                                                        );
                                                    }
                                                    ?>
                                                </span>
                                                <span class="c-button__icon --favorite"><?php oo_get_icon(
                                                    'heart',
                                                ); ?></span>
                                            </span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                                <div class="c-property-details__all-images --open-lightbox">
                                    <button class="c-button --has-icon --desktop">
                                        <span class="c-button__text">
                                            <?php echo sprintf(
                                                __(
                                                    '%d Fotos ansehen',
                                                    'oo_theme',
                                                ),
                                                $picture_count,
                                            ); ?>
                                        </span>
                                        <span class="c-button__icon --arrow-right">
                                            <?php echo oo_get_icon(
                                                'arrow-right',
                                            ); ?>
                                        </span>
                                    </button>
                                    <button class="c-button --only-icon --large --mobile">
                                        <span class="u-screen-reader-only"><?php echo sprintf(
                                            __('%d Fotos ansehen', 'oo_theme'),
                                            $picture_count,
                                        ); ?></span>
                                        <span class="c-button__icon --arrow-right">
                                            <?php echo oo_get_icon(
                                                'arrow-right',
                                            ); ?>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="c-slider__navigation splide__navigation">
                                <div class="c-slider__arrows splide__arrows">
                                    <button class="c-slider__arrow --prev c-button --only-icon --square splide__arrow splide__arrow--prev">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Vorheriges',
                                            'oo_theme',
                                        ); ?></span>
                                        <span class="c-button__icon --chevron-left"><?php oo_get_icon(
                                            'chevron-left',
                                        ); ?></span>
                                    </button>
                                    <button class="c-slider__arrow --next c-button --only-icon --square splide__arrow splide__arrow--next">
                                        <span class="u-screen-reader-only"><?php esc_html_e(
                                            'Nächstes',
                                            'oo_theme',
                                        ); ?></span>
                                        <span class="c-button__icon --chevron-right"><?php oo_get_icon(
                                            'chevron-right',
                                        ); ?></span>
                                    </button>
                                </div>
                                <ul class="c-slider__pagination splide__pagination"></ul>
                            </div>
                        </div>
                    <?php
                    } else {
                         ?>
                        <div class="c-property-details__gallery-picture">
                            <?php if (
                                $property_status ||
                                (Favorites::isFavorizationEnabled() &&
                                    !$is_reference)
                            ) { ?>
                                <div class="c-property-details__flags c-flags">
                                    <?php if ($property_status) { ?>
                                        <span class="c-property-details__status c-flag">
                                            <?php echo ucfirst(
                                                $property_status,
                                            ); ?>
                                        </span>
                                    <?php } ?>
                                
                                    <?php if (
                                        Favorites::isFavorizationEnabled() &&
                                        !$is_reference
                                    ) { ?>
                                        <span class="c-property-details__favorite c-button --only-icon --square" data-onoffice-property-id="<?php echo $property_id; ?>">
                                            <span class="u-screen-reader-only">
                                                <?php
                                                $favorite_label = Favorites::getFavorizationLabel();
                                                if (
                                                    $favorite_label ==
                                                    'Watchlist'
                                                ) {
                                                    esc_html_e(
                                                        __(
                                                            'Zur Merkliste hinzufügen',
                                                            'oo_theme',
                                                        ),
                                                    );
                                                } else {
                                                    esc_html_e(
                                                        __(
                                                            'Zu Favoriten hinzufügen',
                                                            'oo_theme',
                                                        ),
                                                    );
                                                }
                                                ?>
                                            </span>
                                            <span class="c-button__icon --favorite"><?php oo_get_icon(
                                                'heart',
                                            ); ?></span>
                                        </span>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php
                    } ?>
                    <div class="c-property-details__banner-content">
                        <?php if ($current_property['objekttitel']): ?>
                            <h1 class="c-property-details__title o-headline --h3">
                                <?php echo $current_property['objekttitel']; ?>
                            </h1>
                        <?php endif; ?>

                        <?php if ($fields_available) { ?>
                            <div class="c-property-details__features">
                                <?php
                                foreach (
                                    $current_property
                                    as $field => $value
                                ) {
                                    if (
                                        (is_numeric($value) && 0 == $value) ||
                                        $value == '0000-00-00' ||
                                        $value == '0.00' ||
                                        $value == '' ||
                                        empty($value) ||
                                        in_array($field, $dont_echo) ||
                                        in_array($field, $energy_fields)
                                    ) {
                                        continue;
                                    }

                                    array_push($property_features, [
                                        'field' => $field,
                                        'label' => $pEstates->getFieldLabel(
                                            $field,
                                        ),
                                        'value' => $value,
                                        'has_value' => !empty($value)
                                            ? true
                                            : false,
                                    ]);
                                }

                                if ($property_features) {
                                    echo '<div class="c-property-details__features-items c-property-features --on-detail-page">';
                                    foreach ($property_features as $feature) {
                                        if (
                                            function_exists('oo_feature_item')
                                        ) {
                                            oo_feature_item($feature);
                                        }
                                    }
                                    echo '</div>';

                                    echo '<div class="c-property-details__features-items --is-toggle">' .
                                        '<div class="c-property-features --on-detail-page">' .
                                        '</div>' .
                                        '</div>';
                                }
                                ?>
                            </div>
                            <div class="c-property-details__more-wrapper --hidden">
                                <button class="c-property-details__more">
                                    <span class="c-read-more__icon c-button --only-icon">
                                        <span class="c-property-details__more-open c-button__icon --plus"><?php oo_get_icon(
                                            'plus',
                                        ); ?>
                                        </span>
                                        <span class="c-property-details__more-close c-button__icon --minus"><?php oo_get_icon(
                                            'minus',
                                        ); ?>
                                        </span>
                                    </span>
                                    <span class="c-property-details__more-title" 
                                        data-open-text="<?php echo esc_html(
                                            'Mehr anzeigen',
                                            'oo_theme',
                                        ); ?>" 
                                        data-close-text="<?php echo esc_html(
                                            'Weniger anzeigen',
                                            'oo_theme',
                                        ); ?>">
                                        <?php echo esc_html(
                                            'Mehr anzeigen',
                                            'oo_theme',
                                        ); ?>
                                    </span>
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="c-property-details__cols o-row">
                <?php if ($tabs): ?>
                    <div class="c-property-tabs__wrapper c-property-details__col o-col-12 o-col-lg-8">
                        <h2 class="c-property-details__headline o-headline --h3 --is-property-detail">
                            <?php _e('Zur Immobilie', 'oo_theme'); ?>
                        </h2>

                        <?php
                        $firstCheckedTab = true;
                        foreach ($tabs as $i => $tab):
                            if (!empty($tab['content'])):

                                $checked = $firstCheckedTab
                                    ? 'checked="checked" '
                                    : '';
                                $firstCheckedTab = false;
                                ?>
                                <input id="tab-<?php echo $i; ?>" class="c-property-tabs__input --tab-<?php echo $i; ?>" type="radio" name="pct" <?php echo $checked; ?> />
                            <?php
                            endif; ?>

                        <?php
                        endforeach;
                        ?>

                        <nav class="c-property-tabs__nav">
                            <ul class="c-property-tabs__list">
                                <?php foreach ($tabs as $i => $tab): ?>
                                        <li class="c-property-tabs__item --tab-<?php echo $i; ?>">
                                            <label for="tab-<?php echo $i; ?>" class="c-property-tabs__label">
                                                <?php echo $tab['title']; ?>
                                            </label>
                                        </li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                        <section class="c-property-tabs__content-wrapper">
                            <?php foreach ($tabs as $i => $tab):
                                $key = $tab['key']; ?>
                                <div class="c-property-tabs__content --tab-<?php echo $i; ?>">
                                    <?php if (
                                        isset($tab['content']) &&
                                        !empty($tab['content']) &&
                                        is_string($tab['content'])
                                    ) {
                                        echo '<p class="c-property-tabs__content-paragraph">';
                                        echo nl2br($tab['content']);
                                        echo '</p>';
                                    } ?>

                                    <?php if ($key == 'energieausweistyp') { ?>
                                        <div class="c-property-features --is-energy">
                                            <?php
                                            $energy_fields = is_array(
                                                $tab['content'],
                                            )
                                                ? $tab['content']
                                                : [];
                                            foreach (
                                                $energy_fields
                                                as $energy_item
                                            ) {

                                                $energy_value =
                                                    $current_property[
                                                        $energy_item
                                                    ];

                                                if (
                                                    (is_numeric(
                                                        $energy_value,
                                                    ) &&
                                                        0 == $energy_value) ||
                                                    $energy_value ==
                                                        '0000-00-00' ||
                                                    $energy_value == '0.00' ||
                                                    $energy_value == '' ||
                                                    empty($energy_value)
                                                ) {
                                                    continue;
                                                }
                                                ?>

                                                <dl class="c-property-features__criteria">
                                                    <dt class="c-property-features__label">
                                                        <?php esc_html_e(
                                                            $pEstates->getFieldLabel(
                                                                $energy_item,
                                                            ),
                                                        ); ?>
                                                    </dt>
                                                    <dd class="c-property-features__value">
                                                        <?php echo nl2br(
                                                            $energy_value,
                                                        ); ?>
                                                    </dd>
                                                </dl>

                                            <?php
                                            }
                                            ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <?php if ($key == 'lage' && $map):
                                        echo $map;
                                    endif; ?>
                                    <?php
                                    if ($key == 'video') {
                                        // MOVIE ELEMENTS
                                        if (
                                            !empty($property_movie_players) ||
                                            !empty($property_movie_links)
                                        ) {
                                            echo '<div class="c-property-details__media">';

                                            // GET MOVIE PLAYERS
                                            if (
                                                !empty(
                                                    $property_movie_players
                                                ) &&
                                                is_array(
                                                    $property_movie_players,
                                                )
                                            ) {
                                                echo '<div class="c-property-details__embeds">';

                                                foreach (
                                                    $property_movie_players
                                                    as $property_movie_player
                                                ) {
                                                    if (
                                                        !empty(
                                                            $property_movie_player[
                                                                'title'
                                                            ]
                                                        )
                                                    ) {
                                                        echo '<p class="c-property-tabs__content-paragraph">';
                                                        echo $property_movie_player[
                                                            'title'
                                                        ];
                                                        echo '</p>';
                                                    }
                                                    echo '<div class="c-property-details__video --' .
                                                        oo_get_service_domain(
                                                            $property_movie_player[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '">';
                                                    echo $property_movie_player[
                                                        'player'
                                                    ];
                                                    echo '</div>';
                                                }

                                                echo '</div>';
                                            }

                                            // GET MOVIE LINKS
                                            if (
                                                !empty($property_movie_links) &&
                                                is_array($property_movie_links)
                                            ) {
                                                echo '<div class="c-property-details__buttons c-buttons">';
                                                foreach (
                                                    $property_movie_links
                                                    as $property_movie_link
                                                ) {
                                                    // Button Text
                                                    $button_title = !empty(
                                                        $property_movie_link[
                                                            'title'
                                                        ]
                                                    )
                                                        ? esc_attr(
                                                            $property_movie_link[
                                                                'title'
                                                            ],
                                                        )
                                                        : esc_html(
                                                            'Video starten',
                                                            'oo_theme',
                                                        );

                                                    echo '<a class="c-button --has-icon" href="' .
                                                        esc_attr(
                                                            $property_movie_link[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '" target="_blank" title="' .
                                                        $button_title .
                                                        '">';
                                                    echo '<span class="c-button__text">';
                                                    echo $button_title;
                                                    echo '</span>';
                                                    echo '<span class="c-button__icon --arrow">';
                                                    echo oo_get_icon(
                                                        'arrow-right',
                                                    );
                                                    echo '</span>';
                                                    echo '</a>';
                                                }
                                                echo '</div>';
                                            }

                                            echo '</div>';
                                        }
                                    }

                                    if ($key == 'links') {
                                        // Links
                                        if (
                                            !empty($property_links) ||
                                            !empty($property_link_embeds)
                                        ) {
                                            echo '<div class="c-property-details__media">';

                                            if (
                                                !empty($property_link_embeds) &&
                                                is_array($property_link_embeds)
                                            ) {
                                                echo '<div class="c-property-details__embeds">';

                                                foreach (
                                                    $property_link_embeds
                                                    as $property_link_embed
                                                ) {
                                                    echo '<div class="c-property-details__iframe --' .
                                                        oo_get_service_domain(
                                                            $property_link_embed[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '">';
                                                    echo $property_link_embed[
                                                        'player'
                                                    ];
                                                    echo '</div>';
                                                }

                                                echo '</div>';
                                            }

                                            if (
                                                !empty($property_links) &&
                                                is_array($property_links)
                                            ) {
                                                echo '<div class="c-property-details__buttons c-buttons">';
                                                foreach (
                                                    $property_links
                                                    as $property_link
                                                ) {
                                                    // Button Text
                                                    $button_title = !empty(
                                                        $property_link['title']
                                                    )
                                                        ? esc_attr(
                                                            $property_link[
                                                                'title'
                                                            ],
                                                        )
                                                        : esc_attr(
                                                            'Link öffnen',
                                                            'oo_theme',
                                                        );

                                                    echo '<a class="c-button --has-icon" href="' .
                                                        esc_attr(
                                                            $property_link[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '" target="_blank" title="' .
                                                        $button_title .
                                                        '">';
                                                    echo '<span class="c-button__text">';
                                                    echo $button_title;
                                                    echo '</span>';
                                                    echo '<span class="c-button__icon --arrow">';
                                                    echo oo_get_icon(
                                                        'arrow-right',
                                                    );
                                                    echo '</span>';
                                                    echo '</a>';
                                                }
                                                echo '</div>';
                                            }

                                            echo '</div>';
                                        }
                                    }

                                    if ($key == 'ogulo') {
                                        // Ogulo / 3D Rundgang
                                        if (
                                            !empty($property_ogulo_embeds) ||
                                            !empty($property_ogulo_links)
                                        ) {
                                            echo '<div class="c-property-details__media">';

                                            if (
                                                !empty(
                                                    $property_ogulo_embeds
                                                ) &&
                                                is_array($property_ogulo_embeds)
                                            ) {
                                                echo '<div class="c-property-details__embeds">';

                                                foreach (
                                                    $property_ogulo_embeds
                                                    as $property_ogulo_embed
                                                ) {
                                                    if (
                                                        !empty(
                                                            $property_ogulo_embed[
                                                                'title'
                                                            ]
                                                        )
                                                    ) {
                                                        echo '<p class="c-property-tabs__content-paragraph">';
                                                        echo $property_ogulo_embed[
                                                            'title'
                                                        ];
                                                        echo '</p>';
                                                    }
                                                    echo '<div class="c-property-details__iframe --' .
                                                        oo_get_service_domain(
                                                            $property_ogulo_embed[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '">';
                                                    echo $property_ogulo_embed[
                                                        'player'
                                                    ];
                                                    echo '</div>';
                                                }

                                                echo '</div>';
                                            }

                                            if (
                                                !empty($property_ogulo_links) &&
                                                is_array($property_ogulo_links)
                                            ) {
                                                echo '<div class="c-property-details__buttons c-buttons">';
                                                foreach (
                                                    $property_ogulo_links
                                                    as $property_ogulo_link
                                                ) {
                                                    // Button Text
                                                    $button_title = !empty(
                                                        $property_ogulo_link[
                                                            'title'
                                                        ]
                                                    )
                                                        ? esc_attr(
                                                            $property_ogulo_link[
                                                                'title'
                                                            ],
                                                        )
                                                        : esc_attr(
                                                            '360°-Rundgang starten',
                                                            'oo_theme',
                                                        );

                                                    echo '<a class="c-button --has-icon" href="' .
                                                        esc_attr(
                                                            $property_ogulo_link[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '" target="_blank" title="' .
                                                        $button_title .
                                                        '">';
                                                    echo '<span class="c-button__text">';
                                                    echo $button_title;
                                                    echo '</span>';
                                                    echo '<span class="c-button__icon --arrow">';
                                                    echo oo_get_icon(
                                                        'arrow-right',
                                                    );
                                                    echo '</span>';
                                                    echo '</a>';
                                                }
                                                echo '</div>';
                                            }

                                            echo '</div>';
                                        }
                                    }

                                    if ($key == 'object') {
                                        // Objects

                                        if (
                                            !empty($property_object_embeds) ||
                                            !empty($property_object_links)
                                        ) {
                                            echo '<div class="c-property-details__media">';

                                            if (
                                                !empty(
                                                    $property_object_embeds
                                                ) &&
                                                is_array(
                                                    $property_object_embeds,
                                                )
                                            ) {
                                                echo '<div class="c-property-details__embeds">';
                                                foreach (
                                                    $property_object_embeds
                                                    as $property_object_embed
                                                ) {
                                                    if (
                                                        !empty(
                                                            $property_object_embed[
                                                                'title'
                                                            ]
                                                        )
                                                    ) {
                                                        echo '<p class="c-property-tabs__content-paragraph">';
                                                        echo $property_object_embed[
                                                            'title'
                                                        ];
                                                        echo '</p>';
                                                    }
                                                    echo '<div class="c-property-details__iframe --' .
                                                        oo_get_service_domain(
                                                            $property_object_embed[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '">';
                                                    echo $property_object_embed[
                                                        'player'
                                                    ];
                                                    echo '</div>';
                                                }

                                                echo '</div>';
                                            }

                                            if (
                                                !empty(
                                                    $property_object_links
                                                ) &&
                                                is_array($property_object_links)
                                            ) {
                                                echo '<div class="c-property-details__buttons c-buttons">';
                                                foreach (
                                                    $property_object_links
                                                    as $property_object_link
                                                ) {
                                                    // Button Text
                                                    $button_title = !empty(
                                                        $property_object_link[
                                                            'title'
                                                        ]
                                                    )
                                                        ? esc_attr(
                                                            $property_object_link[
                                                                'title'
                                                            ],
                                                        )
                                                        : esc_attr(
                                                            'Objekt-Link öffnen',
                                                            'oo_theme',
                                                        );

                                                    echo '<a class="c-button --has-icon" href="' .
                                                        esc_attr(
                                                            $property_object_link[
                                                                'url'
                                                            ],
                                                        ) .
                                                        '" target="_blank" title="' .
                                                        $button_title .
                                                        '">';
                                                    echo '<span class="c-button__text">';
                                                    echo $button_title;
                                                    echo '</span>';
                                                    echo '<span class="c-button__icon --arrow">';
                                                    echo oo_get_icon(
                                                        'arrow-right',
                                                    );
                                                    echo '</span>';
                                                    echo '</a>';
                                                }
                                                echo '</div>';
                                            }

                                            echo '</div>';
                                        }
                                    }
                                    ?>

                                </div>
                            <?php
                            endforeach; ?>
                        </section>
                    </div>
                <?php endif; ?>

                <div class="c-property-details__aside c-property-details__col o-col-12 o-col-lg-4">
                
                    <?php require_once 'property-contact.php'; ?>

                    <div class="c-property-details__aside-buttons c-buttons --is-column">
                        <div class="c-property-details__sharing">
                            <?php
                            global $wp;

                            $property_detail_page =
                                get_field('general', 'option')[
                                    'property_detail'
                                ] ?? [];
                            $property_share_button = filter_var(
                                $property_detail_page['property_share_button'],
                                FILTER_VALIDATE_BOOLEAN,
                            );

                            if ($property_share_button) {
                                oo_get_template(
                                    'components',
                                    '',
                                    'component-share',
                                    [
                                        'button_class' =>
                                            'c-button --full-width',
                                        'popup_id' => 'property_detail_share',
                                        'share_link' => home_url(
                                            add_query_arg([], $wp->request),
                                        ),
                                    ],
                                );
                            }
                            ?>
                        </div>
                        <?php
                        if ($pEstates->getDocument() != '') {
                            echo '<div class="c-property-details__expose">';
                            echo '<a class="c-button --full-width --has-icon --ghost" href="' .
                                $pEstates->getDocument() .
                                '">';
                            echo '<span class="c-button__text">';
                            echo esc_html__('Exposé herunterladen', 'oo_theme');
                            echo '</span>';
                            echo '<span class="c-button__icon">';
                            echo oo_get_icon('arrow-down', 'svg');
                            echo '</span>';
                            echo '</a>';
                            echo '</div>';
                        }
                        if (!empty($shortcode_form)) {
                            echo '<div class="c-property-details__form">';
                            echo '<a class="c-button --full-width --has-icon" href="#request">';
                            echo '<span class="c-button__text">';
                            echo esc_html__('Schnellanfrage', 'oo_theme');
                            echo '</span>';
                            echo '<span class="c-button__icon">';
                            echo oo_get_icon('arrow-right', 'svg');
                            echo '</span>';
                            echo '</a>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($pEstates->getEstateUnits())) { ?>
                <div class="c-property-details__row o-row">
                    <div class="c-property-details__main o-col-12">
                        <div class="c-property-details__table">
                            <?php echo $pEstates->getEstateUnits(); ?>
                        </div>
                    </div>
                </div>
            <?php } ?> 

            <?php if (!empty($pEstates->getSimilarEstates())) { ?>        
                <?php echo $pEstates->getSimilarEstates(); ?>
            <?php } ?>

            <?php if (!empty($shortcode_form)) { ?>
                <div class="c-property-details__row o-row --position-center">
                    <div id="request" class="c-property-details__form o-col-12 o-col-xl-8">
                        <?php echo do_shortcode($shortcode_form); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php
}
?>

<?php if (Favorites::isFavorizationEnabled() && !$is_reference) { ?>
    <?php wp_enqueue_script('oo-favorites-script'); ?>

	<script>
        jQuery(document).ready(function($) {
            onofficeFavorites = new onOffice.favorites(<?php echo json_encode(
                Favorites::COOKIE_NAME,
            ); ?>);
            onOffice.addFavoriteButtonLabel = function(i, element) {
                var favorite = $(element);
                var propertyId = favorite.attr('data-onoffice-property-id');
                var favoriteText = favorite.find('.u-screen-reader-text');
                var favoriteIcon = favorite.find('.--favorite');
                var favoriteClass = '--filled';
                if (!onofficeFavorites.favoriteExists(propertyId)) {
                    favoriteText.text('<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e(__('Zur Merkliste hinzufügen', 'oo_theme'));
                    } else {
                        esc_html_e(__('Zu Favoriten hinzufügen', 'oo_theme'));
                    } ?>');
                    favoriteIcon.removeClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.add(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                } else {
                    favoriteText.text('<?php if (
                        $favorite_label == 'Watchlist'
                    ) {
                        esc_html_e(__('Von Merkliste entfernen', 'oo_theme'));
                    } else {
                        esc_html_e(__('Von Favoriten entfernen', 'oo_theme'));
                    } ?>');
                    favoriteIcon.addClass(favoriteClass);
                    favorite.on('click', function() {
                        onofficeFavorites.remove(propertyId);
                        onOffice.addFavoriteButtonLabel(0, favorite);
                    });
                }
            };
            $('.c-property-details__favorite').each(onOffice.addFavoriteButtonLabel);
        });
	</script>
<?php } ?>
