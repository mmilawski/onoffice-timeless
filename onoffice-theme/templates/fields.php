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
        echo '<option value=""></option>';
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

if (!function_exists('get_unique_field_id')) {
    /**
     * Generates a unique, sequential ID for a given field name.
     *
     * @param string $fieldName The base name for the ID.
     * @param string|int|null $prefix An optional prefix (like a form number) for grouping.
     * @return string The unique ID.
     */
    function get_unique_field_id(string $fieldName, $prefix = null): string
    {
        static $counters = [];
        $fieldName = esc_attr($fieldName);
        $key = $prefix ? $prefix . '_' . $fieldName : $fieldName;

        if (!isset($counters[$key])) {
            $counters[$key] = 0;
        }

        $counters[$key]++;

        return esc_attr($key . '_' . $counters[$key]);
    }
}

/* fields in search forms */
if (!function_exists('renderFieldEstateSearch')) {
    function renderFieldEstateSearch(
        string $fieldName,
        array $properties,
        string $formId,
    ) {
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
        } elseif ($typeCurrentInput === 'hidden') {
            $inputType = 'type="hidden"';
        } else {
            $inputType = 'type="text"';
            $inputClass = 'o-input';
        }

        // placeholder
        if (str_contains($fieldName, 'miete')) {
            $placeholder = esc_html__('z.B. "950"', 'oo_theme');
        } elseif (str_contains($fieldName, 'preis')) {
            $placeholder = esc_html__('z.B. "500.000"', 'oo_theme');
        } elseif (str_contains($fieldName, 'flaeche')) {
            $placeholder = esc_html__('z.B. "80"', 'oo_theme');
        } elseif (str_contains($fieldName, 'zimmer')) {
            $placeholder = esc_html__('z.B. "4"', 'oo_theme');
        } elseif ($fieldName == 'radius') {
            $placeholder = esc_html__('z.B. "10"', 'oo_theme');
        } elseif (in_array($typeCurrentInput, $typesMultiselect)) {
            $placeholder = esc_html__('Bitte wählen', 'oo_theme');
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $placeholder = esc_html__('Bitte wählen', 'oo_theme');
        }

        if ($fieldName === 'regionaler_zusatz') {
            $output .= '<div class="o-label --is-multiple-select">';
            $output .= '<label for="' . $fieldName . '-ts-control">';
            $output .= $fieldLabel;
            $output .= '</label>';
            $output .= renderRegionalAddition(
                $fieldName,
                $selectedValue ?? [],
                true,
                false,
                $properties['label'],
                $properties['permittedvalues'] ?? null,
            );
            $output .= '</div>';
        } elseif (
            $fieldName === 'ort' &&
            !empty($properties['permittedvalues']) &&
            function_exists('renderCityField')
        ) {
            $output .= '<div class="o-label --is-multiple-select">';
            $output .= '<label for="' . $fieldName . '-ts-control">';
            $output .= $fieldLabel;
            $output .= '</label>';
            $output .= renderCityField($fieldName, $properties);
            $output .= '</div>';
        } elseif ($fieldName === 'message') {
            $output .= '<label class="o-label --is-textarea">';
            $output .= $fieldLabel;
            $output .=
                '<textarea class="o-textarea" name="' .
                esc_html($fieldName) .
                '" ' .
                renderAutocomplete($fieldName) .
                '>' .
                $selectedValue .
                '</textarea>';
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
            $output .= '<div class="o-label --is-multiple-select">';
            $output .=
                '<label for="' . $fieldName . '_' . $formId . '-ts-control">';
            $output .= $fieldLabel;
            $output .= '</label>';
            $output .=
                '<select aria-hidden="true" tabindex="-1" class="o-select --multiple" ' .
                renderAutocomplete($fieldName) .
                ' id="' .
                esc_html($fieldName) .
                '_' .
                $formId .
                '" name="' .
                esc_html($fieldName) .
                '[]" multiple="multiple">';
            $output .= $htmlOptions;
            $output .= '</select>';
            $output .= '</div>';
        } elseif (in_array($typeCurrentInput, $typesBoolean)) {
            $output .= '<div class="o-label --is-single-select">';
            $output .=
                '<label for="' . $fieldName . '_' . $formId . '-ts-control">';
            $output .= $fieldLabel;
            $output .= '</label>';
            $output .=
                '<select aria-hidden="true" tabindex="-1" class="o-select --single" ' .
                renderAutocomplete($fieldName) .
                ' id="' .
                esc_html($fieldName) .
                '_' .
                $formId .
                '" name="' .
                esc_html($fieldName) .
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
            $output .= '</div>';
        } elseif (
            in_array($typeCurrentInput, $typesFloat) ||
            in_array($typeCurrentInput, $typesDate)
        ) {
            $output .= '<label class="o-label --is-range">';
            if (
                str_contains($fieldName, 'preis') ||
                str_contains($fieldName, 'miete')
            ) {
                $output .= $fieldLabel . ' ' . esc_html__('bis', 'oo_theme');
                $output .=
                    '<input class="' .
                    $inputClass .
                    '" ' .
                    $inputType .
                    renderAutocomplete($fieldName) .
                    ' name="' .
                    esc_attr($fieldName) .
                    '__bis" value="' .
                    esc_attr(
                        isset($selectedValue[1]) ? $selectedValue[1] : '',
                    ) .
                    '">';
            } else {
                $output .= $fieldLabel . ' ' . esc_html__('ab', 'oo_theme');
                $output .=
                    '<input class="' .
                    $inputClass .
                    '" ' .
                    $inputType .
                    renderAutocomplete($fieldName) .
                    ' name="' .
                    esc_attr($fieldName) .
                    '__von" value="' .
                    esc_attr(
                        isset($selectedValue[0]) ? $selectedValue[0] : '',
                    ) .
                    '">';
            }
            $output .= '</label>';
        } elseif ($typeCurrentInput === 'hidden') {
            $output .=
                '<input class="' .
                $inputClass .
                '" ' .
                $inputType .
                renderAutocomplete($fieldName) .
                ' name="' .
                esc_attr($fieldName) .
                '" value="' .
                esc_attr($selectedValue) .
                '" >';
        } else {
            $lengthAttr =
                !is_null($properties['length']) && $fieldName != 'radius'
                    ? 'maxlength="' . esc_attr($properties['length']) . '"'
                    : '';
            $output .= '<label class="o-label --is-input">';
            $output .= $fieldLabel;
            $output .=
                '<input class="' .
                $inputClass .
                '" ' .
                $inputType .
                renderAutocomplete($fieldName) .
                ' name="' .
                esc_attr($fieldName) .
                '" value="' .
                esc_attr($selectedValue) .
                '" placeholder="' .
                ($placeholder ?? '') .
                '" ' .
                $lengthAttr .
                '>';
            $output .= '</label>';
        }
        echo $output;
    }
}

