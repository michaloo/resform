<?php

namespace Resform\Model;


class Person extends \Resform\Lib\Model {

    var $input_filters = array(
        'person_id'    => 'integer',
        'event_id'    => 'integer',
        'room_type_id' => 'integer | nullify',
        'transport_id' => 'integer | nullify',

        'sex'        => 'stringtrim',
        'first_name' => 'stringtrim',
        'last_name'  => 'stringtrim',
        'birth_date' => 'stringtrim | datify',
        'email'      => 'stringtrim',
        'phone'      => 'stringtrim',
        'city'       => 'stringtrim',

        'is_disabled'     => 'boolify',
        'disability_type' => 'stringtrim | nullify',
        'has_stairs_accessibility' => 'stringtrim | nullify',
        'guardian_person_name' => 'stringtrim | nullify',

        'is_disabled_guardian'     => 'boolify',
        'disabled_person_name' => 'stringtrim | nullify',

        'is_underaged_guardian' => 'boolify',

        'family_first_name[*]'    => 'cleanarray',
        'family_last_name[*]'     => 'cleanarray',
        'family_birth_date[*]'    => 'cleanarray | datify',

        'accept_regulation'  => 'boolify',
        'accept_information' => 'boolify',

        'comments' => 'stringtrim',

        'notes_1' => 'stringtrim',
        'notes_2' => 'stringtrim',
        'notes_3' => 'stringtrim',

        'color_1' => 'stringtrim',
        'color_2' => 'stringtrim',
        'color_3' => 'stringtrim'


    );

    var $validators = array(
        'sex'        => array('required | inlist({"list":["male","female"]})'),
        'first_name' => array("required"),
        'last_name'  => array("required"),
        'birth_date' => array('required | Datetime({"format":"d-m-Y"})'),
        'email'      => array("required | Email"),
        'phone'      => array("required"),
        'city'       => array("required"),

        'is_disabled'     => array("required"),
        'disability_type' => array('requiredWhen({"item":"is_disabled","rule":"Equal","rule_options":{"value":true}})'),
        'has_stairs_accessibility' => array('requiredWhen({"item":"is_disabled","rule":"Equal","rule_options":{"value":true}})'),
        'guardian_person_name' => array('requiredWhen({"item":"is_disabled","rule":"Equal","rule_options":{"value":true}})'),

        'is_disabled_guardian' => array("required"),
        'disabled_person_name' => array('requiredWhen({"item":"is_disabled_guardian","rule":"Equal","rule_options":{"value":true}})'),

        'is_underaged_guardian' => array("required"),

        'family_first_name[*]' => array("required"),
        'family_last_name[*]'  => array("required"),
        'family_birth_date[*]' => array('required | Datetime({"format":"d-m-Y"})'),

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
        'sex'        => '',
        'first_name' => '',
        'last_name'  => '',
        'birth_date' => '',
        'email'      => '',
        'phone'      => '',
        'city'       => '',

        'is_disabled'     => '',
        'disability_type' => '',
        'has_stairs_accessibility' => '',
        'guardian_person_name' => '',

        'is_disabled_guardian' => '',
        'disabled_person_name' => '',

        'is_underaged_guardian' => '',


        'comments' => '',
        'transport_id' => '',

        'notes_1'     => '',
        'notes_2'     => '',
        'notes_3'     => '',

        'color_1'      => '',
        'color_2'      => '',
        'color_3'      => '',
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

            'is_disabled',
            'disability_type',
            'has_stairs_accessibility',
            'guardian_person_name',
            'is_disabled_guardian',
            'disabled_person_name',

            'is_underaged_guardian',

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

            //var_dump('<pre>', $errors, '</pre>');

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

    function age($date_string) {

        $age = \DateTime::createFromFormat('d-m-Y', $date_string);
        $date = new \DateTime();

        $diff = $date->diff($age);

        return $diff->y;
    }

    function countFamily($values) {

        if (!is_array($values['family_birth_date'])) {
            return 0;
        }

        $older_members = array_filter($values['family_birth_date'], function($date) {
            return $this->age($date) > 3;
        });

        $family_members_count = count($older_members);

        // max(
        //     count($values['family_first_name']),
        //     count($values['family_last_name']),
        //     count($values['family_birth_date'])
        // );
        return $family_members_count;
    }

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
            $values['is_family_guardian'] = true;
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
        //var_dump($query);
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
                $family_member_values['is_family_guardian']
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

        if (count(array_filter($errors)) === 0) {
            $this->db->query("COMMIT");
            return array(
                'person_id' => $family_person_id,
                'errors' => array()
            );
        } else {
            $this->db->query("ROLLBACK");
            return array(
                'person_id' => $family_person_id,
                'errors'    => $errors
            );
        }
    }

