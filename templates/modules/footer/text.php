<?php
/**
 * Module Name: Text
 * @param $args
 * Get values from the parameter
 */

// Helpers
$content = $args['content'] ?? [];
$location = $args['location'] ?? 'footer';

$headline = $content['headline'] ?? null;
$text = $content['text']['wysiwyg'] ?? null;
$buttons = $content['buttons']['buttons'] ?? [];

if (!empty($headline)):
    oo_get_template('components', '', 'component-headline', [
        'headline' => [
            'text' => strip_tags($headline),
            'size' => 'span',
        ],
        'additional_headline_class' => 'c-module-text__headline',
    ]);
endif;

if (!empty($text)):
    echo '<div class="c-module-text__text o-text --is-wysiwyg">' .
        wp_kses_post($text) .
        '</div>';
endif;

if (!empty($buttons[0]['link'])):
    oo_get_template('components', '', 'component-buttons', [
        'buttons' => $buttons,
        'icon_first' => 'arrow-right',
        'icon_second' => 'arrow-right',
        'additional_button_class' =>
            'c-module-text__button --on-bg-' . $location,
        'additional_container_class' => 'c-module-text__buttons --is-column',
    ]);
endif;
