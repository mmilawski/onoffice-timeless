<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$buttons = get_field('buttons') ?? null;

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-leadgenerator o-section --<?php echo $bg_color; ?>">
    <?php echo color_bridge_vue($bg_color); ?>
    <div class="c-leadgenerator__container o-container">
    <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-leadgenerator__content o-row --position-center">
            <?php oo_get_template('components', '', 'component-headline', [
                'headline' => $headline,
                'additional_headline_class' =>
                    'c-leadgenerator__headline o-col-12 o-col-xl-8',
            ]); ?> 
            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-leadgenerator__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>
            </div>

        <?php } ?>

        <?php echo do_shortcode(
            '[on-office-vue-addons frontend="customerArea" is-leadgen=true]',
        ); ?>

        <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-leadgenerator__buttons-wrapper o-row --position-center">
            <?php oo_get_template('components', '', 'component-buttons', [
                'buttons' => $buttons['buttons'],
                'additional_button_class' => $bg_color
                    ? '--on-' . $bg_color
                    : '',
                'additional_container_class' =>
                    'c-leadgenerator__buttons o-col-12 --position-center',
            ]); ?>
            </div>
        <?php } ?>
	</div>
</section>