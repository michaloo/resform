<?php

namespace Resform\Lib;

abstract class ModelsCollection {

    var $db;
    var $model_class;

    function __construct($db, $model_class) {
        $this->db          = $db;
        $this->model_class = $model_class;

        return $this;
    }

    function create($data) {
        return new $this->model_class($this->db, $data);
    }

    function map($stdObj) {
        return new $this->model_class($this->db, $stdObj);
    }

}
