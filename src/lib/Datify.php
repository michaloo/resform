<?php

namespace Resform\Lib;

class Datify extends \Sirius\Filtration\Filter\AbstractFilter
{

    function pad($string, $length) {
        return str_pad($string, $length, "0", STR_PAD_LEFT);
    }

    function filterSingle($value, $valueIdentifier = null) {

        $filtered = trim($value);

        $filtered = str_replace(" ", "-", $filtered);

        if (substr_count($filtered, "-") != 2) {
            return null;
        }

        $tmp = explode("-", $filtered);

        $filtered = array(
            $this->pad($tmp[0], 2),
            $this->pad($tmp[1], 2),
            $this->pad($tmp[2], 4)
        );

        return join("-", $filtered);
    }


}
