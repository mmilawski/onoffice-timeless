<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$shortcode = get_field('shortcode') ?? null;

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
if (!empty(get_field('property_search_result'))) {
    $result = get_field('property_search_result') ?? null;
} elseif (!empty(get_field('sites', 'option')['property_search_result'])) {
    $result = get_field('sites', 'option')['property_search_result'] ?? null;
} else {
    $result = null;
}
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-property-search o-section --<?php echo $bg_color; ?>">
    <div class="c-property-search__container o-container">
        <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-property-search__content o-row --position-center">
                <?php if (!empty($headline['text'])) {
                    oo_get_template('components', '', 'component-headline', [
                        'headline' => $headline,
                        'additional_headline_class' =>
                            'c-property-search__headline o-col-12 o-col-lg-10 o-col-xl-8',
                    ]);
                } ?>
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-property-search__text o-text --is-wysiwyg o-col-12 o-col-lg-10 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="c-property-search__content o-row --position-center">
            <div class="c-property-search__shortcode o-col-12 o-col-lg-10 o-col-xl-8">
                <?php if (!empty($shortcode)) {
                    echo do_shortcode($shortcode);
                } ?>
            </div>
        </div>
    </div>
</section>