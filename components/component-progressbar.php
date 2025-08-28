<?php

/** @var array $args */
$args = wp_parse_args($args, [
    'totalPages' => 0,
    'pageTitles' => [],
    'additional_class' => '',
    'in_modal' => false,
]);

if (empty($args['totalPages']) || $args['totalPages'] <= 1) {
    return;
}
?>

<div class="c-progressbar <?php echo esc_attr(
    $args['additional_class'],
); ?> <?php echo $args['in_modal']
     ? '--in-modal'
     : ''; ?>" data-page-titles='<?php echo esc_attr(
    json_encode(array_values($args['pageTitles'])),
); ?>'>
    <div class="c-progressbar__bar"></div>
    <div class="c-progressbar__status"></div>
    <?php for ($i = 1; $i <= $args['totalPages']; $i++): ?>
        <div class="c-progressbar__step <?php if ($i === 1) {
            echo '--is-active';
        } ?>" data-step="<?php echo $i; ?>">
            <div class="c-progressbar__circle">
            </div>
            <span class="c-progressbar__label">
                <span class="c-progressbar__label-number --is-mobile --in-modal"><?php echo esc_html(
                    $i,
                ); ?></span>
                <?php if (!empty($args['pageTitles'][$i - 1])): ?>
                    <span class="c-progressbar__label-title --not-in-modal"><?php echo esc_html(
                        $args['pageTitles'][$i - 1],
                    ); ?></span>
                <?php endif; ?>
            </span>
        </div>
    <?php endfor; ?>
</div>