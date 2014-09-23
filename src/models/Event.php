<?php

namespace Resform\Model;


class Event extends \Resform\Lib\Model {

    var $keys = array('name', 'start_time', 'end_time');

    function save() {
        $query = "INSERT INTO {$this->db->prefix}resform_events ({$this->showKeys()}) VALUES ({$this->showValues()})";
        var_dump($query);
        return $this->db->query($query);
    }

    function update() {
        $query = "UPDATE {$this->db->prefix}resform_events SET {$this->showPairs()} WHERE event_id = {$this->data['event_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function delete() {
        $query = "DELETE FROM {$this->db->prefix}resform_events WHERE event_id = {$this->data['event_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
