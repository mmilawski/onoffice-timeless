<?php
$counter = $args['counter'] ?? [];
$has_icons = $args['has_icons'] ?? false;

$start_value = !empty($counter['start_value'])
    ? (float) $counter['start_value']
    : 0;
$end_value = !empty($counter['end_value'])
    ? (float) $counter['end_value']
    : 100;
$prefix = $counter['prefix'] ?? '';
$postfix = $counter['postfix'] ?? '';
$card_headline = $counter['headline'] ?? '';
$card_text = $counter['text'] ?? '';
$icon = $counter['icon'] ?? '';

$is_slider = $args['is_slider'] ?? false;
$header_level = (int) ($args['header_level'] ?? 3);
?>

<?php if ($is_slider): ?>
<div class="c-slider__slide splide__slide">
<?php endif; ?>
<article class="c-success-counter-card" data-start="<?php echo esc_attr(
    $start_value,
); ?>" data-end="<?php echo esc_attr($end_value); ?>">
    <div class="c-success-counter-card__header">
        <span class="c-success-counter-card__number" data-value="<?php echo esc_attr(
            $end_value,
        ); ?>">
            <span class="u-screen-reader-only">
                <?php echo esc_html($prefix . $end_value . $postfix); ?>
            </span>
            <span aria-hidden="true">
                <?php if (!empty($prefix)): ?>
                    <span class="c-success-counter-card__prefix"><?php echo wp_kses_post(
                        $prefix,
                    ); ?></span>
                <?php endif; ?>
                
                <span class="c-success-counter-card__value-number"><?php echo esc_html(
                    $start_value,
                ); ?></span>
                
                <?php if (!empty($postfix)): ?>
                    <span class="c-success-counter-card__postfix"><?php echo wp_kses_post(
                        $postfix,
                    ); ?></span>
                <?php endif; ?>
            </span>
        </span>

        <?php if ($has_icons): ?>
            <div class="c-success-counter-card__icon-container">
                <?php if (!empty($icon)): ?>
                    <?php echo oo_get_icon($icon, true, [
                        'class' => 'c-success-counter-card__icon',
                    ]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($card_headline)): ?>
        <div class="c-success-counter-card__content">
            <?php
            echo "<h{$header_level} class='c-success-counter-card__title'>";
            echo esc_html($card_headline);
            echo "</h{$header_level}>";
            ?>
            <?php if (!empty($card_text)): ?>
                <div class="c-success-counter-card__text o-text --is-wysiwyg">
                    <?php echo wp_kses_post($card_text); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</article>
<?php if ($is_slider): ?>
</div>
<?php endif; ?>
