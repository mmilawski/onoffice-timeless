<?php

use onOffice\WPlugin\Region\Region;
use onOffice\WPlugin\Region\RegionController;
use onOffice\WPlugin\Types\FieldTypes;

if (!function_exists('printRegion')) {
    function printRegion(
        onOffice\WPlugin\Region\Region $pRegion,
        $selected = [],
        $level = 0,
    ) {
        if (isset($_GET['regionaler_zusatz'])) {
            $selectStr = in_array(
                $pRegion->getId(),
                $_GET['regionaler_zusatz'],
                false,
            );
        } elseif (is_array($selected)) {
            $selectStr = in_array($pRegion->getId(), $selected, false);
        }

        echo '<option value="' .
            esc_html($pRegion->getId()) .
            '" ' .
            ($selectStr ? ' selected' : '') .
            ' class="level-' .
            $level .
            '" data-level="level-' .
            $level .
            '">' .
            esc_html($pRegion->getName()) .
            '</option>';
        foreach ($pRegion->getChildren() as $pRegionChild) {
            printRegion($pRegionChild, $selected, $level + 1);
        }
    }
}

if (!function_exists('printCountry')) {
    function printCountry($values, $selectedValue)
    {
        foreach ($values as $key => $name) {
            $selected = null;
            if ($key == $selectedValue) {
                $selected = 'selected';
            }
            echo '<option value="' .
                esc_attr($key) .
                '" ' .
                $selected .
                '>' .
                esc_html($name) .
                '</option>';
        }
    }
}

