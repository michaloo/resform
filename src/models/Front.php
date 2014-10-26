<?php

namespace Resform\Model;


class Front extends \Resform\Lib\Model {

    function filter_sex($sex) {
        return strtolower($sex);
    }

    function validate_sex($sex) {

        if (! $sex) {
            return "Pole jest wymagane";
        }

        if ($sex !== "male" && $sex !== "female") {
            return "Błędna wartość";
        }
        return false;
    }

    function filter_first_name($first_name) {
        return ucfirst($first_name);
    }

    function validate_first_name($first_name) {
        if (! $first_name) {
            return "Pole jest wymagane";
        }

        return false;
    }

    function filter_last_name($last_name) {
        return ucfirst($last_name);
    }

    function validate_last_name($last_name) {
        if (! $last_name) {
            return "Pole jest wymagane";
        }

        return false;
    }

    function filter_birth_date($birth_date) {

        return trim($birth_date);
    }

    function validate_birth_date($birth_date) {
        if (! $birth_date) {
            return "Pole jest wymagane";
        }

        $date = \DateTime::createFromFormat('d-m-Y', $birth_date);

        if (! $date) {
            return "Błędny format";
        }

        return false;
    }

    function filter_email($email) {
        return trim($email);
    }

    function validate_email($email) {
        if (! $email) {
            return "Pole jest wymagane";
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Błędny format";
        }

        return false;
    }

    function filter_family_first_name($first_names) {
        return array_map(array($this, "filter_first_name"), $first_names);
    }

    function filter_family_last_name($last_names) {
        return array_map(array($this, "filter_last_name"), $last_names);
    }

    function filter_family_birth_date($birth_dates) {
        return array_map(array($this, "filter_birth_date"), $birth_dates);
    }

    function validate_family_first_name($first_names) {
        return array_filter(array_map(array($this, "validate_first_name"), $first_names));
    }

    function validate_family_last_name($last_names) {
        return array_filter(array_map(array($this, "validate_last_name"), $last_names));
    }

    function validate_family_birth_date($birth_dates) {
        return array_filter(array_map(array($this, "validate_birth_date"), $birth_dates));
    }
}
