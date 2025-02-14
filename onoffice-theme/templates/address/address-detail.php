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

// Image width
$image_width_xs = '380';
$image_width_sm = '512';
$image_width_md = '694';
$image_width_lg = '350';
$image_width_xl = '350';
$image_width_xxl = '350';
$image_width_xxxl = '350';

$show_more_count = get_field('show_more_count') ?? 12;

$placeholder_image =
    get_field('general', 'option')['address_detail']['placeholder_image'] ??
    null;

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
        $phone = $escapedValues['Telefon1'] ?? null;
        $phone_url = oo_clean_link_number($phone);
        $image_url['url'] = !empty($escapedValues['imageUrl'])
            ? $escapedValues['imageUrl']
            : (!empty($placeholder_image['url'])
                ? $placeholder_image['url']
                : null);
        $image_url['alt'] = $pAddressList->generateImageAlt($addressId) ?? null;

        $jobTitle = $escapedValues['jobTitle'] ?? null;

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

        $fields_counter = count(
            array_diff_key($escapedValues, array_flip($dont_echo)),
        );
        $fields_more = $show_more_count;

        $features_list_first = array_slice($escapedValues, 0, $fields_more);
        $features_list_last = array_slice(
            $escapedValues,
            $fields_more,
            $fields_counter,
        );
        ?>
            <section class="o-section --bg-transparent">
                <div class="o-container">
                    <div class="o-row --center">
                        <div class="c-address-details o-col-12">
                            <div class="c-address-details__main">
                                <div class="c-address-details__media">
                                    <?php if (!empty($image_url['url'])) {
                                        oo_get_template(
                                            'components',
                                            '',
                                            'component-image',
                                            [
                                                'picture_class' =>
                                                    'c-address-details__picture o-picture',
                                                'image' => $image_url,
                                                'additional_cloudimg_params' =>
                                                    '&func=crop&gravity=face',
                                                'loading' => 'eager',
                                                'decoding' => 'auto',
                                                'dimensions' => [
                                                    '575' => [
                                                        'w' => $image_width_xs,
                                                        'h' => round(
                                                            ($image_width_xs *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '1600' => [
                                                        'w' => $image_width_xxxl,
                                                        'h' => round(
                                                            ($image_width_xxxl *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '1400' => [
                                                        'w' => $image_width_xxl,
                                                        'h' => round(
                                                            ($image_width_xxl *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '1200' => [
                                                        'w' => $image_width_xl,
                                                        'h' => round(
                                                            ($image_width_xl *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '992' => [
                                                        'w' => $image_width_lg,
                                                        'h' => round(
                                                            ($image_width_lg *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '768' => [
                                                        'w' => $image_width_md,
                                                        'h' => round(
                                                            ($image_width_md *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                    '576' => [
                                                        'w' => $image_width_sm,
                                                        'h' => round(
                                                            ($image_width_sm *
                                                                2) /
                                                                3,
                                                        ),
                                                    ],
                                                ],
                                            ],
                                        );
                                    } else {
                                         ?><picture class="c-address-details__picture o-picture"></picture><?php
                                    } ?>
                                    <?php if (!empty($shortCodeForm)) { ?>
                                        <a class="c-address-details__contact c-button" href="#contact_form"><?php esc_html_e(
                                            'Kontaktieren Sie mich',
                                            'oo_theme',
                                        ); ?></a>
                                    <?php } ?>
                                    <?php
                                    if (!empty($phone)) {
                                        echo '<a href="' .
                                            esc_url('tel:' . $phone_url) .
                                            '" class="c-address-details__contact c-button --ghost --hide-desktop">';
                                        echo __('Anrufen', 'oo_theme');
                                        echo '</a>';
                                    }

                                    oo_get_template(
                                        'components',
                                        '',
                                        'component-share',
                                        [
                                            'button_class' =>
                                                'c-address-details__contact c-button --ghost',
                                            'button_title' => esc_html__(
                                                'Kontakt teilen',
                                                'oo_theme',
                                            ),
                                            'share_link' => $current_url,
                                        ],
                                    );
                                    ?>
                                </div>
                                <div class="c-address-details__info">
                                    <div class="c-address-details__headline">
                                        <?php if (
                                            (!empty(
                                                $escapedValues['Vorname']
                                            ) &&
                                                !empty(
                                                    $escapedValues['Name']
                                                )) ||
                                            !empty($escapedValues['Zusatz1'])
                                        ): ?>
                                            <h1 class="o-headline --h2 --text-color">
                                                <?php if (
                                                    !empty(
                                                        $escapedValues[
                                                            'Vorname'
                                                        ]
                                                    ) &&
                                                    !empty(
                                                        $escapedValues['Name']
                                                    )
                                                ) {
                                                    echo $escapedValues[
                                                        'Vorname'
                                                    ] .
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
                                        <?php if (!empty($jobTitle)): ?>
                                            <h2 class="o-headline --h3 --text-color"><?php echo $jobTitle; ?></h2>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (
                                        !empty(
                                            array_filter(
                                                array_column($networks, 'url'),
                                            )
                                        )
                                    ): ?>
                                            <div class="c-address-details__networks-ratings">
                                                <?php oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-social-media',
                                                    [
                                                        'networks' => $networks,
                                                    ],
                                                ); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($ratingsUrl) { ?>
                                            <div class="c-address-details__review">
                                                <?php oo_get_template(
                                                    'components',
                                                    '',
                                                    'component-stars',
                                                    [
                                                        'rating' => 5.0,
                                                        'size' => 'small',
                                                        'light_empty_stars' => true,
                                                    ],
                                                ); ?>
                                                <p>
                                                    <a href="<?php echo $ratingsUrl; ?>" class="c-link --underlined" target="_blank"><?php esc_html_e(
    'Bewertungen',
    'oo_theme',
); ?></a>
                                                </p>
                                            </div>
                                        <?php } ?>
                                    <div class="c-address-details__info-table">
                                        <?php if (
                                            is_array($features_list_first)
                                        ) {
                                            foreach (
                                                $features_list_first
                                                as $field => $value
                                            ) {
                                                if (
                                                    empty($value) ||
                                                    in_array($field, $dont_echo)
                                                ) {
                                                    continue;
                                                }
                                                if (
                                                    !str_starts_with(
                                                        $field,
                                                        'default',
                                                    )
                                                ) {
                                                    $value = is_array($value)
                                                        ? esc_html(
                                                            implode(
                                                                ', ',
                                                                $value,
                                                            ),
                                                        )
                                                        : esc_html($value);
                                                    if ($field === 'Email') {
                                                        $value = "<a href=\"mailto:$value\" class=\"c-link --text-color --text-truncate\">$value</a>";
                                                    }
                                                    if ($field === 'Telefon1') {
                                                        $cleanNumber = oo_clean_link_number(
                                                            $value,
                                                        );
                                                        $value = "<a href=\"tel:$cleanNumber\" class=\"c-link --text-color\">$value</a>";
                                                    }
                                                    if ($field === 'Telefax1') {
                                                        $cleanNumber = oo_clean_link_number(
                                                            $value,
                                                        );
                                                        $value = "<a href=\"fax:$cleanNumber\" class=\"c-link --text-color\">$value</a>";
                                                    }
                                                    echo '<dl class="c-address-details__criteria ' .
                                                        ($field === 'Telefon1'
                                                            ? '--hide-mobile'
                                                            : '') .
                                                        '"><dt class="c-address-details__criteria-name">' .
                                                        esc_html(
                                                            $pAddressList->getFieldLabel(
                                                                $field,
                                                            ),
                                                        ) .
                                                        '</dt>' .
                                                        "\n" .
                                                        '<dd class="c-address-details__criteria-value">' .
                                                        $value .
                                                        '</dd></dl>' .
                                                        "\n";
                                                }
                                            }
                                        } ?>
                                    </div>
                                    <div class="c-address-details__info-table --is-toggle">
                                        <?php if (
                                            is_array($features_list_last)
                                        ) {
                                            foreach (
                                                $features_list_last
                                                as $field => $value
                                            ) {
                                                if (
                                                    empty($value) ||
                                                    in_array($field, $dont_echo)
                                                ) {
                                                    continue;
                                                }
                                                if (
                                                    !str_starts_with(
                                                        $field,
                                                        'default',
                                                    )
                                                ) {
                                                    $value = is_array($value)
                                                        ? esc_html(
                                                            implode(
                                                                ', ',
                                                                $value,
                                                            ),
                                                        )
                                                        : esc_html($value);
                                                    if ($field === 'Email') {
                                                        $value = "<a href=\"mailto:$value\" class=\"c-link --text-color\">$value</a>";
                                                    }
                                                    if ($field === 'Telefon1') {
                                                        $cleanNumber = oo_clean_link_number(
                                                            $value,
                                                        );
                                                        $value = "<a href=\"tel:$cleanNumber\" class=\"c-link --text-color\">$value</a>";
                                                    }
                                                    if ($field === 'Telefax1') {
                                                        $cleanNumber = oo_clean_link_number(
                                                            $value,
                                                        );
                                                        $value = "<a href=\"fax:$cleanNumber\" class=\"c-link --text-color\">$value</a>";
                                                    }

                                                    echo '<dl class="c-address-details__criteria ' .
                                                        ($field === 'Telefon1'
                                                            ? '--hide-mobile'
                                                            : '') .
                                                        '"><dt class="c-address-details__criteria-name">' .
                                                        esc_html(
                                                            $pAddressList->getFieldLabel(
                                                                $field,
                                                            ),
                                                        ) .
                                                        '</dt>' .
                                                        "\n" .
                                                        '<dd class="c-address-details__criteria-value">' .
                                                        $value .
                                                        '</dd></dl>' .
                                                        "\n";
                                                }
                                            }
                                        } ?>
                                    </div>
                                    <?php if (
                                        $fields_counter > $fields_more
                                    ) { ?>
                                        <div>
                                            <button
                                                class="c-address-details__more c-button --show-more"
                                                data-open-text="<?php esc_html_e(
                                                    'Mehr anzeigen',
                                                    'oo_theme',
                                                ); ?>"
                                                data-close-text="<?php esc_html_e(
                                                    'Weniger anzeigen',
                                                    'oo_theme',
                                                ); ?>"
                                            >
                                                <?php esc_html_e(
                                                    'Mehr anzeigen',
                                                    'oo_theme',
                                                ); ?>
                                            </button>
                                        </div>
                                    <?php } ?>
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
    <section class="o-section --bg-transparent --bg-transparent-mixed c-address-details__estates-section">
        <div class="o-container c-address-details__estates">
            <?php if (!empty($shortCodeActiveEstates)) { ?>
                <div class="o-row c-address-details__active-estates">
                    <div class="o-col o-col-12">
                        <h2><?php esc_html_e(
                            'Mein Portfolio',
                            'oo_theme',
                        ); ?></h2>
                        <div>
                            <?php echo do_shortcode($shortCodeActiveEstates); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($shortCodeReferenceEstates)) { ?>
                <div class="o-row c-address-details__reference-estates">
                    <div class="o-col o-col-12">
                        <h2><?php esc_html_e(
                            'Meine Referenzen',
                            'oo_theme',
                        ); ?></h2>
                        <div>
                            <?php echo do_shortcode(
                                $shortCodeReferenceEstates,
                            ); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php endif; ?>

<?php foreach ($sonstiges as $key => $item) {
    $label = $item['label'];
    $value = $item['value'];
    if (!empty($value)) { ?>
            <section class="o-section --bg-transparent c-text" id="<?php echo clean_id(
                $label,
            ); ?>">
                <div class="c-text__container o-container">
                    <div class="c-text__row o-row --position-center">
                        <h2 class="c-text__headline o-col-12 o-col-lg-8">
                          <?php echo nl2br($label); ?>
                        </h2>
                    </div>
                    <div class="c-text__row o-row --position-center">
                        <div class="c-text__content o-col-12 o-col-lg-8 --is-wysiwyg">
                            <p>
                                <?php echo nl2br(
                                    htmlspecialchars(
                                        $value,
                                        ENT_QUOTES,
                                        'UTF-8',
                                    ),
                                ); ?>
                            </p>
                        </div>
                    </div>
                </section>
                <?php }
} ?>

<?php if (!empty($shortCodeForm)) { ?>
    <section class="o-section --bg-transparent --bg-transparent-mixed" id="contact_form">
      <div class="o-container">
        <div class="o-row">
          <div class="c-address-details o-col-12 o-col-lg-8">
            <h2><?php esc_html_e(
                'Nutzen Sie unser Kontaktformular',
                'oo_theme',
            ); ?></h2>
            <div class="detail-contact-form">
              <?php echo do_shortcode($shortCodeForm); ?>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php } ?>
