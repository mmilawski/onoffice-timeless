<?php

// Content

$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$type = get_field('type') ?? null;
$url = get_field('url') ? esc_url(get_field('url')) : null;
$script_source_url = get_field('script_source_url')
    ? esc_url(get_field('script_source_url'))
    : null;

$heyflow_flow_id = get_field('heyflow_flow_id')
    ? esc_attr(get_field('heyflow_flow_id'))
    : null;
$heyflow_screen = get_field('heyflow_screen')
    ? esc_attr(get_field('heyflow_screen'))
    : null;

$sprengnetter_api_key = get_field('sprengnetter_api_key')
    ? esc_attr(get_field('sprengnetter_api_key'))
    : null;

$bottimmo_choose_url = get_field('bottimmo_choose_url')
    ? esc_attr(get_field('bottimmo_choose_url'))
    : null;
$bottimmo_url = get_field('bottimmo_url')
    ? esc_attr(get_field('bottimmo_url'))
    : null;
$bottimmo_data_company = get_field('bottimmo_data_company')
    ? esc_attr(get_field('bottimmo_data_company'))
    : null;
$bottimmo_data_slug = get_field('bottimmo_data_slug')
    ? esc_attr(get_field('bottimmo_data_slug'))
    : null;

$bottimmo_data_variant = get_field('bottimmo_data_variant')
    ? esc_attr(get_field('bottimmo_data_variant'))
    : null;

$iwa_data_key = get_field('iwa_data_key')
    ? esc_attr(get_field('iwa_data_key'))
    : null;

$iframe_script_url = 'https://iframe.immowissen.org/loader.min.js';

switch ($bottimmo_choose_url) {
    case 'demo':
        $iframe_script_url = 'https://iframe.bottimmo.show/loader.min.js';
        break;
    case 'individual':
        $iframe_script_url = $bottimmo_url;
        break;
}

$imag_id = get_field('imag_id') ? esc_attr(get_field('imag_id')) : null;

$immosparrow_broker_id = get_field('immosparrow_broker_id')
    ? esc_attr(get_field('immosparrow_broker_id'))
    : null;

$maklaro_type = get_field('maklaro_type')
    ? esc_attr(get_field('maklaro_type'))
    : 'lead';
$maklaro_partner_id = get_field('maklaro_partner_id')
    ? esc_attr(get_field('maklaro_partner_id'))
    : null;
$maklaro_lead_flow_code = get_field('maklaro_lead_flow_code')
    ? esc_attr(get_field('maklaro_lead_flow_code'))
    : null;
$maklaro_conversion_tracking_name = get_field(
    'maklaro_conversion_tracking_name',
)
    ? esc_attr(get_field('maklaro_conversion_tracking_name'))
    : null;
$maklaro_event_tracking_name = get_field('maklaro_event_tracking_name')
    ? esc_attr(get_field('maklaro_event_tracking_name'))
    : null;
$maklaro_widget_id = get_field('maklaro_widget_id')
    ? esc_attr(get_field('maklaro_widget_id'))
    : null;

$baufi_lead_partner_id = get_field('baufi_lead_partner_id')
    ? esc_attr(get_field('baufi_lead_partner_id'))
    : null;
$baufi_lead_iframe_type = get_field('baufi_lead_iframe_type')
    ? esc_attr(get_field('baufi_lead_iframe_type'))
    : null;
$baufi_lead_button_text = get_field('baufi_lead_button_text')
    ? esc_attr(get_field('baufi_lead_button_text'))
    : null;

$pricehubble_version = get_field('pricehubble_version')
    ? esc_attr(get_field('pricehubble_version'))
    : null;

$pricehubble_api_key = get_field('pricehubble_api_key')
    ? esc_attr(get_field('pricehubble_api_key'))
    : null;

$pricehubble_textcolor_v1 = get_field('pricehubble_textcolor_v1')
    ? esc_attr(get_field('pricehubble_textcolor_v1'))
    : null;

