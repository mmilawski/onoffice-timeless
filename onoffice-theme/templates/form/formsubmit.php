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

$buttonClass = 'c-form__button c-button oo-js-submit-button';
$buttonLabel = esc_html($pForm->getGenericSetting('submitButtonLabel'));

/** @var \onOffice\WPlugin\Form $pForm */
if (!$pForm->needsReCaptcha()) {
    echo '<button class="' . $buttonClass . '">' . $buttonLabel . '</button>';
    return;
}

$config = oo_get_recaptcha_config();
$formNo = $pForm->getFormNo();

if ($config['type'] === 'none') {
    echo '<button class="' . $buttonClass . '">' . $buttonLabel . '</button>';
    return;
}

wp_enqueue_script('oo-recaptcha');

if ($config['type'] === 'enterprise') {
    oo_render_recaptcha_enterprise(
        $formNo,
        $config['siteKey'],
        $buttonClass,
        $buttonLabel,
    ); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ooRecaptchaEnterpriseInit(<?php echo json_encode(
                $formNo,
            ); ?>, <?php echo json_encode($config['siteKey']); ?>);
            ooRecaptchaUsercentrics(<?php echo json_encode($formNo); ?>);
        });
    </script>
    <?php
} else {
    // TODO: Remove later, when Enterprise reCAPTCHA is fully rolled out
    oo_render_recaptcha_classic(
        $formNo,
        $config['siteKey'],
        $buttonClass,
        $buttonLabel,
    ); ?>
    <script>
        window['submitForm<?php echo esc_js($formNo); ?>'] = function() {
            ooRecaptchaClassicSubmit(<?php echo json_encode($formNo); ?>);
        };
        document.addEventListener('DOMContentLoaded', function() {
            let form = document.querySelector('form[id^="onoffice-form"] input[name="oo_formno"][value="<?php echo esc_js(
                $formNo,
            ); ?>"]')?.parentElement;
            let btn = form?.querySelector('.oo-js-submit-button');
            if (form && btn && onOffice?.captchaControl) {
                onOffice.captchaControl(form, btn);
            }
            ooRecaptchaUsercentrics(<?php echo json_encode($formNo); ?>);
        });
    </script>
    <?php
}
