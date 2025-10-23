<?php
/**
 * Module Name: Customer Login
 * @param $args
 * Get values from the parameter
 */

$location = $args['location'] ?? 'header';
$internalUrl = '/';

wp_enqueue_script('oo-customerarea-script');

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

    // Get customer area URL if logged in
    if (
        $isLoggedIn &&
        class_exists(OnOfficeVueAddons\Service\BlockService::class)
    ) {
        $blockService = new OnOfficeVueAddons\Service\BlockService();
        $moduleUrls = $blockService->getVueModulesUrls();
        $internalUrl = !empty($moduleUrls['customerArea'])
            ? $moduleUrls['customerArea'][0]
            : '/';
    }
}

// Prepare translations for JavaScript
$translations = [
    'login' => esc_html__('Login', 'oo_theme'),
    'myAccount' => esc_html__('Mein Konto', 'oo_theme'),
];

// Escape for HTML attribute after JSON encoding
$translationsJson = esc_attr(json_encode($translations));
?>

<dl class="c-module-customer-login__list --underlined <?php echo '--on-bg-' .
    $location; ?>" 
    tabindex="0" 
    role="button" 
    data-translations="<?php echo $translationsJson; ?>"
    aria-label="<?php echo $isLoggedIn
        ? $translations['myAccount']
        : $translations['login']; ?>">
    <dt class="c-module-customer-login__label"><?php oo_get_icon(
        'user',
    ); ?></dt>
    <dd class="c-module-customer-login__value">
        <?php if ($isLoggedIn): ?>
            <a href="<?php echo $internalUrl; ?>" class="c-module-customer-login__link c-link --underlined --on-bg-header"><?php echo $translations[
    'myAccount'
]; ?></a>
        <?php else: ?>
            <span class="c-module-customer-login__link c-link --underlined --on-bg-header --open-popup" data-popup="customer-login"><?php echo $translations[
                'login'
            ]; ?></span>
        <?php endif; ?>
    </dd>
</dl>