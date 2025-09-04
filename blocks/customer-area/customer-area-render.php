<?php

$headline = get_field('headline') ?? null;
$text = get_field('text') ?? null;
$buttons = get_field('buttons') ?? null;
$anchor = get_field('anchor') ?? null;

$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
?>
<section <?php oo_block_id(
    $block,
); ?> class="c-customer-area o-section --<?php echo $bg_color; ?>">

    <?php
    // WP-Websites and Vue Preset Bridge
    echo '<style> ';
    echo ':root {';

    switch ($bg_color) {
        case 'bg-light':
            echo "
                        --p-oo-background: var(--oo-color-light-bg, var(--oo-color-bg)); /* background variable from parent .o-section */
                        
                        --p-oo-primary: var(--oo-color-light-primary, var(--oo-color-primary)); /* the main color used by element */
                        --p-oo-primary-hover: var(--oo-color-light-mix-primary-contrast-20); /* the main color:hover used by element */
                        --p-oo-primary-opacity: var(--oo-color-light-transparent-primary-20); /* the main color:lighten used by element */
                        
                        --p-oo-primary-text: var(--oo-color-light-contrast-primary); /* the main text color used by the element */
                        --p-oo-primary-text-hover: var(--oo-color-light-contrast-primary); /* the main text color:hover used by the element */
                        
                        --p-oo-content-background: var(--oo-color-light-mix-bg-text-30); 
                        --p-oo-content-color: var(--oo-color-light-text, var(--oo-color-text));
                        --p-oo-content-color-hover: color-mix(in srgb, var(--p-oo-content-color) 75%, var(--p-oo-content-background) 25%);
                        --p-oo-content-border: var(--oo-color-dark-mix-bg-contrast-50, color-mix(in srgb, var(--p-oo-content-color) 25%, var(--p-oo-content-background) 75%));

                        --p-oo-form-background: var(--p-oo-background);
                        --p-oo-form-color: var(--p-oo-content-color);
                        --p-oo-form-border: var(--p-oo-content-border);
                        --p-oo-form-dropdown-background: var(--p-oo-background);
                        --p-oo-form-dropdown-color: var(--oo-color-light-text);

                        --p-oo-headline-color: var(--oo-color-light-headline, var(--oo-color-headline));
                    ";
            break;

        case 'bg-dark':
            echo "
                        --p-oo-background: var(--oo-color-dark-bg, var(--oo-color-bg)); /* background variable from parent .o-section */

                        --p-oo-primary: var(--oo-color-dark-primary, var(--oo-color-primary)); /* the main color used by element */
                        --p-oo-primary-hover: var(--oo-color-dark-mix-primary-contrast-20); /* the main color:hover used by element */
                        --p-oo-primary-opacity: var(--oo-color-dark-transparent-primary-20); /* the main color:lighten used by element */

                        --p-oo-primary-text: var(--oo-color-dark-contrast-primary); /* the main text color used by the element */
                        --p-oo-primary-text-hover: var(--oo-color-dark-contrast-primary); /* the main text color:hover used by the element */

                        --p-oo-content-background: var(--oo-color-dark-mix-bg-text-30); 
                        --p-oo-content-color: var(--oo-color-dark-text, var(--oo-color-text));
                        --p-oo-content-color-hover: color-mix(in srgb, var(--p-oo-content-color) 75%, var(--p-oo-content-background) 25%);
                        --p-oo-content-border: var(--oo-color-dark-mix-bg-contrast-50, color-mix(in srgb, var(--p-oo-content-color) 25%, var(--p-oo-content-background) 75%));

                        --p-oo-form-background: var(--p-oo-background);
                        --p-oo-form-color: var(--p-oo-content-color);
                        --p-oo-form-border: var(--p-oo-content-border);
                        --p-oo-form-dropdown-background: var(--p-oo-background);
                        --p-oo-form-dropdown-color: var(--oo-color-dark-text);

                        --p-oo-headline-color: var(--oo-color-dark-headline, var(--oo-color-headline));
                    ";
            break;

        case 'bg-primary':
            echo "
                        --p-oo-background: var(--oo-color-primary-bg, var(--oo-color-bg)); /* background variable from parent .o-section */

                        --p-oo-primary: var(--oo-color-primary-primary, var(--oo-color-primary)); /* the main color used by element */
                        --p-oo-primary-hover: var(--oo-color-primary-mix-primary-contrast-20); /* the main color:hover used by element */
                        --p-oo-primary-opacity: var(--oo-color-primary-transparent-primary-20); /* the main color:lighten used by element */

                        --p-oo-primary-text: var(--oo-color-primary-contrast-primary); /* the main text color used by the element */
                        --p-oo-primary-text-hover: var(--oo-color-primary-contrast-primary); /* the main text color:hover used by the element */

                        --p-oo-content-background: var(--oo-color-primary-mix-bg-text-30); 
                        --p-oo-content-color: var(--oo-color-primary-text, var(--oo-color-text));
                        --p-oo-content-color-hover: color-mix(in srgb, var(--p-oo-content-color) 75%, var(--p-oo-content-background) 25%);
                        --p-oo-content-border: var(--oo-color-primary-mix-bg-contrast-50, color-mix(in srgb, var(--p-oo-content-color) 25%, var(--p-oo-content-background) 75%));

                        --p-oo-form-background: var(--p-oo-background);
                        --p-oo-form-color: var(--p-oo-content-color);
                        --p-oo-form-border: var(--p-oo-content-border);
                        --p-oo-form-dropdown-background: var(--p-oo-background);
                        --p-oo-form-dropdown-color: var(--oo-color-primary-text);

                        --p-oo-headline-color: var(--oo-color-primary-headline, var(--oo-color-headline));
                    ";
            break;

        case 'bg-secondary':
            echo "
                        --p-oo-background: var(--oo-color-secondary-bg, var(--oo-color-bg)); /* background variable from parent .o-section */

                        --p-oo-primary: var(--oo-color-secondary-primary, var(--oo-color-primary)); /* the main color used by element */
                        --p-oo-primary-hover: var(--oo-color-secondary-mix-primary-contrast-20); /* the main color:hover used by element */
                        --p-oo-primary-opacity: var(--oo-color-secondary-transparent-primary-20); /* the main color:lighten used by element */

                        --p-oo-primary-text: var(--oo-color-secondary-contrast-primary); /* the main text color used by the element */
                        --p-oo-primary-text-hover: var(--oo-color-secondary-contrast-primary); /* the main text color:hover used by the element */

                        --p-oo-content-background: var(--oo-color-secondary-mix-bg-text-30); 
                        --p-oo-content-color: var(--oo-color-secondary-text, var(--oo-color-text));
                        --p-oo-content-color-hover: color-mix(in srgb, var(--p-oo-content-color) 75%, var(--p-oo-content-background) 25%);
                        --p-oo-content-border: var(--oo-color-secondary-mix-bg-contrast-50, color-mix(in srgb, var(--p-oo-content-color) 25%, var(--p-oo-content-background) 75%));

                        --p-oo-form-background: var(--p-oo-background);
                        --p-oo-form-color: var(--p-oo-content-color);
                        --p-oo-form-border: var(--p-oo-content-border);
                        --p-oo-form-dropdown-background: var(--p-oo-background);
                        --p-oo-form-dropdown-color: var(--oo-color-secondary-text);
                        
                        --p-oo-headline-color: var(--oo-color-secondary-headline, var(--oo-color-headline));
                    ";
            break;

        default:
        case 'bg-transparent':
            echo "
                        --p-oo-background: var(--oo-color-bg, var(--oo-color-bg)); /* background variable from parent .o-section */
                        
                        --p-oo-primary: var(--oo-color-primary, var(--oo-color-primary)); /* the main color used by element */
                        --p-oo-primary-hover: var(--oo-color-mix-primary-contrast-20); /* the main color:hover used by element */
                        --p-oo-primary-opacity: var(--oo-color-transparent-primary-20); /* the main color:lighten used by element */
                        
                        --p-oo-primary-text: var(--oo-color-contrast-primary); /* the main text color used by the element */
                        --p-oo-primary-text-hover: var(--oo-color-contrast-primary); /* the main text color:hover used by the element */
                        
                        --p-oo-content-background: var(--oo-color-mix-bg-text-30); 
                        --p-oo-content-color: var(--oo-color-text, var(--oo-color-text));
                        --p-oo-content-color-hover: color-mix(in srgb, var(--p-oo-content-color) 75%, var(--p-oo-content-background) 25%);
                        --p-oo-content-border: var(--oo-color-mix-bg-contrast-50, color-mix(in srgb, var(--p-oo-content-color) 25%, var(--p-oo-content-background) 75%));
                        
                        --p-oo-form-background: var(--p-oo-background);
                        --p-oo-form-color: var(--p-oo-content-color);
                        --p-oo-form-border: var(--p-oo-content-border);
                        --p-oo-form-dropdown-background: var(--p-oo-background);
                        --p-oo-form-dropdown-color: var(--oo-color-text);

                        --p-oo-headline-color: var(--oo-color-headline, var(--oo-color-headline));
                    ";
            break;
    }

    echo '}';
    echo '</style>';
    ?>

    <div class="c-customer-area__container o-container">

    <?php if (!empty($headline['text']) || !empty($text['wysiwyg'])) { ?>
            <div class="c-customer-area__content o-row --position-center">
                <?php if (!empty($headline['text'])) { ?>
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-headline',
                        [
                            'headline' => $headline,
                            'additional_headline_class' =>
                                'c-customer-area__headline o-col-12 o-col-xl-8',
                        ],
                    ); ?> 
                <?php } ?>
    
                <?php if (!empty($text['wysiwyg'])) { ?>
                    <div class="c-customer-area__text o-text --is-wysiwyg o-col-12 o-col-xl-8">
                        <?php echo $text['wysiwyg']; ?>
                    </div>
                <?php } ?>
    
            </div>
    <?php } ?>

    <?php echo do_shortcode(
        "[on-office-vue-addons frontend='customerArea']",
    ); ?>

    <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
            <div class="c-customer-area__content o-row --position-center">
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-customer-area__buttons o-col-12 --position-center',
                ]); ?>
            </div>
    <?php } ?>

</div>
</section>