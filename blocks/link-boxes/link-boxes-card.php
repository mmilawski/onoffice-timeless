<?php
// From link boxes block
$boxes = get_field('boxes') ?? [];
$settings = get_field('settings') ?? [];
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'], FILTER_VALIDATE_BOOLEAN);
?>

<?php foreach ($boxes as $card) {

    // Content
    $type = $card['type'] ?? null;
    $content = $card[$type];
    $url = $content['url'];
    $text = $card['text'] ?? [];
    $headline = $card['headline'] ?? [];
    $link = $card['link'] ?? [];
    $alt_text = !empty($content['alt'])
        ? $content['alt']
        : (!empty($content['title'])
            ? $content['title']
            : null);
    $type = wp_check_filetype($url)['ext'] === 'svg' ? 'svg' : $type;

    $dimensions =
        $type === 'image'
            ? [
                '575' => ['w' => 541, 'h' => round((541 * 2) / 3)], // xs
                '576' => ['w' => 510, 'h' => round((510 * 2) / 3)], // sm
                '768' => ['w' => 333, 'h' => round((333 * 2) / 3)], // md
                '992' => ['w' => 450, 'h' => round((450 * 2) / 3)], // lg
                '1200' => ['w' => 350, 'h' => round((350 * 2) / 3)], // xl
                '1400' => ['w' => 414, 'h' => round((414 * 2) / 3)], // xxl
                '1600' => ['w' => 458, 'h' => round((458 * 2) / 3)], // xxxl
            ]
            : [
                '575' => ['w' => 477, 'h' => round((477 * 2) / 3)], // xs
                '576' => ['w' => 446, 'h' => round((446 * 2) / 3)], // sm
                '768' => ['w' => 270, 'h' => round((270 * 2) / 3)], // md
                '992' => ['w' => 386, 'h' => round((386 * 2) / 3)], // lg
                '1200' => ['w' => 286, 'h' => round((286 * 2) / 3)], // xl
                '1400' => ['w' => 350, 'h' => round((350 * 2) / 3)], // xxl
                '1600' => ['w' => 394, 'h' => round((394 * 2) / 3)], // xxxl
            ];
    ?>
    
    <?php if (
        !empty($headline) ||
        !empty($text['wysiwyg']) ||
        !empty($link['url'])
    ) { ?>
    <article class="c-link-boxes-card <?php if ($is_slider) {
        echo '--on-slider c-slider__slide splide__slide';
    } ?>">
        <?php if (!empty($content)) {

            if (!empty($link['url'])) {

                $button_text = $link['title']
                    ? $link['title']
                    : esc_html__('Mehr erfahren', 'oo_theme');

                $aria_label = !empty($headline)
                    ? sprintf('%s zu %s', $button_text, $headline)
                    : $button_text;
                ?>
            <a class="c-link-boxes-card__link --has-<?php echo $type; ?>"
                aria-label="<?php echo esc_attr($aria_label); ?>"
                <?php echo oo_set_link_attr($link); ?>>
            <?php
            } else {
                 ?>
            <div class="c-link-boxes-card__wrapper --has-<?php echo $type; ?>">
            <?php
            }
            if ($type === 'svg') {
                $svg_content = file_get_contents($url);
                if (!empty($svg_content)) {
                    $svg = new SimpleXMLElement($svg_content);
                    $svg->addAttribute('role', 'img');
                    $svg->addAttribute('aria-label', $alt_text);
                    $svg->addAttribute('class', 'c-link-boxes-card__svg');
                    echo "{$svg->asXML()}";
                }
            } elseif (!empty($content)) {
                oo_get_template('components', '', 'component-image', [
                    'image' => $content,
                    'picture_class' => 'c-link-boxes-card__picture o-picture',
                    'image_class' => 'c-link-boxes-card__image o-image',
                    'additional_cloudimg_params' =>
                        $type === 'image' ? '&func=crop' : '',
                    'dimensions' => $dimensions,
                ]);
            }
            ?>
        <?php if (!empty($link['url'])) { ?>
            </a>
        <?php } else { ?>
            </div>
        <?php } ?>
        <?php
        } ?>

            <div class="c-link-boxes-card__content">

                <?php if (!empty($headline)) { ?>
                    <h3 class="c-link-boxes-card__title o-headline --h3"><?php echo $headline; ?></h3>
                <?php } ?>

                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-link-boxes-card__text o-text --is-wysiwyg">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>


                <?php if (!empty($link['url'])) {

                    $button_text = $link['title']
                        ? $link['title']
                        : esc_html__('Mehr erfahren', 'oo_theme');

                    $aria_label = !empty($headline)
                        ? sprintf('%s zu %s', $button_text, $headline)
                        : $button_text;

                    echo '<a class="c-link-boxes-card__button c-button" aria-label="' .
                        esc_attr($aria_label) .
                        '" ' .
                        oo_set_link_attr($link) .
                        '>';
                    echo $button_text;
                    echo '</a>';
                    ?>
                <?php
                } ?>
            </div>
    </article>  
    <?php } ?>
<?php
} ?>