/* fields in search forms */
if (!function_exists('renderFieldEstateSearch')) {
    function renderFieldEstateSearch(string $fieldName, array $properties)
    {
        $output = '';
        $typeCurrentInput = $properties['type'];
        $permittedValues = $properties['permittedvalues'];
        $selectedValue = $properties['value'];
        $fieldLabel = $properties['label'];
        $inputType = 'type="text" ';
        $isSelected = false;

        // field types
        $typesMultiselect = [
            FieldTypes::FIELD_TYPE_SINGLESELECT,
            FieldTypes::FIELD_TYPE_MULTISELECT,
        ];
        $typesFloat = [
            FieldTypes::FIELD_TYPE_FLOAT,
            FieldTypes::FIELD_TYPE_INTEGER,
        ];
        $typesDate = [
            FieldTypes::FIELD_TYPE_DATETIME,
            FieldTypes::FIELD_TYPE_DATE,
        ];
        $typesBoolean = [FieldTypes::FIELD_TYPE_BOOLEAN];

        // type and class
        if (in_array($typeCurrentInput, $typesFloat)) {
            $inputType = 'type="number" step="1"';
            $inputClass = 'o-input --number';
        } elseif (in_array($typeCurrentInput, $typesDate)) {
            $inputType = 'type="date"';
            $inputClass = 'o-input --date';
        } elseif ($fieldName == 'radius') {
            $inputType = 'type="number" step="1"';
            $inputClass = 'o-input --radius';
        } else {
            $inputType = 'type="text"';
            $inputClass = 'o-input';
        }

        // placeholder
        if (in_array($typeCurrentInput, $typesMultiselect)) {
            $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        } else {
            $placeholder = $fieldLabel;
        }

        if ($fieldName === 'regionaler_zusatz') {
            $output .= '<label class="o-label --is-multiple-select">';
            $output .= renderRegionalAddition(
                $fieldName,
                $selectedValue ?? [],
                true,
                false,
                $properties['label'],
                $properties['permittedvalues'] ?? null,
            );
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        } elseif (
            $fieldName === 'ort' &&
            !empty($properties['permittedvalues']) &&
            function_exists('renderCityField')
        ) {
            $output .= '<label class="o-label --is-multiple-select">';
            $output .= renderCityField($fieldName, $properties);
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        } elseif ($fieldName === 'message') {
            $output .= '<label class="o-label --is-textarea">';
            $output .=
                '<textarea class="o-textarea" name="' .
                esc_html($fieldName) .
                '" placeholder="' .
                $placeholder .
                '" rows="5">' .
                $selectedValue .
                '</textarea>';
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        } elseif (in_array($typeCurrentInput, $typesMultiselect)) {
            $htmlOptions = '';
            foreach ($permittedValues as $key => $value) {
                if (is_array($selectedValue)) {
                    $isSelected = in_array($key, $selectedValue, true);
                } else {
                    $isSelected = $selectedValue == $key;
                }
                $htmlOptions .=
                    '<option value="' .
                    esc_attr($key) .
                    '"' .
                    ($isSelected ? ' selected' : '') .
                    '>' .
                    esc_html($value) .
                    '</option>';
            }
            $output .= '<label class="o-label --is-multiple-select">';
            $output .=
                '<select class="o-select --multiple" name="' .
                esc_html($fieldName) .
                '[]" multiple="multiple" data-placeholder="' .
                $placeholder .
                '">';
            $output .= $htmlOptions;
            $output .= '</select>';
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $output .= '<label class="o-label --is-single-select">';
            $output .=
                '<select class="o-select --single" name="' .
                esc_html($fieldName) .
                '" data-placeholder="' .
                $placeholder .
                '">';
            $output .=
                '<option value="" ' .
                ($isSelected ? ' selected' : '') .
                '>' .
                $placeholder .
                '</option>';
            $output .=
                '<option value="y" ' .
                ($isSelected ? ' selected' : '') .
                '>' .
                esc_html__('Ja', 'oo_theme') .
                '</option>';
            $output .=
                '<option value="n" ' .
                ($isSelected ? ' selected' : '') .
                '>' .
                esc_html__('Nein', 'oo_theme') .
                '</option>';
            $output .= '</select>';
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        } elseif (
            in_array($typeCurrentInput, $typesFloat) ||
            in_array($typeCurrentInput, $typesDate)
        ) {
            $output .= '<label class="o-label --is-range">';
            if (
                str_contains($fieldName, 'preis') ||
                str_contains($fieldName, 'miete')
            ) {
                $output .=
                    '<input class="' .
                    $inputClass .
                    '" ' .
                    $inputType .
                    ' name="' .
                    esc_attr($fieldName) .
                    '__bis" placeholder="' .
                    $fieldLabel .
                    ' ' .
                    esc_html__('bis', 'oo_theme') .
                    '" value="' .
                    esc_attr(
                        isset($selectedValue[1]) ? $selectedValue[1] : '',
                    ) .
                    '">';
                $output .=
                    '<span class="o-label__text">' .
                    $fieldLabel .
                    ' ' .
                    esc_html__('bis', 'oo_theme') .
                    '</span>';
            } else {
                $output .=
                    '<input class="' .
                    $inputClass .
                    '" ' .
                    $inputType .
                    ' name="' .
                    esc_attr($fieldName) .
                    '__von" placeholder="' .
                    $fieldLabel .
                    ' ' .
                    esc_html__('ab', 'oo_theme') .
                    '" value="' .
                    esc_attr(
                        isset($selectedValue[0]) ? $selectedValue[0] : '',
                    ) .
                    '">';
                $output .=
                    '<span class="o-label__text">' .
                    $fieldLabel .
                    ' ' .
                    esc_html__('ab', 'oo_theme') .
                    '</span>';
            }
            $output .= '</label>';
        } else {
            $lengthAttr =
                !is_null($properties['length']) && $fieldName != 'radius'
                    ? 'maxlength="' . esc_attr($properties['length']) . '"'
                    : '';
            $output .= '<label class="o-label --is-input">';
            $output .=
                '<input class="' .
                $inputClass .
                '" ' .
                $inputType .
                ' name="' .
                esc_attr($fieldName) .
                '" value="' .
                esc_attr($selectedValue) .
                '" placeholder="' .
                $placeholder .
                '" ' .
                $lengthAttr .
                '>';
            $output .= '<span class="o-label__text">' . $fieldLabel . '</span>';
            $output .= '</label>';
        }
        echo $output;
    }
}

