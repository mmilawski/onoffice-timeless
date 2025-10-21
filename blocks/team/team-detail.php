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

// Image width
$image_width_xs = '510';
$image_width_sm = '511';
$image_width_md = '693';
$image_width_lg = '927';
$image_width_xl = '463';
$image_width_xxl = '543';
$image_width_xxxl = '598';

$rating_provider = $card['rating_provider'];
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
?>

<dialog id="team-<?php echo $post_id; ?>" class="c-dialog --bg-transparent" aria-labelledby="team-<?php echo $post_id; ?>-title" aria-describedby="team-<?php echo $post_id; ?>-desc">
    <div class="c-dialog__wrapper">
        <button class="c-dialog__close --close-popup c-button --only-icon" aria-label="<?php echo esc_html__(
            'Fenster schließen',
            'oo_theme',
        ); ?>">
            <span class="c-button__icon --close"><?php oo_get_icon(
                'close',
            ); ?></span>
        </button>
        <div class="c-dialog__content" id="team-<?php echo $post_id; ?>-desc">
            <div class="c-team-detail"> 
                <?php if (!empty($image)) { ?>
                    <?php oo_get_template('components', '', 'component-image', [
                        'image' => $image,
                        'picture_class' => 'c-team-detail__picture o-picture',
                        'image_class' => 'c-team-detail__image o-image',
                        'additional_cloudimg_params' =>
                            '&func=crop&gravity=face' ?? null,
                        'dimensions' => [
                            '575' => [
                                'w' => $image_width_xs,
                                'h' => $image_width_xs,
                            ],
                            '1600' => [
                                'w' => $image_width_xxxl,
                                'h' => $image_width_xxxl,
                            ],
                            '1400' => [
                                'w' => $image_width_xxl,
                                'h' => $image_width_xxl,
                            ],
                            '1200' => [
                                'w' => $image_width_xl,
                                'h' => $image_width_xl,
                            ],
                            '992' => [
                                'w' => $image_width_lg,
                                'h' => $image_width_lg,
                            ],
                            '768' => [
                                'w' => $image_width_md,
                                'h' => $image_width_md,
                            ],
                            '576' => [
                                'w' => $image_width_sm,
                                'h' => $image_width_sm,
                            ],
                        ],
                    ]); ?>
                <?php } else { ?>
                    <div class="c-team-card__picture"></div>
                <?php } ?>

                <div class="c-team-detail__content">
                    <?php if (!empty($name) || !empty($job)) { ?>
                        <div class="c-team-detail__headline">
                            <?php if (!empty($job)) { ?>
                                <p class="c-team-detail__job"><?php echo $job; ?></p>
                            <?php } ?>
                            <?php if (!empty($name)) { ?>
                                <p class="c-team-detail__name o-headline --h3" id="team-<?php echo $post_id; ?>-title"><?php echo $name; ?></p>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    
                    <?php if (
                        !empty($card['rating_provider']) &&
                        $card['rating_provider'] !== 'none'
                    ): ?>
                        <p class="c-team-card__contact --is-stars">
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-stars',
                                [
                                    'rating' => $rating,
                                    'size' => 'small',
                                    'light_empty_stars' => true,
                                ],
                            ); ?>
                            <?php if ($rating_provider === 'google') { ?>
                                <a href="<?php echo $place_id_url; ?>" target="_blank"><?php esc_html_e(
    'Zu den Bewertungen',
    'oo_theme',
); ?></a>
                            <?php } ?>
                            <?php if (
                                $rating_provider === 'proven_expert' &&
                                $proven_expert_url
                            ) { ?>
                                <a href="<?php echo $proven_expert_url; ?>" target="_blank"><?php esc_html_e(
    'Zu den Bewertungen',
    'oo_theme',
); ?></a>
                            <?php } ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($card['languages'])) { ?>
                        <p class="c-team-card__languages"><?php echo $card[
                            'languages'
                        ]; ?></p>
                    <?php } ?>


                    <div class="c-team-detail__data">
                        <?php if (
                            !empty($contact['phone']) ||
                            !empty($contact['mobile']) ||
                            !empty($contact['fax']) ||
                            !empty(
                                $contact['email'] ||
                                    !empty($card['languages']) ||
                                    $networks
                            )
                        ) { ?>
                            <div class="c-team-card__contact-wrapper">
                                <p class="c-team-detail__title"><?php esc_html_e(
                                    'Kontakt:',
                                    'oo_theme',
                                ); ?></p>
                                <?php if (!empty($contact['email'])): ?>
                                    <p class="c-team-card__contact --is-email">
                                        <?php oo_get_template(
                                            'components',
                                            '',
                                            'component-email',
                                            [
                                                'email' => $contact['email'],
                                                'additional_link_class' =>
                                                    '--is-underlined --text-color',
                                                'aria-label' => sprintf(
                                                    esc_attr__(
                                                        'E-Mail senden an %s',
                                                        'oo_theme',
                                                    ),
                                                    $contact['email'],
                                                ),
                                            ],
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($contact['phone'])): ?>
                                    <p class="c-team-card__contact --is-phone">
                                        <?php oo_get_template(
                                            'components',
                                            '',
                                            'component-contact-numbers',
                                            [
                                                'number' => $contact['phone'],
                                                'country_code' =>
                                                    $contact['phone-country'],
                                                'additional_link_class' =>
                                                    '--is-underlined --text-color',
                                                'aria-label' => esc_attr__(
                                                    'Telefonnummer %s anrufen',
                                                    'oo_theme',
                                                ),
                                            ],
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($contact['mobile'])): ?>
                                    <p class="c-team-card__contact --is-mobile">
                                        <?php oo_get_template(
                                            'components',
                                            '',
                                            'component-contact-numbers',
                                            [
                                                'number' => $contact['mobile'],
                                                'country_code' =>
                                                    $contact['mobile-country'],
                                                'additional_link_class' =>
                                                    '--is-underlined --text-color',
                                                'aria-label' => esc_attr__(
                                                    'Mobilnummer %s anrufen',
                                                    'oo_theme',
                                                ),
                                            ],
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($contact['fax'])): ?>
                                    <p class="c-team-card__contact --is-fax">
                                        <?php oo_get_template(
                                            'components',
                                            '',
                                            'component-contact-numbers',
                                            [
                                                'number' => $contact['fax'],
                                                'country_code' =>
                                                    $contact['fax-country'],
                                                'additional_link_class' =>
                                                    '--is-underlined --text-color',
                                                'aria-label' => esc_attr__(
                                                    'Fax an %s senden',
                                                    'oo_theme',
                                                ),
                                            ],
                                        ); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($networks)): ?>
                                    <?php oo_get_template(
                                        'components',
                                        '',
                                        'component-social-media',
                                        [
                                            'networks' => $networks,
                                            'additional_container_class' =>
                                                'c-team-card__contact --is-networks',
                                        ],
                                    ); ?>
                                <?php endif; ?>
                            </div>
                        <?php } ?>

                        <?php if (!empty($wysiwyg)) { ?>
                            <div class="c-team-detail__description-wrapper">
                                <p class="c-team-detail__title"><?php esc_html_e(
                                    'Zur Person:',
                                    'oo_theme',
                                ); ?></p>
                                <div class="c-team-detail__description o-text --is-wysiwyg">
                                    <?php echo $wysiwyg; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</dialog>