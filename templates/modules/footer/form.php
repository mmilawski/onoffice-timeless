<?php
/**
 * Module Name: Form
 * @param $args
 * Get values from the parameter
 */

// Helpers
$content = $args['content'] ?? [];
$location = $args['location'] ?? 'footer';

$headline = $content['headline'] ?? null;
$text = $content['text']['wysiwyg'] ?? null;
$shortcode = $content['shortcode'] ?? null;

if (!empty($headline)):
    oo_get_template('components', '', 'component-headline', [
        'headline' => [
            'text' => strip_tags($headline),
            'size' => 'span',
        ],
        'additional_headline_class' => 'c-module-form__headline',
    ]);
endif;

if (!empty($text)): ?>
    <div class="c-module-form__text o-text --is-wysiwyg">
        <?php echo $text; ?>
    </div>
<?php endif;

if (!empty($shortcode)): ?>
    <div class="c-module-form__form --on-bg-footer">
        <?php echo do_shortcode($shortcode); ?>		
    </div>
<?php endif;
