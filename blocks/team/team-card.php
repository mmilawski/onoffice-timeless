<?php
// Post ID
$post_id = get_the_ID() ?? null;

// Content
$card = get_field('team', $post_id) ?? [];
$image = $card['image'] ?? [];
$name = $card['name'] ?? null;
$job = $card['job'] ?? null;
$description = $card['description'] ?? [];
$wysiwyg = $description['wysiwyg'] ?? null;
$contact = $card['contact'] ?? [];
$networks = $card['networks'] ?? [];

// From team block
// Content
$is_description = filter_var(
    get_field('show_description') ?? null,
    FILTER_VALIDATE_BOOLEAN,
);

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$third_parties = get_field('third_parties', 'option') ?? null;

// Slider
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);

// Image width
$image_width_xs = '543';
$image_width_sm = '512';
$image_width_md = '330';
$image_width_lg = '448';
$image_width_xl = '450';
$image_width_xxl = '343';
$image_width_xxxl = '378';

$rating_provider = $card['rating_provider'] ?? null;
$rating = 0.0;
$google_api_key = $card['google_api_key'] ?? null;
$place_id = $third_parties['google']['place_id'] ?? null;
$place_id_override = $card['place_id'] ?? null;
$proven_expert_username = $card['proven_expert_username'] ?? null;
$proven_expert_password = $card['proven_expert_password'] ?? null;
$proven_expert_url = '';

if ($rating_provider === 'google') {
    if (!empty($place_id_override)) {
        $place_id = $place_id_override;
    }

    if ($google_api_key && $place_id) {
        $rating =
            oo_get_google_place($place_id, $google_api_key, 5, 'rating')[
                'rating'
            ] ?? 0.0;
    }
} elseif ($rating_provider === 'proven_expert') {
    if ($proven_expert_username && $proven_expert_password) {
        if (function_exists('oo_get_proven_expert_rating')) {
            $rating =
                oo_get_proven_expert_rating(
                    $proven_expert_username,
                    $proven_expert_password,
                    'rating',
                )['ratingValue'] ?? 0.0;
        }
        if (function_exists('oo_get_proven_expert_rating')) {
            $proven_expert_url = oo_get_proven_expert_profile_url(
                $proven_expert_username,
                $proven_expert_password,
            );
        }
    }
}
?>

