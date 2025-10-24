<?php

// Content
$background_image = get_field('background_image') ?? [];
$background_image_url = $background_image['url'] ?? null;
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$boxes = get_field('boxes') ?? [];
$buttons = get_field('buttons') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'], FILTER_VALIDATE_BOOLEAN);

// Background size
$background_width_xs = '576';
$background_width_sm = '768';
$background_width_md = '992';
$background_width_lg = '1200';
$background_width_xl = '1400';
$background_width_xxl = '1600';
$background_width_xxxl = '1920';
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-link-boxes o-section --<?php echo $bg_color; ?> <?php echo !empty(
     $background_image_url
 )
     ? '--has-bg-image'
     : ''; ?>">

    <?php if (!empty($background_image_url)): ?>
        <div class="c-link-boxes__background">
        <?php oo_get_template('components', '', 'component-image', [
            'image' => $background_image,
            'picture_class' => 'c-link-boxes__picture o-picture',
            'image_class' => 'c-link-boxes__image o-image',
            'dimensions' => [
                '575' => [
                    'w' => $background_width_xs,
                ],
                '1600' => [
                    'w' => $background_width_xxxl,
                ],
                '1400' => [
                    'w' => $background_width_xxl,
                ],
                '1200' => [
                    'w' => $background_width_xl,
                ],
                '992' => [
                    'w' => $background_width_lg,
                ],
                '768' => [
                    'w' => $background_width_md,
                ],
                '576' => [
                    'w' => $background_width_sm,
                ],
            ],
        ]); ?>
        </div>
    <?php endif; ?>
 
    <div class="c-link-boxes__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-link-boxes__wrapper o-row">
                <div class="c-link-boxes__content o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-link-boxes__headline',
                        ],
                    ); ?>
            <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-link-boxes__text o-text --is-wysiwyg">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
            </div>
        <?php } ?>
    
        <?php if (!empty($boxes)) { ?>
            <?php if ($is_slider) { ?>
                <div class="c-link-boxes__slider --on-<?php echo $bg_color; ?> c-slider --is-link-boxes-slider splide" data-splide='{"perPage":1,"perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"768":{"perPage":2},"1200":{"perPage":3}}}'>
                    <div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                            <?php include 'link-boxes-card.php'; ?>
                        </div>
                    </div>

                    <div class="c-slider__navigation splide__navigation">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow c-icon-button --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon c-icon-button__icon --chevron-left"><?php oo_get_icon(
                                    'chevron-left',
                                ); ?></span>
                            </button>
                            <button class="c-slider__arrow c-icon-button --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-slider__arrow-icon c-icon-button__icon --chevron-right"><?php oo_get_icon(
                                    'chevron-right',
                                ); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="c-link-boxes__boxes">
                    <?php include 'link-boxes-card.php'; ?>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-link-boxes__buttons-wrapper o-row">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-link-boxes__buttons o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
                ]); ?>
            </div>
        <?php } ?>
    
    </div>
</section>