/* Autofill / Autocomplete */
if (!function_exists('renderAutocomplete')) {
    function renderAutocomplete($fieldName)
    {
        switch ($fieldName) {
            case 'Briefanrede':
            case 'Anrede':
                return 'autocomplete="honorific-prefix"';
                break;

            case 'Vorname':
            case 'Vorname2':
            case 'gwgWbVorname':
                return 'autocomplete="given-name"';
                break;

            case 'Name':
            case 'Name2':
                return 'autocomplete="family-name"';
                break;

            case 'Telefon1':
                return 'autocomplete="tel"';
                break;

            case 'Email':
                return 'autocomplete="email"';
                break;

            case 'Zusatz1':
            case 'employer':
                return 'autocomplete="organization"';
                break;

            case 'jobTitle':
            case 'jobPosition':
            case 'Position_iU':
                return 'autocomplete="organization-title"';
                break;

            case 'gwgGeburtsname':
            case 'gwgWbName':
                return 'autocomplete="name"';
                break;

            case 'gwgWbGeburtsdatum':
            case 'gwgGeburtsdatum':
                return 'autocomplete="bday"';
                break;

            case 'Land':
                return 'autocomplete="country-name"';
                break;

            case 'plz':
            case 'Plz':
            case 'gwgWbPlz':
                return 'autocomplete="postal-code"';
                break;

            case 'ort':
            case 'Ort':
            case 'gwgWbOrt':
                return 'autocomplete="address-level2"';
                break;

            case 'Strasse':
            case 'strasse':
            case 'gwgWbStrasse':
                return 'autocomplete="street-address"';
                break;

            default:
                return 'autocomplete="off"';
                break;
        }
    }
}

