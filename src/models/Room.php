<?php

namespace Resform\Model;


class Room extends \Resform\Lib\Model {

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

}
