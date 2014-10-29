<?php

namespace Resform\Model;


class Person extends \Resform\Lib\Model {

    var $input_filters = array(
        'person_id'    => 'integer',
        'room_type_id' => 'integer | nullify',
        'transport_id' => 'integer | nullify',

        'sex'        => 'stringtrim',
        'first_name' => 'stringtrim',
        'last_name'  => 'stringtrim',
        'birth_date' => 'stringtrim',
        'email'      => 'stringtrim',
        'phone'      => 'stringtrim',
        'city'       => 'stringtrim',

        'disabled'             => 'boolify',
        'disabled_guardian'    => 'boolify',
        'stairs_accessibility' => 'boolify',

        'family_first_name[*]'    => 'cleanarray',
        'family_last_name[*]'     => 'cleanarray',
        'family_birth_date[*]'    => 'cleanarray',

        'accept_regulation'  => 'boolify',
        'accept_information' => 'boolify',
        'status' => 'stringtrim',
        'color' => 'stringtrim'

    );

    var $validators = array(
        'sex'        => array('required | inlist({"list":["male","female"]})'),
        'first_name' => array("required"),
        'last_name'  => array("required"),
        'birth_date' => array('required | Datetime({"format":"d-m-Y"})(Błędny format daty)()'),
        'email'      => array("required"),
        'phone'      => array("required"),
        'city'       => array("required"),

        'family_first_name[*]' => array("required"),
        'family_last_name[*]'  => array("required"),
        'family_birth_date[*]' => array('required | Datetime({"format":"d-m-Y"})(Błędny format daty)()'),

        'room_type_id' => array("required"),
        'transport_id' => array("required"),

        'accept_regulation'  => array('required | inlist({"list":[true]})'),
        'accept_information' => array('required | inlist({"list":[true]})'),
    );

    var $output_filters = array(
        'birth_date' => 'normalizedate(input_format=d-m-Y&output_format=Y-m-d)',

        'family_birth_date[*]' => 'normalizedate(input_format=d-m-Y&output_format=Y-m-d)'
    );

    var $editable = array(
        'sex'        => array('required | inlist({"list":["male","female"]})'),
        'first_name' => array("required"),
        'last_name'  => array("required"),
        'birth_date' => array('required | Datetime({"format":"d-m-Y"})(Błędny format daty)()'),
        'email'      => array("required"),
        'phone'      => array("required"),
        'city'       => array("required"),
        'status'     => array("required"),
        'color'     => array("required"),

        'room_type_id' => array("required"),
        'transport_id' => array("required"),
    );

    var $steps = array(
        array(),
        array(
            'sex',
            'first_name',
            'last_name',
            'birth_date',
            'email',
            'phone',
            'city',

            'disabled',
            'disabled_guardian',
            'stairs_accessibility',

            'family_first_name[*]',
            'family_last_name[*]',
            'family_birth_date[*]'
        ),
        array(
            'room_type_id',
            'transport_id',
            'comments'
        ),
        array(
            'accept_regulation',
            'accept_information'
        )
    );

    protected $step_filters;
    protected $step_validators;

    function __construct($db, $input_filter, $output_filter, $validator, $step_filters, $step_validators) {
        parent::__construct($db, $input_filter, $output_filter, $validator);

        $this->step_filters    = $step_filters;
        $this->step_validators = $step_validators;

        return $this;
    }

    function validate_step($step, $data) {

        if (isset($this->step_validators[$step])) {
            $validators = array_intersect_key($this->validators, array_flip($this->steps[$step-1]));
            $this->step_validators[$step]->add($validators);

            $this->step_validators[$step]->validate($data);
            $errors = $this->step_validators[$step]->getMessages();
        } else {
            $errors = $this->validate($data);
        }

        return $errors;
    }


    function input_filter_step($step, $data) {

        if (isset($this->step_filters[$step])) {

            $filters = array_intersect_key($this->input_filters, array_flip($this->steps[$step-1]));

            $this->step_filters[$step]->add($filters);
            //$data = array_intersect_key($data, $filters);
            $filtered = $this->step_filters[$step]->filter($data);
        } else {
            $filtered = $this->input_filter($data);
        }

        return $filtered;
    }

