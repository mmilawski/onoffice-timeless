<?php
// Post ID
$post_id = get_the_ID() ?? null;

// Content
$card = get_field('team', $post_id) ?? null;
$image = $card['image'] ?? null;
$name = $card['name'] ?? null;
$job = $card['job'] ?? null;
$description = $card['description'] ?? [];
$wysiwyg = $description['wysiwyg'] ?? null;
$contact = $card['contact'] ?? null;

// From team block
// Content
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

// Image width
$image_width_xs = '544';
$image_width_sm = '512';
$image_width_md = '330';
$image_width_lg = '448';
$image_width_xl = '352';
$image_width_xxl = '416';
$image_width_xxxl = '460';

$rating_provider = $card['rating_provider'] ?? null;
$rating = 0.0;
$google_api_key = $card['google_api_key'] ?? null;
$place_id = $card['place_id'] ?? null;
$place_id_url = "https://www.google.com/maps/place/?q=place_id:$place_id";
$proven_expert_username = $card['proven_expert_username'];
$proven_expert_password = $card['proven_expert_password'];
$proven_expert_url = '';

if ($rating_provider === 'google') {
    if ($google_api_key && $place_id) {
        $rating = floatval(
            oo_get_google_place($place_id, $google_api_key, 'rating') ?? 0.0,
        );
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

// Description
$description_word_count = str_word_count(trim(strip_tags($wysiwyg))) ?? 0;
$is_long_description = $description_word_count > 75 ? true : false;
?>

<article class="c-team-card --on-<?php echo $bg_color; ?> <?php if (
     $is_slider
 ) {
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
    <div class="c-team-card__content">
        <?php if ($name) { ?>
            <p class="c-team-card__name o-headline --h3"><?php echo $name; ?></p>
        <?php } ?>
        <?php if ($job) { ?>
            <p class="c-team-card__job"><?php echo $job; ?></p>
        <?php } ?>
        <?php if (
            !empty($card['rating_provider']) &&
            $card['rating_provider'] !== 'none'
        ): ?>
            <p class="c-team-card__contact --is-stars">
                <?php oo_get_template('components', '', 'component-stars', [
                    'rating' => $rating,
                    'size' => 'small',
                    'light_empty_stars' => true,
                ]); ?>
                <?php if ($rating_provider === 'google') { ?>
                            <a href="<?php echo esc_url(
                                $place_id_url,
                            ); ?>" target="_blank"><?php esc_html_e(
    'Zu den Bewertungen',
    'oo_theme',
); ?></a>
                        <?php } ?>
                        <?php if (
                            $rating_provider === 'proven_expert' &&
                            $proven_expert_url
                        ) { ?>
                            <a href="<?php echo esc_url(
                                $proven_expert_url,
                            ); ?>" target="_blank"><?php esc_html_e(
    'Zu den Bewertungen',
    'oo_theme',
); ?></a>
                        <?php } ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($card['languages'])) { ?>
            <p class="c-team-card__languages">Sprachen: <?php echo $card[
                'languages'
            ]; ?></p>
        <?php } ?>
        
        <?php if (
            $contact['phone'] ||
            $contact['mobile'] ||
            $contact['fax'] ||
            $card['networks'] ||
            $contact['email']
        ) { ?>
            <?php if ($contact['phone']): ?>
                <dl class="c-team-card__contact --is-phone">
                    <dt class="c-team-card__contact-label"><?php esc_html_e(
                        'Tel.:',
                        'oo_theme',
                    ); ?></dt>
                    <dd class="c-team-card__contact-value"><?php oo_get_template(
                        'components',
                        '',
                        'component-contact-numbers',
                        [
                            'number' => $contact['phone'],
                            'country_code' => $contact['phone-country'],
                            'additional_link_class' => $bg_color
                                ? '--text-color --on-' . $bg_color
                                : '--text-color',
                        ],
                    ); ?>
                    </dd>
                </dl>
            <?php endif; ?>

            <?php if ($contact['mobile']): ?>
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
                            'country_code' => $contact['mobile-country'],
                            'additional_link_class' => $bg_color
                                ? '--text-color --on-' . $bg_color
                                : '--text-color',
                        ],
                    ); ?>
                    </dd>
                </dl>
            <?php endif; ?>

            <?php if ($contact['fax']): ?>
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
                            'country_code' => $contact['fax-country'],
                            'additional_link_class' => $bg_color
                                ? '--text-color --on-' . $bg_color
                                : '--text-color',
                        ],
                    ); ?>
                    </dd>
                </dl>
            <?php endif; ?>

            <?php if ($contact['email']): ?>
                <dl class="c-team-card__contact --is-email">
                    <dt class="c-team-card__contact-label"><?php esc_html_e(
                        'E-Mail:',
                        'oo_theme',
                    ); ?>
                    <dd class="c-team-card__contact-value"><?php oo_get_template(
                        'components',
                        '',
                        'component-email',
                        [
                            'email' => $contact['email'],
                            'additional_link_class' => $bg_color
                                ? '--text-color --on-' . $bg_color
                                : '--text-color',
                        ],
                    ); ?>
                    </dd>
                </dl>
            <?php endif; ?>
            <?php if (!empty($card['networks'])): ?>
                <p class="c-team-card__contact --is-networks">
                    <?php oo_get_template(
                        'components',
                        '',
                        'component-social-media',
                        [
                            'networks' => $card['networks'],
                        ],
                    ); ?>
                </p>
            <?php endif; ?>
        <?php } ?>
    </div>
    <?php if (!empty($description['wysiwyg']) && $is_description) { ?>
        <div class="c-team-card__description o-text --is-wysiwyg <?php echo $is_long_description
            ? '--shorten'
            : ''; ?>">
            <?php echo $description['wysiwyg']; ?>
        </div>
        <?php if ($is_long_description) { ?>
            <div class="c-team-card__more c-read-more">
                <button class="c-read-more__text --more"><?php echo esc_html(
                    'Mehr anzeigen',
                    'oo_theme',
                ); ?></button> 
                <button class="c-read-more__text --less"><?php echo esc_html(
                    'Weniger anzeigen',
                    'oo_theme',
                ); ?></button>
            </div>
        <?php } ?>
    <?php } ?>
</article>