/* fields in forms */
if (!function_exists('renderFormField')) {
    function renderFormField(
        string $fieldName,
        onOffice\WPlugin\Form $pForm,
        bool $searchCriteriaRange = true,
    ): string {
        $output = '';
        $typeCurrentInput = $pForm->getFieldType($fieldName);

        if (method_exists($pForm, 'isHiddenField')) {
            $isHiddenField = $pForm->isHiddenField($fieldName);
        } else {
            $isHiddenField = false;
        }

        if ($isHiddenField) {
            $name = esc_html($fieldName);
            $value = $pForm->getFieldValue($fieldName, true);

            if ($typeCurrentInput === FieldTypes::FIELD_TYPE_BOOLEAN) {
                $value = empty($value) ? 'u' : ($value == true ? 'y' : 'n');
            }

            if ($typeCurrentInput === FieldTypes::FIELD_TYPE_MULTISELECT) {
                $value = is_array($value) ? implode(', ', $value) : $value;
            }

            return '<input type="hidden" name="' .
                esc_attr($name) .
                '" value="' .
                esc_attr($value) .
                '">';
        }

        $isRequired = $pForm->isRequiredField($fieldName);
        $addition = $isRequired ? '&nbsp;*' : '';
        $requiredAttribute = $isRequired ? 'required ' : '';
        $permittedValues = $pForm->getPermittedValues($fieldName, true);
        $selectedValue = $pForm->getFieldValue($fieldName, true);
        $fieldLabel = $pForm->getFieldLabel($fieldName, true);
        $isSelected = false;
        $isRangeValue =
            $pForm->isSearchcriteriaField($fieldName) && $searchCriteriaRange;

        if ($fieldName == 'range') {
            $typeCurrentInput =
                onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_INTEGER;
        }

        // field types
        $typesMultiselect = [FieldTypes::FIELD_TYPE_MULTISELECT];
        $typesSingleselect = [FieldTypes::FIELD_TYPE_SINGLESELECT];
        $typesFloat = [
            FieldTypes::FIELD_TYPE_FLOAT,
            FieldTypes::FIELD_TYPE_INTEGER,
        ];
        $typesDate = [
            FieldTypes::FIELD_TYPE_DATETIME,
            FieldTypes::FIELD_TYPE_DATE,
        ];
        $typesBoolean = [FieldTypes::FIELD_TYPE_BOOLEAN];

        // type and class
        if (in_array($typeCurrentInput, $typesFloat)) {
            $inputType = 'type="number" step="1"';
            $inputClass = 'o-input --number';
        } elseif (in_array($typeCurrentInput, $typesDate)) {
            $inputType = 'type="date"';
            $inputClass = 'o-input --date';
        } elseif ($fieldName == 'radius') {
            $inputType = 'type="number" step="1"';
            $inputClass = 'o-input --radius';
        } elseif ($fieldName == 'Email') {
            $inputType =
                'type="email" pattern="([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4})$"';
            $inputClass = 'o-input --email';
        } else {
            $inputType = 'type="text"';
            $inputClass = 'o-input';
        }

        // placeholder
        if (in_array($typeCurrentInput, $typesMultiselect)) {
            $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        } elseif (in_array($typeCurrentInput, $typesSingleselect)) {
            $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        } else {
            $placeholder = $fieldLabel;
        }

        if ($fieldName === 'regionaler_zusatz') {
            if (!is_array($selectedValue)) {
                $selectedValue = [];
            }
            $output .= '<label class="o-label --is-multiple-select">';
            $output .= renderRegionalAddition(
                $fieldName,
                $selectedValue,
                true,
                $isRequired,
                $fieldLabel,
                $permittedValues ?? null,
            );
            $output .=
                '<span class="o-label__text">' .
                $fieldLabel .
                $addition .
                '</span>';
            $output .= '</label>';
        } elseif ($fieldName === 'message') {
            $output .= '<label class="o-label --is-textarea">';
            $output .=
                '<textarea class="o-textarea" name="' .
                esc_html($fieldName) .
                '"' .
                $requiredAttribute .
                ' placeholder="' .
                $placeholder .
                '" rows="5">' .
                $selectedValue .
                '</textarea>';
            $output .=
                '<span class="o-label__text">' .
                $fieldLabel .
                $addition .
                '</span>';
            $output .= '</label>';
        } elseif (in_array($typeCurrentInput, $typesSingleselect)) {
            $output .= '<label class="o-label --is-single-select">';
            $output .=
                '<select class="o-select --single" name="' .
                esc_html($fieldName) .
                '" size="1" ' .
                $requiredAttribute .
                ' data-placeholder="' .
                $placeholder .
                '">';
            $output .= '<option value="">' . $placeholder . '</option>';
            foreach ($permittedValues as $key => $value) {
                if (is_array($selectedValue)) {
                    $isSelected = in_array($key, $selectedValue, true);
                } else {
                    $isSelected = $selectedValue == $key;
                }
                $output .=
                    '<option value="' .
                    esc_attr($key) .
                    '" ' .
                    ($isSelected ? 'selected' : '') .
                    '>' .
                    esc_html($value) .
                    '</option>';
            }
            $output .= '</select>';
            $output .=
                '<span class="o-label__text">' .
                $fieldLabel .
                $addition .
                '</span>';
            $output .= '</label>';
        } elseif (in_array($typeCurrentInput, $typesMultiselect)) {
            $htmlOptions = '';
            foreach ($permittedValues as $key => $value) {
                if (is_array($selectedValue)) {
                    $isSelected = in_array($key, $selectedValue, true);
                } else {
                    $isSelected = $selectedValue == $key;
                }
                $htmlOptions .=
                    '<option value="' .
                    esc_attr($key) .
                    '"' .
                    ($isSelected ? ' selected' : '') .
                    '>' .
                    esc_html($value) .
                    '</option>';
            }
            $output .= '<label class="o-label --is-multiple-select">';
            $output .=
                '<select class="o-select --multiple" name="' .
                esc_html($fieldName) .
                '[]" multiple="multiple" ' .
                $requiredAttribute .
                ' data-placeholder="' .
                $placeholder .
                '">';
            $output .= $htmlOptions;
            $output .= '</select>';
            $output .=
                '<span class="o-label__text">' .
                $fieldLabel .
                $addition .
                '</span>';
            $output .= '</label>';
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $output .= '<label class="o-label o-control --is-boolean">';
            $output .=
                '<input class="o-control__input" type="checkbox" id="' .
                esc_attr($fieldName) .
                '" name="' .
                esc_attr($fieldName) .
                '" value="y" ' .
                $requiredAttribute .
                ' ' .
                ($selectedValue === true ? 'checked' : '') .
                '>';
            $output .= '<span class="o-control__label">';
            $output .=
                '<span class="o-control__text">' .
                $fieldLabel .
                $addition .
                '</span>';
            $output .= '</span>';
            $output .= '</label>';
        } else {
            $value = esc_attr($pForm->getFieldValue($fieldName, true));

            if (
                $isRangeValue &&
                $pForm->inRangeSearchcriteriaInfos($fieldName) &&
                (is_array(
                    $pForm->getSearchcriteriaRangeInfosForField($fieldName),
                ) &&
                    count(
                        $pForm->getSearchcriteriaRangeInfosForField($fieldName),
                    )) > 0
            ) {
                wp_enqueue_style('oo-nouislider-style');
                wp_enqueue_script('oo-nouislider-script');
                $rangeMax = 500;
                $rangeStep = 1;
                $kind = '';
                $type = 'number';
                if (str_contains($fieldName, 'kaufpreis')) {
                    $rangeMax = 10000000;
                    $rangeStep = 100;
                    $kind = 'price';
                    $type = 'text';
                } elseif (str_contains($fieldName, 'kaltmiete')) {
                    $rangeMax = 10000;
                    $rangeStep = 50;
                    $kind = 'price';
                    $type = 'text';
                } elseif (str_contains($fieldName, 'anzahl_zimmer')) {
                    $rangeMax = 10;
                    $rangeStep = 0.5;
                    $kind = 'rooms';
                    $type = 'text';
                } elseif (str_contains($fieldName, 'wohnflaeche')) {
                    $kind = 'surface';
                } elseif (str_contains($fieldName, 'grundstuecksflaeche')) {
                    $kind = 'surface';
                }
                $rangeMin = $rangeStep;
                if (str_contains($fieldName, 'kaufpreis')) {
                    $placeholderAddition = ' (in EUR)';
                } elseif (str_contains($fieldName, 'kaltmiete')) {
                    $placeholderAddition = ' (in EUR)';
                } elseif (str_contains($fieldName, 'anzahl_zimmer')) {
                    $placeholderAddition = '';
                } elseif (str_contains($fieldName, 'wohnflaeche')) {
                    $placeholderAddition = ' (in m²)';
                } elseif (str_contains($fieldName, 'grundstuecksflaeche')) {
                    $placeholderAddition = ' (in m²)';
                } else {
                    $placeholderAddition = '';
                }
                $output .= '<label class="o-label --is-range">';
                $output .=
                    '<div class="o-fieldset --is-range --is-fieldset' .
                    ucwords($fieldName) .
                    ' ' .
                    esc_attr($kind) .
                    '">';

                $output .=
                    '<div id="o-range--' .
                    esc_attr($fieldName) .
                    '" class="o-range o-range--' .
                    esc_attr($fieldName) .
                    '"></div>' .
                    '<div data-refid="o-range--' .
                    esc_attr($fieldName) .
                    '" class="o-range__fixed-tooltips">' .
                    '<div class="o-range__fixed-tooltip-from"></div>' .
                    '<div class="o-range__fixed-tooltip-to"></div>' .
                    '</div>';
                foreach (
                    $pForm->getSearchcriteriaRangeInfosForField($fieldName)
                    as $key => $rangeDescription
                ) {
                    $value = esc_attr($pForm->getFieldValue($key, true));
                    $inputClass = 'o-input --number';
                    if (substr($key, -3) == 'von') {
                        $keyname = 'from';
                        $value = isset($_GET[$key])
                            ? str_replace(['.', ','], ['', '.'], $_GET[$key])
                            : $rangeMin;
                    } else {
                        $keyname = 'up';
                        $value = isset($_GET[$key])
                            ? str_replace(['.', ','], ['', '.'], $_GET[$key])
                            : $rangeMax;
                    }
                    $output .=
                        '<input id="' .
                        esc_attr($key) .
                        '" class="o-input o-input--' .
                        $keyname .
                        '" name="' .
                        esc_attr($key) .
                        '" type="hidden" value="' .
                        $value .
                        '" min="' .
                        $rangeMin .
                        '" max="' .
                        $rangeMax .
                        '" step="' .
                        $rangeStep .
                        '" ' .
                        $requiredAttribute .
                        '>';
                }
                $output .= '</div>';
                $output .=
                    '<span class="o-label__text">' .
                    $fieldLabel .
                    $addition .
                    '</span>';
                $output .= '</label>';
            } else {
                $output .= '<label class="o-label --is-input">';
                $output .=
                    '<input  class="' .
                    $inputClass .
                    '" id="' .
                    esc_attr($fieldName) .
                    '" ' .
                    $inputType .
                    ' name="' .
                    esc_attr($fieldName) .
                    '" value="' .
                    $value .
                    '" placeholder="' .
                    $placeholder .
                    '" ' .
                    $requiredAttribute .
                    '>';
                $output .=
                    '<span class="o-label__text">' .
                    $fieldLabel .
                    $addition .
                    '</span>';
                $output .= '</label>';
            }
        }
        return $output;
    }
}

