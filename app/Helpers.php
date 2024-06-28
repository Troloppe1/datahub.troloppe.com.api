<?php

function getTestData($key = '')
{
    $data = Storage::get('public/new-street-data-form.json');
    try {
        $data = $key ? json_decode($data)->{$key} : json_decode($data);
    } catch (e) {
        $data = [];
    }
    return $data;
}
function setTestData($key, $value)
{
    $data = getTestData();
    $data->{$key} = $value;
    Storage::put('public/new-street-data-form.json', json_encode($data));
    return $data;
}
