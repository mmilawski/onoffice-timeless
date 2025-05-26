<?php

// Settings
$settings = get_field('settings') ?? [];
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$slider = get_field('slider') ?? [];
$is_slider = filter_var($slider['slider'] ?? null, FILTER_VALIDATE_BOOLEAN);
$third_parties = get_field('third_parties', 'option') ?? null;

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
                <div class="c-google-review-card__date c-flag">
                  <?php echo htmlspecialchars($date); ?>
                </div>
                <div class="c-google-review-card__name o-headline --h3">
                  <p><?php echo htmlspecialchars($author); ?></p>
                </div>
            </div>
          <?php } ?>
          <div class="c-google-review-card__contents <?php echo $is_long_text
              ? '--shorten'
              : ''; ?>">
          <?php if (!empty($text)) { ?>
            <div class="c-google-review-card__text o-text">
                <p>
                    <?php echo htmlspecialchars($text); ?>
                </p>
            </div>
          <?php } ?>
          </div>
          
        <div class="c-google-review-card__more c-read-more">
            <span class="c-read-more__text --more"><?php esc_html_e(
                'Mehr anzeigen',
                'oo_theme',
            ); ?></span> 
            <span class="c-read-more__text --less"><?php esc_html_e(
                'Weniger anzeigen',
                'oo_theme',
            ); ?></span>
        </div>

          <?php if ($rating) { ?>
              <div class="c-google-review-card__stars c-stars">
                  <?php
                  $rating = round($rating * 2) / 2;
                  $stars_total = 5;

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
      </article>
  <?php
  }}
}
