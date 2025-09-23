<?php

/** @var array $args */

// Modal Configuration
$modal_id = $args['modal_id'] ?? 'form-modal';
$modal_title = $args['modal_title'] ?? __('Formular', 'oo_theme');
$modal_class = $args['modal_class'] ?? '';

// Button Configuration
$button_text = $args['button_text'] ?? __('Formular öffnen', 'oo_theme');
$button_class = $args['button_class'] ?? 'c-button';
$button_icon = $args['button_icon'] ?? null;

// Form Configuration
$form_content = $args['form_content'] ?? '';
$form_id = $args['form_id'] ?? null;
$form_no = $args['form_no'] ?? null;

// Display Options
$show_header = $args['show_header'] ?? true;
$additional_dialog_class = $args['additional_dialog_class'] ?? '';
?>

<button class="<?php echo esc_attr($button_class); ?> --open-popup" 
        data-popup="<?php echo esc_attr($modal_id); ?>" 
        aria-haspopup="dialog" 
        aria-controls="<?php echo esc_attr($modal_id); ?>">
    <?php if ($button_icon): ?>
        <?php oo_get_icon($button_icon, true, [
            'class' => 'c-button__icon',
        ]); ?>
    <?php endif; ?>
    <span class="c-button__text"><?php echo esc_html($button_text); ?></span>
</button>

<dialog id="<?php echo esc_attr($modal_id); ?>" 
        class="c-dialog --is-form <?php echo esc_attr(
            $additional_dialog_class,
        ); ?>" 
        aria-label="<?php echo esc_attr($modal_title); ?>"
        aria-describedby="<?php echo esc_attr($modal_id); ?>-desc"
        <?php if ($form_id): ?>data-form-id="<?php echo esc_attr(
    $form_id,
); ?>"<?php endif; ?>
        <?php if ($form_no): ?>data-form-no="<?php echo esc_attr(
    $form_no,
); ?>"<?php endif; ?>>
    <div class="c-dialog__wrapper">
        <?php if ($show_header): ?>
            <div class="c-dialog__header">
                <h2 id="<?php echo esc_attr(
                    $modal_id,
                ); ?>-title" class="c-dialog__title">
                    <?php echo esc_html($modal_title); ?>
                </h2>
                <button class="c-dialog__close --close-popup c-icon-button" 
                    aria-label="<?php esc_attr_e(
                        'Fenster schließen',
                        'oo_theme',
                    ); ?>">
                    <?php echo oo_get_icon('close', true, [
                        'class' => 'c-icon-button__icon --close',
                    ]); ?>
                </button>
            </div>
        <?php endif; ?>
        <div class="c-dialog__content" id="<?php echo esc_attr(
            $modal_id,
        ); ?>-desc">
            <?php echo $form_content; ?>
        </div>
    </div>
</dialog>