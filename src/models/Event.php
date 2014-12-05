<?php

namespace Resform\Model;


class Event extends \Resform\Lib\Model {

    var $input_filters = array(
        'event_id'   => 'integer',

        'name'       => 'stringtrim',
        'start_time' => 'stringtrim',
        'end_time'   => 'stringtrim',
        'reservation_start_time' => 'stringtrim',
        'reservation_end_time'   => 'stringtrim',

        'front_info'     => 'stringtrim',
        'room_type_info' => 'stringtrim',
        'transport_info' => 'stringtrim',
        'regulations'    => 'stringtrim',

        'success_mail_template' => 'stringtrim'
    );

    var $validators = array(

        'name'       => array('required()(Nazwa jest wymagana) | minlength(min=2)(Nazwa musi mieć conajmniej dwa znaki)(Name)'),
        'start_time' => array('required()(Data rozpoczęcia jest wymagana)'),
        'end_time'   => array('required()(Data zakończenia jest wymagana)'),
        'reservation_start_time' => array('required'),
        'reservation_end_time'   => array('required'),
        'front_info'     => array('required'),
        'room_type_info' => array('required'),
        'transport_info' => array('required'),
        'regulations'    => array('required')
    );

    var $output_filters = array(
        'start_time' => 'nullify | normalizedate(input_format=d-m-Y&output_format=Y-m-d)',
        'end_time'   => 'nullify | normalizedate(input_format=d-m-Y&output_format=Y-m-d)',
        'reservation_start_time' => 'nullify | normalizedate({"input_format":"H:i d-m-Y","output_format":"Y-m-d H:i:s"})',
        'reservation_end_time'   => 'nullify | normalizedate({"input_format":"H:i d-m-Y","output_format":"Y-m-d H:i:s"})'
    );

    function __construct($db, $input_filter, $output_filter, $validator, $view_path) {
        parent::__construct($db, $input_filter, $output_filter, $validator);

        $this->view_path = $view_path;

        return $this;
    }

    function get($limit, $page, $orderby, $sort) {
        $query = "SELECT
                re.*,
                (SELECT count(transport_id) FROM {$this->db->prefix}resform_transports WHERE
                event_id = re.event_id GROUP BY event_id) AS transports_count,
                (SELECT count(room_type_id) FROM {$this->db->prefix}resform_room_types AS rrt WHERE
                rrt.event_id = re.event_id GROUP BY event_id) AS room_types_count,
                (SELECT count(room_id) FROM {$this->db->prefix}resform_rooms AS r
                LEFT JOIN {$this->db->prefix}resform_room_types AS rt USING (room_type_id)
                WHERE rt.event_id = re.event_id GROUP BY event_id) AS rooms_count,
                (SELECT count(person_id) FROM {$this->db->prefix}resform_persons WHERE
                event_id = re.event_id GROUP BY event_id) AS persons_count
            FROM {$this->db->prefix}resform_events AS re
            GROUP BY event_id
            LIMIT 20";

        var_dump($query);
        $results = $this->db->get_results($query, ARRAY_A);

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
        $event = array_pop($results);
        $event['success_mail_template'] = $this->_loadTemplate('success_mail_template', $event['event_id']);

        return $event;
    }


    function save($data) {
        $query = "INSERT INTO
        {$this->db->prefix}resform_events ({$this->getKeys($this->validators)})
            VALUES ({$this->getValues($this->validators, $data)})";
        var_dump($query, $data);
        $result = $this->db->query($query);
        $event_id = $this->_getLastId();
        $this->_saveTemplate('success_mail_template', $data['success_mail_template'], $event_id);
        return $result;
    }

    function update($data) {
        $query = "UPDATE {$this->db->prefix}resform_events SET {$this->getPairs($this->validators, $data)} WHERE event_id = {$data['event_id']} LIMIT 1";
        var_dump($query, $data);
        $this->_saveTemplate('success_mail_template', $data['success_mail_template'], $data['event_id']);
        return $this->db->query($query);
    }

    private function _saveTemplate($name, $content, $event_id) {
        $path = $this->view_path . '/events/' . $event_id;
        @mkdir($path, 0777, true);
        file_put_contents($path . '/' . $name, $content);
    }

    private function _loadTemplate($name, $event_id) {
        $path = $this->view_path . '/events/' . $event_id;
        return file_get_contents($path . '/' . $name);
    }

    function delete($id) {
        $query = "DELETE FROM {$this->db->prefix}resform_events WHERE event_id = {$id} LIMIT 1";
        var_dump($query);
        return $this->db->query($query);
    }

    function getActive() {
        $query = <<<SQL
        SELECT re.* -- ,
            -- (SELECT SUM(free_space_count) FROM {$this->db->prefix}resform_rooms_space_count
            --    WHERE event_id = event_id GROUP BY event_id) AS free_space_count
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
