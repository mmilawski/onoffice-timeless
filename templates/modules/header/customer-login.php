<?php
/**
 * Module Name: Customer Login
 * @param $args
 * Get values from the parameter
 */

$location = $args['location'] ?? 'header';
$internalUrl = '';

if (function_exists('oo_is_customer_area_available')) {
    if (!oo_is_customer_area_available()) {
        return;
    }
}

$isLoggedIn = false;

if (
    is_plugin_active('oo-vue-addons/on-office-vue-addons.php') &&
    class_exists(OnOfficeVueAddons\Service\AuthService::class)
) {
    $authService = new OnOfficeVueAddons\Service\AuthService();
    $isLoggedIn = $authService->isUserLoggedIn();
}
?>

<dl class="c-module-login__list --underlined <?php echo '--on-bg-' .
    $location; ?>" tabindex="0" role="button" aria-label="<?php if (
    $isLoggedIn
) {
    esc_html_e('Mein Konto', 'oo_theme');
} else {
    esc_html_e('Login', 'oo_theme');
} ?>">
    <dt class="c-module-login__label"><?php oo_get_icon('user'); ?></dt>
    <dd class="c-module-login__value">
        <?php if ($isLoggedIn): ?>
            <a href="<?php echo $internalUrl; ?>" class="c-module-login__link c-link --underlined --on-bg-header"><?php esc_html_e(
    'Mein Konto',
    'oo_theme',
); ?></a>
        <?php else: ?>
            <span class="c-module-login__link c-link --underlined --on-bg-header --open-popup" data-popup="customer-login"><?php esc_html_e(
                'Login',
                'oo_theme',
            ); ?></span>
        <?php endif; ?>
    </dd>
</dl>

<script>
    const loginModuleButton = document.querySelector('.c-module-login__link.--open-popup');
    // Add keydown listener to parent dl element if it exists
    const parentDl = loginModuleButton.closest('.c-module-login__list[role="button"]');
    if (parentDl) {
        parentDl.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                loginModuleButton.click();
            }
        });
    }
</script>