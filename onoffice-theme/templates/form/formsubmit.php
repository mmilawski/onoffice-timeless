<?php

/**
 *
 *    Copyright (C) 2018  onOffice GmbH
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

// ACF
// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-footer';

$key = get_option('onoffice-settings-captcha-sitekey', '');

/** @var \onOffice\WPlugin\Form $pForm */
if ($pForm->needsReCaptcha() && $key !== '') {

    // Recaptcha validation script
    wp_enqueue_script('oo-captchacontrol-script');

    $formId = $pForm->getGenericSetting('formId');
    ?>
	<script>
		function onSubmit() {
			var element = document.getElementById(<?php echo json_encode($formId); ?>);
			element.submit();
		}
	</script>

	<div id='recaptcha' class="g-recaptcha"
		data-sitekey="<?php echo esc_attr($key); ?>"
		data-callback="onSubmit"
		data-size="invisible"></div>
	<button class="c-form__button c-button --has-icon <?php if (!empty($bg_color)) {
     echo '--on-' . $bg_color;
 } else {
     echo '--on-bg-footer';
 } ?>"><span class="c-button__text"><?php echo esc_html(
    $pForm->getGenericSetting('submitButtonLabel'),
); ?></span><span class="c-button__icon --arrow-right"><?php oo_get_icon(
    'arrow-right',
); ?></span></button>
	<script type="text/javascript">
		var reCAPTCHALoaded = false;
		function loadReCAPTCHA() {
			if (!reCAPTCHALoaded) {         
				var element = document.createElement("script");
				element.src = "https://www.google.com/recaptcha/api.js";
				document.body.appendChild(element);         
				reCAPTCHALoaded = true;
			} 
		}
		window.addEventListener("load", function(){
			var inputs = document.getElementsByClassName("o-input");
			var selects = document.getElementsByClassName("o-select");
			for (var i = 0; i < inputs.length; i++) {
				inputs[i].addEventListener("focus",loadReCAPTCHA);
			}
			for (var i = 0; i < selects.length; i++) {
				selects[i].addEventListener("change",loadReCAPTCHA);
			}
		});
	</script>
	<script>
	(function() {
		var formId = <?php echo json_encode($formId); ?>;
		var formElement = document.getElementById(formId);
		var submitButtonElement = formElement.getElementsByClassName('c-form__button')[0];
		onOffice.captchaControl(formElement, submitButtonElement);
	})();
	</script>
<?php
} else {
     ?>
	<button class="c-form__button c-button --has-icon <?php if (!empty($bg_color)) {
     echo '--on-' . $bg_color;
 } else {
     echo '--on-bg-footer';
 } ?>"><span class="c-button__text"><?php echo esc_html(
    $pForm->getGenericSetting('submitButtonLabel'),
); ?></span><span class="c-button__icon --arrow-right"><?php oo_get_icon(
    'arrow-right',
); ?></span></button>
<?php
}
