<?php

namespace Resform\Lib;


class Filters {

    static function age($string) {


        $age = \DateTime::createFromFormat('Y-m-d', $string);
        $date = new \DateTime();

        $diff = $date->diff($age);

        return $diff->y;
    }

    static function ajaxurl() {
        return admin_url( 'admin-ajax.php' );
    }
}
