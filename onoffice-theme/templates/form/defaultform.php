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

include get_template_directory() . '/onoffice-theme/templates/fields.php';

// ACF
// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? null;
?>

<form method="post" action="#onoffice-form" id="onoffice-form-<?php echo $pForm->getFormNo(); ?>" class="c-form --is-contact-form <?php if (
    !empty($bg_color)
) {
    echo '--on-' . $bg_color;
} ?>">

    <?php if (!empty($pForm->getEstateContextLabel())) {
        echo '<h2 class="c-form__property-context o-headline">';
        echo $pForm->getEstateContextLabel();
        echo '</h2>';
    } ?>
    <input type="hidden" name="oo_formid" value="<?php echo $pForm->getFormId(); ?>">
    <input type="hidden" name="oo_formno" value="<?php echo $pForm->getFormNo(); ?>">

    <?php if (isset($estateId)): ?>
        <input type="hidden" name="Id" value="<?php echo esc_attr(
            $estateId,
        ); ?>">
    <?php endif; ?>

<?php
$addressValues = [];

// Info Messages
require 'info-messages.php';

/* @var $pForm \onOffice\WPlugin\Form */
foreach ($pForm->getInputFields() as $input => $table) {
    $line = renderFormField($input, $pForm);

    $addressValues[] = $line;
}
?>

<?php if (isset($estateId)) {
    /** @var \onOffice\WPlugin\Form $pForm */
    echo '<p class="c-form__context">' .
        $pForm->getEstateContextLabel() .
        '</p>';
} ?>

    <div class="c-form__fieldset">
        <div class="c-form__header">
            <legend class="c-form__legend"><?php esc_html_e(
                'Ihre Kontaktdaten',
                'oo_theme',
            ); ?></legend>
            <p class="c-form__required"><?php esc_html_e(
                '* Pflichtfelder',
                'oo_theme',
            ); ?></p>
        </div>
        <div class="c-form__body">
            <?php if (is_array($addressValues)) {
                echo implode($addressValues);
            } ?>
            <div class="c-form__button-wrapper">
                <?php include get_template_directory() .
                    '/onoffice-theme/templates/form/formsubmit.php'; ?>
            </div>
        </div>
    </div>
</form>