if (!function_exists('renderRegionalAddition')) {
    function renderRegionalAddition(
        string $fieldName,
        array $selectedValue,
        bool $multiple,
        bool $isRequired,
        string $fieldLabel,
        array $permittedValues = null,
    ): string {
        $output = '';
        $name = esc_attr($fieldName) . ($multiple ? '[]' : '');
        $multipleAttr = $multiple ? 'multiple' : 'size="1"';
        $requiredAttribute = $isRequired ? 'required ' : '';
        $placeholder = esc_html__('Bitte wählen ...', 'oo_theme');
        $output .=
            '<select class="o-select --multiple --is-styled" name="' .
            $name .
            '" ' .
            $multipleAttr .
            $requiredAttribute .
            ' data-placeholder="' .
            $placeholder .
            '">';
        $pRegionController = new RegionController();

        if ($permittedValues !== null) {
            $regions = $pRegionController->getParentRegionsByChildRegionKeys(
                array_keys($permittedValues),
            );
        } else {
            $regions = $pRegionController->getRegions();
        }
        ob_start();
        foreach ($regions as $pRegion) {
            /* @var $pRegion Region */
            printRegion($pRegion, $selectedValue ?? []);
        }
        $output .= ob_get_clean();
        $output .= '</select>';
        return $output;
    }
}

if (!function_exists('renderCityField')) {
    function renderCityField(string $fieldName, array $properties): string
    {
        $permittedValues = $properties['permittedvalues'] ?? [];
        $htmlSelect =
            '<select class="o-select --multiple" name="' .
            esc_attr($fieldName) .
            '[]" multiple="multiple">';

        if (is_array($permittedValues)) {
            foreach ($permittedValues as $value) {
                $selected = null;
                if (
                    is_array($properties['value']) &&
                    in_array($value, $properties['value'])
                ) {
                    $selected = 'selected';
                }
                $htmlSelect .=
                    '<option value="' .
                    esc_attr($value) .
                    '" ' .
                    $selected .
                    '>' .
                    esc_attr($value) .
                    '</option>';
            }
        }

        $htmlSelect .= '</select>';

        return $htmlSelect;
    }
}