    function export($event_id) {

        $query   = <<<SQL
        SELECT p.* FROM {$this->db->prefix}resform_persons_with_total_price AS p
        WHERE p.event_id = $event_id
SQL;

        //$results = $this->db->get_results($query, ARRAY_A);

        $result = $this->db->dbh->query($query);

        if (!$result) {
            return false;
        }

        $headers = array_map(function ($h) {
            return $h->name;
        },  $result->fetch_fields());

        $fp = fopen('php://output', 'w');

        if ($fp && $result) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($fp, $headers);

            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                fputcsv($fp, array_values($row));
            }
        }
    }

    function get_family($family_person_id) {

        $query   = <<<SQL
        SELECT p.* FROM {$this->db->prefix}resform_persons AS p
        WHERE p.family_person_id = {$family_person_id}
SQL;

        $results = $this->db->get_results($query, ARRAY_A);

        return $results;
    }

    function get($pager, $event_id) {

        $offset = $pager['limit'] * max((int) $pager['current'] - 1, 0);
        $limit  = $pager['limit'];
        $sort   = $pager['sort'];
        $orderby = $pager['orderby'];

        $query   = <<<SQL
            SELECT SQL_CALC_FOUND_ROWS
                rp.*,
                rrt.name AS room_type_name,
                rrt.room_type_id,
                rt.name AS transport_name,
                (
                    SELECT CONCAT(p.first_name, " ", p.last_name) FROM {$this->db->prefix}resform_persons AS p
                    WHERE p.person_id = rp.family_person_id
                    GROUP BY p.person_id
                ) AS family_guardian_name,
                (
                    SELECT GROUP_CONCAT(CONCAT(p2.first_name, " ", p2.last_name) SEPARATOR ", ") FROM {$this->db->prefix}resform_persons AS p2
                    WHERE p2.family_person_id = rp.person_id
                    GROUP BY p2.family_person_id
                ) AS family_members_name
            FROM {$this->db->prefix}resform_persons_with_price AS rp
            LEFT JOIN {$this->db->prefix}resform_rooms AS rr USING (room_id)
            LEFT JOIN {$this->db->prefix}resform_room_types AS rrt ON rr.room_type_id = rrt.room_type_id
            LEFT JOIN {$this->db->prefix}resform_transports AS rt USING (transport_id)
            WHERE rp.event_id = {$event_id}
            ORDER BY $orderby $sort
            LIMIT $offset, $limit
SQL;

        $results = $this->db->get_results($query, ARRAY_A);
        $total_count = $this->_getTotalCount();

        $pager = $this->_getPager($total_count, $pager);

        return array(
            'data' => $results,
            'pager' => $pager
        );
    }

    function like($search) {

        $query = "
        SELECT
        p.*
        FROM {$this->db->prefix}resform_persons AS p
        LEFT JOIN {$this->db->prefix}resform_room_types AS rt USING (room_type_id)
        WHERE
            first_name LIKE '%$search%'
            OR last_name LIKE '%$search%'
        ";

        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function find($id) {

        $query = <<<SQL
            SELECT
                p.*
            FROM {$this->db->prefix}resform_persons AS p
            LEFT JOIN {$this->db->prefix}resform_room_types AS rt USING (room_type_id)
            WHERE person_id = $id LIMIT 1
SQL;

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }

    function getPriceForId($id) {

        $query = "
        SELECT *
        FROM {$this->db->prefix}resform_persons_with_total_price
        WHERE person_id = $id LIMIT 1
        ";

        //var_dump($query);

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }

    function update($data, $editable = null) {
        if (is_null($editable)) {
            $editable = $this->editable;
        }
        $query = "UPDATE {$this->db->prefix}resform_persons SET {$this->getPairs($editable, $data)} WHERE person_id = {$data['person_id']} LIMIT 1";

        var_dump($query);
        return $this->db->query($query);
    }

    function updateRoomId($persons) {
        $this->db->query("START TRANSACTION");
        $errors = array();

        $updated_persons = array_filter($persons, function($person) use ($errors) {

            $sql_values = $this->getMap(array_keys($person), $person);
            //$sql_keys = $this->getKeys($family_member_values);

            $query = <<<SQL
            UPDATE {$this->db->prefix}resform_persons
                SET room_id = NULL, room_type_id = NULL
                WHERE person_id = {$sql_values["person_id"]}
SQL;
            $count = $this->db->query($query);
            array_push($errors, $this->db->last_error);
            return true;
            //return $count > 0;
        });

        foreach ($updated_persons as $person) {

            $sql_values = $this->getMap(array_keys($person), $person);

            $query = <<<SQL
            UPDATE {$this->db->prefix}resform_persons
                SET room_id = {$sql_values["room_id"]}
                WHERE person_id = {$sql_values["person_id"]}
SQL;


            $count = $this->db->query($query);
            var_dump($query, $count);
            array_push($errors, $this->db->last_error);

            if ($person['is_family_guardian'] == 1) {

                $query = <<<SQL
                UPDATE {$this->db->prefix}resform_persons
                    SET room_id = {$sql_values["room_id"]}
                    WHERE family_person_id = {$sql_values["person_id"]};
SQL;

                $count = $this->db->query($query);
                var_dump($query, $count);
                array_push($errors, $this->db->last_error);
            }
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

    function getGroupedByRooms($persons, $rooms) {

        $grouped_persons = array();

        foreach ($rooms as $room) {
            $grouped_persons[$room["room_id"]] = $room;
        }

        foreach ($persons["data"] as $person) {
            $grouped_persons[$person["room_id"]]["persons"][] = $person;
        }
        return $grouped_persons;
    }

    function getPersonsToEdit($post_data, $persons) {
        $persons_to_edit = array();
        foreach ($post_data as $person_id => $room_id) {

            $search_key = array_search($person_id, array_map(function ($p) { return $p["person_id"]; }, $persons["data"]));
            $search_person = $persons["data"][$search_key];

            if ($search_person["room_id"] != (int) $room_id) {

                $search_person["room_id"] = (int) $room_id;
                $persons_to_edit[] = $search_person;
            }
        }
        return $persons_to_edit;
    }

    function delete($id) {
        $query = "DELETE FROM {$this->db->prefix}resform_persons WHERE person_id = {$id} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
