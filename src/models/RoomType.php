<?php

namespace Resform\Model;


class RoomType extends \Resform\Lib\Model {

    var $keys = array('name', 'space_count', 'room_count', 'price', 'bathroom', 'event_id');

    function save() {
        $query = "INSERT INTO {$this->db->prefix}resform_room_types ({$this->showKeys()}) VALUES ({$this->showValues()})";
        var_dump($query);
        return $this->db->query($query);
    }

    function update() {
        $query = "UPDATE {$this->db->prefix}resform_room_types SET {$this->showPairs()} WHERE room_type_id = {$this->data['room_type_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function delete() {
        $query = "DELETE FROM {$this->db->prefix}resform_room_types WHERE room_type_id = {$this->data['room_type_id']} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

}
