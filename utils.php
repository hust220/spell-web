<?php

function getVal($array, $key, $default) {
    return array_key_exists($key, $array) ? $array[$key] : $default;
}
