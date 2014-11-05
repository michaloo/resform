<?php

namespace Resform\Lib;


class Filters {

    static function rot13Filter($string) {


        $age = \DateTime::createFromFormat('Y-m-d', $string);
        $date = new \DateTime();

        $diff = $date->diff($age);

        return $diff->y;
    }
}
