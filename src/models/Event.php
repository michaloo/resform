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
        $query   = "SELECT
                re.*,
                count(rp.person_id) AS persons_count,
                (SELECT count(room_type_id) FROM {$this->db->prefix}resform_room_types WHERE
                event_id = event_id GROUP BY event_id) AS room_types_count,
                (SELECT count(room_id) FROM {$this->db->prefix}resform_rooms WHERE
                room_type_id = room_type_id GROUP BY event_id) AS rooms_count
            FROM {$this->db->prefix}resform_events AS re
            LEFT JOIN {$this->db->prefix}resform_room_types AS rrt USING (event_id)
            LEFT JOIN {$this->db->prefix}resform_persons AS rp USING (room_type_id)
            LIMIT 20";
        $results = $this->db->get_results($query, ARRAY_A);
        var_dump($this->db->last_error);
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
            (SELECT SUM(free_space_count) FROM {$this->db->prefix}resform_rooms_space_count
                WHERE event_id = event_id GROUP BY event_id) AS free_space_count
        FROM {$this->db->prefix}resform_events AS re
        LEFT JOIN {$this->db->prefix}resform_room_types AS rrt USING (event_id)
        LEFT JOIN {$this->db->prefix}resform_transports AS rt USING (event_id)
        WHERE CURRENT_TIMESTAMP() BETWEEN reservation_start_time AND reservation_end_time
        GROUP BY event_id
        LIMIT 1
SQL;
        $results = $this->db->get_results($query, ARRAY_A);

        return $results;
    }

}
