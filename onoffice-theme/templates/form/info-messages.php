<?php
if ($pForm->getFormStatus() === onOffice\WPlugin\FormPost::MESSAGE_SUCCESS) {
    echo '<div class="c-info-messages --is-success">';
    echo '<span class="c-info-messages__icon">';
    oo_get_icon('success');
    echo '</span>';
    echo '<p class="c-info-messages__text">' .
        esc_html__(
            'Vielen Dank für Ihre Anfrage. Wir werden uns schnellstmöglich bei Ihnen melden.',
            'oo_theme',
        ) .
        '</p>';
    echo '</div>';
} elseif (
    $pForm->getFormStatus() === onOffice\WPlugin\FormPost::MESSAGE_ERROR
) {
    echo '<div class="c-info-messages --is-error">';
    echo '<span class="c-info-messages__icon">';
    oo_get_icon('error');
    echo '</span>';
    echo '<p class="c-info-messages__text">' .
        esc_html__(
            'Es ist ein Fehler aufgetreten. Bitte überprüfen Sie Ihre Angaben.',
            'oo_theme',
        ) .
        '</p>';
    echo '</div>';
} elseif (
    $pForm->getFormStatus() ===
    \onOffice\WPlugin\FormPost::MESSAGE_REQUIRED_FIELDS_MISSING
) {
    echo '<div class="c-info-messages --is-error">';
    echo '<span class="c-info-messages__icon">';
    oo_get_icon('warning');
    echo '</span>';
    echo '<p class="c-info-messages__text">' .
        esc_html__(
            'Es wurden nicht alle Pflichtfelder ausgefüllt. Bitte überprüfen Sie Ihre Angaben.',
            'oo_theme',
        ) .
        '</p>';
    echo '</div>';
} elseif (
    $pForm->getFormStatus() ===
    onOffice\WPlugin\FormPost::MESSAGE_RECAPTCHA_SPAM
) {
    echo '<div class="c-info-messages --is-error">';
    echo '<span class="c-info-messages__icon">';
    oo_get_icon('error');
    echo '</span>';
    echo '<p class="c-info-messages__text">' .
        esc_html__('Spam erkannt!', 'oo_theme') .
        '</p>';
    echo '</div>';
}

if ($pForm->getFormStatus()) {
    ?>
    <script>
      window.addEventListener("load", function () {
        const message = document.querySelector(".c-info-messages");
        if (message) {
          message.scrollIntoView({ behavior: "smooth", block: "center" });
          const form = message.parentElement
          if(form && form.classList.contains('c-form')){
            form.reset()
            form.querySelectorAll('input, textarea').forEach(el => {
              el.value = ''
            })
            document.querySelectorAll('select.tomselected').forEach(sel => {
              sel.tomselect.clear()
            })
          }
        }
      });
    </script>
    <?php
}

?>
