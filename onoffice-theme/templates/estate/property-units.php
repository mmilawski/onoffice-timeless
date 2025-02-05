<?php

$dont_echo = ['vermarktungsstatus'];

$pEstatesClone = clone $pEstates;
$pEstatesClone->resetEstateIterator();
?>

<?php if (
    (bool) $pEstates->estateIterator() == true &&
    !empty($pEstates->estateIterator())
) { ?>

    <h2 class="c-property-detail__headline o-headline --h2">
        <?php esc_html_e('Einheiten', 'oo_theme'); ?>
    </h2>
    <div class="c-property-details__units-table c-table --is-scrollable">
        <table class="c-table__wrapper">
            <thead class="c-table__head">
                <tr class="c-table__row --is-head">
                    <?php
                    $empty_columns = [];
                    while (
                        $current_property = $pEstatesClone->estateIterator()
                    ) {
                        if (!empty($current_property)) {
                            foreach ($current_property as $field => $value) {
                                if (in_array($field, $dont_echo)) {
                                    continue;
                                }

                                if (!isset($empty_columns[$field])) {
                                    $empty_columns[$field] = true;
                                }

                                if (
                                    !(
                                        (is_numeric($value) && 0 == $value) ||
                                        $value == '0000-00-00' ||
                                        $value == '0.00' ||
                                        $value == 'Nein' ||
                                        $value == 'No' ||
                                        $value == 'Ne' ||
                                        $value == '' ||
                                        empty($value)
                                    )
                                ) {
                                    $empty_columns[$field] = false;
                                }
                            }
                        }
                    }

                    $pEstates->resetEstateIterator();
                    $first_property = $pEstates->estateIterator();

                    if ($first_property) {
                        foreach ($first_property as $field => $value) {
                            if (
                                in_array($field, $dont_echo) ||
                                (isset($empty_columns[$field]) &&
                                    $empty_columns[$field])
                            ) {
                                continue;
                            }

                            echo '<th class="c-table__data">';
                            echo $pEstates->getFieldLabel($field);
                            echo '</th>';
                        }
                    }

                    echo '<th class="c-table__data">';
                    echo esc_html__('Details', 'oo_theme');
                    echo '</th>';
                    ?>
                </tr>
            </thead>
            <tbody class="c-table__body">
                <?php
                $pEstates->resetEstateIterator();
                while ($current_property = $pEstates->estateIterator()) {
                    echo '<tr class="c-table__row --is-body">';
                    foreach ($current_property as $field => $value):
                        if (
                            in_array($field, $dont_echo) ||
                            (isset($empty_columns[$field]) &&
                                $empty_columns[$field])
                        ) {
                            continue;
                        }

                        if (
                            (is_numeric($value) && 0 == $value) ||
                            $value == '0000-00-00' ||
                            $value == '0.00' ||
                            $value == 'Nein' ||
                            $value == 'No' ||
                            $value == 'Ne' ||
                            $value == '' ||
                            empty($value)
                        ) {
                            $value = '-';
                            $class = '--empty';
                        } else {
                            $value = $value;
                            $class = '';
                        }

                        echo '<td class="c-table__data ' .
                            $class .
                            '" data-label="' .
                            $pEstates->getFieldLabel($field) .
                            '">';
                        echo $value;
                        echo '</td>';
                    endforeach;

                    echo '<td class="c-table__data" data-label="' .
                        esc_html__('Details', 'oo_theme') .
                        '">';
                    if (!empty($pEstates->getEstateLink())) {
                        echo '<a class="c-link --underlined --on-bg-transparent" href="' .
                            esc_url($pEstates->getEstateLink()) .
                            '">';
                    }
                    echo esc_html__('Zur Einheit', 'oo_theme');
                    echo '</a>';
                    echo '</td>';

                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php } ?>
