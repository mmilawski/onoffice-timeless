<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$counters = get_field('counters') ?: [];

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$background_image = get_field('background_image') ?? [];
$background_image_url = $background_image['url'] ?? null;
$animation_speed = get_field('animation_speed') ?? 2;
$animation_speed = max(1, min(5, round($animation_speed, 1)));
$buttons = get_field('buttons') ?? [];
$anchor_id = get_field('anchor_id') ?? '';

$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Background size
$background_width_xs = '576';
$background_width_sm = '768';
$background_width_md = '992';
$background_width_lg = '1200';
$background_width_xl = '1400';
$background_width_xxl = '1600';
$background_width_xxxl = '1920';

// set header level
$header_level = !empty($headline['text'])
    ? sanitize_header_level($headline['size'] ?? 'h2') + 1
    : 2;

$sub_header_level = !empty($header_level) ? $header_level + 1 : 3;

$has_any_description = !oo_is_array_column_empty($counters, 'text');
$has_any_icon_global = !oo_is_array_column_empty($counters, 'icon');

$counter_groups = [];
$group_size = 3;

foreach (array_chunk($counters, $group_size) as $group_index => $group) {
    $counter_groups[$group_index] = [
        'counters' => $group,
        'has_icons' => !oo_is_array_column_empty($group, 'icon'),
    ];
}
?>

<section 
    <?php oo_block_id($block); ?> 
    class="c-success-counter o-section --<?php echo esc_attr(
        $bg_color,
    ); ?> <?php echo !$has_any_icon_global
     ? '--no-icons'
     : ''; ?> <?php echo !empty($background_image_url)
     ? '--has-bg-image'
     : ''; ?>" 
    data-animation-speed="<?php echo esc_attr($animation_speed); ?>"
>

    <?php if (!empty($background_image_url)): ?>
        <div class="c-success-counter__background">
            <?php oo_get_template('components', '', 'component-image', [
                'image' => $background_image,
                'picture_class' => 'c-success-counter__picture o-picture',
                'image_class' => 'c-success-counter__image o-image',
                'dimensions' => [
                    '575' => ['w' => $background_width_xs],
                    '1600' => ['w' => $background_width_xxxl],
                    '1400' => ['w' => $background_width_xxl],
                    '1200' => ['w' => $background_width_xl],
                    '992' => ['w' => $background_width_lg],
                    '768' => ['w' => $background_width_md],
                    '576' => ['w' => $background_width_sm],
                ],
            ]); ?>
        </div>
    <?php endif; ?>

    <div class="c-success-counter__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])): ?>
            <div class="c-success-counter__row o-row">
                <div class="c-success-counter__content <?php echo empty(
                    $background_image_url
                )
                    ? ' o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1'
                    : ' o-col-12 o-col-xl-8'; ?>">
                    
                    <?php if (!empty($headline['text'])) {
                        oo_get_template(
                            'components',
                            '',
                            'component-headline',
                            [
                                'headline' => $headline,
                                'additional_headline_class' =>
                                    'c-success-counter__title',
                            ],
                        );
                    } ?>

                    <?php if (!empty($text['wysiwyg'])): ?>
                        <div class="c-success-counter__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($counters)): ?>
            <?php if ($is_slider): ?>
                <div class="c-success-counter__slider c-slider --is-cards-slider --is-success-counter-slider splide <?php echo !$has_any_description
                    ? '--no-description'
                    : ''; ?>" 
                    data-splide='{"perPage":1,"perMove":1,"gap":32,"pagination":false,"snap":true,"lazyLoad":"nearby","padding":"1rem","mediaQuery":"min","breakpoints":{"992":{"perPage":2},"1400":{"perPage":3}}}'>
                    
                    <div class="c-slider__track splide__track">
                        <div class="c-slider__list splide__list">
                            <?php foreach ($counters as $counter): ?>
                                <?php oo_get_template(
                                    'blocks/success-counter',
                                    '',
                                    'success-counter-card',
                                    [
                                        'counter' => $counter,
                                        'has_icons' => $has_any_icon_global,
                                        'is_slider' => true,
                                        'header_level' => $header_level,
                                    ],
                                ); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="c-slider__navigation splide__navigation">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow c-icon-button --prev splide__arrow splide__arrow--prev">
                                <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <?php echo oo_get_icon('chevron-left', true, [
                                    'class' =>
                                        'c-icon-button__icon --chevron-left',
                                ]); ?>
                            </button>
                            <button class="c-slider__arrow c-icon-button --next splide__arrow splide__arrow--next">
                                <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <?php echo oo_get_icon('chevron-right', true, [
                                    'class' =>
                                        'c-icon-button__icon --chevron-right',
                                ]); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="c-success-counter__counters <?php echo !$has_any_description
                    ? '--no-description'
                    : ''; ?>">
                    <?php foreach ($counter_groups as $group): ?>
                        <?php foreach ($group['counters'] as $counter): ?>
                            <?php oo_get_template(
                                'blocks/success-counter',
                                '',
                                'success-counter-card',
                                [
                                    'counter' => $counter,
                                    'has_icons' => $group['has_icons'],
                                    'is_slider' => false,
                                    'header_level' => $header_level,
                                ],
                            ); ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($buttons['buttons'][0]['link'])): ?>
            <div class="c-success-counter__buttons-wrapper o-row">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-success-counter__buttons o-col-12 o-col-lg-10 o-col-xl-8 u-offset-lg-1',
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
</section>