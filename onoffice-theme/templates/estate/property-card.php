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

use onOffice\WPlugin\Favorites;

$dont_echo = [
    'objekttitel',
    'objektbeschreibung',
    'lage',
    'ausstatt_beschr',
    'sonstige_angaben',
];
$location_fields = ['plz', 'ort', 'land'];
$price_fields = get_price_fields();

$pEstatesClone = clone $pEstates;
$pEstatesClone->resetEstateIterator();

$iframe_display = filter_var(
    get_field('iframe_display', 'option')['display_as_iframe'] ?? false,
    FILTER_VALIDATE_BOOLEAN,
);

// image alignment
$image_positions = get_field('image_positions') ?? [];
$image_position_vertical = $image_positions['vertical'] ?? 'center';
$image_position_horizontal = $image_positions['horizontal'] ?? 'center';

if (
    $image_position_vertical === 'center' &&
    $image_position_horizontal === 'center'
) {
    $image_position_vertical = 'center';
    $image_position_horizontal = '';
} elseif (
    $image_position_vertical === 'center' &&
    $image_position_horizontal != 'center'
) {
    $image_position_vertical = '';
} elseif (
    $image_position_horizontal === 'center' &&
    $image_position_vertical != 'center'
) {
    $image_position_horizontal = '';
}

$even = true;

// get header level from parent block
$header_level = get_current_header_level() + 1;

$property_counter = 0;
while ($current_property = $pEstatesClone->estateIterator()):

    $even = !$even;

    $property_counter++;
    $property_status = $current_property['vermarktungsstatus'];
    unset($current_property['vermarktungsstatus']);

    $property_url = esc_url($pEstatesClone->getEstateLink());
    $property_id = $pEstatesClone->getCurrentEstateId();
    $raw_values = $pEstatesClone->getRawValues();
    $is_address_shared = !empty($current_property['strasse']);
    $is_reference = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['referenz'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
    );
    $is_secret_sale = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['secret_sale'] ??
            null,
        FILTER_VALIDATE_BOOLEAN,
    );

    $placeholder_image = oo_should_show_secret_sale_placeholder($is_secret_sale)
        ? oo_get_secret_sale_placeholder()
        : '';

    $is_restricted_view = $pEstatesClone->getViewRestrict();

    $is_location_fields = false;
    $is_price_fields = false;
    $is_fields = false;
    foreach ($current_property as $field => $value) {
        if (
            (is_numeric($value) && 0 == $value) ||
            $value == '0000-00-00' ||
            $value == '0.00' ||
            $value == '' ||
            (is_string($value) &&
                $value !== '' &&
                !is_numeric($value) &&
                ($raw_values->getValueRaw($property_id)['elements'][$field] ??
                    null) ===
                    '0') || // skip negative boolean fields
            empty($value) ||
            in_array($field, $dont_echo)
        ) {
            continue;
        }

        if (in_array($field, $location_fields)) {
            $is_location_fields = true;
        }

        if (in_array($field, $price_fields)) {
            $is_price_fields = true;
        }

        if (
            !in_array($field, $location_fields) &&
            !in_array($field, $price_fields)
        ) {
            $is_fields = true;
        }
    }

    $property_pictures = [];
    if (oo_should_show_secret_sale_placeholder($is_secret_sale)) {
        if ($placeholder_image) {
            $property_pictures = [
                [
                    'url' => $placeholder_image,
                    'alt' => esc_html__('Immobilienbild', 'oo_theme'),
                ],
            ];
        }
    } else {
        foreach ($pEstatesClone->getEstatePictures() as $id) {
            $picture_values = $pEstatesClone->getEstatePictureValues($id);
            $property_pictures[] = [
                'url' => $pEstatesClone->getEstatePictureUrl($id),
                'alt' => !empty($picture_values['title'])
                    ? esc_html($picture_values['title'])
                    : esc_html__('Immobilienbild', 'oo_theme'),
            ];
        }
    }

    $pictures_count = is_array($property_pictures)
        ? count($property_pictures)
        : 0;
    $is_visible_property_detail =
        !$is_reference || ($is_reference && !$is_restricted_view);

    $layout = $layout ?? 'tile';

    // image width
    if ($layout === 'tile') {
        $image_width_xs = '575';
        $image_width_sm = '767';
        $image_width_md = '991';
        $image_width_lg = '587';
        $image_width_xl = '687';
        $image_width_xxl = '517';
        $image_width_xxxl = '624';
    } else {
        $image_width_xs = '575';
        $image_width_sm = '767';
        $image_width_md = '991';
        $image_width_lg = '599';
        $image_width_xl = '531';
        $image_width_xxl = '613';
        $image_width_xxxl = '707';
    }
    ?>

