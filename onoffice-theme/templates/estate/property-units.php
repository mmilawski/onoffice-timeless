<?php
$dontEcho = [
    'vermarktungsstatus',
    'objekttitel',
    'objektbeschreibung',
    'lage',
    'ausstatt_beschr',
    'sonstige_angaben',
];

$countEstates = clone $pEstates;
$totalUnits = 0;
$countEstates->resetEstateIterator();
while ($countEstates->estateIterator()) {
    $totalUnits++;
}

$pEstates->resetEstateIterator();
if ($pEstates->estateIterator()): ?>
    <div class="c-property-details__units-wrapper">
        <div class="c-property-details__units o-container">
            <div class="c-property-details__units-row o-row">
                <div class="c-property-details__units-content u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
                    <h2 class="c-property-details__headline o-headline">
                        <?php esc_html_e('Einheiten', 'oo_theme'); ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="c-property-details__unit-container o-container-fluid"> 
            <div id="unitslider" class="c-property-details__units-slider c-slider --is-properties-units-slider splide"
                data-splide='{
                    "perPage": 3,
                    "perMove": 1,
                    "gap": "1rem",
                    "snap": true,
                    "lazyLoad": "nearby",
                    "pagination": false,
                    "arrows": false,
                    "drag": <?php echo $totalUnits >= 4 ? 'true' : 'false'; ?>,
                    "page": false,
                    "breakpoints": {
                        "768": {
                            "perPage": 1
                        }
                    }
                }'>
                <div class="c-slider__track splide__track">
                    <div class="c-slider__list splide__list">
                        <?php
                        $pEstates->resetEstateIterator();
                        $is_slider = true;
                        $bg_color = 'bg-transparent';

                        require 'property-card.php';
                        ?>
                    </div>
                </div>

                <?php if ($totalUnits >= 4): ?>
                    <div class="c-slider__navigation splide__navigation o-container --is-properties-slider">
                        <div class="c-slider__progress splide__progress">
                            <div class="c-slider__progress-bar splide__progress-bar"></div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow --prev splide__arrow splide__arrow--prev">
                                <span class="c-slider__arrow-text u-screen-reader-only">
                                    <?php esc_html_e(
                                        'Vorheriges',
                                        'oo_theme',
                                    ); ?>
                                </span>
                                <span class="c-slider__arrow-icon --chevron-left"><?php oo_get_icon(
                                    'chevron-left',
                                    true,
                                ); ?></span>
                            </button>
                            <button class="c-slider__arrow --next splide__arrow splide__arrow--next">
                                <span class="c-slider__arrow-text u-screen-reader-only">
                                    <?php esc_html_e('Nächstes', 'oo_theme'); ?>
                                </span>
                                <span class="c-slider__arrow-icon --chevron-right"><?php oo_get_icon(
                                    'chevron-right',
                                    true,
                                ); ?></span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
