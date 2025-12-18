<?php
/**
 * Sticky Google Reviews Pop-up Template
 */

// Helpers
$content = $args ?? [];
$text = $content['text'] ?? null;
$bg_color = $content['settings']['bg_color'] ?? 'bg-transparent';

$popup_id = $content['id'] ?? 'google-rating-fixed';
$type = $content['type'] ?? 'sticky-google-reviews';
?>

<dialog <?php echo oo_popup_get_id_attribute(
    $popup_id,
); ?> <?php echo oo_popup_get_data_attributes(
     $popup_id,
     $content,
     $type,
 ); ?> class="c-popup-sticky --<?php echo $bg_color; ?> --is-sticky-google-reviews" role="status" aria-live="polite" aria-atomic="true">
    <div class="c-popup-sticky__wrapper">
        <button class="c-popup-sticky__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
            'Fenster schließen',
            'oo_theme',
        ); ?>">
            <?php oo_get_icon('close', true, [
                'class' => 'c-icon-button__icon --close',
            ]); ?>
        </button>
        <?php if (!empty($text)) { ?>
            <div class="c-popup-sticky__content">
                <div class="c-popup-sticky__text --is-wysiwyg">
                    <?php echo oo_convert_headings_to_spans($text); ?>
                </div>
            </div>
        <?php } ?>
    </div>
</dialog>