<?php

namespace Resform\Lib;

class Datify extends \Sirius\Filtration\Filter\AbstractFilter
{

    function filterSingle($value, $valueIdentifier = null) {

        $filtered = trim($value);

        $filtered = str_replace(" ", "-", $filtered);

        return $filtered;
    }


}
