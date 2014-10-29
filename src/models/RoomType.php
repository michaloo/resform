<?php

namespace Resform\Model;


class RoomType extends \Resform\Lib\Model {

    var $input_filters = array(
        'room_type_id' => 'integer',
        'event_id'     => 'integer',

        'name'        => 'stringtrim',
        'space_count' => 'integer',
        'room_count'  => 'integer',
        'price'       => 'double',
        'bathroom'    => 'boolify',
    );

    var $validators = array(
        'name'        => array("required"),
        'space_count' => array("required"),
        'room_count'  => array("required"),
        'price'       => array("required"),
        'bathroom'    => array("required"),
        'event_id'    => array("required")
    );

    function getFree($event_id) {
        $query = "SELECT
                room_type_name AS name,
                room_type_id AS room_type_id,
                r.*
            FROM {$this->db->prefix}resform_room_types_space_count AS r
            WHERE event_id = $event_id LIMIT 20";
        var_dump($query);
        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function get($event_id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_room_types WHERE event_id = $event_id LIMIT 20";
        var_dump($query);
        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function find($id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_room_types WHERE room_type_id = $id LIMIT 1";
        var_dump($query);

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }

    function save($data) {
        $query = "INSERT INTO
            {$this->db->prefix}resform_room_types ({$this->getKeys($this->validators)})
            VALUES ({$this->getValues($this->validators, $data)})";
        var_dump($query);
        return $this->db->query($query);
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_room_types SET {$this->getPairs($this->validators, $data)} WHERE room_type_id = {$data['room_type_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function delete($id) {
        $query = "DELETE FROM {$this->db->prefix}resform_room_types WHERE room_type_id = {$id} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
