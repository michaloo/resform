<?php

namespace Resform\Model;


class Front extends \Resform\Lib\Model {

    function validate_sex($sex) {

        if (! $sex) {
            return "Sex is required";
        }

        if ($sex !== "male" && $sex !== "fmale") {
            return "Bad sex value";
        }
        return false;
    }

    function filter_sex($sex) {
        return $sex;
    }

    function filter_first_name($first_name) {
        return $first_name;
    }

    function filter_last_name($last_name) {
        return $last_name;
    }

    function filter_birth_date($birth_date) {
        return $birth_date;
    }

    function filter_family_first_name($first_names) {
        return array_map(array($this, "filter_first_name"), array_slice($first_names, 1));
    }

    function filter_family_last_name($first_names) {
        return array_map(array($this, "filter_last_name"), array_slice($first_names, 1));
    }

    function filter_family_birth_date($first_names) {
        return array_map(array($this, "filter_birth_date"), array_slice($first_names, 1));
    }
}
