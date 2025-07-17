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
$price_fields = ['kaufpreis', 'kaltmiete', 'nettokaltmiete'];

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

$property_counter = 0;
while ($current_property = $pEstatesClone->estateIterator()):

    $property_counter++;
    $property_status = $current_property['vermarktungsstatus'];
    unset($current_property['vermarktungsstatus']);

    $property_url = esc_url($pEstatesClone->getEstateLink());
    $property_id = $pEstatesClone->getCurrentMultiLangEstateMainId();
    $raw_values = $pEstatesClone->getRawValues();
    $is_reference = filter_var(
        $raw_values->getValueRaw($property_id)['elements']['referenz'] ?? null,
        FILTER_VALIDATE_BOOLEAN,
    );
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

    $first_picture = true;

    // image width
    $image_width_xs = '542';
    $image_width_sm = '510';
    $image_width_md = '692';
    $image_width_lg = '446';
    $image_width_xl = '542';
    $image_width_xxl = '414';
    $image_width_xxxl = '458';
    ?>

<article class="c-property-card --bg-transparent <?php if ($is_slider) {
    echo '--on-slider c-slider__slide splide__slide';
} ?>">

 <?php
 $property_pictures = $pEstatesClone->getEstatePictures();
 $pictures_count = is_array($property_pictures) ? count($property_pictures) : 0;
 $is_visible_property_detail = !$is_reference || !$is_restricted_view;
 $slider_item_classes = 'c-slider__slide splide__slide';
 ?>

<div class="c-property-card__inner --on-bg-transparent --is-properties-images-slider --on-slider <?php echo $pictures_count >
0
    ? 'c-slider splide'
    : ''; ?>"
     <?php if ($pictures_count > 0): ?>
         data-splide='{"perPage":1,"perMove":1,"pagination":false,"arrows":true,"drag":false,"snap":true,"lazyLoad":"nearby","type":"loop"}'
         aria-label="<?php echo __('Property Images Slider', 'oo-theme') .
             ' ' .
             $property_counter; ?>"
     <?php endif; ?>
