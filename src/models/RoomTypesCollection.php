<?php

namespace Resform\Model;


class RoomTypesCollection extends \Resform\Lib\ModelsCollection {

    function get($event_id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_room_types WHERE event_id = $event_id LIMIT 20";
        var_dump($query);
        $results = $this->db->get_results($query, ARRAY_A);
        return array_map(array($this, 'map'), $results);
    }

    function find($id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_room_types WHERE room_type_id = $id LIMIT 1";
        var_dump($query);

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop(array_map(array($this, 'map'), $results));
    }

}
