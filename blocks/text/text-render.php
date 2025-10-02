<?php
// Content
$headline = get_field('headline') ?? [];
$texts = get_field('texts') ?? [];

// Settings
$settings = get_field('settings') ?? [];
$align_text = $settings['align_text'] ?? 'left';
$bg_color = $settings['bg_color'] ?? 'bg-transparent';
$headline_class = 'c-text__headline o-col-12 o-col-xl-10';

if (!empty($texts[0]['text']['wysiwyg'])) {
    $text_count = is_array($texts) ? count($texts) : 1;
    $text_class =
        'c-text__content o-col-12 o-col-xl-' .
        ($text_count === 1 ? '8' : '4') .
        ($text_count === 1 && $align_text !== 'center' ? ' u-offset-md-1' : '');
    $headline_class =
        'c-text__headline o-col-12 o-col-xl-' .
        ($text_count === 3 ? '12' : '8') .
        (($text_count === 1 || $text_count === 2) && $align_text !== 'center'
            ? ' u-offset-md-1'
            : '');
}
?>

<section <?php oo_block_id(
    $block,
); ?> class="c-text --text-align-<?php echo $align_text; ?> o-section --<?php echo $bg_color; ?>" >
    <div class="c-text__container o-container">

        <?php if (!empty($headline['text'])) { ?>
            <div class="c-text__row o-row">
								<?php oo_get_template('components', '', 'component-headline', [
            'headline' => $headline,
            'additional_headline_class' => $headline_class,
        ]); ?> 
            </div>
        <?php } ?>

        <?php if (
            !empty($texts[0]['text']['wysiwyg']) ||
            !empty($texts[0]['buttons']['buttons'][0]['link'])
        ) { ?>
            <div class="c-text__columns o-row">
                <?php if (is_array($texts)) {

                    $i = 0;
                    foreach ($texts as $text_column):

                        $text = $text_column['text'];
                        $buttons = $text_column['buttons'];
                        $offset_class =
                            $i === 0 &&
                            $text_count !== 3 &&
                            $align_text !== 'center'
                                ? ' u-offset-md-1'
                                : '';
                        ?>
                        <div class="<?php echo $text_class . $offset_class; ?>">
                            <?php if (!empty($text['wysiwyg'])) { ?>
                                <div class="c-text__text o-text --is-wysiwyg">
                                    <?php echo $text['wysiwyg']; ?>
                                </div>
                            <?php } ?>

                            <?php if (!empty($buttons['buttons'][0]['link'])) {
                                oo_get_template(
                                    'components',
                                    '',
                                    'component-buttons',
                                    [
                                        'buttons' => $buttons['buttons'],
                                        'additional_button_class' => $bg_color
                                            ? '--on-' . $bg_color
                                            : '',
                                        'additional_container_class' =>
                                            'c-text__buttons',
                                    ],
                                );
                            } ?>
                        </div>
                    <?php $i++;
                    endforeach;
                    ?>
                <?php
                } ?>
            </div>
        <?php } ?>
    </div>
</section>