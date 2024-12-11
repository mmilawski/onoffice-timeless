<?php
/**
 *
 *    Copyright (C) 2018  onOffice Software AG
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

include get_stylesheet_directory() . '/onoffice-theme/templates/fields.php';

// Early return if no fields
$visible = $pEstates->getVisibleFilterableFields();
if (!is_array($visible) || count($visible) === 0) {
    return;
}

// ACF
// Content
$slider = get_field('slider') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

if (get_field('property_search_result')) {
    $result = get_field('property_search_result') ?? null;
} elseif (get_field('sites', 'option')['property_search_result']) {
    $result = get_field('sites', 'option')['property_search_result'] ?? null;
} else {
    $result = null;
}
?>

<form <?php if (!empty($result)) {
    echo 'action="' . get_permalink($result) . '"';
} ?> method="get" class="c-form --is-search-form <?php if (!empty($slider)) {
     echo '--on-banner';
 } ?> <?php if (!empty($bg_color)) {
     echo ' --on-' . $bg_color;
 } else {
     echo '--on-bg-footer';
 } ?>" data-estate-search-name="<?php echo esc_attr($getListName()); ?>">
    <fieldset class="c-form__fieldset">
        <?php
        $number = 0;
        $fields_counter = is_array($visible) ? count($visible) : 0;
        foreach ($visible as $inputName => $properties) {
            if ($fields_counter > 12) {

                if ($number === 12) { ?>
                    <div class="c-form__field-wrapper">
                <?php }
                renderFieldEstateSearch($inputName, $properties);
                if ($number == $fields_counter - 1) { ?>
                        <div class="c-form__button-wrapper">
                            <button class="c-form__button c-button --has-icon <?php if (
                                !empty($bg_color)
                            ) {
                                echo '--on-' . $bg_color;
                            } ?>">
                                <span class="c-button__text"><?php echo esc_attr__(
                                    'Suchen',
                                    'oo_theme',
                                ); ?></span>
                            </button>
                        </div>
                    </div>

                    <div class="c-form__more c-read-more">
                        <div class="c-read-more__wrapper --more">
                            <span class="c-read-more__icon c-button --only-icon">
                                <span class="c-button__icon --plus"><?php oo_get_icon(
                                    'plus',
                                ); ?></span>
                            </span>  
                            <span class="c-read-more__text"><?php echo esc_html(
                                'Mehr anzeigen',
                                'oo_theme',
                            ); ?></span>
                        </div>
                        <div class="c-read-more__wrapper --less">
                            <span class="c-read-more__icon c-button --only-icon">
                                <span class="c-button__icon --plus"><?php oo_get_icon(
                                    'minus',
                                ); ?></span>
                            </span>  
                            <span class="c-read-more__text"><?php echo esc_html(
                                'Weniger anzeigen',
                                'oo_theme',
                            ); ?></span>
                        </div>
				    </div>
                <?php }
                ?>
            <?php
            } else {
                renderFieldEstateSearch($inputName, $properties); ?>
                <?php if ($number == $fields_counter - 1) { ?>
                    <div class="c-form__button-wrapper">
                        <button class="c-form__button c-button --has-icon <?php if (
                            !empty($bg_color)
                        ) {
                            echo '--on-' . $bg_color;
                        } ?>">
                            <span class="c-button__text"><?php echo esc_attr__(
                                'Suchen',
                                'oo_theme',
                            ); ?></span>
                            <span class="c-button__icon --arrow-right"><?php oo_get_icon(
                                'arrow-right',
                            ); ?></span>
                        </button>
                    </div>
                <?php }
            }
            $number++;
        }
        ?>
    </fieldset>
</form>