$pricehubble_activecolor_v1 = get_field('pricehubble_activecolor_v1')
    ? esc_attr(get_field('pricehubble_activecolor_v1'))
    : null;

$pricehubble_buttoncolor_v1 = get_field('pricehubble_buttoncolor_v1')
    ? esc_attr(get_field('pricehubble_buttoncolor_v1'))
    : null;

$pricehubble_primarycolor_v2 = get_field('pricehubble_primarycolor_v2')
    ? esc_attr(get_field('pricehubble_primarycolor_v2'))
    : null;

$pricehubble_gatrackingid = get_field('pricehubble_gatrackingid')
    ? esc_attr(get_field('pricehubble_gatrackingid'))
    : null;

// Settings
$settings = get_field('settings') ?? null;

$iframe_class = match ($type) {
    'onoffice' => 'ooiframe --is-onoffice',
    'ogulo' => '--is-ogulo',
    'calendly' => '--is-calendly',
    'timum' => '--is-timum',
    'matterport' => '--is-matterport',
    'sprengnetter' => '--is-sprengnetter',
    'energieausweisformulare' => '--is-energieausweisformulare',
    'bottimmo' => '--is-bottimmo',
    'heyflow' => '--is-heyflow',
    'immobilienwertanalyse' => '--is-iwa',
    'imag' => '--is-imag',
    'immosparrow' => '--is-immosparrow',
    'vcita' => '--is-vcita',
    'maklaro' => '--is-maklaro',
    'drklein' => 'ooiframe --is-dr-klein',
    'propform' => '--is-propform',
    'prohyp' => '--is-prohyp',
    'baufi_lead' => '--is-baufi-lead',
    'immowelt' => '--is-immowelt',
    'pricehubble' => '--is-pricehubble',
    default => '',
};

$iframe_name = match ($type) {
    'onoffice' => 'ooimmoframe',
    'ogulo' => 'ogulo',
    'calendly' => 'calendly',
    'timum' => 'timum',
    'matterport' => 'matterport',
    'sprengnetter' => 'sprengnetter',
    'energieausweisformulare' => 'energieausweisformulare',
    'bottimmo' => 'bottimmo',
    'heyflow' => 'heyflow',
    'immobilienwertanalyse' => 'immobilienwertanalyse',
    'imag' => 'imag',
    'immosparrow' => 'immosparrow',
    'vcita' => 'vcita',
    'maklaro' => 'maklaro',
    'drklein' => 'ooimmoframe',
    'propform' => 'propform',
    'prohyp' => 'prohyp',
    'baufi_lead' => 'baufi_lead',
    'immowelt' => 'immowelt',
    'pricehubble' => 'PriceHubble',
    default => '',
};

