<?php

/**
 *
 *    Copyright (C) 2024  onOffice GmbH
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

use onOffice\WPlugin\AddressList;
global $wp;
?>

<?php
$dont_echo = ['Vorname', 'Name', 'Sonstige1', 'Sonstige2', 'Sonstige3'];

// Background width
$background_width_xs = '576';
$background_width_sm = '768';
$background_width_md = '992';
$background_width_lg = '1200';
$background_width_xl = '1400';
$background_width_xxl = '1600';
$background_width_xxxl = '1920';

$show_more_count = get_field('show_more_count') ?? 12;

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';

$current_url = home_url(add_query_arg([], $wp->request)) ?? '';
/* @var $pAddressList AddressList */
$currentAddressArr = [];
if (
    is_object($pAddressList) &&
    method_exists($pAddressList, 'getCurrentAddress')
) {
    $currentAddressArr = $pAddressList->getCurrentAddress() ?? [];
}
if (!empty($currentAddressArr && is_array($currentAddressArr))) {
    foreach ($currentAddressArr as $addressId => $escapedValues) {

        $email = $escapedValues['Email'] ?? null;
        $imageUrl['url'] = $escapedValues['imageUrl'] ?? null;
        $imageUrl['alt'] = $pAddressList->generateImageAlt($addressId) ?? null;
        $jobTitle = $escapedValues['jobTitle'] ?? null;
        $phone = $escapedValues['Telefon1'] ?? null;
        $phone_url = oo_clean_link_number($phone);

        $sonstiges = [
            'Sonstige1' => [
                'label' => $pAddressList->getFieldLabel('Sonstige1') ?? null,
                'value' => $escapedValues['Sonstige1'] ?? null,
            ],
            'Sonstige2' => [
                'label' => $pAddressList->getFieldLabel('Sonstige2') ?? null,
                'value' => $escapedValues['Sonstige2'] ?? null,
            ],
            'Sonstige3' => [
                'label' => $pAddressList->getFieldLabel('Sonstige3') ?? null,
                'value' => $escapedValues['Sonstige3'] ?? null,
            ],
        ];

        $ratingsUrl = $escapedValues['bewertungslinkWebseite'] ?? null;
        $networks = [
            'facebook' => ['url' => $escapedValues['facebook'] ?? null],
            'instagram' => ['url' => $escapedValues['instagram'] ?? null],
            'linkedin' => ['url' => $escapedValues['linkedin'] ?? null],
            'twitter' => ['url' => $escapedValues['twitter'] ?? null],
            'xing' => ['url' => $escapedValues['xing'] ?? null],
            'youtube' => ['url' => $escapedValues['youtube'] ?? null],
            'tiktok' => ['url' => $escapedValues['tiktok'] ?? null],
            'pinterest' => ['url' => $escapedValues['pinterest'] ?? null],
        ];
        unset($escapedValues['imageUrl']);
        unset($escapedValues['jobTitle']);
        unset($escapedValues['Sonstige1']);
        unset($escapedValues['bewertungslinkWebseite']);
        unset($escapedValues['facebook']);
        unset($escapedValues['instagram']);
        unset($escapedValues['linkedin']);
        unset($escapedValues['twitter']);
        unset($escapedValues['xing']);
        unset($escapedValues['youtube']);
        unset($escapedValues['tiktok']);
        unset($escapedValues['pinterest']);

        $pAddressList->setAddressId($addressId);
        $shortCodeActiveEstates = $pAddressList->getShortCodeActiveEstates();
        $shortCodeReferenceEstates = $pAddressList->getShortCodeReferenceEstates();
        $shortCodeForm = $pAddressList->getShortCodeForm();

        $fields_counter = count($escapedValues);
        $fields_more = $show_more_count;

        $features_list_first = array_slice($escapedValues, 0, $fields_more);
        $features_list_last = array_slice(
            $escapedValues,
            $fields_more,
            $fields_counter,
        );
        ?>
            <section class="o-section --bg-transparent --bg-transparent-mixed">
                <div class="o-container">
                    <div class="o-row --center">
                        <div class="c-address-details o-col-12">
                            <div class="c-address-details__main <?php echo $ratingsUrl
                                ? '--has-review'
                                : ''; ?>">
                                <div class="c-address-details__media">
                                    <?php if (!empty($imageUrl)) {
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-image',
                                            [
                                                'picture_class' =>
                                                    'c-address-details__picture o-picture',
                                                'image' => $imageUrl,
                                                'additional_cloudimg_params' =>
                                                    '&func=crop&gravity=face',
                                                'loading' => 'eager',
                                                'decoding' => 'auto',
                                                'dimensions' => [
                                                    '575' => [
                                                        'w' => $background_width_xs,
                                                        'h' => $background_width_xs,
                                                    ],
                                                    '1600' => [
                                                        'w' => $background_width_xxxl,
                                                        'h' => round(
                                                            ($background_width_xxxl *
                                                                9) /
                                                                16,
                                                        ),
                                                    ],
                                                    '1400' => [
                                                        'w' => $background_width_xxl,
                                                        'h' => round(
                                                            ($background_width_xxl *
                                                                9) /
                                                                16,
                                                        ),
                                                    ],
                                                    '1200' => [
                                                        'w' => $background_width_xl,
                                                        'h' => round(
                                                            ($background_width_xl *
                                                                9) /
                                                                16,
                                                        ),
                                                    ],
                                                    '992' => [
                                                        'w' => $background_width_lg,
                                                        'h' => $background_width_lg,
                                                    ],
                                                    '768' => [
                                                        'w' => $background_width_md,
                                                        'h' => $background_width_md,
                                                    ],
                                                    '576' => [
                                                        'w' => $background_width_sm,
                                                        'h' => $background_width_sm,
                                                    ],
                                                ],
                                            ],
                                        );
                                    } ?>
                                    <div class="c-address-details__share c-buttons">
                                        <?php
                                        global $wp;

                                        $property_detail_page =
                                            get_field('general', 'option')[
                                                'property_detail'
                                            ] ?? [];
                                        $property_share_button = filter_var(
                                            $property_detail_page[
                                                'property_share_button'
                                            ],
                                            FILTER_VALIDATE_BOOLEAN,
                                        );

                                        if ($property_share_button) {
                                            oo_get_template(
                                                'components',
                                                '',
                                                'component-share',
                                                [
                                                    'button_class' =>
                                                        'c-button --small',
                                                    'button_title' => __(
                                                        'Teilen',
                                                        'oo-theme',
                                                    ),
                                                    'popup_id' =>
                                                        'address_detail_share',
                                                    'share_link' => home_url(
                                                        add_query_arg(
                                                            [],
                                                            $wp->request,
                                                        ),
                                                    ),
                                                ],
                                            );
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="c-address-details__name">
                                    <?php if (!empty($jobTitle)): ?>
                                        <h2 class="c-address-details__subline o-headline --text-color"><?php echo $jobTitle; ?></h2>
                                    <?php endif; ?>
                                    <?php if (
                                        (!empty($escapedValues['Vorname']) &&
                                            !empty($escapedValues['Name'])) ||
                                        !empty($escapedValues['Zusatz1'])
                                    ): ?>
                                        <h1 class="c-address-details__headline o-headline">
                                            <?php if (
                                                !empty(
                                                    $escapedValues['Vorname']
                                                ) &&
                                                !empty($escapedValues['Name'])
                                            ) {
                                                echo $escapedValues['Vorname'] .
                                                    ' ' .
                                                    $escapedValues['Name'];
                                            } else {
                                                if (
                                                    !empty(
                                                        $escapedValues[
                                                            'Zusatz1'
                                                        ]
                                                    )
                                                ) {
                                                    echo $escapedValues[
                                                        'Zusatz1'
                                                    ];
                                                    array_push(
                                                        $dont_echo,
                                                        'Zusatz1',
                                                    );
                                                }
                                            } ?>
                                        </h1>
                                    <?php endif; ?>
                                </div>

                                <?php if ($ratingsUrl) { ?>
                                    <div class="c-address-details__review">
                                        <div class="c-address-details__stars">
                                            <?php oo_get_template(
                                                'components',
                                                '',
                                                'component-stars',
                                                [
                                                    'rating' => 5.0,
                                                    'size' => 'medium',
                                                    'light_empty_stars' => false,
                                                ],
                                            ); ?>
                                        </div>
                                        <p>
                                            <a
                                                href="<?php echo $ratingsUrl; ?>" 
                                                class="c-link --underlined" 
                                                target="_blank"><?php esc_html_e(
                                                    'Bewertungen',
                                                    'oo_theme',
                                                ); ?>
                                            </a>
                                        </p>
                                    </div>
                                <?php } ?>

                                <div class="c-address-details__info">
                                    <div class="c-address-details__criteria">
                                        <div class="c-address-details__criteria-items">
                                            <?php if (
                                                is_array($features_list_first)
                                            ) {
                                                foreach (
                                                    $features_list_first
                                                    as $field => $value
                                                ) {
                                                    if (
                                                        empty($value) ||
                                                        in_array(
                                                            $field,
                                                            $dont_echo,
                                                        )
                                                    ) {
                                                        continue;
                                                    }
                                                    if (
                                                        !str_starts_with(
                                                            $field,
                                                            'default',
                                                        )
                                                    ) {
                                                        $value = is_array(
                                                            $value,
                                                        )
                                                            ? esc_html(
                                                                implode(
                                                                    ', ',
                                                                    $value,
                                                                ),
                                                            )
                                                            : esc_html($value);
                                                        if (
                                                            $field === 'Email'
                                                        ) {
                                                            $value = "<a href=\"mailto:$value\" class=\"c-link --text-color --text-truncate\" title=\"$value\">$value</a>";
                                                        }
                                                        if (
                                                            $field ===
                                                            'Telefon1'
                                                        ) {
                                                            $value = "<a href=\"tel:$value\" class=\"c-link --text-color\">$value</a>";
                                                        }
                                                        if (
                                                            $field ===
                                                            'Telefax1'
                                                        ) {
                                                            $value = "<a href=\"fax:$value\" class=\"c-link --text-color\">$value</a>";
                                                        }
                                                        echo '<dl class="c-address-details__criterion ' .
                                                            ($field ===
                                                            'Telefon1'
                                                                ? '--hide-mobile'
                                                                : '') .
                                                            '"><dt class="c-address-details__criterion-name">' .
                                                            esc_html(
                                                                $pAddressList->getFieldLabel(
                                                                    $field,
                                                                ),
                                                            ) .
                                                            '</dt>' .
                                                            "\n" .
                                                            '<dd class="c-address-details__criterion-value">' .
                                                            $value .
                                                            '</dd></dl>' .
                                                            "\n";
                                                    }
                                                }
                                            } ?>
                                        </div>
                                        <div class="c-address-details__criteria-items --is-toggle">
                                            <?php if (
                                                is_array($features_list_last)
                                            ) {
                                                foreach (
                                                    $features_list_last
                                                    as $field => $value
                                                ) {
                                                    if (
                                                        empty($value) ||
                                                        in_array(
                                                            $field,
                                                            $dont_echo,
                                                        )
                                                    ) {
                                                        continue;
                                                    }
                                                    if (
                                                        !str_starts_with(
                                                            $field,
                                                            'default',
                                                        )
                                                    ) {
                                                        $value = is_array(
                                                            $value,
                                                        )
                                                            ? esc_html(
                                                                implode(
                                                                    ', ',
                                                                    $value,
                                                                ),
                                                            )
                                                            : esc_html($value);
                                                        if (
                                                            $field === 'Email'
                                                        ) {
                                                            $value = "<a href=\"mailto:$value\" class=\"c-link --text-color --text-truncate\" title=\"$value\">$value</a>";
                                                        }
                                                        if (
                                                            $field ===
                                                            'Telefon1'
                                                        ) {
                                                            $value = "<a href=\"tel:$value\" class=\"c-link --text-color\">$value</a>";
                                                        }
                                                        if (
                                                            $field ===
                                                            'Telefax1'
                                                        ) {
                                                            $value = "<a href=\"fax:$value\" class=\"c-link --text-color\">$value</a>";
                                                        }
                                                        echo '<dl class="c-address-details__criterion ' .
                                                            ($field ===
                                                            'Telefon1'
                                                                ? '--hide-mobile'
                                                                : '') .
                                                            '"><dt class="c-address-details__criterion-name">' .
                                                            esc_html(
                                                                $pAddressList->getFieldLabel(
                                                                    $field,
                                                                ),
                                                            ) .
                                                            '</dt>' .
                                                            "\n" .
                                                            '<dd class="c-address-details__criterion-value">' .
                                                            $value .
                                                            '</dd></dl>' .
                                                            "\n";
                                                    }
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                    <?php if (
                                        $fields_counter > $fields_more
                                    ) { ?>
                                        <div class="c-address-details__more-wrapper --hidden">
                                            <button class="c-address-details__more">
                                                <span class="c-read-more__icon c-button --only-icon">
                                                    <span class="c-address-details__more-open c-button__icon --plus"><?php oo_get_icon(
                                                        'plus',
                                                    ); ?>
                                                    </span>
                                                    <span class="c-address-details__more-close c-button__icon --minus"><?php oo_get_icon(
                                                        'minus',
                                                    ); ?>
                                                    </span>
                                                </span>
                                                <span class="c-address-details__more-title" 
                                                    data-open-text="<?php echo esc_html(
                                                        'Mehr anzeigen',
                                                        'oo_theme',
                                                    ); ?>" 
                                                    data-close-text="<?php echo esc_html(
                                                        'Weniger anzeigen',
                                                        'oo_theme',
                                                    ); ?>">
                                                    <?php echo esc_html(
                                                        'Mehr anzeigen',
                                                        'oo_theme',
                                                    ); ?>
                                                </span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                    <div class="c-address-details__contacts">
                                        <?php if (!empty($shortCodeForm)) {

                                            echo '<a class="c-address-details__contact c-button --has-icon" href="#contact_form">';
                                            echo '<span class="c-button__text">';
                                            esc_html_e(
                                                'Kontaktformular',
                                                'oo_theme',
                                            );
                                            echo '</span>';
                                            echo '<span class="c-button__icon --arrow">';
                                            echo oo_get_icon('arrow-right');
                                            echo '</span>';
                                            echo '</a>';
                                            ?>
                                        <?php
                                        } ?>
                                        <?php
                                        if (!empty($phone)) {
                                            echo '<a class="c-address-details__contact c-button --has-icon --ghost --hide-desktop" href="' .
                                                esc_url('tel:' . $phone_url) .
                                                '">';
                                            echo '<span class="c-button__text">';
                                            echo __('Anrufen', 'oo_theme');
                                            echo '</span>';
                                            echo '<span class="c-button__icon --arrow">';
                                            echo oo_get_icon('arrow-right');
                                            echo '</span>';
                                            echo '</a>';
                                        }
                                        if (
                                            !oo_is_array_column_empty(
                                                $sonstiges,
                                                'value',
                                            ) ||
                                            !oo_is_array_column_empty(
                                                $networks,
                                                'url',
                                            )
                                        ) {
                                            echo '<a class="c-address-details__contact c-button --small --has-icon --ghost" href="#about">';
                                            echo '<span class="c-button__text">';
                                            echo __('Über mich', 'oo_theme');
                                            echo '</span>';
                                            echo '<span class="c-button__icon --arrow">';
                                            echo oo_get_icon('arrow-right');
                                            echo '</span>';
                                            echo '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
<?php
    }
}
?>
<?php if (
    !empty($shortCodeActiveEstates) ||
    !empty($shortCodeReferenceEstates)
): ?>
		<section class="o-section --<?php echo $bg_color; ?> c-address-details__estates-section">
			<div class="o-container c-address-details__estates">
                <?php if (!empty($shortCodeActiveEstates)) { ?>
                    <div class="c-address-details__estates-active">
                        <div class="o-row c-address-details__title-wrapper">
                            <h2 class="c-address-details__title o-col-12 o-col-lg-8 o-col-xl-6 --is-underlined o-headline"><?php esc_html_e(
                                'Mein Portfolio',
                                'oo_theme',
                            ); ?></h2>
                        </div>
                        <div class="o-row c-property-list --on-address-detail --is-active-estates">
                            <?php echo do_shortcode($shortCodeActiveEstates); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($shortCodeReferenceEstates)) { ?>
                    <div class="c-address-details__estates-reference">
                        <div class="o-row c-address-details__title-wrapper">
                            <h2 class="c-address-details__title o-col-12 o-col-lg-8 o-col-xl-6 --is-underlined o-headline"><?php esc_html_e(
                                'Meine Referenzen',
                                'oo_theme',
                            ); ?></h2>
                        </div>
                        <div class="o-row c-property-list --on-address-detail --is-referenz-estates">
                            <?php echo do_shortcode(
                                $shortCodeReferenceEstates,
                            ); ?>
                        </div>
                    </div>
                <?php } ?>
			</div>
		</section>
<?php endif; ?>

<?php if (is_array($sonstiges)) {
    if (!oo_is_array_column_empty($sonstiges, 'value')) {
        $first = true;
        foreach ($sonstiges as $key => $item) {
            $label = $item['label'];
            $value = $item['value'];
            $first_id = $first ? 'id="about"' : '';
            if (!empty($value)) { ?>
                <section class="o-section --bg-transparent c-text" <?php echo $first_id; ?>>
                    <div class="c-text__container o-container">
                        <div class="c-text__row o-row --position-center">
                            <h2 class="c-address-details__text-headline c-text__headline o-col-12 o-col-lg-8">
                                <?php echo esc_html($label); ?>
                            </h2>
                        </div>
                        <div class="c-text__row o-row --position-center">
                            <div class="c-address-details__text-content c-text__content o-col-12 o-col-lg-8 --is-wysiwyg">
                                <p>
                                    <?php echo nl2br(esc_html($value)); ?>
                                </p>
                            </div>
                        </div>
                        <?php if (
                            $first &&
                            !oo_is_array_column_empty($networks, 'url')
                        ): ?>
                            <div class="c-text__row o-row --position-center">
                                <div class="c-address-details__networks-ratings o-col-12 o-col-lg-8">
                                    <?php oo_get_template(
                                        'components',
                                        '',
                                        'component-social-media',
                                        [
                                            'networks' => $networks,
                                            'additional_container_class' =>
                                                '--is-address',
                                        ],
                                    ); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php $first = false;}
        }
    } else {
         ?>
        <section class="o-section --bg-transparent c-text" id="about">
            <div class="c-text__container o-container">
                <div class="c-text__row o-row --position-center">
                    <h2 class="c-address-details__text-headline c-text__headline o-col-12 o-col-lg-8">
                        <?php echo $pAddressList->getFieldLabel('Sonstige1') ??
                            null; ?>
                    </h2>
                </div>
                <?php if (!oo_is_array_column_empty($networks, 'url')): ?>
                    <div class="c-text__row o-row --position-center">
                        <div class="c-address-details__networks-ratings o-col-12 o-col-lg-8">
                            <?php oo_get_template(
                                'components',
                                '',
                                'component-social-media',
                                [
                                    'networks' => $networks,
                                    'additional_container_class' =>
                                        '--is-address',
                                ],
                            ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
} ?>


<?php if (!empty($shortCodeForm)) { ?>
		<section class="o-section --bg-transparent --bg-transparent-mixed" id="contact_form">
			<div class="o-container">
				<div class="o-row --center">
					<div class="c-address-details o-col-12 o-col-lg-8">
						<h2><?php esc_html_e('Nutzen Sie unser Kontaktformular', 'oo_theme'); ?></h2>
						<div class="detail-contact-form">
							<?php echo do_shortcode($shortCodeForm); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
<?php } ?>