if (!function_exists('renderIconButtonsField')) {
    /**
     * Renders selectable icon buttons for a form field.
     * Used in lead generator forms for specific fields.
     *
     * @param string $fieldName The name of the field.
     * @param onOffice\WPlugin\Form $pForm The form object.
     * @return string The HTML for the icon buttons.
     */
    function renderIconButtonsField(
        string $fieldName,
        onOffice\WPlugin\Form $pForm,
    ): string {
        $output = '';
        $permittedValues = $pForm->getPermittedValues($fieldName, true);

        $dependencies = $pForm->getFieldDependencies($fieldName);
        $selectedValue = $pForm->getFieldValue($fieldName, true);
        $fieldLabel = $pForm->getFieldLabel($fieldName, true);
        $isRequired = $pForm->isRequiredField($fieldName);
        $addition = $isRequired ? '&nbsp;*' : '';
        $uniqueId = get_unique_field_id($fieldName, $pForm->getFormNo());
        $labelId = $uniqueId . '-label';
        $requiredAttribute = $isRequired ? 'required' : '';

        $output .= '<div class="o-label" data-icon-button-group>';
        $output .=
            '<span class="o-label__text" id="' .
            $labelId .
            '">' .
            $fieldLabel .
            $addition .
            '</span>';

        // Hidden input to store the actual value for form submission
        $output .=
            '<input class="o-control__input" tabindex="-1" aria-hidden="true" name="' .
            esc_attr($fieldName) .
            '" id="' .
            $uniqueId .
            '" value="' .
            esc_attr($selectedValue) .
            '" ' .
            $requiredAttribute .
            ' aria-invalid="false">';

        $dependenciesAttribute = !empty($dependencies)
            ? ' data-dependencies="' .
                esc_attr(wp_json_encode($dependencies)) .
                '"'
            : '';
        $output .=
            '<div class="c-selectable-cards"' . $dependenciesAttribute . '>';

        foreach ($permittedValues as $key => $value) {
            $isSelected = $selectedValue == $key;

            $output .=
                '<button type="button" tabindex="0" class="c-selectable-card' .
                ($isSelected ? ' --is-selected' : '') .
                '" data-value="' .
                esc_attr($key) .
                '" title="' .
                esc_attr($value) .
                '" aria-pressed="' .
                ($isSelected ? 'true' : 'false') .
                '"  aria-label="' .
                esc_attr(strip_tags($fieldLabel)) .
                ': ' .
                esc_attr($value) .
                '" >';

            ob_start();
            $output .=
                '<span class="c-selectable-card__icon" aria-hidden="true">';
            oo_get_leadgenerator_icon($key, true);
            $output .= ob_get_clean();
            $output .= '</span>';

            $output .=
                '<span class="c-selectable-card__label">' .
                esc_html($value) .
                '</span>';
            $output .= '</button>';
        }

        $output .= '</div>'; // .c-selectable-cards
        $output .= '</div>'; // .o-label

        // We render the standard dropdown and hide it if the buttons are shown initially.
        $output .= '<div class="view--dropdown" style="display: none;">';
        // Temporarily store and remove the field name
        ob_start();
        $dropdownHtml = renderFormField($fieldName, $pForm);
        // Remove the name attribute from the select element to prevent duplicate submission
        $dropdownHtml = preg_replace(
            '/name=["\'][^"\']+["\']/',
            'data-field-name="' . esc_attr($fieldName) . '"',
            $dropdownHtml,
        );
        $output .= $dropdownHtml;
        ob_end_clean();

        $output .= '</div>';

        return $output;
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
        $uniqueId = get_unique_field_id($fieldName, $pForm->getFormNo());
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

            return '<input type="hidden"' .
                renderAutocomplete($fieldName) .
                ' name="' .
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

        if ($fieldName === 'range_ort') {
            // BFSG Fix: duplicate label error for range_ort and ort
            $fieldLabel = esc_html__('Ort (Umkreissuche)', 'oo_theme');
        }

        if ($fieldName === 'regionaler_zusatz') {
            if (!is_array($selectedValue)) {
                $selectedValue = [];
            }
            $output .= '<div class="o-label --is-multiple-select">';
            $output .= '<label for="' . $fieldName . '-ts-control">';
            $output .= $fieldLabel . $addition;
            $output .=
                '<span class="u-screen-reader-only">' .
                $pForm->getFormId() .
                '_' .
                $pForm->getFormNo() .
                '</span>';
            $output .= '</label>';
            $output .= renderRegionalAddition(
                $fieldName,
                $selectedValue,
                true,
                $isRequired,
                $fieldLabel,
                $permittedValues ?? null,
            );
            $output .= '</div>';
        } elseif ($fieldName === 'message') {
            $output .= '<label class="o-label --is-textarea">';
            $output .= $fieldLabel . $addition;
            $output .=
                '<span class="u-screen-reader-only">' .
                $pForm->getFormId() .
                '_' .
                $pForm->getFormNo() .
                '</span>';
            $output .=
                '<textarea class="o-textarea" name="' .
                esc_html($fieldName) .
                '"' .
                renderAutocomplete($fieldName) .
                $requiredAttribute .
                '>' .
                $selectedValue .
                '</textarea><div class="c-form__error-message"></div>';
            $output .= '</label>';
        } elseif (
            \onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_SINGLESELECT ==
                $typeCurrentInput &&
            ($fieldName !== 'range_land' && $fieldName !== 'range_ort')
        ) {
            $output .= '<div class="o-label --is-single-select">';
            $output .= '<label for="' . $uniqueId . '-ts-control">';
            $output .= $fieldLabel . $addition;
            $output .=
                '<span class="u-screen-reader-only">' .
                $pForm->getFormId() .
                '_' .
                $pForm->getFormNo() .
                '</span>';
            $output .= '</label>';
            $output .=
                '<select aria-hidden="true" tabindex="-1" class="o-select --single" id="' .
                $uniqueId .
                '" name="' .
                esc_html($fieldName) .
                '" size="1" ' .
                renderAutocomplete($fieldName) .
                $requiredAttribute .
                '>';
            /* translators: %s will be replaced with the translated field name. */
            $output .=
                '<option value=""></option>';
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
            $output .= '</select><div class="c-form__error-message"></div>';
            $output .= '</div>';
        } elseif (
            \onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_MULTISELECT ===
            $typeCurrentInput
        ) {
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
            $output .= '<div class="o-label --is-multiple-select">';
            $output .= '<label for="' . $uniqueId . '-ts-control">';
            $output .= $fieldLabel . $addition;
            $output .=
                '<span class="u-screen-reader-only">' .
                $pForm->getFormId() .
                '_' .
                $pForm->getFormNo() .
                '</span>';
            $output .= '</label>';
            $output .=
                '<select aria-hidden="true" tabindex="-1" class="o-select --multiple" ' .
                renderAutocomplete($fieldName) .
                ' id="' .
                $uniqueId .
                '" name="' .
                esc_html($fieldName) .
                '[]" multiple="multiple" ' .
                $requiredAttribute .
                '>';
            $output .= $htmlOptions;
            $output .= '</select><div class="c-form__error-message"></div>';
            $output .= '</div>';
        } elseif (
            \onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_BOOLEAN ===
            $typeCurrentInput
        ) {
            $output .= '<label class="o-label o-control --is-boolean">';
            $output .=
                '<input class="o-control__input" type="checkbox" id="' .
                $uniqueId .
                '" name="' .
                esc_attr($fieldName) .
                '" value="y" ' .
                $requiredAttribute .
                renderAutocomplete($fieldName) .
                ' ' .
                ($selectedValue === true ? 'checked' : '') .
                '>';
            $output .= '<span class="o-control__label">';
            $output .=
                '<span class="o-control__text">' .
                $fieldLabel .
                $addition .
                '<span class="u-screen-reader-only">' .
                $pForm->getFormId() .
                '_' .
                $pForm->getFormNo() .
                '</span>' .
                '</span>';
            $output .= '</span><div class="c-form__error-message"></div>';
            $output .= '</label>';
        } else {
            $inputType = 'type="text"';
            $inputClass = 'o-input';
            $value = esc_attr($pForm->getFieldValue($fieldName, true));
            if (
                $typeCurrentInput ===
                    onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_FLOAT ||
                $typeCurrentInput ===
                    'urn:onoffice-de-ns:smart:2.5:dbAccess:dataType:float'
            ) {
                $inputType = 'type="number" step="1"';
                $inputClass = 'o-input --number';
            } elseif (
                $typeCurrentInput ===
                    onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_INTEGER ||
                $typeCurrentInput ===
                    'urn:onoffice-de-ns:smart:2.5:dbAccess:dataType:decimal'
            ) {
                $inputType = 'type="number" step="1"';
                $inputClass = 'o-input --number';
            } elseif ($fieldName == 'Email') {
                $inputType = 'type="email"';
                $inputClass = 'o-input --email';
            } elseif (
                $typeCurrentInput ===
                onOffice\WPlugin\Types\FieldTypes::FIELD_TYPE_DATE
            ) {
                $inputType = 'type="date"';
                $inputClass = 'o-input --date';
            }

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
                if (str_contains($fieldName, 'flaeche')) {
                    $placeholderAddition = ' (in m²)';
                } else {
                    $placeholderAddition = '';
                }
                $output .= '<div class="o-label --is-range" role="group">';
                $output .= $fieldLabel . $addition;
                $output .=
                    '<span class="u-screen-reader-only">' .
                    $pForm->getFormId() .
                    '_' .
                    $pForm->getFormNo() .
                    '</span>';
                $output .= '<div class="o-fieldset">';
                foreach (
                    $pForm->getSearchcriteriaRangeInfosForField($fieldName)
                    as $key => $rangeDescription
                ) {
                    $value = esc_attr($pForm->getFieldValue($key, true));
                    $inputClass = 'o-input --number';

                    // Create unique aria-label based on field name and range type (von/bis)
                    $uniqueLabel = '';
                    if (substr($key, -3) == 'von') {
                        $uniqueLabel =
                            esc_attr(strip_tags($fieldLabel)) .
                            ' ' .
                            esc_html__('Minimalwert', 'oo_theme');
                    } else {
                        $uniqueLabel =
                            esc_attr(strip_tags($fieldLabel)) .
                            ' ' .
                            esc_html__('Maximalwert', 'oo_theme');
                    }

                    $output .=
                        '<input  class="' .
                        $inputClass .
                        '" ' .
                        $inputType .
                        ' name="' .
                        esc_attr($key) .
                        '" value="' .
                        $value .
                        '" placeholder="' .
                        esc_attr($rangeDescription) .
                        $placeholderAddition .
                        '" ' .
                        $requiredAttribute .
                        renderAutocomplete($fieldName) .
                        ' aria-label="' .
                        $uniqueLabel .
                        '"><div class="c-form__error-message"></div>';
                }
                $output .= '</div>';
                $output .= '</div>';
            } else {
                $output .= '<label class="o-label --is-input">';
                $output .= $fieldLabel . $addition;
                $output .=
                    '<span class="u-screen-reader-only">' .
                    $pForm->getFormId() .
                    '_' .
                    $pForm->getFormNo() .
                    '</span>';
                $output .=
                    '<input  class="' .
                    $inputClass .
                    '" id="' .
                    $uniqueId .
                    '" ' .
                    $inputType .
                    ' name="' .
                    esc_attr($fieldName) .
                    '" value="' .
                    $value .
                    '" ' .
                    $requiredAttribute .
                    renderAutocomplete($fieldName) .
                    '><div class="c-form__error-message"></div>';
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
        $multipleAttr = $multiple ? 'multiple ' : 'size="1" ';
        $requiredAttribute = $isRequired ? 'required ' : '';
        $output .=
            '<select aria-hidden="true" tabindex="-1" class="o-select --multiple --is-styled" ' .
            renderAutocomplete($fieldName) .
            ' id="' .
            esc_html($fieldName) .
            '" name="' .
            $name .
            '" ' .
            $multipleAttr .
            $requiredAttribute .
            '>';
        $pRegionController = new RegionController();

        if ($permittedValues !== null) {
            $regions = $pRegionController->getParentRegionsByChildRegionKeys(
                array_keys($permittedValues),
            );
        } else {
            $regions = $pRegionController->getRegions();
        }
        ob_start();
        echo '<option value=""></option>';
        foreach ($regions as $pRegion) {
            /* @var $pRegion Region */
            printRegion($pRegion, $selectedValue ?? []);
        }
        $output .= ob_get_clean();
        $output .= '</select><div class="c-form__error-message"></div>';
        return $output;
    }
}

if (!function_exists('renderCityField')) {
    function renderCityField(
        string $fieldName,
        array $properties,
        string $requiredAttribute = '',
    ): string {
        $permittedValues = $properties['permittedvalues'] ?? [];
        $htmlSelect =
            '<select aria-hidden="true" tabindex="-1" class="o-select --multiple" ' .
            renderAutocomplete($fieldName) .
            ' id="' .
            esc_html($fieldName) .
            '" name="' .
            esc_attr($fieldName) .
            '[]" multiple="multiple" ' .
            $requiredAttribute .
            '>';

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
        $htmlSelect .= '</select><div class="c-form__error-message"></div>';

        return $htmlSelect;
    }
}
