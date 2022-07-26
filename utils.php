<?php
function default_val($value, $default)
{
    if ($value == NULL) {
        return $default;
    } else {
        return $value;
    }
}

function search_term_to_like($term)
{
    return "page_text LIKE '%$term%'";
}

function persist_value_default($value, $default)
{
    persist_value(default_val($value, $default));
}

function persist_value($value)
{
    if ($value != NULL) {
        echo "value=\"$value\"";
    }
}
function persist_checked($value)
{
    if ($value) {
        echo "checked";
    }
}


//DOESN'T WORK
// function persist_checked_default($value, $default)
// {
//     persist_checked(default_val($value, $default));
// }