<?php // start flags

    ob_start(); ?>
<?php if (
    $property_status ||
    (Favorites::isFavorizationEnabled() && !$is_reference && !$iframe_display)
): ?>
    <div class="c-property-card__flags c-flags">
        <?php if ($property_status): ?>
            <span class="c-property-card__status c-flag --property-status">
                <?php echo ucfirst($property_status); ?>
            </span>
        <?php endif; ?>

        <?php if (
            Favorites::isFavorizationEnabled() &&
            !$is_reference &&
            !$iframe_display
        ):

            $favorite_label = Favorites::getFavorizationLabel();
            $favorite_text =
                $favorite_label == 'Watchlist'
                    ? esc_html__('Zur Merkliste hinzufügen', 'oo_theme')
                    : esc_html__('Zu Favoriten hinzufügen', 'oo_theme');
            $favorite_icon =
                $favorite_label == 'Watchlist' ? 'bookmark' : 'heart';
            ?>
            <button class="c-property-card__favorite c-icon-button" data-onoffice-property-id="<?php echo $property_id; ?>" aria-label="<?php echo $favorite_text; ?>">
                <?php oo_get_icon($favorite_icon, true, [
                    'class' => 'c-icon-button__icon --favorite',
                ]); ?>
            </button>
        <?php
        endif; ?>
    </div>
<?php endif; ?>
<?php // end flags

    $flags_content = ob_get_clean(); ?>

<?php // start image with slider

    ob_start(); ?>
<?php
if ($pictures_count > 0) { ?>
    <div class="c-property-card__inner --is-properties-images-slider c-slider splide" data-splide='{"perPage":1,"perMove":1,"pagination":false,"arrows":true,"drag":false,"snap":true,"lazyLoad":"nearby","type":"loop"}' role="group" aria-roledescription="carousel" aria-label="<?php echo __(
        'Property Images Slider',
        'oo-theme',
    ) .
        ' ' .
        $property_counter; ?>">

        <div class="c-slider__track splide__track">
            <div class="c-slider__list splide__list">

        <?php foreach ($property_pictures as $image): ?>
            <?php if ($is_visible_property_detail): ?>
                <a class="c-property-card__picture-wrapper c-slider__slide splide__slide <?php echo oo_should_show_secret_sale_placeholder(
                    $is_secret_sale,
                )
                    ? '--open-popup'
                    : ''; ?>" href="<?php echo esc_url(
    $property_url,
); ?>" <?php echo oo_should_show_secret_sale_placeholder($is_secret_sale)
    ? 'data-popup="customer-login" data-forceurl="' .
        esc_url($property_url) .
        '"'
    : ''; ?> aria-label="<?php echo sprintf(
     esc_html_x('Zur Detailansicht der Immobilie Nr. %d', 'oo_theme'),
     $property_id,
 ); ?>">
            <?php endif; ?>

            <?php oo_get_template('components', '', 'component-image', [
                'image' => [
                    'url' => $image['url'],
                    'alt' => $image['alt'],
                ],
                'picture_class' =>
                    'c-property-card__picture o-picture c-slider__slide splide__slide',
                'image_class' => 'c-property-card__image o-image',
                'additional_cloudimg_params' =>
                    '&func=crop&gravity=' .
                    $image_position_vertical .
                    $image_position_horizontal,
                'dimensions' => [
                    '575' => [
                        'w' => $image_width_xs,
                        'h' => round(($image_width_xs * 2) / 3),
                    ],
                    '1600' => [
                        'w' => $image_width_xxxl,
                        'h' => round(($image_width_xxxl * 2) / 3),
                    ],
                    '1400' => [
                        'w' => $image_width_xxl,
                        'h' => round(($image_width_xxl * 2) / 3),
                    ],
                    '1200' => [
                        'w' => $image_width_xl,
                        'h' => round(($image_width_xl * 2) / 3),
                    ],
                    '992' => [
                        'w' => $image_width_lg,
                        'h' => round(($image_width_lg * 2) / 3),
                    ],
                    '768' => [
                        'w' => $image_width_md,
                        'h' => round(($image_width_md * 2) / 3),
                    ],
                    '576' => [
                        'w' => $image_width_sm,
                        'h' => round(($image_width_sm * 2) / 3),
                    ],
                ],
            ]); ?>

            <?php if ($is_visible_property_detail): ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

            </div>
        </div>
    
        <div class="c-slider__navigation splide__navigation --is-properties-images-slider <?php echo $pictures_count <=
        1
            ? '--hidden'
            : ''; ?>">
            <div class="c-slider__arrows splide__arrows">
                <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                    <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                        'Vorheriges',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                        'chevron-left',
                    ); ?></span>
                </button>
                <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                    <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                        'Nächstes',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                        'chevron-right',
                    ); ?></span>
                </button>
            </div>
        </div>

        <?php if ($layout === 'list'): ?>
            <?php echo $flags_content; ?>
        <?php endif; ?>
    </div>
