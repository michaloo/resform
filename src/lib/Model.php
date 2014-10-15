<?php

namespace Resform\Lib;

abstract class Model {

    var $keys = array();
    protected $data = array();
    protected $db;

    function __construct($db, $data = array()) {
        $this->db   = $db;
        $this->data = $data;

        return $this;
    }

    function fromPost($post) {

        $values = array_map(function ($key) use ($post) {

            if ($post[$key] === 'false') {
                return false;
            }
            return $post[$key];
        }, $this->keys);

        $this->data = array_merge($this->data, array_combine($this->keys, $values));
        return $this;
    }

    function getMap() {

        $values = array_map(function ($key) {

            if (is_bool($this->data[$key])) {
                return 'FALSE';
            }

            $value = mysqli_real_escape_string($this->db->dbh, $this->data[$key]);

            if (! $value) {
                return 'NULL';
            }

            return "'" . $value . "'";
        }, $this->keys);

        return array_combine($this->keys, $values);
    }

    function showValues() {
        return join(', ', $this->getMap());
    }

    function showKeys() {
        return join(', ', $this->keys);
    }

    function showPairs() {
        $pairs = $this->getMap();
        array_walk($pairs, create_function('&$i,$k','$i=" $k=$i";'));

        return implode(', ', $pairs);
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }
}
