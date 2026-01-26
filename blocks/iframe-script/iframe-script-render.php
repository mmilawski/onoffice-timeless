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
    'avvocato_andreani' => 'avvocato_andreani',
    'baufi_lead' => 'baufi_lead',
    'baufipasst' => 'baufipasst',
    'bottimmo' => 'bottimmo',
    'bottimmo_widget' => 'bottimmo_widget',
    'calendly' => 'calendly',
    'check24' => 'check24',
    'dasinvest' => 'dasinvest',
    'drklein' => 'drklein',
    'energieausweisformulare' => 'energieausweisformulare',
    'europace' => 'europace',
    'energieausweis_vorschau' => 'energieausweis_vorschau',
    'hausverkauf_energieausweis' => 'hausverkauf_energieausweis',
    'erblotse' => 'erblotse',
    'etracker' => 'etracker',
    'exkulpa' => 'exkulpa',
    'fiba_immohyp_zinsinfo' => 'fiba_immohyp_zinsinfo',
    'fincrm' => 'fincrm',
    'finlink' => 'finlink',
    'heyflow' => 'heyflow',
    'ivd' => 'ivd',
    'huettig_rompf_finanzierungsrechner'
        => 'huettig_rompf_finanzierungsrechner',
    'imag' => 'imag',
    'immobilienwertanalyse' => 'immobilienwertanalyse',
    'immonewsfeed' => 'immonewsfeed',
    'immosparrow' => 'immosparrow',
    'immowelt' => 'immowelt',
    'justhome' => 'justhome',
    'maklaro' => 'maklaro',
    'matterport' => 'matterport',
    'flatfinder' => 'flatfinder',
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
    'hype' => 'hype',
    'provenexpert' => 'provenexpert',
    'stmate' => 'stmate',
    'leadmarkt' => 'leadmarkt',
    'itrk' => 'itrk',
    'immobillie' => 'immobillie',
    'immofenster' => 'immofenster',
    'wohnrechner' => 'wohnrechner',
    default => '',
};

// Position
$posiiton_center = !empty($text['wysiwyg']) ? ' --position-center' : '';
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
                        'c-iframe-script__headline u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8',
                ]); ?>
            <?php } ?>

            <?php if (!empty($text['wysiwyg'])) { ?>
                <div class="c-iframe-script__text o-text u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8 --is-wysiwyg">
                    <?php echo $text['wysiwyg']; ?>
                </div>
            <?php } ?>

            <div class="c-iframe-script__col u-offset-lg-1 o-col-12 o-col-lg-10 o-col-xl-8">
            <?php if (!empty($type) && isset($iframe_name)) {
                oo_get_template('templates', 'iframes', $iframe_name, ['']);
            } ?>

            </div>
        </div>
    </div>
</section>