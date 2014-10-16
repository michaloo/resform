<?php

namespace Resform\Lib;

class Boolify extends \Sirius\Filtration\Filter\AbstractFilter
{
    const OPTION_EMPTY_STRING = 'empty_string';
    const OPTION_ZERO = 'zero';

    protected $options = array(
        self::OPTION_EMPTY_STRING => true,
        self::OPTION_ZERO => true
    );

    function filterSingle($value, $valueIdentifier = null) {
        return $value == true;
    }


}
