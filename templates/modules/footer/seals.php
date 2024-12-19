<?php
/**
 * Module Name: Seals
 * @param $args
 * Get values from the parameter
 */

// Helpers
$content = $args['content'] ?? [];
$location = $args['location'] ?? 'footer';

$headline = $content['headline'] ?? null;
$seals = $content['seals']['repeater'] ?? [];

$is_slider = filter_var($content['slider']['slider'], FILTER_VALIDATE_BOOLEAN);

if (!empty($headline)):
    oo_get_template('components', '', 'component-headline', [
        'headline' => [
            'text' => strip_tags($headline),
            'size' => 'span',
        ],
        'additional_headline_class' => 'c-module-seals__headline',
    ]);
endif;

if (empty($seals)) {
    return;
}

if ($is_slider) { ?>
    <div class="c-seals --is-slider">
        <div class="c-seals__slider c-slider --loop --is-seals-slider --on-bg-<?php echo $location; ?> splide" 
            data-splide='{
            "gap": 0,
            "perMove": 1,
            "perView": 3,
            "pagination":true,
            "snap":true,
            "autoWidth": true,
            "lazyLoad":"nearby",
            "type":"loop",
            "focus":"center",
            "updateOnMove": true
        }'>
            <div class="c-slider__track splide__track">
                <div class="c-seals__list c-slider__list splide__list">
                    <?php foreach ($seals as $seal) {
                        echo '<div class="c-seals__item c-slider__slide splide__slide">';
                        echo '<div class="c-seals__wrapper c-slider__wrapper">';
                        echo '<div class="c-seals__cover --is-' .
                            $seal['type'] .
                            '">';
                        oo_set_seal_content($seal, 'c-seals', $is_slider);
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    } ?>
                </div>
            </div>
            <div class="c-slider__arrows splide__arrows">
                <button class="c-slider__arrow c-slider__arrow--prev c-button --only-icon --square splide__arrow splide__arrow--prev">
                    <span class="u-screen-reader-only">
                        <?php esc_html_e('Vorheriges', 'oo_theme'); ?>
                    </span>
                    <span class="c-button__icon --chevron-left"><?php oo_get_icon(
                        'chevron-left',
                    ); ?></span>
                </button>
                <button class="c-slider__arrow c-slider__arrow--next c-button --only-icon --square splide__arrow splide__arrow--next">
                    <span class="u-screen-reader-only">
                        <?php esc_html_e('Nächstes', 'oo_theme'); ?>
                    </span>
                    <span class="c-button__icon --chevron-right"><?php oo_get_icon(
                        'chevron-right',
                    ); ?></span>
                </button>
            </div>
            <ul class="c-slider__pagination splide__pagination"></ul>
        </div>
    </div>
<?php } else { ?>

    <div class="c-seals --is-grid">
        <?php foreach ($seals as $seal) {
            echo '<div class="c-seals__item --is-' . $seal['type'] . '">';
            oo_set_seal_content($seal, 'c-seals', $is_slider);
            echo '</div>';
        } ?>
    </div>
<?php } ?>
