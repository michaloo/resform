<?php

namespace Resform\Model;


class Transport extends \Resform\Lib\Model {

    var $editable = array('event_id', 'name', 'price');

    var $schema = 'schemas/room_type.json';

    var $filters = array(
        'transport_id' => 'integer',
        'event_id'     => 'integer',

        'name'        => 'stringtrim',
        'price'       => 'double',
    );

    function get($event_id) {
        $query = "SELECT * FROM {$this->db->prefix}resform_transports WHERE event_id = $event_id LIMIT 20";
        var_dump($query);
        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function save($data) {
        $query = "INSERT INTO {$this->db->prefix}resform_transports ({$this->getKeys($this->editable)})
        VALUES ({$this->getValues($this->editable, $data)})";
        var_dump($query);
        return $this->db->query($query);
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_transports
        SET {$this->getPairs($this->editable, $data)} WHERE transport_id = {$data['transport_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function delete($data) {
        $query = "DELETE FROM {$this->db->prefix}transports WHERE transport_id = {$data['transport_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