<?php } else { ?>
    <div class="c-property-card__inner">
        <?php if ($is_visible_property_detail): ?>
            <a class="c-property-card__picture-wrapper <?php echo oo_should_show_secret_sale_placeholder(
                $is_secret_sale,
            )
                ? '--open-popup'
                : ''; ?>" href="<?php echo esc_url(
    $property_url,
); ?>" <?php echo oo_should_show_secret_sale_placeholder($is_secret_sale)
    ? 'data-popup="customer-login" data-forceurl="' .
        esc_url($property_url) .
        '"'
    : ''; ?> aria-label="<?php echo sprintf(
     esc_html_x('Zur Detailansicht der Immobilie Nr. %d', 'oo_theme'),
     $property_id,
 ); ?>;">
        <?php endif; ?>
            <div class="c-property-card__picture"></div>
        <?php if ($is_visible_property_detail): ?>
            </a>
        <?php endif; ?>
        <?php if ($layout === 'list'): ?>
            <?php echo $flags_content; ?>
        <?php endif; ?>
    </div>
<?php }
// end image with slider
$image_content = ob_get_clean(); // start location
ob_start();
if (
    $current_property['plz'] ||
    $current_property['ort'] ||
    $current_property['land']
) { ?>
    <p class="c-property-card__location">
        <?php if ($layout === 'tile') { ?>
            <?php if ($current_property['ort']) { ?>
                <span class="c-property-card__location-value">
                    <?php echo $current_property['ort']; ?>
                </span>
            <?php } ?>
            <?php
            $plz = $is_address_shared ? $current_property['plz'] : '';
            if ($plz || $current_property['land']) { ?>
                <span class="c-property-card__location-label"> 
                    <?php
                    echo $plz;
                    echo $plz && $current_property['land'] ? ', ' : '';
                    echo $current_property['land'];
                    ?>
                </span>
            <?php }
            ?>
        <?php } elseif ($layout === 'list') { ?>
            <?php echo $plz; ?> <?php
 echo $current_property['ort'];
 echo $current_property['ort'] && $current_property['land'] ? ', ' : '';
 echo $current_property['land'];
 ?>
        <?php } ?>
    </p>
<?php }
?>
<?php // end location

    $location_content = ob_get_clean(); ?>

<?php // start content

    ob_start(); ?>
