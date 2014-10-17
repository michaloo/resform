<?php

namespace Resform\Lib;

abstract class Model {

    protected $filters = array();
    protected $schema  = array();

    protected $db;
    protected $filter;
    protected $validator;

    function __construct($db, $filter, $validator) {
        $this->db        = $db;
        $this->filter    = $filter;
        $this->validator = $validator;

        return $this;
    }


    function validate($data) {
        $schema = json_decode(file_get_contents(plugin_dir_path( __FILE__ ) . '../' . $this->schema));

        $this->validator->check((object) $data, $schema);
        return $this->validator->getErrors();
    }


    function filter($data) {
        $this->filter->add($this->filters);

        $data = array_intersect_key($data, $this->filters);
        return $this->filter->filter($data);
    }

    function getMap($keys, $data) {

        $values = array_map(function ($key) use ($data) {

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

    function getValues($keys, $data) {
        return join(', ', $this->getMap($keys, $data));
    }

    function getKeys($keys) {
        return join(', ', $keys);
    }

    function getPairs($keys, $data) {
        $pairs = $this->getMap($keys, $data);
        array_walk($pairs, create_function('&$i,$k','$i=" $k=$i";'));

        return implode(', ', $pairs);
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
