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
