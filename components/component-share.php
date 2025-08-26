<?php

/** @var array $args */

$args = $args ?? [];
$popup_title = $args['popup_title'] ?? null;
$button_class = $args['button_class'] ?? null;
$button_title = $args['button_title'] ?? null;
$button_icon = $args['button_icon'] ?? null;
$popup_id = $args['popup_id'] ?? 'share';

// SHARE
$share_link = $args['share_link'] ?? null;
// COPY TO CLIPBOARD
$copy_text = __('Link kopieren', 'oo_theme');
$copied_text = __('Link kopiert!', 'oo_theme');

// SOCIAL MEDIA
$networks =
    [
        'email' => [
            'title' => __('Per E-Mail teilen', 'oo_theme'),
            'url' => 'mailto:?body=' . $share_link,
        ],
        'whatsapp' => [
            'title' => __('Auf WhatsApp teilen', 'oo_theme'),
            'url' => 'https://wa.me/?text=' . $share_link,
        ],
        'facebook' => [
            'title' => __('Auf Facebook teilen', 'oo_theme'),
            'url' =>
                'https://www.facebook.com/sharer/sharer.php?u=' . $share_link,
        ],
        'x' => [
            'title' => __('Auf X teilen', 'oo_theme'),
            'url' => 'https://twitter.com/intent/tweet?text=' . $share_link,
        ],
        'linkedin' => [
            'title' => __('Auf LinkedIn teilen', 'oo_theme'),
            'url' =>
                'https://www.linkedin.com/shareArticle?mini=true&url=' .
                $share_link,
        ],
        'xing' => [
            'title' => __('Auf Xing teilen', 'oo_theme'),
            'url' => 'https://www.xing.com/spi/shares/new?url=' . $share_link,
        ],
        'link' => [
            'title' => $copyText,
            'url' => $share_link,
        ],
    ] ?? [];
?>

<button class="<?php echo $button_class
    ? $button_class
    : 'c-button'; ?> <?php echo $button_icon
     ? '--has-icon'
     : ''; ?> --open-popup" data-popup="<?php echo $popup_id; ?>" aria-haspopup="dialog" aria-controls="<?php echo $popup_id; ?>">
    <span class="c-button__text">
        <?php echo $button_title ??
            esc_html__('Immobilie teilen', 'oo_theme'); ?>
    </span>
    <?php if ($button_icon) { ?>
        <?php oo_get_icon($button_icon, true, ['class' => 'c-button__icon']); ?>
    <?php } ?>
</button>

<dialog id="<?php echo $popup_id; ?>" class="c-dialog" aria-labelledby="<?php echo $popup_id; ?>-title" aria-describedby="<?php echo $popup_id; ?>-desc">
    <div class="c-dialog__wrapper">
        <div class="c-dialog__header">
            <p class="c-dialog__title" id="<?php echo $popup_id; ?>-title">
                <?php echo $popup_title ??
                    esc_html__('Immobilie teilen', 'oo_theme'); ?>
            </p>
            <button class="c-dialog__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
                'Fenster schließen',
                'oo_theme',
            ); ?>">
                <?php oo_get_icon('close', true, [
                    'class' => 'c-icon-button__icon --close',
                ]); ?>
            </button>
        </div>
        <div class="c-dialog__content" id="<?php echo $popup_id; ?>-desc">
            <?php if (!empty($networks)): ?>
                <ul class="c-social-media --is-share">
                    <?php foreach ($networks as $key => $network):

                        $network_url = $network['url'] ?? null;
                        $network_title = $network['title'] ?? ucfirst($key);

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
                                        <span class="c-social-media__text u-screen-reader-only"><?php echo esc_html(
                                            $network['title'],
                                        ); ?></span>
                                        <?php oo_get_icon($key, true, [
                                            'class' => 'c-social-media__icon',
                                        ]); ?>
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
</dialog>