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

    static function translate($string) {
        return str_replace(array(
            'This field is required',
            'This input must be a date having the format d-m-Y',
            'This input must be a valid email address',
            'This input is not one of the accepted values'
        ), array(
            'To pole jest wymagane',
            'Data powinna mieć format dd-mm-rrrr',
            'Niepoprawny adres e-mail',
            'Akceptacja jest wymagana'
        ), $string);
    }
}
