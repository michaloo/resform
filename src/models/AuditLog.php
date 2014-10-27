<?php

namespace Resform\Model;


class AuditLog extends \Resform\Lib\Model {

    function get() {

        $query = "SELECT *
            FROM {$this->db->prefix}resform_audit_logs
            ORDER BY add_time DESC
            LIMIT 200
            ";

        $results = $this->db->get_results($query, ARRAY_A);

        return $results;
    }

}
