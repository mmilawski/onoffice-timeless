<?php
/**
 *
 *    Copyright (C) 2016-2019 onOffice GmbH
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
$bg_color = $settings['bg_color'] ?? 'bg-footer';
?>
<form method="post" action="#onoffice-form" id="onoffice-form" class="c-form --is-interest-form --on-<?php echo $bg_color; ?>">

    <input type="hidden" name="oo_formid" value="<?php echo $pForm->getFormId(); ?>">
    <input type="hidden" name="oo_formno" value="<?php echo $pForm->getFormNo(); ?>">

<?php
$addressValues = [];
$searchcriteriaValues = [];
$hiddenValues = [];
$otherValues = [];

// Info Messages
require 'info-messages.php';

/* @var $pForm \onOffice\WPlugin\Form */
foreach ($pForm->getInputFields() as $input => $table) {
    if (method_exists($pForm, 'isHiddenField')) {
        if ($pForm->isHiddenField($input)) {
            $hiddenValues[] = renderFormField($input, $pForm);
            continue;
        }
    }
    $line = renderFormField($input, $pForm);
    if (
        in_array($input, ['gdprcheckbox', 'Id']) ||
        in_array($input, ['newsletter', 'Id']) ||
        in_array($input, ['krit_bemerkung_oeffentlich', 'Id']) ||
        in_array($input, ['message', 'Id']) ||
        in_array($input, ['AGB_akzeptiert', 'Id']) ||
        in_array($input, ['Rueckruf_akzeptiert', 'Id'])
    ) {
        $table = 'other';
    }
    if ($table == 'address') {
        $addressValues[] = $line;
    }
    if ($table == 'searchcriteria') {
        $searchcriteriaValues[] = $line;
    }
    if ($table == '') {
        $addressValues[] = $line;
    }
    if ($table == 'other') {
        $otherValues[] = $line;
    }
}
?>

<?php if (isset($estateId)) {
    /** @var \onOffice\WPlugin\Form $pForm */
    echo '<p class="c-form__context">' .
        $pForm->getEstateContextLabel() .
        '</p>';
} ?>

    <fieldset class="c-form__fieldset">
        <div class="c-form__header">
            <p class="c-form__legend"><?php echo esc_html__(
                'Ihre Kontaktdaten',
                'oo_theme',
            ); ?></p>
            <p class="c-form__required"><?php echo esc_html__(
                '* Pflichtfelder',
                'oo_theme',
            ); ?></p>
        </div>
        <?php if (is_array($addressValues)) {
            echo implode($addressValues);
        } ?>
    </fieldset>

    <fieldset class="c-form__fieldset">
        <div class="c-form__header">
            <p class="c-form__legend"><?php echo esc_html__(
                'Ihre Suchkriterien',
                'oo_theme',
            ); ?></p>
        </div>
        <?php if (is_array($searchcriteriaValues)) {
            echo implode($searchcriteriaValues);
        } ?>
        <?php if (is_array($otherValues)) {
            echo implode($otherValues);
        } ?>
        <?php if (is_array($hiddenValues)) {
            echo implode($hiddenValues);
        } ?>
        <div class="c-form__button-wrapper">
            <?php include get_template_directory() .
                '/onoffice-theme/templates/form/formsubmit.php'; ?>
        </div>
    </fieldset>

</form>
