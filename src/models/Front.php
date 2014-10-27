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
        if (!is_array($first_names)) {
            $first_names = array();
        }
        return array_map(array($this, "filter_first_name"), $first_names);
    }

    function filter_family_last_name($last_names) {
        if (!is_array($last_names)) {
            $last_names = array();
        }
        return array_map(array($this, "filter_last_name"), $last_names);
    }

    function filter_family_birth_date($birth_dates) {
        if (!is_array($birth_dates)) {
            $birth_dates = array();
        }
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


    function filter_room_type_id($room_type_id) {
        return (int) $room_type_id;
    }

    function validate_room_type_id($room_type_id) {
        if (! $room_type_id) {
            return "Akceptacja jest wymagana";
        }

        return false;
    }

    function filter_transport_id($transport_id) {
        return (int) $transport_id;
    }

    function validate_transport_id($transport_id) {
        if (! $transport_id) {
            return "Akceptacja jest wymagana";
        }

        return false;
    }

    function filter_comments($comments) {
        return $comments;
    }

    function validate_comments($comments) {

        return false;
    }


    function filter_accept_regulation($accept_regulation) {
        return (bool) $accept_regulation;
    }

    function validate_accept_regulation($accept_regulation) {
        if (! $accept_regulation) {
            return "Akceptacja jest wymagana";
        }

        return false;
    }

    function filter_accept_information($accept_information) {
        return (bool) $accept_information;
    }

    function validate_accept_information($accept_information) {
        if (! $accept_information) {
            return "Akceptacja jest wymagana";
        }

        return false;
    }
}
