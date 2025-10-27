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
    <div class="c-property-details__similar">
        <h2 class="c-property-details__headline o-headline"><?php esc_html_e(
            'Weitere Immobilien',
            'oo_theme',
        ); ?></h2>
        <div id="outerslider" class="c-property-details__similar-slider c-slider --is-properties-similar-slider splide" data-splide='{"perPage":3,"perMove":1,"gap":"1rem","snap":true,"lazyLoad":"nearby","pagination":false,"arrows":false,"page":false}'>
            <div class="c-slider__track splide__track">
                <div class="c-slider__list splide__list">
                    <?php
                    $slider = ['slider' => 'yes'];
                    $bg_color = 'bg-transparent';
                    $is_slider = true;

                    require 'property-card.php';
                    ?>
                </div>
            </div>
            <div class="c-slider__navigation splide__navigation --is-properties-slider">
                <div class="c-slider__progress splide__progress">
                    <div class="c-slider__progress-bar splide__progress-bar"></div>
                </div>
                <div class="c-slider__arrows splide__arrows">
                    <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                        <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                            'Vorheriges',
                            'oo_theme',
                        ); ?></span>
                        <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                            'chevron-left',
                        ); ?></span>
                    </button>
                    <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                        <span class="c-slider__arrow-text u-screen-reader-only"><?php esc_html_e(
                            'Nächstes',
                            'oo_theme',
                        ); ?></span>
                        <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                            'chevron-right',
                        ); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
