<?php

$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$buttons = get_field('buttons') ?? null;
$anchor = get_field('anchor') ?? null;

$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
?>
<section <?php oo_block_id(
    $block,
); ?> class="c-customer-area o-section --<?php echo $bg_color; ?>">
    <div class="c-customer-area__container o-container">

    <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-customer-area__content o-row --position-center">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-customer-area__headline o-col-12 o-col-xl-8',
                        ],
                    ); ?> 
                <?php } ?>
    
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-customer-area__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
    
            </div>
    <?php } ?>

    <?php echo do_shortcode(
        "[on-office-vue-addons frontend='customerArea']",
    ); ?>

    <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-customer-area__content o-row --position-center">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-customer-area__buttons o-col-12 --position-center',
                ]); ?>
            </div>
    <?php } ?>

</div>
</section>