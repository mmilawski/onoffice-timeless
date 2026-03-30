<?php
/**
 * Exposé Download Button Component
 */

if (isset($args) && is_array($args)) {
    extract($args);
}

if (!isset($pEstates) || !$pEstates->getDocument()) {
    return;
}

$marketing_type = $pEstates->getRawValues()->getValueRaw($property_id)[
'elements'
]['vermarktungsart'];
$revocation_type = 'sale';
if ($marketing_type === 'miete') {
    $revocation_type = 'rent';
}
$revocation = function_exists('oo_get_revocation_settings')
        ? oo_get_revocation_settings($revocation_type) ?? []
        : [];

$is_revocation_enabled =
        !empty($revocation) && ($revocation['selection'] ?? 'none') !== 'none';
$is_user_logged_in = oo_is_user_logged_in();

$revocation_completed = false;
$show_direct_download = false;
$download_url = '';

$current_user_id = get_current_user_id();
$cookie_name = 'oo_revocation_list_u' . $current_user_id;
$allowed_ids = isset($_COOKIE[$cookie_name])
        ? array_filter(explode(',', $_COOKIE[$cookie_name]))
        : [];

if (isset($_GET['revocation_token'])) {
    $token = sanitize_text_field($_GET['revocation_token']);
    $stored_property_id = get_transient('revocation_token_' . $token);

    if (
            $stored_property_id !== false &&
            (int) $stored_property_id === (int) $property_id
    ) {
        $show_direct_download = true;
        $revocation_completed = true;
        $download_url = $pEstates->getDocument();

        $prop_id_str = (string) ($property_id ?? 0);
        if (!in_array($prop_id_str, $allowed_ids)) {
            $allowed_ids[] = $prop_id_str;

            if (count($allowed_ids) > 50) {
                $allowed_ids = array_slice($allowed_ids, -50);
            }

            $cookie_value = implode(',', array_unique($allowed_ids));
            if (!headers_sent()) {
                setcookie(
                        $cookie_name,
                        $cookie_value,
                        time() + 30 * DAY_IN_SECONDS,
                        COOKIEPATH,
                        COOKIE_DOMAIN,
                        is_ssl(),
                        true,
                );
                $_COOKIE[$cookie_name] = $cookie_value;
            }
        }
    }
}

if (!$show_direct_download) {
    $prop_id_str = (string) ($property_id ?? 0);
    if (in_array($prop_id_str, $allowed_ids)) {
        $revocation_completed = true;
        $download_url = $pEstates->getDocument();
    }
}

// Button Text
$button_text = $is_revocation_enabled
        ? esc_html__('Jetzt Exposé anfordern', 'oo_theme')
        : esc_html__('Details als PDF', 'oo_theme');
?>

<div class="c-property-details__share c-property-details__expose">
    <?php if ($is_revocation_enabled): ?>
        <?php if (
                $show_direct_download ||
                ($is_user_logged_in && $revocation_completed)
        ): ?>
            <a class="c-property-details__expose-button c-button --has-icon --ghost"
               href="<?php echo esc_url($download_url); ?>"
               rel="noopener noreferrer">
                <span class="c-button__text"><?php echo $button_text; ?></span>
                <span class="c-button__icon"><?php oo_get_icon('download'); ?></span>
            </a>
        <?php elseif ($is_user_logged_in): ?>
            <?php
            $revocation_url = '';

            if (
                    $revocation['selection'] === 'individual' &&
                    !empty($revocation['link'])
            ) {
                $revocation_url = oo_generate_agreement_link_for_property(
                        $property_id,
                        $revocation['link'],
                );
            } else {
                $revocation_url = oo_generate_agreement_link_for_property(
                        $property_id,
                );
            }

            if (empty($revocation_url)) {
                $revocation_url = $pEstates->getDocument();
            }
            ?>
            <a class="c-property-details__expose-button c-button --has-icon --ghost"
               href="<?php echo esc_url($revocation_url); ?>"
               rel="noopener noreferrer">
                <span class="c-button__text"><?php echo $button_text; ?></span>
                <span class="c-button__icon"><?php oo_get_icon('download'); ?></span>
            </a>
        <?php else: ?>
            <a class="c-property-details__expose-button c-button --has-icon --ghost --open-popup"
               data-popup="customer-login"
               data-feature="revocation"
               data-forceurl="<?php echo esc_url(
                       add_query_arg('auth', time(), $property_link ?? ''),
               ); ?>"
               data-forceurl-target="_self"
               onclick="if (document.cookie.indexOf('laravel_user_token') !== -1) { location.href = this.getAttribute('data-forceurl'); return false; } sessionStorage.setItem('oo_pending_expose_request', 'true')">
                <span class="c-button__text"><?php echo $button_text; ?></span>
                <span class="c-button__icon"><?php oo_get_icon('download'); ?></span>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <a class="c-property-details__expose-button c-button --has-icon --ghost"
           href="<?php echo esc_url($pEstates->getDocument()); ?>"
           download>
            <span class="c-button__text"><?php echo $button_text; ?></span>
            <span class="c-button__icon"><?php oo_get_icon('download'); ?></span>
        </a>
    <?php endif; ?>
</div>