if (isset($type)) {
    if ($type === 'onoffice' || $type === 'drklein') {
        wp_enqueue_script('oo-iframe-resizer');
        wp_enqueue_script('oo-iframe-noscroll');
    } elseif ($type === 'heyflow') {
        wp_enqueue_script('heyflow-webview');
    }
}
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
                <?php
                $urlTypes = [
                    'onoffice',
                    'ogulo',
                    'calendly',
                    'timum',
                    'matterport',
                    'energieausweisformulare',
                    'vcita',
                    'drklein',
                    'prohyp',
                    'immowelt',
                ];

                if (in_array($type, $urlTypes) && isset($url)): ?>
                    <iframe
                        class="c-iframe-script__iframe <?php echo esc_attr(
                            $iframe_class,
                        ); ?>" 
                        name="<?php echo esc_attr($iframe_name); ?>"
                        src="<?php echo $url; ?>"
                        allowfullscreen="allowfullscreen"
                        data-usercentrics="<?php echo esc_attr(
                            $iframe_name,
                        ); ?>">
                    </iframe>
                <?php endif;
                ?>
                
                <?php if (
                    isset($type) &&
                    $type === 'bottimmo' &&
                    isset($bottimmo_data_company) &&
                    isset($bottimmo_data_slug)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>">

                        <script 
                            src="<?php echo $iframe_script_url; ?>"
                            name="<?php echo isset($iframe_name)
                                ? esc_attr($iframe_name)
                                : ''; ?>"
                            data-company="<?php echo isset(
                                $bottimmo_data_company,
                            )
                                ? esc_attr($bottimmo_data_company)
                                : ''; ?>" 
                            data-slug="<?php echo isset($bottimmo_data_slug)
                                ? esc_attr($bottimmo_data_slug)
                                : ''; ?>"
                            <?php if ($bottimmo_data_variant !== 'compact'): ?> 
                            data-variant="<?php echo !empty(
                                $bottimmo_data_variant
                            )
                                ? esc_attr($bottimmo_data_variant)
                                : ''; ?>"
                            <?php endif; ?>
                                
                            data-consent-cookie="BOTTIMMO"
                            data-usercentrics="BOTTIMMO"
                            data-bottimmo defer>
                        </script>
                    </div>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'heyflow' &&
                    isset($heyflow_flow_id)
                ): ?>
                    <heyflow-wrapper class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>"
                        name="<?php echo isset($iframe_name)
                            ? esc_attr($iframe_name)
                            : ''; ?>"
                        flow-id="<?php echo isset($heyflow_flow_id)
                            ? esc_attr($heyflow_flow_id)
                            : ''; ?>"
                        screen="<?php echo isset($heyflow_screen)
                            ? esc_attr($heyflow_screen)
                            : ''; ?>"
                        dynamic-height style-config='{"width": "100%"}' data-usercentrics="heyflow">
                    </heyflow-wrapper>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'sprengnetter' &&
                    isset($sprengnetter_api_key)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>">
                        <script type="text/plain" src="https://wertindikation.sprengnetter.de/widget.js" data-usercentrics="Sprengnetter Wertindikation" async></script>
                        <sp-widget api-key="<?php echo isset(
                            $sprengnetter_api_key,
                        )
                            ? esc_attr($sprengnetter_api_key)
                            : ''; ?>"></sp-widget>
                    </div>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'immobilienwertanalyse' &&
                    isset($iwa_data_key)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>">
                        <div data-key="<?php echo isset($iwa_data_key)
                            ? esc_attr($iwa_data_key)
                            : ''; ?>" id="iwa-widget">&nbsp;</div>
                        <script type="text/javascript" src="https://www.immobilienwertanalyse.de/iwalead/plugin.js" data-usercentrics="Immobilienwertanalyse"></script>
                    </div>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'imag' &&
                    isset($imag_id)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>">
                        <div id="imag-immobiliensuche"></div>
                        <script type="text/javascript">
                            (function(w, d, k, e) {
                            const cssApi = d.createElement('link');
                            cssApi.rel = 'stylesheet';
                            cssApi.type = 'text/css';
                            cssApi.href = 'https://www.imag-portal.de/property_search_integration/build/api.css';
                            d.head.appendChild(cssApi);

                            const jsApi = d.createElement('script');
                            jsApi.src = 'https://www.imag-portal.de/property_search_integration/build/api.js';
                            jsApi.type = 'text/javascript';
                            jsApi.charset = 'UTF-8';
                            d.body.appendChild(jsApi);
                            jsApi.addEventListener('load', function() {
                                initializePsi(k, e);
                            })
                        })(window, document, '<?php echo isset($imag_id)
                            ? esc_attr($imag_id)
                            : ''; ?>', 'imag-immobiliensuche');
                        </script>
                    </div>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'immosparrow' &&
                    isset($immosparrow_broker_id)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo isset(
                        $iframe_class,
                    )
                        ? esc_attr($iframe_class)
                        : ''; ?>">
                        <script type="text/plain" src="https://wizard.immosparrow.ch/pp/wizardWidget.js" data-usercentrics="ImmoSparrow"></script>
                        <ispw-pp-widget brokerid="<?php echo isset(
                            $immosparrow_broker_id,
                        )
                            ? esc_attr($immosparrow_broker_id)
                            : ''; ?>"/>
                    </div>
                <?php endif; ?>

                <?php if (isset($type) && $type === 'maklaro'): ?>
                    <?php if (
                        isset($maklaro_type) &&
                        $maklaro_type === 'property-search' &&
                        isset($maklaro_widget_id)
                    ): ?>
                        <div class="c-iframe-script__iframe <?php echo isset(
                            $iframe_class,
                        )
                            ? esc_attr($iframe_class)
                            : ''; ?>">
                            <script type="text/plain" src="https://property-search.maklaro.com/main.js" data-usercentrics="Maklaro"></script>
                            <maklaro-property-search data-widget-id="<?php echo isset(
                                $maklaro_widget_id,
                            )
                                ? esc_attr($maklaro_widget_id)
                                : ''; ?>" <?php echo isset(
    $maklaro_event_tracking_name,
)
    ? 'data-event-tracking-callback-name="' .
        esc_attr($maklaro_event_tracking_name) .
        '"'
    : ''; ?> <?php echo isset($maklaro_conversion_tracking_name)
     ? 'data-conversion-tracking-callback-name="' .
         esc_attr($maklaro_conversion_tracking_name) .
         '"'
     : ''; ?>></maklaro-property-search>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        isset($maklaro_type) &&
                        $maklaro_type === 'lead' &&
                        isset($maklaro_partner_id) &&
                        isset($maklaro_lead_flow_code)
                    ): ?>
                        <div class="c-iframe-script__iframe <?php echo isset(
                            $iframe_class,
                        )
                            ? esc_attr($iframe_class)
                            : ''; ?> container"> 
                            <script type="text/plain" src="https://slider.maklaro.com/src.latest.js" data-usercentrics="Maklaro"></script>
                            <div id="maklaro-slider-widget" data-partner-id="<?php echo isset(
                                $maklaro_partner_id,
                            )
                                ? esc_attr($maklaro_partner_id)
                                : ''; ?>" data-language="DE" data-type="evaluation" data-primary-color="#1B76BB" data-lead-flow-code="<?php echo isset(
    $maklaro_lead_flow_code,
)
    ? esc_attr($maklaro_lead_flow_code)
    : ''; ?>" data-max-fullscreen-width="540" data-hide-fullscreen-on-start="1" > </div> 
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (
                    isset($type) &&
                    $type === 'propform' &&
                    isset($script_source_url)
                ) { ?>
                    <div class="c-iframe-script__iframe <?php echo esc_attr(
                        $iframe_class,
                    ); ?>">
                        <script
                            src="<?php echo esc_attr($script_source_url); ?>"
                            name="<?php echo esc_attr($iframe_name); ?>"
                            data-usercentrics="<?php echo esc_attr(
                                $iframe_name,
                            ); ?>">
                        </script>
                    </div>
                <?php } ?>
 
                <?php if (
                    isset($baufi_lead_partner_id) &&
                    isset($baufi_lead_iframe_type)
                ) { ?>
                    <script
                        type="text/plain"
                        src="https://www.baufi-lead.de/baufilead/partner/<?php echo $baufi_lead_partner_id; ?>/imports.js" 
                        data-usercentrics="Baufi Lead">
                    </script>
                    <div class="c-iframe-script__iframe <?php echo $iframe_class; ?>" data-usercentrics="Baufi Lead">
                <?php } ?>

                <?php if (
                    isset($baufi_lead_partner_id) &&
                    isset($baufi_lead_iframe_type) &&
                    $baufi_lead_iframe_type !== 'zinstableau'
                ) { ?>

                    <?php
                    $button = [
                        [
                            'link' => [
                                'title' => esc_html__(
                                    $baufi_lead_button_text,
                                    'oo_theme',
                                ),
                                'url' => 'javascript:void(0);',
                            ],
                        ],
                    ];

                    oo_get_template('components', '', 'component-buttons', [
                        'buttons' => $button,
                        'icon_first' => 'arrow-right',
                        'additional_button_class' => "c-iframe-script__iframe-button baufilead_{$baufi_lead_iframe_type} {$iframe_class} --on-bg-transparent",
                        'additional_container_class' => 'c-map__button-wrapper',
                    ]);
                    } elseif (
                    isset($baufi_lead_partner_id) &&
                    $baufi_lead_iframe_type === 'zinstableau'
                ) { ?>
                    <div class="baufilead_zinstableau" data-usercentrics="Baufi Lead"></div> 
            <?php } ?>
                </div>

                <?php if (
                    $type == 'pricehubble' &&
                    $pricehubble_version == 'v1' &&
                    !empty($pricehubble_api_key)
                ): ?>
                    <div class="c-iframe-script__iframe <?php echo esc_attr(
                        $iframe_class,
                    ); ?>" data-usercentrics="<?php echo esc_attr(
    $iframe_name,
); ?>">
                        <iframe id="fisher-widget"></iframe>
                    </div>

                    <script
                        src="https://fisher.pricehubble.com/widget.js" 
                        data-usercentrics="<?php echo esc_attr(
                            $iframe_name,
                        ); ?>">
                    </script>
                    <script 
                        type="text/plain"
                        data-usercentrics="<?php echo esc_attr(
                            $iframe_name,
                        ); ?>">

                        var apiKey = '<?php echo esc_attr(
                            $pricehubble_api_key,
                        ); ?>';
                        var textColor = '<?php echo esc_attr(
                            $pricehubble_textcolor_v1,
                        ); ?>';
                        var activeColor = '<?php echo esc_attr(
                            $pricehubble_activecolor_v1,
                        ); ?>';
                        var buttonColor = '<?php echo esc_attr(
                            $pricehubble_buttoncolor_v1,
                        ); ?>';
                        var gaTrackingId = '<?php echo esc_attr(
                            $pricehubble_gatrackingid,
                        ); ?>';

                        FisherWidget.init({
                            apiKey: apiKey,
                            iframe: "#fisher-widget",
                            textColor: textColor,
                            activeColor: activeColor,
                            buttonColor: buttonColor,
                            gaTrackingId: gaTrackingId, 
                            consentGranted: true,
                        }); 
                        
                        if (typeof gaTrackingId !== 'undefined' && gaTrackingId !== null && gaTrackingId !== '')  {
                            FisherWidget.grantConsent();
                        } 
                    </script>
                    <?php endif; ?>

                    <?php if (
                        $type == 'pricehubble' &&
                        $pricehubble_version == 'v2' &&
                        !empty($pricehubble_api_key)
                    ): ?>
                        <div 
                            class="c-iframe-script__iframe <?php echo esc_attr(
                                $iframe_class,
                            ); ?>" 
                            data-usercentrics="<?php echo esc_attr(
                                $iframe_name,
                            ); ?>">
                            <iframe id="fisher-widget-v2"></iframe>

                        </div>
            
                        <script
                            src="https://fisher-v2.pricehubble.com/constructor/widget.js" 
                            data-usercentrics="<?php echo esc_attr(
                                $iframe_name,
                            ); ?>">
                        </script>
                        <script
                            type="text/plain"
                            data-usercentrics="<?php echo esc_attr(
                                $iframe_name,
                            ); ?>">

                            var apiKey = '<?php echo esc_attr(
                                $pricehubble_api_key,
                            ); ?>';
                            var primaryColor = '<?php echo esc_attr(
                                $pricehubble_primarycolor_v2,
                            ); ?>';
                            var gaTrackingId = '<?php echo esc_attr(
                                $pricehubble_gatrackingid,
                            ); ?>';

                            FisherWidget.init({
                                apiKey: apiKey,
                                iframe: '#fisher-widget-v2',
                                titleSettings: {
                                    status: 'default'
                                },
                                primaryColor: primaryColor,
                                gaTrackingId: gaTrackingId,
                                consentGranted: true
                            });

                            if (typeof gaTrackingId !== 'undefined' && gaTrackingId !== null && gaTrackingId !== '')  {
                                FisherWidget.grantConsent();
                            } 
                        </script>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>