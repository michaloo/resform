<?php

namespace Resform\Model;


class TransportsCollection extends \Resform\Lib\ModelsCollection {

    function get() {

        $query = "SELECT * FROM {$this->db->prefix}resform_transports LIMIT 20";
        $results = $this->db->get_results($query, ARRAY_A);
        return array_map(array($this, 'map'), $results);
    }

    function find($id) {

        $query = "SELECT * FROM {$this->db->prefix}resform_transports WHERE transport_id = $id LIMIT 1";
        var_dump($query);

        $results = $this->db->get_results($query, ARRAY_A);
        return array_pop(array_map(array($this, 'map'), $results));
    }

}
