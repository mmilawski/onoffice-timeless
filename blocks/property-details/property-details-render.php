<?php
// Content
$shortcode = get_field('shortcode') ?? null;

if (!empty($shortcode)) {
    echo do_shortcode($shortcode);
}
