<?php

namespace Resform\Lib;

abstract class Model {

    protected $input_filters  = array();
    protected $output_filters = array();
    protected $validators     = array();

    protected $db;
    protected $input_filter;
    protected $output_filter;
    protected $validator;

    function __construct($db, $input_filter, $output_filter, $validator) {
        $this->db            = $db;
        $this->input_filter  = $input_filter;
        $this->output_filter = $output_filter;
        $this->validator     = $validator;

        if (! empty($this->input_filters)) {
            $this->input_filter->add($this->input_filters);
        }

        if (! empty($this->output_filters)) {
            $this->output_filter->add($this->output_filters);
        }

        if (! empty($this->validators)) {
            $this->validator->add($this->validators);
        }

        return $this;
    }


    function validate($data) {

        $this->validator->validate($data);
        return $this->validator->getMessages();
    }


    function input_filter($data) {

        $data = array_intersect_key($data, $this->input_filters);
        return $this->input_filter->filter($data);
    }

    function output_filter($data) {
        return $this->output_filter->filter($data);
    }

    function getMap($keys, $data) {

        $values = array_map(function ($key) use ($data) {

            // if (is_array($data[$key])) {
            //     return str_replace('?', "'" . mysqli_real_escape_string($this->db->dbh, $data[$key]["value"]) . "'", $data[$key]["function"]);
            // }

            if (is_bool($data[$key])) {
                return ($data[$key] == true) ? 'true' : 'false';
            }

            $value = mysqli_real_escape_string($this->db->dbh, $data[$key]);

            if (! $value) {
                return 'NULL';
            }

            return "'" . $value . "'";
        }, $keys);

        return array_combine($keys, $values);
    }

    function getValues($fields, $data) {
        return join(', ', $this->getMap(array_keys($fields), $data));
    }

    function getKeys($fields) {
        return join(', ', array_keys($fields));
    }

    function getPairs($fields, $data) {
        $pairs = $this->getMap(array_keys($fields), $data);
        array_walk($pairs, create_function('&$i,$k','$i=" $k=$i";'));

        return implode(', ', $pairs);
    }

    protected function _getLastId() {
        $id = $this->db->insert_id;
        var_dump($id);
        return $id;
    }

    protected function _getTotalCount() {
        $total_count = $this->db->get_results("SELECT FOUND_ROWS() AS total_count");
        return (int) array_pop($total_count)->total_count;
    }

    protected function _getPager($total_count, $limit, $page, $orderby, $sort) {

        $count = max(floor($total_count / $limit), 1);
        $next_page = ($page < $count) ? $page + 1 : null;
        $last_page = ($page < $count) ? $count : null;

        $prev_page = ($page > 1) ? $page - 1 : null;
        $first_page = ($page > 1) ? 1 : null;

        return array(
            'current' => $page,
            'count'   => $count,
            'next'    => $next_page,
            'last'    => $last_page,
            'first'   => $first_page,
            'prev'    => $prev_page,
            'orderby' => $orderby,
            'sort'    => $sort,
            'total_count' => $total_count
        );
    }
}