>

    <?php if ($pictures_count > 0): ?>
        <div class="c-slider__track splide__track">
            <div class="c-slider__list splide__list">
                <?php foreach ($property_pictures as $id): ?>
                    <?php
                    $picture_values = $pEstatesClone->getEstatePictureValues(
                        $id,
                    );
                    $image_alt = $picture_values['title']
                        ? esc_html($picture_values['title'])
                        : esc_html__('Immobilienbild', 'oo_theme');
                    $image_url = $pEstatesClone->getEstatePictureUrl($id);
                    ?>
                    <?php if ($is_visible_property_detail): ?>
                        <a class="c-property-card__picture-wrapper c-slider__slide splide__slide"
                           href="<?php echo esc_url($property_url); ?>"
                           aria-label="<?php echo sprintf(
                               esc_html__(
                                   'Zur Detailansicht der Immobilie Nr. %d',
                                   'oo_theme',
                               ),
                               $property_id,
                           ); ?>">
                    <?php endif; ?>

                    <?php oo_get_template('components', '', 'component-image', [
                        'image' => ['url' => $image_url, 'alt' => $image_alt],
                        'picture_class' =>
                            'c-property-card__picture o-picture' .
                            (!$is_visible_property_detail
                                ? ' c-slider__slide splide__slide'
                                : ''),
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
    <?php else: ?>
        <?php if ($is_visible_property_detail): ?>
            <a class="c-property-card__picture-wrapper"
               href="<?php echo esc_url($property_url); ?>"
               aria-label="<?php echo sprintf(
                   esc_html_x(
                       'Zur Detailansicht der Immobilie Nr. %d',
                       'oo_theme',
                   ),
                   $property_id,
               ); ?>">
        <?php endif; ?>
        <div class="c-property-card__picture"></div>
        <?php if ($is_visible_property_detail): ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($pictures_count > 0): ?>
        <div class="c-slider__navigation splide__navigation --is-properties-images-slider">
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
    <?php endif; ?>

    <?php if (
        $property_status ||
        (Favorites::isFavorizationEnabled() &&
            !$is_reference &&
            !$iframe_display)
    ): ?>
        <div class="c-property-card__flags c-flags --space-between">
            <?php if ($property_status): ?>
                <span class="c-property-card__status c-flag --property-status">
                    <?php echo ucfirst($property_status); ?>
                </span>
            <?php endif; ?>

            <?php if (
                Favorites::isFavorizationEnabled() &&
                !$is_reference &&
                !$iframe_display
            ): ?>
                <?php
                $favorite_label = Favorites::getFavorizationLabel();
                $favorite_text =
                    $favorite_label == 'Watchlist'
                        ? esc_html__('Zur Merkliste hinzufügen', 'oo_theme')
                        : esc_html__('Zu Favoriten hinzufügen', 'oo_theme');
                $favorite_icon =
                    $favorite_label == 'Watchlist' ? 'bookmark' : 'star';
                ?>
                <button class="c-property-card__favorite c-icon-button --small-corners"
                        data-onoffice-property-id="<?php echo $property_id; ?>"
                        aria-label="<?php echo $favorite_text; ?>">
                    <?php oo_get_icon($favorite_icon, true, [
                        'class' => 'c-icon-button__icon --favorite',
                    ]); ?>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

    <div class="c-property-card__content">
    <div class="c-property-card__main-content-group">
        <?php if ($current_property['objekttitel']) { ?>
            <span class="c-property-card__title o-headline --h3">
                <?php echo $current_property['objekttitel']; ?>
            </span>
        <?php } ?>
        <?php if ($is_fields) { ?>
            <div class="c-property-card__features c-item-features">
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
                                '0') || // skip negative boolean fields
                        $value == '' ||
                        empty($value) ||
                        in_array($field, $dont_echo) ||
                        in_array($field, $location_fields) ||
                        in_array($field, $price_fields)
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
                    <span class="c-item-features__item">
                        <?php
                        $dont_echo_label = [
                            'objektart',
                            'objekttyp',
                            'vermarktungsart',
                        ];
                        if (!in_array($field, $dont_echo_label)) {
                            esc_html_e($pEstates->getFieldLabel($field) . ': ');
                        }

                        if (is_array($value)) {
                            esc_html_e(implode(', ', $value));
                        } else {
                            echo esc_html($value);
                        }
                        ?>
                    </span>
                <?php
                } ?>
                <span class="c-item-features__item <?php if (
                    !$current_property['plz'] &&
                    !$current_property['ort'] &&
                    !$current_property['land']
                ) {
                    echo '--empty';
                } ?>"><?php if ($current_property['plz']) {
    echo $current_property['plz'];
} ?> <?php
 if ($current_property['ort'] && $current_property['ort'] !== '') {
     echo $current_property['ort'];
 }
 if (
     ($current_property['plz'] || $current_property['ort']) &&
     $current_property['land']
 ) {
     echo ', ';
 }
 if ($current_property['land']) {
     echo $current_property['land'];
 }
 ?></span>
            </div>
        <?php } ?>
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
    <div class="c-property-card__footer">
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
                        ($raw_values->getValueRaw($property_id)['elements'][
                            $price_field
                        ] ??
                            null) ===
                            '0') || // skip negative boolean fields
                    $price_value == '' ||
                    empty($price_value)
                ) {
                    continue;
                }
                ?>
                <div class="c-property-card__price">
                    <span class="o-headline --h4 --text-color">
                        <?php
                        esc_html_e($pEstates->getFieldLabel($price_field));
                        echo ':';
                        ?>
                        <?php if (is_array($price_value)) {
                            esc_html_e(implode(', ', $price_value));
                        } else {
                            echo esc_html($price_value);
                        } ?>
                    </span>
                </div>
            <?php
            } ?>
        <?php } ?>
        <?php if ($is_visible_property_detail) { ?>
            <a class="c-property-card__button c-button --small-corners --full-width --on-bg-transparent" href="<?php echo $property_url; ?>" aria-label="<?php echo sprintf(
    esc_html_x('Zur Detailansicht der Immobilie Nr. %d', 'oo_theme'),
    $property_id,
); ?>">
                <?php esc_html_e('Zur Detailansicht', 'oo_theme'); ?>
            </a>
        <?php } ?>
    </div>
</div>
</article>

<?php
endwhile;
?>

<?php if (Favorites::isFavorizationEnabled()) { ?>
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
<?php } ?>
