<?php
Form::macro('labelCheckbox', function ($name, $value, $checked = false, $label = null, $attr = array()) {
    $label = is_null($label) ? ucwords(str_replace(array('-', '_'), ' ', $name)) : $label;
    $attr['id'] = isset($attr['checkId']) ? $attr['checkId'] : $name;
    unset($attr['checkId']);

    $attr['labelClass'] = isset($attr['labelClass']) ? implode(" ", explode(" ", $attr['labelClass'])) : "";

    $html = '<label for="' . $attr['id'] . '" class="' . $attr['labelClass'] . '">';
    unset($attr['labelClass']);

    $attr['class'] = isset($attr['checkClass']) ? implode(" ", explode(" ", $attr['checkClass'])) : null;
    unset($attr['checkClass']);

    if ($attr['class'] === null) {
        unset($attr['class']);
    }

    $html .= Form::checkbox($name, $value, $checked, $attr) . $label . '</label>';
    return $html;
});