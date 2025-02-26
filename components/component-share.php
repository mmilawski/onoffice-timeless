<?php

/** @var array $args */

$args = $args ?? [];
$popup_title = $args['popup_title'] ?? null;
$button_class = $args['button_class'] ?? null;
$button_title = $args['button_title'] ?? null;
$button_icon = $args['button_icon'] ?? null;
$popup_id = $args['popup_id'] ?? '';
$popup_id_data = !empty($args['popup_id'])
    ? 'data-popup="' . $args['popup_id'] . '"'
    : '';

// SHARE
$share_link = $args['share_link'] ?? null;
// COPY TO CLIPBOARD
$copy_text = __('Link kopieren', 'oo_theme');
$copied_text = __('Link kopiert!', 'oo_theme');

// SOCIAL MEDIA
$networks = [
    'email' => [
        'url' => 'mailto:?body=' . $share_link,
    ],
    'whatsapp' => [
        'url' => 'https://wa.me/?text=' . $share_link,
    ],
    'facebook' => [
        'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $share_link,
    ],
    'x' => [
        'url' => 'https://twitter.com/intent/tweet?text=' . $share_link,
    ],
    'linkedin' => [
        'url' =>
            'https://www.linkedin.com/shareArticle?mini=true&url=' .
            $share_link,
    ],
    'xing' => [
        'url' => 'https://www.xing.com/spi/shares/new?url=' . $share_link,
    ],
    'link' => [
        'url' => $share_link,
    ],
];
?>

<a href="#" class="<?php echo $button_class
    ? $button_class . ' --open-popup'
    : 'c-button'; ?> <?php echo $button_icon
     ? '--has-icon'
     : ''; ?>" <?php echo $popup_id_data; ?>>
    <span class="c-button__text">
    <?php echo $button_title ?? esc_html__('Immobilie teilen', 'oo_theme'); ?>
    </span>
    <?php if ($button_icon) { ?>
        <span class="c-button__icon"><?php oo_get_icon($button_icon); ?></span>
    <?php } ?>
</a>


<div class="c-popup --is-share --bg-transparent" <?php if (!empty($popup_id)) {
    echo 'id="' . $popup_id . '"';
} ?>>
	<div class="c-popup__wrapper">
        <div class="c-popup__header">
            <p class="c-popup__title">
                <?php echo $popup_title ??
                    esc_html__('Immobilie teilen', 'oo_theme'); ?>
            </p>
            <button class="c-popup__close c-icon-button">
                <span class="c-icon-button__text u-screen-reader-only"><?php esc_html_e(
                    'Fenster schließen',
                    'oo_theme',
                ); ?></span>
                <span class="c-icon-button__icon --close"><?php oo_get_icon(
                    'close',
                ); ?></span>
            </button>
        </div>
		<div class="c-popup__content">
    <?php if (!empty($networks)): ?>
        <ul class="c-social-media --is-share">
            <?php foreach ($networks as $key => $network):

                $network_url = $network['url'] ?? null;
                if (!empty($network_url)): ?>
                    <li class="c-social-media__item --<?php echo esc_attr(
                        $key,
                    ); ?>">
                        <?php if ($key == 'link'): ?>
                            <?php oo_copy_clipboard(
                                $network_url,
                                $copy_text,
                                $copied_text,
                            ); ?>
                        <?php else: ?>
                            <a class="c-social-media__link" href="<?php echo esc_url(
                                $network_url,
                            ); ?>" rel="noopener noreferrer" target="_blank">
                                <?php oo_get_icon($key); ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endif;
                ?>
            <?php
            endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
	</div>
	<div class="c-popup__overlay"></div>
</div>