<div class="c-property-card__content">
    <div class="c-property-card__main-content-group">
        <?php if ($layout === 'tile'): ?>
            <?php echo $flags_content; ?>
        <?php endif; ?>

        <?php if ($layout === 'list'): ?>
            <?php echo $location_content; ?>
        <?php endif; ?>

        <?php if ($current_property['objekttitel']) { ?>
            <?php echo "<h{$header_level} " .
                'class="c-property-card__title o-headline --h3">' .
                $current_property['objekttitel'] .
                "</h{$header_level}>"; ?>
        <?php } ?>

        <?php if ($is_price_fields || $is_location_fields) { ?>
            <div class="c-property-card__top">
                <?php if ($layout === 'tile'): ?>
                    <?php echo $location_content; ?>
                <?php endif; ?>

                <?php if ($is_price_fields) { ?>
                    <?php foreach ($price_fields as $price_field) {

                        $price_value = $current_property[$price_field];
                        if (
                            (is_numeric($price_value) && 0 == $price_value) ||
                            $price_value == '0000-00-00' ||
                            $price_value == '0.00' ||
                            (is_string($price_value) &&
                                $price_value !== '' &&
                                !is_numeric($price_value) &&
                                ($raw_values->getValueRaw($property_id)[
                                    'elements'
                                ][$price_field] ??
                                    null) ===
                                    '0') || // skip negative boolean fields
                            $price_value == '' ||
                            empty($price_value)
                        ) {
                            continue;
                        }
                        $class = '--text-color';
                        if (
                            $masking_attributes = oo_apply_secret_sale_masking(
                                $price_field,
                                $is_secret_sale,
                            )
                        ) {
                            $price_value = $masking_attributes['value'];
                            $class = $masking_attributes['class'];
                        }
                        ?>
                        <div class="c-property-card__price">
                            <?php if ($layout === 'tile') { ?>
                                <span class="c-property-card__price-value <?php echo esc_attr(
                                    $class,
                                ); ?>">
                                    <?php is_array($price_value)
                                        ? esc_html_e(
                                            implode(', ', $price_value),
                                        )
                                        : esc_html_e($price_value); ?>
                                </span>
                                <span class="c-property-card__price-label">
                                    <?php esc_html_e(
                                        $pEstates->getFieldLabel($price_field),
                                    ); ?>
                                </span>
                            <?php } elseif ($layout === 'list') { ?>
                                <span class="c-property-card__price-value <?php echo esc_attr(
                                    $class,
                                ); ?>">
                                    <?php esc_html_e(
                                        $pEstates->getFieldLabel($price_field),
                                    ); ?>:
                                    <?php is_array($price_value)
                                        ? esc_html_e(
                                            implode(', ', $price_value),
                                        )
                                        : esc_html_e($price_value); ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php
                    } ?>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="c-property-card__features c-item-features">
            <?php if ($is_fields) { ?>
                <?php foreach ($current_property as $field => $value) {

                    if (
                        (is_numeric($value) && 0 == $value) ||
                        $value == '0000-00-00' ||
                        $value == '0.00' ||
                        (is_string($value) &&
                            $value !== '' &&
                            !is_numeric($value) &&
                            ($raw_values->getValueRaw($property_id)['elements'][
                                $field
                            ] ??
                                null) ===
                                '0') ||
                        // skip negative boolean fields
                        $value == '' ||
                        empty($value) ||
                        in_array($field, $dont_echo) ||
                        in_array($field, $location_fields) ||
                        in_array($field, $price_fields)
                    ) {
                        continue;
                    }
                    if (
                        !$is_address_shared &&
                        in_array($field, ['plz', 'hausnummer', 'strasse'], true)
                    ) {
                        continue;
                    }
                    if (
                        ($raw_values->getValueRaw($property_id)['elements'][
                            'provisionsfrei'
                        ] ??
                            null) ===
                            '1' &&
                        in_array(
                            $field,
                            ['innen_courtage', 'aussen_courtage'],
                            true,
                        )
                    ) {
                        continue;
                    }
                    ?>
                    <?php
                    $class = '';
                    if (
                        $masking_attributes = oo_apply_secret_sale_masking(
                            $field,
                            $is_secret_sale,
                        )
                    ) {
                        $value = $masking_attributes['value'];
                        $class = $masking_attributes['class'];
                    }
                    ?>
                    <dl class="c-item-features__item">
                        <dt class="c-item-features__value<?php echo $class; ?>">
                            <?php echo esc_html($value); ?>
                        </dt>
                        <dd class="c-item-features__label" title="<?php echo esc_html(
                            $pEstates->getFieldLabel($field),
                        ); ?>">
                            <?php echo esc_html(
                                $pEstates->getFieldLabel($field),
                            ); ?>
                        </dd>
                    </dl>
                <?php
                } ?>
            <?php } ?> 

            <?php if ($is_visible_property_detail): ?>
                <a class="c-property-card__button c-button <?php echo oo_should_show_secret_sale_placeholder(
                    $is_secret_sale,
                )
                    ? '--open-popup'
                    : ''; ?>" href="<?php echo $property_url; ?>" <?php echo oo_should_show_secret_sale_placeholder(
    $is_secret_sale,
)
    ? 'data-popup="customer-login" data-forceurl="' .
        esc_url($property_url) .
        '"'
    : ''; ?> aria-label="<?php echo sprintf(
     esc_html_x('Zur Detailansicht der Immobilie Nr. %d', 'oo_theme'),
     $property_id,
 ); ?>">
                    <span class="c-property-card__button-text">
                        <?php esc_html_e(
                            'Zur Detail&shy;ansicht',
                            'oo_theme',
                        ); ?>
                    </span>
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($current_property['objektbeschreibung'])) { ?>
            <div class="c-property-card__description">
                <h4 class="c-property-card__description-headline">
                    <?php esc_html_e(
                        $pEstates->getFieldLabel('objektbeschreibung'),
                    ); ?>:
                </h4>
                <p class="c-property-card__description-text">
                    <?php echo nl2br(
                        $current_property['objektbeschreibung'],
                    ); ?>
                </p>
            </div>
        <?php } ?>
    </div>
