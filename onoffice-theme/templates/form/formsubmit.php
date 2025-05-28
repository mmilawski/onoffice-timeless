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
            const selectorFormById = `form[id="onoffice-form"] input[name="oo_formno"][value="<?php echo $pFormNo; ?>"]`;
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
        document.addEventListener('DOMContentLoaded', () => {
            const selectorFormById = `form[id="onoffice-form"] input[name="oo_formno"][value="<?php echo $pFormNo; ?>"]`;
            const form = document.querySelector(selectorFormById)?.parentElement;
            const submitButtonElement = form.querySelector('.c-form__button');

            if (!form) {
                console.error('Form not found.');
                return;
            }

            if (onOffice && typeof onOffice.captchaControl === 'function') {
                onOffice.captchaControl(form, submitButtonElement);
            }
        });
	</script>
	<button class="c-form__button c-button <?php if (
     !empty($settings['bg_color'])
 ) {
     echo '--on-' . $settings['bg_color'];
 } else {
     echo '--on-bg-footer';
 } ?>"><?php echo esc_html(
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
