<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$buttons = get_field('buttons') ?? null;

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

$appointment_type = get_field('appointment_type') ?? null;
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-appointment o-section --<?php echo $bg_color; ?>">
    <div class="c-appointment__container o-container">
    <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-appointment__content o-row --position-center">
            <?php oo_get_template('components', '', 'component-headline', [
                'headline' => $headline,
                'additional_headline_class' =>
                    'c-appointment__headline o-col-12 o-col-xl-8',
            ]); ?> 
            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-appointment__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
        </div>

        <?php } ?>
            <?php if (!empty($appointment_type)) {
                echo do_shortcode(
                    "[on-office-vue-addons frontend='booking' profile='" .
                        esc_attr($appointment_type) .
                        "']",
                );
            } ?>
                  <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
                    <div class="c-appointment__buttons-wrapper o-row --position-center">
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-buttons',
                        [
                            'buttons' => $buttons['buttons'],
                            'additional_button_class' => $bg_color
                                ? '--on-' . $bg_color
                                : '',
                            'additional_container_class' =>
                                'c-appointment__buttons o-col-12 --position-center',
                        ],
                    ); ?>
            </div>
        <?php } ?>
	</div>
</section>