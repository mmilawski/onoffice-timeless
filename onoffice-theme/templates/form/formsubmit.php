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

// Settings
$post_id = get_the_ID();
$is_popup = get_post_type($post_id) === OO_POPUPS_CPT_ID ?? false;

if ($is_popup) {
    $settings = get_field('popup', $post_id)['settings'] ?? null;
} else {
    $settings = get_field('settings', $post_id) ?? null;
}

$key = get_option('onoffice-settings-captcha-sitekey', '');

/** @var \onOffice\WPlugin\Form $pForm */
if ($pForm->needsReCaptcha() && $key !== '') {

    $formId = $pForm->getGenericSetting('formId');
    $pFormNo = $pForm->getFormNo();
    ?>
    <script>
        function submitForm<?php echo $pFormNo; ?>(e) {
            const selectorFormById = `form[id^="onoffice-form"] input[name="oo_formno"][value="<?php echo $pFormNo; ?>"]`;
            const form = document.querySelector(selectorFormById)?.parentElement;
            const submitButtonElement = form.querySelector('.c-form__button');

            if (!form) {
                console.error('Form not found.');
                return;
            }

            form.submit();
            submitButtonElement.disabled = true;
            submitButtonElement.classList.add('onoffice-unclickable-form');
        }
	</script>
	<div class="g-recaptcha"
		data-sitekey="<?php echo esc_attr($key); ?>" 
		data-callback="submitForm<?php echo esc_attr(
      $pFormNo,
  ); ?>" data-size="invisible">
	</div>
    <script>
        // Script needs to run on every form even on the same page.
        (() => {
            // Define the Usercentrics template IDs for Google Recaptcha and the desired consent state.
            const serviceConsents = [
                { id: 'Hko_qNsui-Q', consent: true }, // Google Recaptcha v2
                { id: 'cfADcn3E3', consent: true }, // Google Recaptcha v3
            ];

            /**
             * Asynchronously checks if the Usercentrics Consent Management Platform (CMP) is initialized.
             * @returns {Promise<boolean>} A promise that resolves to true if the CMP is ready, otherwise false.
             */
            const ensureCmpIsInitialized = async (timeoutMs = 5000) => {
              const interval = 100; // ms between checks
              const maxAttempts = timeoutMs / interval;
              let attempts = 0;

              while (attempts < maxAttempts) {
                try {
                  if (window.__ucCmp) {
                    const initialized = await window.__ucCmp.isInitialized();
                    if (initialized) {
                      return true; // Ready!
                    }
                  }
                } catch (error) {
                  console.error('Error checking CMP initialization:', error);
                }

                await new Promise(resolve => setTimeout(resolve, interval));
                attempts++;
              }

              console.warn('CMP did not initialize within timeout');
              return false;
            };


          /**
             * Checks if consent has been given for any of the specified service IDs.
             * @param {string[]} serviceIds - An array of service IDs to check for consent.
             * @returns {boolean} True if consent is granted for at least one of the IDs, otherwise false.
             */
            function hasConsent(serviceIds = []) {
                // Access the Set of whitelisted services from the Usercentrics object.
                const raw = uc?.whitelisted?.value;
                if (!(raw instanceof Set)) return false;

                // Flatten the consented service entries into a single array of IDs.
                // An entry can contain multiple IDs separated by '|'.
                const allowed = Array.from(raw)
                    .flatMap(entry => entry.split('|').map(id => id.trim()));

                // Return true if any of the required service IDs are in the allowed list.
                return serviceIds.some(id => allowed.includes(id));
            }
        
            document.addEventListener('DOMContentLoaded', () => {
                const selectorFormById = `form[id^="onoffice-form"] input[name="oo_formno"][value="<?php echo $pFormNo; ?>"]`;
                const form = document.querySelector(selectorFormById)?.parentElement;
                const submitButtonElement = form.querySelector('.c-form__button');

                if (!form || !submitButtonElement) {
                    console.warn('Form or submit button not found for CMP logic.');
                    return;
                }

                // Async IIFE to handle CMP logic.
                (async () => {
                    const isReady = await ensureCmpIsInitialized();
                    // Proceed only if the Usercentrics CMP is initialized.
                    if (isReady) {
                        // If consent for reCaptcha is already granted, enable the form's submit button.
                        if (hasConsent(["Hko_qNsui-Q", "cfADcn3E3"])) { 
                            submitButtonElement.disabled = false;
                        } else {
                            // If consent is not granted, replace the placeholder "Accept" buttons with clones that have a custom event listener.
                            document.querySelectorAll('[mock=uc-recaptcha-mock] .uc-inline-button-accept').forEach(button => {
                                // Cloning the node and replacing it is a robust way to remove all existing event listeners.
                                const newButton = button.cloneNode(true);
                                button.parentNode.replaceChild(newButton, button);

                                // Add a new listener to grant consent programmatically via the Usercentrics API.
                                newButton.addEventListener('click', async () => {
                                    try {
                                        // Update and save the consent state.
                                        __ucCmp.updateServicesConsents(serviceConsents);
                                        window.__ucCmp.saveConsents();
                                        // Enable all submit buttons on forms with reCAPTCHA once consent is given.
                                        document.querySelectorAll('form[id^="onoffice-form"]').forEach(form => {
                                            const recaptcha = form.querySelector('div.g-recaptcha');
                                            const button = form.querySelector('.c-form__button');
                                            if (recaptcha && button) {
                                                button.disabled = false;
                                            }
                                        });
                                    } catch (error) {
                                        console.error('Failed to update consents:', error);
                                    }
                                });
                            });
                        }
                    }
                })();

                if (!form) {
                    console.error('Form not found.');
                    return;
                }

                if (onOffice && typeof onOffice.captchaControl === 'function') {
                    onOffice.captchaControl(form, submitButtonElement);
                }
            });
        })();
	</script>
	<button class="c-form__button c-button <?php if (
     !empty($settings['bg_color'])
 ) {
     echo '--on-' . $settings['bg_color'];
 } else {
     echo '--on-bg-footer';
 } ?>" disabled><?php echo esc_html(
    $pForm->getGenericSetting('submitButtonLabel'),
); ?></button>


<?php
} else {
     ?>
	<button class="c-form__button c-button <?php if (
     !empty($settings['bg_color'])
 ) {
     echo '--on-' . $settings['bg_color'];
 } ?>"><?php echo esc_html(
    $pForm->getGenericSetting('submitButtonLabel'),
); ?></button>
<?php
}
