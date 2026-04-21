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

$property_link =
    isset($property_link) && is_string($property_link) ? $property_link : '';

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

$revocation_completed = function_exists('oo_check_revocation_status')
    ? oo_check_revocation_status((int) $property_id)
    : false;
$download_url = $pEstates->getDocument();

$current_user_id = get_current_user_id();

// Button Text
$button_text = $is_revocation_enabled
    ? ($revocation_completed
        ? esc_html__('Exposé herunterladen', 'oo_theme')
        : esc_html__('Jetzt Exposé anfordern', 'oo_theme'))
    : esc_html__('Details als PDF', 'oo_theme');
?>

<div class="c-property-details__share c-property-details__expose">
    <?php if ($is_revocation_enabled): ?>
        <?php if ($is_user_logged_in && $revocation_completed): ?>
            <a class="c-property-details__expose-button c-button --has-icon --ghost"
               href="<?php echo esc_url($download_url); ?>"
               rel="noopener noreferrer">
                <span class="c-button__text"><?php echo $button_text; ?></span>
                <span class="c-button__icon"><?php oo_get_icon(
                    'download',
                ); ?></span>
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
                <span class="c-button__icon"><?php oo_get_icon(
                    'download',
                ); ?></span>
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
                <span class="c-button__icon"><?php oo_get_icon(
                    'download',
                ); ?></span>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <a class="c-property-details__expose-button c-button --has-icon --ghost"
           href="<?php echo esc_url($pEstates->getDocument()); ?>"
           download>
            <span class="c-button__text"><?php echo $button_text; ?></span>
            <span class="c-button__icon"><?php oo_get_icon(
                'download',
            ); ?></span>
        </a>
    <?php endif; ?>
</div>