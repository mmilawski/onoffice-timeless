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
); ?> class="c-search-request o-section --<?php echo $bg_color; ?>">
    <?php echo color_bridge_vue($bg_color); ?>
    <div class="c-search-request__container o-container">
        <div class="c-search-request__row o-row">
            <?php if (
                !empty($headline['text']) ||
                !empty($text['wysiwyg'])
            ) { ?>
                <div class="c-search-request__content o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-search-request__headline',
                        ],
                    ); ?>
                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-search-request__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="c-search-request__form-wrapper o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                <?php echo do_shortcode(
                    '[on-office-vue-addons frontend="customerArea" is-search-request=true]',
                ); ?>
            </div>

            <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-search-request__buttons o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
                ]); ?>
            <?php } ?>
        </div>
	</div>
</section>