<article role="group" <?php if (!empty($name)) {
    echo 'aria-labelledby="name-' . $post_id . '"';
} ?> <?php if (!empty($job)) {
     echo 'aria-describedby="desc-' . $post_id . '"';
 } ?> class="c-team-card --on-<?php echo $bg_color; ?> <?php if ($is_slider) {
     echo '--on-slider c-slider__slide splide__slide';
 } ?>">
    <?php if (!empty($image)) { ?>
        <?php oo_get_template('components', '', 'component-image', [
            'image' => $image,
            'picture_class' => 'c-team-card__picture o-picture',
            'image_class' => 'c-team-card__image o-image',
            'additional_cloudimg_params' => '&func=crop&gravity=face',
            'dimensions' => [
                '575' => [
                    'w' => $image_width_xs,
                    'h' => round(($image_width_xs * 4) / 3),
                ],
                '1600' => [
                    'w' => $image_width_xxxl,
                    'h' => round(($image_width_xxxl * 4) / 3),
                ],
                '1400' => [
                    'w' => $image_width_xxl,
                    'h' => round(($image_width_xxl * 4) / 3),
                ],
                '1200' => [
                    'w' => $image_width_xl,
                    'h' => round(($image_width_xl * 4) / 3),
                ],
                '992' => [
                    'w' => $image_width_lg,
                    'h' => round(($image_width_lg * 4) / 3),
                ],
                '768' => [
                    'w' => $image_width_md,
                    'h' => round(($image_width_md * 4) / 3),
                ],
                '576' => [
                    'w' => $image_width_sm,
                    'h' => round(($image_width_sm * 4) / 3),
                ],
            ],
        ]); ?>
    <?php } else { ?>
        <div class="c-team-card__picture"></div>
    <?php } ?>
    <div class="c-team-card__overlay">
        <div class="c-team-card__icon c-button --only-icon --more">
            <span class="c-button__icon --plus">
                <?php oo_get_icon('plus'); ?>
            </span>
        </div>
        <div class="c-team-card__icon c-button --only-icon --less">
            <span class="c-button__icon --minus">    
                <?php oo_get_icon('minus'); ?>
            </span>
        </div>

        <?php if (!empty($job)) { ?>
            <div class="c-team-card__row --job">
                <p id="desc-<?php echo $post_id; ?>" class="c-team-card__job"><?php echo $job; ?></p>
            </div>
        <?php } ?>

        <div class="c-team-card__row --name">
            <?php if (!empty($name)) { ?>
                <p id="name-<?php echo $post_id; ?>" class="c-team-card__name"><?php
$words = explode(' ', $name);
$word_count = count($words);
$first_line = implode(' ', array_slice($words, 0, ceil($word_count / 2)));
$second_line = implode(' ', array_slice($words, ceil($word_count / 2)));
echo $first_line . '<br>' . $second_line;
?></p>
            <?php } ?>
        </div>

        <?php if (
            !empty($card['rating_provider']) &&
            $card['rating_provider'] !== 'none' &&
            isset($rating)
        ): ?>
            <div class="c-team-card__row --rating --star-color-bg-primary">
                <?php if ($card['rating_provider'] === 'google') {
                    oo_get_template('components', '', 'component-stars', [
                        'rating' => $rating,
                        'size' => 'medium-small',
                        'light_empty_stars' => false,
                    ]);
                } ?>
            </div>
        <?php endif; ?>

        <?php if (
            !empty($contact['phone']) ||
            !empty($contact['mobile']) ||
            !empty($contact['fax']) ||
            !empty($contact['email']) ||
            !empty($card['languages'])
        ) { ?>
            <div class="c-team-card__row --contact">

                <?php if (!empty($card['languages'])) { ?>
                    <p class="c-team-card__languages">
                        <?php esc_html_e('Sprachen:', 'oo_theme'); ?> 
                        <?php echo $card['languages']; ?>
                    </p>
                <?php } ?>
                
                <?php if (
                    !empty($contact['phone']) ||
                    !empty($contact['mobile']) ||
                    !empty($contact['fax']) ||
                    !empty($contact['email']) ||
                    !empty($networks)
                ) { ?>
                    <div class="c-team-card__contact-wrapper">
                        <?php if (!empty($contact['mobile'])): ?>
                            <dl class="c-team-card__contact --is-mobile">
                                <dt class="c-team-card__contact-label"><?php esc_html_e(
                                    'Mobile:',
                                    'oo_theme',
                                ); ?></dt>
                                <dd class="c-team-card__contact-value"><?php oo_get_template(
                                    'components',
                                    '',
                                    'component-contact-numbers',
                                    [
                                        'number' => $contact['mobile'],
                                        'country_code' =>
                                            $contact['mobile-country'],
                                        'additional_link_class' => $bg_color
                                            ? '--is-underlined --text-color --on-' .
                                                $bg_color
                                            : '--is-underlined --text-color',
                                        'aria-label' => esc_attr__(
                                            'Mobilnummer %s anrufen',
                                            'oo_theme',
                                        ),
                                    ],
                                ); ?></dd>
                            </dl>
                        <?php elseif (!empty($contact['phone'])): ?>
                            <dl class="c-team-card__contact --is-phone">
                                <dt class="c-team-card__contact-label"><?php esc_html_e(
                                    'Tel.:',
                                    'oo_theme',
                                ); ?></dt>    
                                <dd class="c-team-card__contact-value">
                                    <?php oo_get_template(
                                        'components',
                                        '',
                                        'component-contact-numbers',
                                        [
                                            'number' => $contact['phone'],
                                            'country_code' =>
                                                $contact['phone-country'],
                                            'additional_link_class' => $bg_color
                                                ? '--is-underlined --text-color --on-' .
                                                    $bg_color
                                                : '--is-underlined --text-color',
                                            'aria-label' => esc_attr__(
                                                'Telefonnummer %s anrufen',
                                                'oo_theme',
                                            ),
                                        ],
                                    ); ?>
                                </dd>
                            </dl>
                        <?php elseif (!empty($contact['fax'])): ?>
                            <dl class="c-team-card__contact --is-fax">
                                <dt class="c-team-card__contact-label"><?php esc_html_e(
                                    'Fax:',
                                    'oo_theme',
                                ); ?></dt>
                                <dd class="c-team-card__contact-value"><?php oo_get_template(
                                    'components',
                                    '',
                                    'component-contact-numbers',
                                    [
                                        'number' => $contact['fax'],
                                        'country_code' =>
                                            $contact['fax-country'],
                                        'additional_link_class' => $bg_color
                                            ? '--is-underlined --text-color --on-' .
                                                $bg_color
                                            : '--is-underlined --text-color',
                                        'aria-label' => esc_attr__(
                                            'Fax an %s senden',
                                            'oo_theme',
                                        ),
                                    ],
                                ); ?></dd>
                            </dl>
                        <?php endif; ?>
                        <?php if (!empty($contact['email'])): ?>
                            <dl class="c-team-card__contact --is-email">
                                <dt class="c-team-card__contact-label"><?php esc_html_e(
                                    'E-Mail:',
                                    'oo_theme',
                                ); ?></dt>
                                <dd class="c-team-card__contact-value"><?php oo_get_template(
                                    'components',
                                    '',
                                    'component-email',
                                    [
                                        'email' => $contact['email'],
                                        'additional_link_class' => $bg_color
                                            ? '--is-underlined --text-color --on-' .
                                                $bg_color
                                            : '--is-underlined --text-color',
                                        'aria-label' => sprintf(
                                            esc_attr__(
                                                'E-Mail senden an %s',
                                                'oo_theme',
                                            ),
                                            $contact['email'],
                                        ),
                                    ],
                                ); ?></dd>
                            </dl>
                        <?php endif; ?>
                            </div>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($networks && array_filter($networks)): ?>
            <div class="c-team-card__row --social">
                <?php oo_get_template(
                    'components',
                    '',
                    'component-social-media',
                    [
                        'networks' => $networks,
                        'additional_container_class' =>
                            'c-team-card__social-links --is-team-card',
                    ],
                ); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($wysiwyg) && $is_description) { ?>
            <div class="c-team-card__row --action">
                <button class="c-team-card__button c-button --ghost <?php
                echo $bg_color ? '--on-' . $bg_color . ' ' : ' ';
                echo $is_slider ? '--open-popup-team-slider' : '--open-popup';
                ?>" data-popup="team-<?php echo $post_id; ?>" aria-haspopup="dialog" aria-controls="team-<?php echo $post_id; ?>">
                    <span class="c-button__text"><?php esc_html_e(
                        'Mehr erfahren',
                        'oo_theme',
                    ); ?></span>
                </button>
            </div>
        <?php } ?>
    </div>
</article>