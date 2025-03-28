<?php

// Content

$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$type = get_field('type') ?? null;
$url = get_field('url') ? esc_url(get_field('url')) : null;

// Settings
$settings = get_field('settings') ?? null;

$iframe_name = match ($type) {
    'areabutler' => 'areabutler',
    'baufi_lead' => 'baufi_lead',
    'baufipasst' => 'baufipasst',
    'bottimmo' => 'bottimmo',
    'calendly' => 'calendly',
    'check24' => 'check24',
    'drklein' => 'drklein',
    'energieausweisformulare' => 'energieausweisformulare',
    'etracker' => 'etracker ',
    'exkulpa' => 'exkulpa',
    'heyflow' => 'heyflow',
    'huettig_rompf_finanzierungsrechner'
        => 'huettig_rompf_finanzierungsrechner',
    'imag' => 'imag',
    'immobilienwertanalyse' => 'immobilienwertanalyse',
    'immonewsfeed' => 'immonewsfeed ',
    'immosparrow' => 'immosparrow',
    'immowelt' => 'immowelt',
    'justhome' => 'justhome',
    'maklaro' => 'maklaro',
    'matterport' => 'matterport',
    'meinungsmeister' => 'meinungsmeister',
    'ogulo' => 'ogulo',
    'onoffice' => 'onoffice',
    'pricehubble' => 'pricehubble',
    'prohyp' => 'prohyp',
    'propform' => 'propform',
    'sprengnetter' => 'sprengnetter',
    'timum' => 'timum',
    'trustlocal' => 'trustlocal',
    'vcita' => 'vcita',
    default => '',
};
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-iframe-script o-section --<?php echo $settings['bg_color']; ?>"> 
    <div class="c-iframe-script__container o-container">
        <div class="c-iframe-script__row o-row">

            <?php if (!empty($headline['text'])) { ?>
                <?php oo_get_template('components', '', 'component-headline', [
                    'headline' => $headline,
                    'additional_headline_class' =>
                        'c-iframe-script__headline o-col-12 o-col-xl-8',
                ]); ?>
            <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-iframe-script__text o-text o-col-12 o-col-xl-8 --is-wysiwyg">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>

            <div class="c-iframe-script__col o-col-12">
            <?php if (!empty($type) && isset($iframe_name)) {
                oo_get_template('templates', 'iframes', $iframe_name, ['']);
            } ?>

            </div>
        </div>
    </div>
</section>