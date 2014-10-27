<?php

namespace Resform\Model;


class Event extends \Resform\Lib\Model {

    var $filters = array(
        'event_id'   => 'integer',

        'name'       => 'stringtrim',
        'start_time' => 'stringtrim',
        'end_time'   => 'stringtrim',
        'reservation_start_time' => 'stringtrim',
        'reservation_end_time'   => 'stringtrim',

        'front_info'     => 'stringtrim',
        'room_type_info' => 'stringtrim',
        'transport_info' => 'stringtrim',
        'regulations'    => 'stringtrim'
    );

    var $editable = array(
        'name',
        'start_time',
        'end_time',
        'reservation_start_time',
        'reservation_end_time',
        'front_info',
        'room_type_info',
        'transport_info',
        'regulations',
    );

    var $schema = 'schemas/event.json';


    function get($limit, $page, $orderby, $sort) {
        $query   = "SELECT * FROM {$this->db->prefix}resform_events LIMIT 20";
        $results = $this->db->get_results($query, ARRAY_A);
        $total_count = $this->_getTotalCount();
        $pager = $this->_getPager($total_count, $limit, $page, $orderby, $sort);
        return array(
            'data' => $results,
            'pager' => $pager
        );
    }

    function find($id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_events WHERE event_id = $id LIMIT 1";
        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }


    function save($data) {
        $query = "INSERT INTO
        {$this->db->prefix}resform_events ({$this->getKeys($this->editable)})
            VALUES ({$this->getValues($this->editable, $data)})";
        var_dump($query);
        return $this->db->query($query);
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_events SET {$this->getPairs($this->editable, $data)} WHERE event_id = {$data['event_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function delete($id) {
        $query = "DELETE FROM {$this->db->prefix}resform_events WHERE event_id = {$id} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function getActive() {
        $query = <<<SQL
        SELECT re.*,
            GROUP_CONCAT(CONCAT_WS(',', rrt.room_type_id, rrt.name)  SEPARATOR ';') AS room_types,
            GROUP_CONCAT(CONCAT_WS(',', rt.transport_id, rt.name)  SEPARATOR ';') AS transports
        FROM {$this->db->prefix}resform_events AS re
        LEFT JOIN {$this->db->prefix}resform_room_types AS rrt USING (event_id)
        LEFT JOIN {$this->db->prefix}resform_transports AS rt USING (event_id)
        LIMIT 1
SQL;
        $results = $this->db->get_results($query, ARRAY_A);

        $results = array_map(function($row) {
            $row['room_types'] = explode(';', $row['room_types']);
            $row['room_types'] = array_map(function($rt) {
                $rt = array_combine(array("id", "name"), explode(",", $rt));
                return $rt;
            }, $row['room_types']);

            $row['transports'] = explode(';', $row['transports']);
            $row['transports'] = array_map(function($rt) {
                $rt = array_combine(array("id", "name"), explode(",", $rt));
                return $rt;
            }, $row['transports']);

            return $row;
        }, $results);

        return $results;
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

        $sql_values = $this->getValues(array_keys($values), $values);
        $sql_keys = $this->getKeys(array_keys($values));

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
            $sql_values = $this->getValues(array_keys($family_member_values), $family_member_values);
            $sql_keys = $this->getKeys(array_keys($family_member_values));

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
            return array();
        } else {
            $this->db->query("ROLLBACK");
            return $errors;
        }
    }
}
