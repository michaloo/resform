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


    //
    // function fromPost($post) {
    //
    //     $values = array_map(function ($key) use ($post) {
    //
    //         if ($post[$key] === 'false') {
    //             return false;
    //         }
    //         return $post[$key];
    //     }, $this->keys);
    //
    //     $this->data = array_merge($this->data, array_combine($this->keys, $values));
    //     return $this;
    // }

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

    // public function __set($name, $value) {
    //     $this->data[$name] = $value;
    // }
    //
    // public function __get($name) {
    //     if (array_key_exists($name, $this->data)) {
    //         return $this->data[$name];
    //     }
    //     return null;
    // }
    //
    // public function __isset($name) {
    //     return isset($this->data[$name]);
    // }
    //
    // public function __unset($name) {
    //     unset($this->data[$name]);
    // }
}
