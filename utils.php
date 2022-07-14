<?php
function default_val($value, $default)
{
    if ($value == NULL) {
        return $default;
    } else {
        return $value;
    }
}
