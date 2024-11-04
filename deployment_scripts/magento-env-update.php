<?php

$env_file_path = $argv[1];
$search_key    = $argv[2];
$value_update  = $argv[3];

//    $env_file_path="C:/xampp/htdocs/test/env_brands.php";
//    $search_key="cache/frontend/default/backend_options/server";
//    $value_update="testMMMMM";

try {
    $array = include $env_file_path;
} catch(Exception $e) {
    throw new Exception($e->getMessage());
}

//check search key is exists
function array_get_value(array &$array, $parents, $glue = '/')
{
    if (! is_array($parents)) {
        $parents = explode($glue, $parents);
    }

    $ref = &$array;

    foreach ((array) $parents as $parent) {
        if (is_array($ref) && array_key_exists($parent, $ref)) {
            $ref = &$ref[$parent];
        } else {
            return null;
        }
    }

    return $ref;
}

//Update value to path
function array_set_value(array &$array, $parents, $value, $glue = '/')
{
    if (! is_array($parents)) {
        $parents = explode($glue, (string) $parents);
    }

    $ref = &$array;

    foreach ($parents as $parent) {
        if (isset($ref) && ! is_array($ref)) {
            $ref = [];
        }

        $ref = &$ref[$parent];
    }

    $ref = $value;
}

try {
    $old_value = array_get_value($array, $search_key);

    if ($old_value != null) {
        array_set_value($array, $search_key, $value_update);

        $content = '<?php 
            return 
               ' . var_export($array, true) . ';';
        $content = str_replace('array (', '[', $content);
        $content = str_replace('),', '],', $content);
        $content = str_replace(');', '];', $content);
        file_put_contents($env_file_path, $content);
        echo 'success';
    } else {
        throw new Exception('Key path not found!');
    }
} catch(Exception $e) {
    throw new Exception($e->getMessage());
}
