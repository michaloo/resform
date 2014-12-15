<?php

namespace Resform\Model;


class Room extends \Resform\Lib\Model {

    var $input_filters = array(
        'room_id'    => 'integer',
        'room_manual_number' => 'stringtrim'
    );

    var $validators = array(
        'room_manual_number'       => array('required'),
    );

    var $output_filters = array(
    );

    function get($event_id) {

        $query = "SELECT *
            FROM {$this->db->prefix}resform_rooms_space_count
            WHERE event_id = $event_id
            ORDER BY room_type_id
            LIMIT 20
            ";

        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_rooms SET {$this->getPairs($this->validators, $data)} WHERE room_id = {$data['room_id']} LIMIT 1";
        var_dump($query, $data);
        return $this->db->query($query);
    }

}
