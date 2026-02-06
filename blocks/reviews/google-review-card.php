<?php

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$third_parties = get_field('third_parties', 'option') ?? null;
$color_stars = get_field('color_stars') ?? 'gold';

// Google APIs
$google_api_key = get_option('onoffice-settings-googlemaps-key') ?? null;
$place_id = $third_parties['google']['place_id'] ?? null;
$place_id_override = get_field('place_id') ?? null;
$count_stars = is_numeric($val = get_field('count_stars')) ? (int) $val : 5;

if (!empty($place_id_override)) {
    $place_id = $place_id_override;
}

if (
    function_exists('oo_get_google_place') &&
    !empty($google_api_key) &&
    !empty($place_id)
) {
    $reviews = oo_get_google_place($place_id, $google_api_key, $count_stars);

    if (is_array($reviews)) { ?>
        <?php foreach ($reviews as $review) {

            $uniqid = 'google-review-' . uniqid();

            $photo = $review['authorAttribution']['photoUri'] ?? null;
            $author = $review['authorAttribution']['displayName'] ?? null;
            $text = $review['originalText']['text'] ?? null;
            $text_word_count = str_word_count(trim(strip_tags($text))) ?? 0;

            // Review Item
            $rating = $review['rating'] ?? null;
            $publish_time = $review['publishTime'];
            $date_time = new DateTime($publish_time);
            $date = $date_time->format('d.m.Y');
            ?>
            <article class="c-google-review-card --bg-transparent <?php if (
                $is_slider
            ) {
                echo '--on-slider c-slider__slide splide__slide';
            } ?> <?php echo '--is-' . $type . '-reviews'; ?>">
                <?php if (!empty($author) && !empty($date)) { ?>
                    <div class="c-google-review-card__author">
                    <div class="c-google-review-card__image">
                                <?php if (
                                    !empty($photo)
                                ) { ?> <img loading="lazy" referrerpolicy="no-referrer" src="<?php echo htmlspecialchars(
     $photo,
 ); ?>" alt="<?php echo htmlspecialchars(
    $author,
); ?>" width="64" height="64"/> <?php } ?>

                

</div>
                            <div><span class="c-google-review-card__name o-headline --h5"><?php echo htmlspecialchars(
                                $author,
                            ); ?></span>
                                        <p class="c-google-review-card__date">
                                            <?php echo htmlspecialchars(
                                                $date,
                                            ); ?>
                                        </p>
                
                                    </div>
                                        
                    </div>
                <?php } ?>
                <?php if ($rating) { ?>
                    <?php
                    $rating = round($rating * 2) / 2;
                    $stars_total = 5;
                    ?>
                                 <div class="c-google-review-card__stars c-stars --star-color-<?php echo $color_stars; ?>" role="img" aria-label="<?php echo sprintf(
    esc_attr__('Bewertung: %1$s von %2$s Sternen', 'oo_theme'),
    $rating,
    $stars_total,
); ?>">
                        <?php
                        for ($i = 0; $i < floor($rating); $i++) {
                            $stars_total--;
                            echo '<span class="c-stars__star --filled">';
                            oo_get_icon('star');
                            echo '</span>';
                        }

                        if ($rating - floor($rating) === 0.5) {
                            $stars_total--;
                            echo '<span class="c-stars__star --half">';
                            echo '<span class="c-stars__star --filled">';
                            oo_get_icon('star');
                            echo '</span>';
                            echo '<span class="c-stars__star --empty">';
                            oo_get_icon('star');
                            echo '</span>';
                            echo '</span>';
                        }

                        for ($i = 0; $i < $stars_total; $i++) {
                            echo '<span class="c-stars__star --empty">';
                            oo_get_icon('star');
                            echo '</span>';
                        }
                        ?>
                    </div>
                <?php } ?> 
                <?php if (!empty($text)) { ?>
                    <div class="c-google-review-card__contents">
                        <div class="c-google-review-card__text o-text" id="<?php echo $uniqid; ?>">
                            <p>
                                <?php echo htmlspecialchars($text); ?>
                            </p>
                        </div>
                        <button class="c-google-review-card__more c-read-more --open-popup"
                                data-review-show-more
                                data-popup="<?php echo $uniqid; ?>-dialog"
                                aria-haspopup="dialog"
                                data-open-text="<?php esc_html_e(
                                    'Mehr anzeigen',
                                    'oo_theme',
                                ); ?>"
                                data-close-text="<?php esc_html_e(
                                    'Weniger anzeigen',
                                    'oo_theme',
                                ); ?>"
                                aria-expanded="false"
                                aria-controls="<?php echo $uniqid; ?>">
                            <?php esc_html_e('Mehr anzeigen', 'oo_theme'); ?>
                        </button>
                    </div>
                <?php } ?>
            </article>
            <dialog id="<?php echo $uniqid; ?>-dialog" class="c-dialog --is-review --<?php echo $bg_color; ?>" aria-labelledby="<?php echo $uniqid; ?>-dialog">
                <div class="c-dialog__wrapper">
                    <button class="c-dialog__close c-icon-button --close-popup" aria-label="<?php esc_html_e(
                        'Fenster schließen',
                        'oo_theme',
                    ); ?>">
                        <?php oo_get_icon('close', true, [
                            'class' => 'c-icon-button__icon --close',
                        ]); ?>
                    </button>
                    <div class="c-review-google-detail">
                        <div class="c-review-google-detail__author">
                            <?php if (!empty($author) && !empty($photo)) { ?>
                                <div class="c-review-google-detail__image">
                                    <img loading="lazy" referrerpolicy="no-referrer" src="<?php echo htmlspecialchars(
                                        $photo,
                                    ); ?>" alt="<?php echo htmlspecialchars(
    $author,
); ?>" width="64" height="64"/>
                                </div>
                                <div class="c-review-google-detail__info">
                                    <span class="c-review-google-detail__name o-headline --h5"><?php echo htmlspecialchars(
                                        $author,
                                    ); ?></span>
                                    <?php if (!empty($date)) { ?>
                                        <p class="c-review-google-detail__date">
                                            <?php echo htmlspecialchars(
                                                $date,
                                            ); ?>
                                        </p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($rating) { ?>
                            <?php
                            $rating = round($rating * 2) / 2;
                            $stars_total = 5;
                            ?>
                            <div class="c-review-google-detail__stars c-stars --star-color-<?php echo $color_stars; ?>" role="img" aria-label="<?php echo sprintf(
    esc_attr__('Bewertung: %1$s von %2$s Sternen', 'oo_theme'),
    $rating,
    $stars_total,
); ?>">
                                <?php
                                for ($i = 0; $i < floor($rating); $i++) {
                                    $stars_total--;
                                    echo '<span class="c-stars__star --filled">';
                                    oo_get_icon('star');
                                    echo '</span>';
                                }

                                if ($rating - floor($rating) === 0.5) {
                                    $stars_total--;
                                    echo '<span class="c-stars__star --half">';
                                    echo '<span class="c-stars__star --filled">';
                                    oo_get_icon('star');
                                    echo '</span>';
                                    echo '<span class="c-stars__star --empty">';
                                    oo_get_icon('star');
                                    echo '</span>';
                                    echo '</span>';
                                }

                                for ($i = 0; $i < $stars_total; $i++) {
                                    echo '<span class="c-stars__star --empty">';
                                    oo_get_icon('star');
                                    echo '</span>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <div class="c-review-google-detail__content">
                            <?php if (!empty($text)) { ?>
                                <div class="c-review-google-detail__text o-text --is-wysiwyg">
                                    <?php echo htmlspecialchars($text); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </dialog>
        <?php
        }}
}
