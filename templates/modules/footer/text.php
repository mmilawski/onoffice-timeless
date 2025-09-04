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
    echo '<ul class="c-module-links__list">';
    foreach ($buttons as $button) {
        $link_item = $button['link'] ?? [];

        if (!empty($button['link'])):
            echo '<li class="c-module-links__item">';
            $attr = oo_set_link_attr($link_item);
            $attr = preg_replace('/\s*title="[^"]*"/i', '', $attr);

            echo '<a class="c-module-links__link c-link --underlined --on-bg-' .
                $location .
                '" ' .
                $attr .
                '>';
            if (!empty($link_item['title'])) {
                echo $link_item['title'];
            } else {
                _e('Mehr erfahren', 'oo_theme');
            }
            echo '</a>';
            echo '</li>';
        endif;
    }
    echo '</ul>';
endif;