</div>
<?php // end content

    $main_content = ob_get_clean(); ?>

<article class="c-property-card <?php echo '--' . $layout; ?> <?php echo $even
     ? '--even'
     : ''; ?> <?php if ($is_slider) {
     echo 'c-slider__slide splide__slide';
 } ?>">
    <?php if ($layout === 'tile') { ?>
        <?php echo $image_content; ?>
        <?php echo $main_content; ?>
    <?php } else { ?>
        <div class="c-property-card__list-wrapper">
            <?php if ($even) { ?>
                <?php echo $main_content; ?>
                <?php echo $image_content; ?>
            <?php } else { ?>
                <?php echo $image_content; ?>
                <?php echo $main_content; ?>
            <?php } ?>
        </div>
    <?php } ?>
</article>

<?php
endwhile;
?>

<?php
$isMPSWatchlistActive = false;
if (class_exists('OnOfficeVueAddons\Service\WatchlistService')) {
    $watchlistService = new OnOfficeVueAddons\Service\WatchlistService();
    $isMPSWatchlistActive = $watchlistService->is_watchlist_active();
}
if ($isMPSWatchlistActive):
    wp_enqueue_script('on_office_vue_addons-watchlist');
    wp_localize_script('on_office_vue_addons-watchlist', 'watchlist_options', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('on_office_vue_addons_nonce'),
        'labels' => [
            'add' =>
                $favorite_label == 'Watchlist'
                    ? __('Zur Merkliste hinzufügen', 'oo_theme')
                    : __('Zu Favoriten hinzufügen', 'oo_theme'),
            'remove' =>
                $favorite_label == 'Watchlist'
                    ? __('Von Merkliste entfernen', 'oo_theme')
                    : __('Von Favoriten entfernen', 'oo_theme'),
        ],
    ]);
else:
    if (Favorites::isFavorizationEnabled()) {
        wp_enqueue_script('oo-favorites-script'); ?>

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
                        var labelAdd = '<?php echo esc_js(
                            $favorite_label == 'Watchlist'
                                ? __('Zur Merkliste hinzufügen', 'oo_theme')
                                : __('Zu Favoriten hinzufügen', 'oo_theme'),
                        ); ?>';
                        favoriteText.text(labelAdd);
                        favorite.attr('title', labelAdd);
                        favorite.attr('aria-label', labelAdd);
                        favoriteIcon.removeClass(favoriteClass);
                        favorite.on('click', function() {
                            onofficeFavorites.add(propertyId);
                            onOffice.addFavoriteButtonLabel(0, favorite);
                        });
                    } else {
                        var labelRemove = '<?php echo esc_js(
                            $favorite_label == 'Watchlist'
                                ? __('Von Merkliste entfernen', 'oo_theme')
                                : __('Von Favoriten entfernen', 'oo_theme'),
                        ); ?>';
                        favoriteText.text(labelRemove);
                        favorite.attr('title', labelRemove);
                        favorite.attr('aria-label', labelRemove);
                        favoriteIcon.addClass(favoriteClass);
                        favorite.on('click', function() {
                            onofficeFavorites.remove(propertyId);
                            onOffice.addFavoriteButtonLabel(0, favorite);
                        });
                    }
                };
                $('.c-property-card__favorite').each(onOffice.addFavoriteButtonLabel);
            });
        </script>
    <?php
    }
endif;
 ?>
