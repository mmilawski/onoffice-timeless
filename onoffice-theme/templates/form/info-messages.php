<?php
if ($pForm->getFormStatus() === onOffice\WPlugin\FormPost::MESSAGE_SUCCESS) {
    echo '<p class="c-info-messages --is-success">' .
        esc_html__(
            'Vielen Dank für Ihre Anfrage. Wir werden uns schnellstmöglich bei Ihnen melden.',
            'oo_theme',
        ) .
        '</p>';
} elseif (
    $pForm->getFormStatus() === onOffice\WPlugin\FormPost::MESSAGE_ERROR
) {
    echo '<p class="c-info-messages --is-error">' .
        esc_html__(
            'Es ist ein Fehler aufgetreten. Bitte überprüfen Sie Ihre Angaben.',
            'oo_theme',
        ) .
        '</p>';
} elseif (
    $pForm->getFormStatus() ===
    \onOffice\WPlugin\FormPost::MESSAGE_REQUIRED_FIELDS_MISSING
) {
    echo '<p class="c-info-messages --is-error">' .
        esc_html__(
            'Es wurden nicht alle Pflichtfelder ausgefüllt. Bitte überprüfen Sie Ihre Angaben.',
            'oo_theme',
        ) .
        '</p>';
} elseif (
    $pForm->getFormStatus() ===
    onOffice\WPlugin\FormPost::MESSAGE_RECAPTCHA_SPAM
) {
    echo '<p class="c-info-messages --is-error">' .
        esc_html__('Spam erkannt!', 'oo_theme') .
        '</p>';
}

?>
