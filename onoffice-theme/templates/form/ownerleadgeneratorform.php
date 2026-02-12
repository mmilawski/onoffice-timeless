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

use onOffice\WPlugin\Form;
use onOffice\WPlugin\FormPost;

include get_template_directory() . '/onoffice-theme/templates/fields.php';

wp_enqueue_script('oo-paging-script');
wp_enqueue_script('oo-leadgenerator-script');

// ACF
// Settings
$settings = get_field('settings') ?? [];

$showFormAsModal =
    $pForm->getShowFormAsModal() &&
    $pForm->getFormStatus() !== FormPost::MESSAGE_SUCCESS;

$estateValues = [];
$hiddenValues = [];

if ($pForm->getFormStatus() !== FormPost::MESSAGE_SUCCESS) {
    /* @var $pForm Form */
    // Define which fields should be rendered as icon buttons
    $iconButtonFields = ['nutzungsart', 'objektart', 'objekttyp'];

    foreach ($pForm->getInputFields() as $input => $table) {
        if ($pForm->isHiddenField($input)) {
            $hiddenValues[] = renderFormField($input, $pForm);
            continue;
        }

        if (
            in_array($input, $iconButtonFields) &&
            function_exists('renderIconButtonsField')
        ) {
            $line = renderIconButtonsField($input, $pForm);
        } else {
            $line = renderFormField($input, $pForm);
        }

        // Wrap each field/group in its own container for individual styling and spacing.
        $line = '<div class="c-form__field-group">' . $line . '</div>';

        $pageNumber = $pForm->getPagePerForm($input);
        if (!isset($estateValues[$pageNumber])) {
            $estateValues[$pageNumber] = [];
        }
        $estateValues[$pageNumber][] = $line;
    }
}

// Capture form content in buffer
ob_start();
?>
<form method="post" action="#onoffice-form" id="onoffice-form-<?php echo $pForm->getFormNo(); ?>" class="c-form --is-owner-leadgenerator-form --custom-validation">
    <input type="hidden" name="oo_formid" value="<?php echo $pForm->getFormId(); ?>">
    <input type="hidden" name="oo_formno" value="<?php echo $pForm->getFormNo(); ?>">
    <?php wp_nonce_field(
        'onoffice_form_' . esc_attr($pForm->getFormId()),
        'onoffice_nonce',
        false,
    ); ?>
    <?php // Info Messages

require 'info-messages.php'; ?>

    <?php if ($pForm->getFormStatus() !== FormPost::MESSAGE_SUCCESS): ?>
        <div data-oo-form-paging data-form-id="leadform-<?php echo sanitize_title(
            $pForm->getFormId(),
        ); ?>" data-form-no="<?php echo $pForm->getFormNo(); ?>" class="c-form__wrapper">
            <?php
            $totalPages = count($estateValues);
            $rawPageTitles = $pForm->getPageTitlesByCurrentLanguage();
            $pageTitles = [];
            if (is_array($rawPageTitles)) {
                foreach ($rawPageTitles as $titleData) {
                    $pageTitles[(int) $titleData['page'] - 1] =
                        $titleData['value'];
                }
            }
            ?>
            <div id="leadform-<?php echo sanitize_title(
                $pForm->getFormId(),
            ); ?>">
                <?php foreach ($estateValues as $pageNumber => $fields): ?>
                    <div class="lead-lightbox lead-page-<?php echo $pageNumber; ?>">
                        <div class="c-form__fieldset">
                            <div class="c-form__header">
                                <legend class="c-form__legend">
                                    <?php echo $pageTitles[
                                        $pageNumber - 1
                                    ]; ?></legend>
                                <div class="c-form__required"><?php echo esc_html__(
                                    '* Pflichtfelder',
                                    'oo_theme',
                                ); ?></div>
                            </div>
                                <?php echo implode($fields); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php echo implode($hiddenValues); ?>
            </div>
            <div class="c-form__button-wrapper --is-paged">
                <?php if ($totalPages > 1): ?>
                    <button type="button" class="c-form__button c-button --ghost leadform-back --is-hidden">
                        <?php echo esc_html__('Zurück', 'oo_theme'); ?>
                    </button>

                    <div class="c-form__progress leadform-progress"></div>

                    <button type="button" class="c-form__button c-button leadform-forward">
                        <?php echo esc_html__('Weiter', 'oo_theme'); ?>
                    </button>
                <?php endif; ?>

                <div class="leadform-submit" style="display:none;">
                    <?php include get_template_directory() .
                        '/onoffice-theme/templates/form/formsubmit.php'; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</form>
<?php
$form_content = ob_get_clean();

// Render form based on display setting
if ($showFormAsModal):
    // Use the form modal component
    oo_get_template('components', '', 'component-form-modal', [
        'modal_id' => 'leadform-modal-' . $pForm->getFormId(),
        'modal_title' => __('Ihre Immobilie bewerten lassen', 'oo_theme'),
        'pageTitles' => $pageTitles,
        'button_text' => __('Bewertung starten', 'oo_theme'),
        'button_class' => 'c-button --primary',
        'form_content' => $form_content,
        'form_id' => $pForm->getFormId(),
        'form_no' => $pForm->getFormNo(),
        'additional_dialog_class' => '--is-form-modal --is-leadgen',
    ]);
    // Render form directly
else:
    echo $form_content;
endif;


?>
