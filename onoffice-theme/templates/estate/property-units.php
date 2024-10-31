<?php

$dont_echo = ['vermarktungsstatus'];

$pEstatesClone = clone $pEstates;
$pEstatesClone->resetEstateIterator();
?>

<?php if (
    (bool) $pEstates->estateIterator() == true &&
    !empty($pEstates->estateIterator())
) { ?>

<div class="c-property-details__units">
	<h2 class="c-property-detail__headline o-headline --h2">
		<?php esc_html_e('Einheiten', 'oo_theme'); ?>
	</h2>
	<div class="c-table --is-scrollable">
    <table class="c-property-details__units-table c-table__wrapper">
            <thead class="c-property-details__units-head c-table__head">
                <tr class="c-table__row --is-head">
                <?php
                $emptyColumns = [];
                $estates = [];
                while ($currentEstate = $pEstatesClone->estateIterator()) {
                    $estates[] = $currentEstate;

                    if (!empty($currentEstate)) {
                        foreach ($currentEstate as $field => $value) {
                            if (in_array($field, $dont_echo)) {
                                continue;
                            }

                            if (!isset($emptyColumns[$field])) {
                                $emptyColumns[$field] = true;
                            }

                            if (
                                !(
                                    (is_numeric($value) && 0 == $value) ||
                                    $value == '0000-00-00' ||
                                    $value == '0.00' ||
                                    $value == '' ||
                                    empty($value)
                                )
                            ) {
                                $emptyColumns[$field] = false;
                            }
                        }
                    }
                }

                foreach ($pEstates->estateIterator() as $field => $value) {
                    if (
                        in_array($field, $dont_echo) ||
                        (isset($emptyColumns[$field]) && $emptyColumns[$field])
                    ) {
                        continue;
                    }

                    echo '<th class="c-table__data">';
                    echo $pEstates->getFieldLabel($field);
                    echo '</th>';
                }

                echo '<th class="c-table__data">';
                echo esc_html('Details', 'oo_theme');
                echo '</th>';
                ?>
            </tr>
            </thead> 
            <tbody class="c-property-details__units-body c-table__body">
            <?php if (is_array($estates)) {
                foreach ($estates as $currentEstate):
                    echo '<tr class="c-table__row --is-body">';
                    foreach ($currentEstate as $field => $value):
                        if (
                            in_array($field, $dont_echo) ||
                            (isset($emptyColumns[$field]) &&
                                $emptyColumns[$field])
                        ) {
                            continue;
                        }

                        if (
                            (is_numeric($value) && 0 == $value) ||
                            $value == '0000-00-00' ||
                            $value == '0.00' ||
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
                        esc_html('Details', 'oo_theme') .
                        '">';
                    if (!empty($pEstatesClone->getEstateLink())) {
                        echo '<a class="c-link --has-icon --chevron-right" href="' .
                            esc_url($pEstatesClone->getEstateLink()) .
                            '">';
                    }
                    echo esc_html('Zur Einheit', 'oo_theme');
                    echo oo_get_icon('chevron-right');
                    echo '</a>';
                    echo '</td>';

                    echo '</tr>';
                endforeach;
            } ?>
            </tbody>
        </table>
	</div>
</div>
<?php } ?>
