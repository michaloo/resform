<?php

namespace Resform\Model;


class Transport extends \Resform\Lib\Model {

    var $input_filters = array(
        'transport_id' => 'integer',
        'event_id'     => 'integer',

        'name'        => 'stringtrim',
        'price'       => 'double',
    );

    var $validators = array(
        'event_id' => array("required"),
        'name'     => array("required"),
        'price'    => array("required")
    );

    function find($id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_transports WHERE transport_id = $id LIMIT 1";


        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop($results);
    }

    function get($event_id) {
        $query = "SELECT * FROM {$this->db->prefix}resform_transports WHERE event_id = $event_id LIMIT 20";

        $results = $this->db->get_results($query, ARRAY_A);
        return $results;
    }

    function save($data) {
        $query = "INSERT INTO {$this->db->prefix}resform_transports ({$this->getKeys($this->validators)})
        VALUES ({$this->getValues($this->validators, $data)})";

        return $this->db->query($query);
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_transports
        SET {$this->getPairs($this->validators, $data)} WHERE transport_id = {$data['transport_id']} LIMIT 1";

        return $this->db->query($query);
    }

    function delete($data) {
        $query = "DELETE FROM {$this->db->prefix}transports WHERE transport_id = {$data['transport_id']} LIMIT 1";

        return $this->db->query($query);
    }

}
