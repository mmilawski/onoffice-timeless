<?php
// Content
$headline = get_field('headline') ?? [];
$text = get_field('text') ?? [];
$type = get_field('type') ?? 'all';
$team = get_field('team') ?? [];
$buttons = get_field('buttons') ?? [];
$is_description = filter_var(
    get_field('show_description') ?? null,
    FILTER_VALIDATE_BOOLEAN,
);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Query Posts Settings
switch ($type) {
    case 'all':
        $post_in = null;
        $order_by = 'menu_order';
        break;

    case 'individual':
        $post_in = $team;
        $order_by = 'post__in';
        break;

    default:
        $post_in = null;
        $order_by = 'menu_order';
}

$query_args = [
    'post_type' => 'oo_team',
    'post_status' => 'publish',
    'hide_empty' => true,
    'posts_per_page' => -1,
    'post__in' => $post_in,
    'orderby' => $order_by,
    'order' => 'ASC',
    'suppress_filters' => false,
];

$team_query = new WP_Query($query_args);

// set header level for submodule
$size = !empty($headline['text'])
    ? sanitize_header_level($headline['size'])
    : 1;
set_current_header_level($size);
?>

<section <?php oo_block_id(
    $block,
); ?>  class="c-team o-section --<?php echo $bg_color; ?>">
    <div class="c-team__container o-container">
        <div class="c-team__row o-row --position-center">
            <?php if (
                !empty($headline['text']) ||
                !empty($text['wysiwyg'])
            ) { ?>
                <div class="c-team__content o-col-12 o-col-xl-8">
                    <?php if (!empty($headline['text'])) { ?>
                        <?php oo_get_template(
                            'components',
                            '',
                            'component-headline',
                            [
                                'headline' => $headline,
                                'additional_headline_class' =>
                                    'c-team__headline',
                            ],
                        ); ?>
                    <?php } ?>

                    <?php if (!empty($text['wysiwyg'])) { ?>
                        <div class="c-team__text o-text --is-wysiwyg">
                            <?php echo $text['wysiwyg']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            
            <?php if ($team_query->have_posts()): ?>
                <?php if (!$is_slider) { ?>
                    <div class="c-team__members o-col-12 o-col-xl-10">
                        <?php
                        while ($team_query->have_posts()):
                            $team_query->the_post();
                            setup_postdata($team_query);
                            require 'team-card.php';
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                <?php } else { ?>
                    <div class="c-team__slider --on-<?php echo $bg_color; ?> o-col-12 c-slider --is-team-slider splide" data-splide='{"perPage":1,"gap":32,"snap":true,"lazyLoad":"nearby","mediaQuery":"min","breakpoints":{"768":{"perPage":2},"1400":{"perPage":3}}}'>
                        <div class="c-slider__track splide__track o-col-12 o-col-xl-10">
                            <div class="c-slider__list splide__list">
                                <?php
                                while ($team_query->have_posts()):
                                    $team_query->the_post();
                                    setup_postdata($team_query);
                                    require 'team-card.php';
                                endwhile;
                                wp_reset_postdata();
                                ?> 
                            </div>
                        </div>
                        <div class="c-slider__arrows splide__arrows">
                            <button class="c-slider__arrow --prev c-button --only-icon --square splide__arrow splide__arrow--prev">
                                <span class="u-screen-reader-only"><?php esc_html_e(
                                    'Vorheriges',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-button__icon --chevron-left"><?php oo_get_icon(
                                    'chevron-left',
                                ); ?></span>
                            </button>
                            <button class="c-slider__arrow --next c-button --only-icon --square splide__arrow splide__arrow--next">
                                <span class="u-screen-reader-only"><?php esc_html_e(
                                    'Nächstes',
                                    'oo_theme',
                                ); ?></span>
                                <span class="c-button__icon --chevron-right"><?php oo_get_icon(
                                    'chevron-right',
                                ); ?></span>
                            </button>
                        </div>
                        <ul class="c-slider__pagination splide__pagination"></ul>
                    </div>
                <?php } ?>
            <?php endif; ?>
            <?php if (!empty($buttons['buttons'][0]['link'])) { ?>
                <?php oo_get_template('components', '', 'component-buttons', [
                    'buttons' => $buttons['buttons'],
                    'icon_first' => 'arrow-right',
                    'icon_second' => 'arrow-right',
                    'additional_button_class' => $bg_color
                        ? '--on-' . $bg_color
                        : '',
                    'additional_container_class' =>
                        'c-team__buttons o-col-12 o-col-xl-8',
                ]); ?>
            <?php } ?>
        </div>
    </div>
    <?php // Popup

if ($is_description) {
        while ($team_query->have_posts()):
            $team_query->the_post();
            setup_postdata($team_query);

            $post_id = get_the_ID() ?? null;
            $card = get_field('team', $post_id) ?? [];
            $description = $card['description'] ?? [];
            $wysiwyg = $description['wysiwyg'] ?? null;

            if (!empty($wysiwyg)) {
                require 'team-detail.php';
            }
        endwhile;
        wp_reset_postdata();
    } ?>
</section>