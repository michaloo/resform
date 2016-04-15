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

    static function format_price($value) {
        return str_replace(".", ",", money_format('%.2n', $value));
    }

    static function translate($string) {
        return str_replace(array(
            'This field is required',
            'This input must be a date having the format Y-m-d',
            'This input must be a valid email address',
            'This input is not one of the accepted values',
            'female',
            'male',
            'no',
            'alone',
            'with_guardian'
        ), array(
            'To pole jest wymagane',
            'Data powinna mieć format rrrr-mm-dd',
            'Niepoprawny adres e-mail',
            'Akceptacja jest wymagana',
            'żeński',
            'męski',
            'nie',
            'sam',
            'z opiekunem'
        ), $string);
    }
}