    /*function filter_sex($sex) {
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

    function filter_phone($phone) {
        return trim($phone);
    }

    function validate_phone($phone) {
        if (! $phone) {
            return "Pole jest wymagane";
        }

        return false;
    }

    function filter_city($city) {
        return trim($city);
    }

    function validate_city($city) {
        if (! $city) {
            return "Pole jest wymagane";
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

    function filter_disabled($disabled) {
        return (bool) $disabled;
    }

    function filter_disabled_guardian($disabled_guardian) {
        return (bool) $disabled_guardian;
    }

    function filter_stairs_accessibility($stairs_accessibility) {
        return (bool) $stairs_accessibility;
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
    }*/

    function register($values) {
        $family_members_count = max(
            count($values['family_first_name']),
            count($values['family_last_name']),
            count($values['family_birth_date'])
        );
        $family = array();
        for ($i = 0; $i < $family_members_count; $i++) {
            array_push($family, array_combine(
                array("first_name", "last_name", "birth_date"),
                array(
                    $values['family_first_name'][$i],
                    $values['family_last_name'][$i],
                    $values['family_birth_date'][$i]
                )
            ));
        }

        if (count($family) > 0) {
            $values['family_guardian'] = true;
        }

        unset(
            $values['step'],
            $values['family_first_name'],
            $values['family_last_name'],
            $values['family_birth_date'],
            $values['accept_regulation'],
            $values['accept_information']
        );

        // $values['birth_date'] = array(
        //     "function" => "STR_TO_DATE(?, '%d-%m-%Y')",
        //     "value"    => $values['birth_date']
        // );

        $sql_values = $this->getValues($values, $values);
        $sql_keys = $this->getKeys($values);

        $query = <<<SQL
        INSERT INTO  {$this->db->prefix}resform_persons (
            {$sql_keys}
        ) VALUES (
            {$sql_values}
        );
SQL;
        var_dump($query);
        //$this->db->show_errors();
        $this->db->query("START TRANSACTION");
        $errors = array();
        $this->db->query($query);
        array_push($errors, $this->db->last_error);
        $family_person_id = $this->db->insert_id;

        foreach ($family as $family_member) {
            $family_member_values = array_merge($values, $family_member);
            $family_member_values['family_person_id'] = $family_person_id;
            unset(
                $family_member_values['family_guardian']
            );

            // $family_member_values['birth_date'] = array(
            //     "function" => "STR_TO_DATE(?, '%d-%m-%Y')",
            //     "value"    => $family_member_values['birth_date']
            // );

            $sql_values = $this->getValues($family_member_values, $family_member_values);
            $sql_keys = $this->getKeys($family_member_values);

            $query = <<<SQL
            INSERT INTO {$this->db->prefix}resform_persons (
                {$sql_keys}
            ) VALUES (
                {$sql_values}
            );
SQL;
            $this->db->query($query);
            array_push($errors, $this->db->last_error);
        }
        var_dump($errors);
        if (count(array_filter($errors)) === 0) {
            $this->db->query("COMMIT");
            return array();
        } else {
            $this->db->query("ROLLBACK");
            return $errors;
        }
    }

    function get($limit, $page, $orderby, $sort) {
        $query   = "SELECT * FROM {$this->db->prefix}resform_persons LIMIT 20";
        $results = $this->db->get_results($query, ARRAY_A);
        $total_count = $this->_getTotalCount();
        $pager = $this->_getPager($total_count, $limit, $page, $orderby, $sort);
        return array(
            'data' => $results,
            'pager' => $pager
        );
    }

    function find($id) {

        $query = "
            SELECT
                p.*,
                rt.event_id
            FROM {$this->db->prefix}resform_persons AS p
            LEFT JOIN {$this->db->prefix}resform_room_types AS rt USING (room_type_id)
            WHERE person_id = $id LIMIT 1
        ";

        var_dump($query);

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_persons SET {$this->getPairs($this->editable, $data)} WHERE person_id = {$data['person_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
