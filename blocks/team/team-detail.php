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
$image_width_xs = '575';
$image_width_sm = '767';
$image_width_md = '350';
$image_width_lg = '424';
$image_width_xl = '385';
$image_width_xxl = '439';
$image_width_xxxl = '528';

$rating_provider = $card['rating_provider'];
$rating = 0.0;
$google_api_key = $card['google_api_key'] ?? null;
$place_id = $third_parties['google']['place_id'] ?? null;
$place_id_override = $card['place_id'] ?? null;
$proven_expert_username = $card['proven_expert_username'];
$proven_expert_password = $card['proven_expert_password'];
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

<dialog id="team-<?php echo $post_id; ?>" class="c-dialog --team-dialog" aria-labelledby="team-<?php echo $post_id; ?>-title" aria-describedby="team-<?php echo $post_id; ?>-desc">
    <div class="c-dialog__wrapper">
        <button class="c-dialog__close c-icon-button --close-popup" aria-label="<?php echo esc_html__(
            'Fenster schließen',
            'oo_theme',
        ); ?>">
            <?php echo oo_get_icon('close', true, [
                'class' => 'c-icon-button__icon --close',
            ]); ?>
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
                    <div class="c-team-detail__picture"></div>
                <?php } ?>

                <div class="c-team-detail__content">
                    <div class="c-team-detail__content-wrapper">
                        <?php if (!empty($name) || !empty($job)) { ?>
                            <?php if (!empty($job)) { ?>
                                <p class="c-team-detail__job"><?php echo $job; ?></p>
                            <?php } ?>
                            <?php if (!empty($name)) { ?>
                                <p class="c-team-detail__name" id="team-<?php echo $post_id; ?>-title"><?php echo $name; ?></p>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <?php if (
                        !empty($card['rating_provider']) &&
                        $card['rating_provider'] !== 'none' &&
                        isset($rating)
                    ): ?>
<div class="c-team-detail__reviews --star-color-bg-primary">
                
         
                           
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-stars',
                                [
                                    'rating' => $rating,
                                    'size' => 'medium-small',
                                    'light_empty_stars' => true,
                                ],
                            ); ?>  </div>
                            
                            
                          <?php endif; ?>

                    <?php if (!empty($card['languages'])) { ?>
                        <p class="c-team-detail__languages">
                            <?php esc_html_e('Sprachen:', 'oo_theme'); ?> 
                            <?php echo $card['languages']; ?>
                        </p>
                    <?php } ?>


                    
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
                        <div class="c-team-detail__contact-block">
                            <?php if (!empty($contact['phone'])): ?>
                                <dl class="c-team-detail__contact --is-phone">
                                    <dt class="c-team-detail__contact-label"><?php esc_html_e(
                                        'Tel.:',
                                        'oo_theme',
                                    ); ?></dt>
                                    <dd class="c-team-detail__contact-value"><?php oo_get_template(
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
                                    ); ?></dd>
                                </dl>
                            <?php endif; ?>

                            <?php if (!empty($contact['mobile'])): ?>
                                <dl class="c-team-detail__contact --is-mobile">
                                    <dt class="c-team-detail__contact-label"><?php esc_html_e(
                                        'Mobile:',
                                        'oo_theme',
                                    ); ?></dt>
                                    <dd class="c-team-detail__contact-value"><?php oo_get_template(
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
                                    ); ?></dd>
                                </dl>
                            <?php endif; ?>

                            <?php if (!empty($contact['fax'])): ?>
                                <dl class="c-team-detail__contact --is-fax">
                                    <dt class="c-team-detail__contact-label"><?php esc_html_e(
                                        'Fax:',
                                        'oo_theme',
                                    ); ?></dt>
                                    <dd class="c-team-detail__contact-value"><?php oo_get_template(
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
                                    ); ?></dd>
                                </dl>
                            <?php endif; ?>
                            <?php if (!empty($contact['email'])): ?>
                                <dl class="c-team-detail__contact --is-email">
                                    <dt class="c-team-detail__contact-label"><?php esc_html_e(
                                        'E-Mail:',
                                        'oo_theme',
                                    ); ?>
                                    <dd class="c-team-detail__contact-value"><?php oo_get_template(
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
                                    ); ?></dd>
                                </dl>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($networks)): ?>
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-social-media',
                                [
                                    'networks' => $networks,
                                    'additional_container_class' =>
                                        'c-team-detail__contact --is-networks',
                                ],
                            ); ?>
                        <?php endif; ?>
                    <?php } ?>

                    <?php if (!empty($wysiwyg)) { ?>
                        <div class="c-team-detail__description-wrapper">
                            <div class="c-team-detail__description o-text --is-wysiwyg">
                                <?php echo $wysiwyg; ?>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
            </div>
        </div>
    </div>
</dialog>