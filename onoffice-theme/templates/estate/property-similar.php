<?php

$dontEcho = [
    'objekttitel',
    'objektbeschreibung',
    'lage',
    'ausstatt_beschr',
    'sonstige_angaben',
]; ?>

<?php if (
    (bool) $pEstates->estateIterator() == true &&
    !empty($pEstates->estateIterator())
) { ?>
    <div class="c-property-details__similar o-row --position-center">
        <h2 class="c-property-details__headline o-col-12 o-col-lg-8 o-col-xl-6 --is-underlined o-headline"><?php esc_html_e(
            'Weitere Immobilien',
            'oo_theme',
        ); ?></h2>
        <div class=" o-col-12 c-slider --is-properties-similar-slider splide" data-splide='{"perPage":1,"perMove":1,"gap":0,"snap":true,"lazyLoad":"nearby"}'>
            <div class="c-slider__track splide__track o-col-12 o-col-xl-10">
                <div class="c-slider__list splide__list">
                    <?php
                    $slider = ['slider' => 'yes'];
                    $settings = ['bg_color' => 'bg-transparent'];
                    $is_slider = true;

                    require 'property-card.php';
                    ?>
                </div>
            </div>
            <div class="c-slider__arrows splide__arrows">
                <button class="c-slider__arrow c-slider__arrow--prev c-button --only-icon --square splide__arrow splide__arrow--prev">
                    <span class="u-screen-reader-only"><?php esc_html_e(
                        'Vorheriges',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-button__icon --chevron-left"><?php oo_get_icon(
                        'chevron-left',
                    ); ?></span>
                </button>
                <button class="c-slider__arrow c-slider__arrow--next c-button --only-icon --square splide__arrow splide__arrow--next">
                    <span class="u-screen-reader-only"><?php esc_html_e(
                        'Nächstes',
                        'oo_theme',
                    ); ?></span>
                    <span class="c-button__icon --chevron-right"><?php oo_get_icon(
                        'chevron-right',
                    ); ?></span>
                </button>
            </div>
            <ul class="c-slider__pagination splide__pagination"></ul>
        </div>
    </div>
<?php } ?>
