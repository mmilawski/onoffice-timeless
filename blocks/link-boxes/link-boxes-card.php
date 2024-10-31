<?php
// From link boxes block
$boxes = get_field('boxes') ?? [];
$settings = get_field('settings') ?? [];
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'], FILTER_VALIDATE_BOOLEAN);
?>

<?php foreach ($boxes as $card) {

    // Content
    $type = $card['type'] ?? 'image';
    $image = $card['image'] ?? [];
    $image_url = $image['url'] ?? null;
    $text = $card['text'] ?? [];
    $headline = $card['headline'] ?? [];
    $link = $card['link'] ?? [];
    $icon = $card['icon'] ?? [];
    $icon_url = $icon['url'] ?? null;

    // Image width
    $image_width_xs = '539';
    $image_width_sm = '508';
    $image_width_md = '690';
    $image_width_lg = '444';
    $image_width_xl = '540';
    $image_width_xxl = '636';
    $image_width_xxxl = '702';

    // Icon width
    $icon_width_xs = '411';
    $icon_width_sm = '380';
    $icon_width_md = '562';
    $icon_width_lg = '316';
    $icon_width_xl = '412';
    $icon_width_xxl = '508';
    $icon_width_xxxl = '574';
    ?>
    <article class="c-link-boxes-card --bg-transparent <?php if ($is_slider) {
        echo '--on-slider c-slider__slide splide__slide';
    } ?>">
        <?php if (!empty($image) || !empty($icon)) { ?>
        <?php if (!empty($link['url'])) { ?>
            <a class="c-link-boxes-card__wrapper --has-<?php echo $type; ?>" <?php echo oo_set_link_attr(
    $link,
); ?>>
        <?php } else { ?>
            <div class="c-link-boxes-card__wrapper --has-<?php echo $type; ?>">
        <?php } ?>
            <?php if (!empty($image) && $type === 'image'): ?>
                <?php oo_get_template('components', '', 'component-image', [
                    'image' => $image,
                    'picture_class' => 'c-link-boxes-card__picture o-picture',
                    'image_class' => 'c-link-boxes-card__image o-image',
                    'dimensions' => [
                        '575' => [
                            'w' => $image_width_xs,
                            'h' => round(($image_width_xs * 3) / 4),
                        ],
                        '1600' => [
                            'w' => $image_width_xxxl,
                            'h' => round(($image_width_xxxl * 3) / 4),
                        ],
                        '1400' => [
                            'w' => $image_width_xxl,
                            'h' => round(($image_width_xxl * 3) / 4),
                        ],
                        '1200' => [
                            'w' => $image_width_xl,
                            'h' => round(($image_width_xl * 3) / 4),
                        ],
                        '992' => [
                            'w' => $image_width_lg,
                            'h' => round(($image_width_lg * 3) / 4),
                        ],
                        '768' => [
                            'w' => $image_width_md,
                            'h' => round(($image_width_md * 3) / 4),
                        ],
                        '576' => [
                            'w' => $image_width_sm,
                            'h' => round(($image_width_sm * 3) / 4),
                        ],
                    ],
                ]); ?>
            <?php endif; ?>
            
            <?php if (!empty($icon) && $type === 'icon'):
                $file_type = wp_check_filetype($icon_url);

                if ($file_type['ext'] == 'svg') {
                    $svg_content = file_get_contents($icon_url);
                    if (!empty($svg_content)) {
                        $svg = new SimpleXMLElement($svg_content);
                        $svg->addAttribute('class', 'c-link-boxes-card__icon');
                        echo "{$svg->asXML()}";
                    }
                } else {
                    oo_get_template('components', '', 'component-image', [
                        'image' => $icon,
                        'picture_class' =>
                            'c-link-boxes-card__picture o-picture',
                        'image_class' => 'c-link-boxes-card__image o-image',
                        'additional_cloudimg_params' => '&func=bound',
                        'dimensions' => [
                            '575' => [
                                'w' => $icon_width_xs,
                                'h' => round(($icon_width_xs * 3) / 4),
                            ],
                            '1600' => [
                                'w' => $icon_width_xxxl,
                                'h' => round(($icon_width_xxxl * 3) / 4),
                            ],
                            '1400' => [
                                'w' => $icon_width_xxl,
                                'h' => round(($icon_width_xxl * 3) / 4),
                            ],
                            '1200' => [
                                'w' => $icon_width_xl,
                                'h' => round(($icon_width_xl * 3) / 4),
                            ],
                            '992' => [
                                'w' => $icon_width_lg,
                                'h' => round(($icon_width_lg * 3) / 4),
                            ],
                            '768' => [
                                'w' => $icon_width_md,
                                'h' => round(($icon_width_md * 3) / 4),
                            ],
                            '576' => [
                                'w' => $icon_width_sm,
                                'h' => round(($icon_width_sm * 3) / 4),
                            ],
                        ],
                    ]);
                }
            endif; ?>
        <?php if (!empty($link['url'])) { ?>
            </a>
        <?php } else { ?>
            </div>
        <?php } ?>
        <?php } ?>

        <?php if (
            !empty($headline) ||
            !empty($text['wysiwyg']) ||
            !empty($link['url'])
        ) { ?>
            <div class="c-link-boxes-card__content">

                <?php if (!empty($headline)) { ?>
                    <h3 class="c-link-boxes-card__title o-headline --h3"><?php echo $headline; ?></h3>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-link-boxes-card__text o-text">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>


                <?php if (!empty($link['url'])) {

                    $button_text = $link['title']
                        ? $link['title']
                        : esc_html__('Mehr erfahren', 'oo_theme');
                    echo '<a class="c-link-boxes-card__button c-button --on-bg-transparent" ' .
                        oo_set_link_attr($link) .
                        '>';
                    echo $button_text;
                    echo '</a>';
                    ?>
                <?php
                } ?>
            </div>
        <?php } ?>
    </article>
<?php
} ?>
